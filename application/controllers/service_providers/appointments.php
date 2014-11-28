<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointments extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->layout->set_title('Appointments');
		$this->layout->set_breadcrumb('Appointments', '/service-providers/appointments');

		$this->load->model(array('appointments_model', 'model_service_providers'));
		$this->load->helper(array('array', 'form'));
		$this->load->config('datasets');

		$this->data['sp_id'] = $this->session->userdata('sp_id');
	}




	public function index($page = 0)
	{
		$this->data['master'] = ($this->session->userdata('master'));

		// Get array of items to filter main data with for search/filter
		$filter = $this->input->get();

		if ( ! element('date_from', $filter))
			$filter['date_from'] = date('d/m/Y');

		if ( ! element('date_to', $filter))
			$filter['date_to'] = date('d/m/Y', strtotime('+7 days'));

		// Use datetime of appointment for range by default
		$filter['date_field'] = element('date_field', $filter, 'a_datetime');

		// Ensure the service provider ID is always set to current service provider
		$filter['a_sp_id'] = $this->data['sp_id'];

		$total = $this->appointments_model->count_all_appointments($filter);

		$this->load->library('pagination');
		$config['base_url'] = '/service-providers/appointments/';
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


		// If the filter form has not been submitted get next 10 confirmed upcoming appointments
		if ( ! $this->input->get('x'))
		{
			$filter = array(
				'a_sp_id' => $this->data['sp_id'],
				'date_field' => 'a_datetime',
				'future' => 1,
				'a_status' => 'Confirmed',
			);

			$_GET['order'] = 'a_datetime';
			$_GET['sort'] = 'asc';

			$this->data['upcoming'] = $this->appointments_model->get_all_appointments(0, 20, $filter);
		}

		$this->layout->set_title('Appointments');
		$this->layout->set_breadcrumb('Appointments');
		$this->layout->set_view_script('service_providers/appointments/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	public function options()
	{
		//$this->auth->check_master_logged_in();
		$this->data['options'] = $this->appointments_model->get_options($this->data['sp_id']);

		if ($this->input->post())
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('ao_length', 'Appointment length', 'required|integer');
			for ($num = 1; $num <= 7; $num++)
			{
				$this->form_validation->set_rules("day[$num][ao_first_appt_time]", "First appointment time", "");
				$this->form_validation->set_rules("day[$num][ao_last_appt_time]", "Last appointment time", "");
				$this->form_validation->set_rules("day[$num][ao_capacity]", "Capacity", "required|integer");
			}

			if ($this->form_validation->run())
			{
				$data = array();

				$post_data = $this->input->post('day');

				for ($num = 1; $num <= 7; $num++)
				{
					$data[] = array(
						'ao_sp_id' => $this->data['sp_id'],
						'ao_first_appt_time' => ( (int) $post_data[$num]['ao_capacity'] > 0) ? date('H:i:00', strtotime($post_data[$num]['ao_first_appt_time'])) : NULL,
						'ao_last_appt_time' => ( (int) $post_data[$num]['ao_capacity'] > 0) ? date('H:i:00', strtotime($post_data[$num]['ao_last_appt_time'])) : NULL,
						'ao_capacity' => (int) $post_data[$num]['ao_capacity'],
						'ao_length' => $this->input->post('ao_length'),
						'ao_day_of_week' => $num,
					);
				}

				if ($this->appointments_model->set_options($this->data['sp_id'], $data))
				{
					$this->session->set_flashdata('action', 'Options have been saved.');
				}

				redirect('/service-providers/appointments');
			}

		}

		$this->layout->set_title('Appointment options');
		$this->layout->set_breadcrumb('Appointment options');
		$this->layout->set_view_script('service_providers/appointments/options');

		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Page to add or reschedule an appointment
	 */
	function schedule($start = 'now')
	{
		$this->data['sp'] = $this->model_service_providers->get_service_provider($this->data['sp_id']);

		// Set start date as now or next week
		if ($start === 'now')
		{
			$start_date = time();
			$this->data['title'] = 'This week';
		}
		elseif ($start === 'next')
		{
			$start_date = strtotime('+7 days');
			$this->data['title'] = 'Next week';
		}

		// Get the options
		$this->data['ao'] = $this->appointments_model->get_options($this->data['sp_id']);

		if ( ! $this->data['ao'])
			show_error('No opening times have been set.');

		@$ao_length = $this->data['ao'][1]['ao_length'];

		// Date range for week
		$start_date_format = date('Y-m-d', $start_date);
		$end_date_format = date('Y-m-d', strtotime('+7 days', $start_date));

		$week_start = new DateTime($start_date_format);
		$week_end = new DateTime($end_date_format);
		$period_interval = new DateInterval('P1D');
		$this->data['days'] = new DatePeriod($week_start, $period_interval, $week_end);

		// First & last appointment time across all days for SP
		$first_last_times = $this->appointments_model->get_first_last_appt_times($this->data['sp_id']);

		// Time range during the day
		$first_appt_time = new DateTime($first_last_times['first']);
		$last_appt_time = new DateTime($first_last_times['last']);
		$last_appt_time = $last_appt_time->modify('+' . $ao_length . ' minutes');
		$period_interval = new DateInterval('PT' . $ao_length . 'M');

		// Available time slots
		$this->data['slots'] = new DatePeriod($first_appt_time, $period_interval, $last_appt_time);

		// Appointment count for times/dates within the range
		$this->data['appt_count'] = $this->appointments_model->count_within_range($this->data['sp_id'], $start_date_format, $end_date_format);

		// Start (either now or next, 7 days)
		$this->data['start'] = $start;

		// Rescheduling appointment?
		if ($this->input->get('action') === 'reschedule')
		{
			// Rescheduling appointment. Get info about it first
			$reschedule_a_id = $this->input->get('a_id');
			$appointment = $this->appointments_model->get($reschedule_a_id);
			$this->data['reschedule'] = $appointment;

			$this->layout->set_title('Reschedule appointment');
			$this->layout->set_breadcrumb('Reschedule appointment');
		}
		else
		{
			// Making a new appointment
			$this->layout->set_title('Make new appointment');
			$this->layout->set_breadcrumb('Make new appointment');
		}

		$this->load->vars($this->data);
		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/appointments/schedule.js'));
		$this->layout->set_view_script('service_providers/appointments/schedule');
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
		$sp_id = $this->data['sp_id'];
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

				// Send email to client?

				if ( (int) $this->input->post('send_email') === 1)
				{
					if ($this->appointments_model->send_email($a_id))
					{
						$this->session->set_flashdata('action', 'The appointment details have been saved and an email has been sent to the client.');
					}
				}

				redirect('service-providers/appointments/set/' . $a_id);
			}
		}

		$this->data['appointment'] =& $appointment;
		$this->data['sp'] = $this->model_service_providers->get_service_provider($appointment['a_sp_id']);

		$this->data['form_elements']['titles'] = array('Mr', 'Mrs', 'Miss', 'Ms', 'Other');

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/appointments/set.js'));

		$this->layout->set_view_script('service_providers/appointments/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Cancel an appointment during the data capture process
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
			redirect('service-providers/appointments');
		}
	}




	/**
	 * Update the status of an appointment once it has been confirmed for the first time
	 */
	public function update()
	{
		$a_id = $this->input->post('a_id');
		$new_status = $this->input->post('a_status');

		$uri = 'service-providers/appointments/set/' . $a_id;

		if ( ! in_array($new_status, array('Reserved','Confirmed','Cancelled (Client)','Cancelled (SP)','Attended','DNA')))
			redirect($uri);

		$this->appointments_model->update($a_id, array('a_status' => $this->input->post('a_status')));

		// Set flash status depending on updated status
		switch ($new_status)
		{
			case 'Cancelled (Client)':
			case 'Cancelled (SP)':
				$flash = 'The appointment has been cancelled.';
				break;

			case 'Attended':
				$flash = 'The appointment was attended by the client.';
				break;

			case 'DNA':
				$flash = 'The client did not attend this appointment.';
				break;
		}

		$this->session->set_flashdata('action', $flash);
		redirect($uri);
	}




}
