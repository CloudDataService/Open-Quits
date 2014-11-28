<?php

class Appointments extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_pms_logged_in();
		$this->layout->set_title('Appointments');
		$this->layout->set_breadcrumb('Appointments', '/pms/appointments');

		$this->load->model(array('model_service_providers', 'postcode_model', 'appointments_model'));
		$this->load->helper('array');
		$this->load->config('datasets');

		// Remove expired reserved appointment bookings
		$this->appointments_model->prune();
	}



	function index($page = 0)
	{
		// Get array of items to filter data with
		$filter = $this->input->get();

		if ( ! element('date_from', $filter))
			$filter['date_from'] = date('d/m/Y');

		if ( ! element('date_to', $filter))
			$filter['date_to'] = date('d/m/Y', strtotime('+7 days'));

		// Use datetime of appointment for range by default
		$filter['date_field'] = element('date_field', $filter, 'a_datetime');

		$total = $this->appointments_model->count_all_appointments($filter);

		$this->load->library('pagination');
		$config['base_url'] = '/pms/appointments/';
		$config['total_rows'] = $total;
		$config['per_page'] = (@$_GET['pp'] ? (int) $_GET['pp'] : $_GET['pp'] = 20);
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);

		$this->data['appointments'] = $this->appointments_model->get_all_appointments($page, $config['per_page'], $filter);
		$this->data['total'] = ($this->data['appointments'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['appointments'])) . ' of ' . $total . '.' : '0 results');

		$sort = array('sort' => (@$_GET['sort'] == 'asc' ? 'desc' : 'asc'));
		$sort += $filter;
		$this->data['sort'] = '&amp;' . http_build_query($sort, '', '&amp;');
		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['filter'] =& $filter;

		$this->layout->set_view_script('pms/appointments/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Creates an initial appointment by reserving the date & time.
	 *
	 * Called by XHR and returns JSON.
	 */
	function book()
	{
		$res = array();

		// Get data from POST
		$sp_id = $this->input->post('sp_id');
		$date = $this->input->post('date');
		$time = $this->input->post('time');

		// For rescheduling
		$a_id = $this->input->post('a_id');

		if ($a_id)
		{
			// Reschedule appointment
			if ($this->appointments_model->reschedule($a_id, $sp_id, $date, $time))
			{
				$res['status'] = 'ok';
				$res['a_id'] = $a_id;
				$res['reschedule'] = TRUE;
			}
			else
			{
				$res['status'] = 'err';
				$res['msg'] = 'The appointment could not be re-scheduled for that time.';
			}
		}
		else
		{
			// Creating new appointment
			$a_id = $this->appointments_model->create($sp_id, $date, $time);

			if ($a_id === FALSE)
			{
				$res['status'] = 'err';
				$res['msg'] = 'No appointments available for that time.';
			}
			else
			{
				$res['status'] = 'ok';
				$res['a_id'] = $a_id;
			}
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($res, JSON_NUMERIC_CHECK));
	}




	/**
	 * Update the appointment and client details
	 */
	public function set($a_id = 0)
	{
		$appointment = $this->appointments_model->get($a_id);

		if ( ! $appointment)
		{
			show_error('Could not find appointment by ID ' . $a_id);
		}

		if ($this->input->post())
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('ac_title', 'Title', 'required');
			$this->form_validation->set_rules('ac_fname', 'First name', 'required');
			$this->form_validation->set_rules('ac_sname', 'Last name', 'required');
			$this->form_validation->set_rules('ac_address', 'Address', '');
			$this->form_validation->set_rules('ac_post_code', 'Post code', '');
			$this->form_validation->set_rules('ac_tel_daytime', 'Daytime tel', '');
			$this->form_validation->set_rules('ac_tel_mobile', 'Mobile tel', '');
			$this->form_validation->set_rules('ac_email', 'Email address', '');

			if ($this->form_validation->run())
			{
				// OK!

				// Client data
				$client_data = array(
					'ac_title' => $this->input->post('ac_title'),
					'ac_title_other' => ($this->input->post('ac_title_other')) ?: NULL,
					'ac_fname' => $this->input->post('ac_fname'),
					'ac_sname' => $this->input->post('ac_sname'),
					'ac_address' => $this->input->post('ac_address'),
					'ac_post_code' => format_postcode($this->input->post('ac_post_code')),
					'ac_tel_daytime' => $this->input->post('ac_tel_daytime'),
					'ac_tel_mobile' => $this->input->post('ac_tel_mobile'),
					'ac_email' => $this->input->post('ac_email'),
				);

				// Add or update client
				$ac_id = $this->appointments_model->set_client($appointment['a_ac_id'], $client_data);

				// Appointment data array to use for update
				$appt_data = array(
					'a_ac_id' => $ac_id,
				);

				// Check for appointment status
				if ($appointment['a_status'] == 'Reserved')
				{
					// Appointment WAS reserved - is now being updated for the first time
					// Set status to Confirmed
					$appt_data['a_status'] = 'Confirmed';
				}
				else
				{
					// Allow the status to be set by post data if currently any other status than Reserved
					if ($this->input->post('a_status'))
					{
						$appt_data['a_status'] = $this->input->post('a_status');
					}
				}

				// Update the appointment
				if ($this->appointments_model->update($a_id, $appt_data))
				{
					$this->session->set_flashdata('action', 'The appointment details have been saved.');
				}
				else
				{
					show_error('There was an error updating the appointment.');
				}

				if ( (int) $this->input->post('send_email') === 1)
				{
					if ($this->appointments_model->send_email($a_id))
					{
						$this->session->set_flashdata('action', 'The appointment details have been saved and an email has been sent to the client.');
					}
				}

				redirect('pms/appointments/set/' . $a_id);
			}
		}

		$this->data['appointment'] =& $appointment;
		$this->data['sp'] = $this->model_service_providers->get_service_provider($appointment['a_sp_id']);

		$this->data['form_elements']['titles'] = array('Mr', 'Mrs', 'Miss', 'Ms', 'Other');

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/pms/appointments/set.js'));

		$this->layout->set_view_script('pms/appointments/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Cancel an appointment, to abandon it during the client detail capture process
	 */
	public function cancel($a_id = 0)
	{
		$appointment = $this->appointments_model->get($a_id);

		if ( ! $appointment)
		{
			show_error('Could not find appointment by ID ' . $a_id);
		}

		if ($appointment['a_status'] === 'Reserved')
		{
			$this->appointments_model->delete($a_id);
			redirect('pms/service_providers/search?post_code=' . $this->session->userdata('search_post_code'));
		}

		if ($this->input->get('source') === 'client')
		{
			$this->appointments_model->update($a_id, array('a_status' => 'Cancelled (Client)'));
			$this->session->set_flashdata('action', 'The appointment has been cancelled.');
			redirect('pms/appointments/set/' . $a_id);
		}
	}


}
