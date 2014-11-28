<?php

class Communications extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->model(array('communications_model', 'sms_templates_model', 'sms_model', 'model_monitoring_forms'));

		$this->layout->set_title('Communications');
		$this->layout->set_breadcrumb('Communications', '/admin/communications');
	}




	public function index()
	{
		redirect('');
	}




	/**
	 * Create a new communication based on MF search parameters
	 */
	public function create_from_search()
	{

		$this->layout->set_title('Send Bulk SMS');
		$this->layout->set_breadcrumb('Send Bulk SMS');

		if ($this->input->post())
		{
			$template = $this->sms_templates_model->get($this->input->post('sms_t_id'));

			// Create new communication
			$c_data = array(
				'c_notes' => $this->input->post('c_notes'),
				'c_text' => $template['sms_t_text'],
				'c_type' => 's',
				'c_status' => 'w',
				'c_total_clients' => count($this->input->post('mf_id')),
			);

			$c_id = $this->communications_model->insert($c_data);

			if ( ! $c_id)
			{
				$this->session->set_flashdata('action', 'There was an error creating the communication entry.');
				redirect('admin/monitoring_forms/index');
				return;
			}


			// Should have all MF IDs in the POST data
			$mf_ids = $this->input->post('mf_id');

			// Need to get names and numbers of clients
			$clients = $this->model_monitoring_forms->get_by_ids($mf_ids);

			$this->load->library('sms');

			$messages = array();
			$created = time();

			foreach ($clients as $client)
			{
				$messages[] = array(
					's_c_id' => $c_id,		// communication
					's_mf_id' => $client['monitoring_form_id'],		// monitoring form attached to
					's_sms_t_id' => $template['sms_t_id'],		// template ID
					's_message' => $this->sms->str_replace($template['sms_t_text'], $client),		// name replacement
					's_to_number' => str_replace(' ', '', $client['tel_mobile']),		// format number
					's_a_id' => $this->session->userdata('admin_id'), // sender
					's_sps_id' => NULL, // wasnt sent by staff
					's_status' => NULL,		// initial status
					's_created' => $created,		// timestamp
				);
			}

			if ($this->sms_model->add_bulk($messages))
			{
				$this->session->set_flashdata('action', 'The messages have been added to the queue and will be delivered soon.');
			}
			else
			{
				$this->session->set_flashdata('action', 'There was an error adding the messages to the queue.');
			}

			redirect('admin/monitoring_forms/index');
			return;
		}

		// No template? No can do!
		if ( ! $this->input->get('sms_t_id'))
		{
			$this->session->set_flashdata('action', 'Please choose an SMS template to use.');
			redirect('admin/monitoring_forms/index?' . http_build_query($this->input->get()));
			return;
		}

		// Get MFs from params
		$this->data['monitoring_forms'] = $this->model_monitoring_forms->get_monitoring_forms(0, FALSE);

		// Get the template they want to use
		$this->data['template'] = $this->sms_templates_model->get($this->input->get('sms_t_id'));

		$this->layout->set_view_script('/admin/communications/create_from_search');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}



}

/* End of file: ./application/controllers/admin/communications.php */