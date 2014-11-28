<?php

class Ajax extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		// Auth checking
		$session = $this->session->userdata('logged_in');
		$admin = $this->session->userdata('admin_id');
		$provider = $this->session->userdata('sps_id');

		$valid = ($session && ($admin || $provider));

		if ( ! $valid)
		{
			show_error('Not logged in.');
		}
	}


	public function get_gps()
	{
		$this->load->model('model_gps');
		$data = $this->model_gps->get_gps_ajax();

		$gps = array();

		foreach ($data as $gp)
		{
			$gps[] = $gp['gp'];
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($gps));
	}




	/**
	 * Search for advisors to add to a MF
	 */
	public function get_advisors()
	{
		$this->load->model('advisors_model');
		$data = $this->advisors_model->get_advisors_ajax();

		$advisors = array();

		foreach ($data as $advisor)
		{
			$advisors[] = array('label' => $advisor['advisor'], 'value' => $advisor['a_id']);
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($advisors));
	}


	/**
	 * Get a users Advisor ID (guess)
	 */
	public function get_advisor_id()
	{
		$this->load->model('advisors_model');
		$data = $this->advisors_model->get_advisors_ajax();

		$advisor = array();

		if (count($data) > 0)
		{
			$advisor = array(
				'label' => $data[0]['advisor'],
				'value' => $data[0]['a_id'],
				'code' => $data[0]['code'],
			);
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($advisor));
	}


	/**
	 * Update a users Advisor ID
	 */
	public function set_advisor_id()
	{
		$this->load->model('model_service_providers');

		$user_id = $this->session->userdata('sps_id');

		$_POST['email'] = $this->session->userdata('email');
		$_POST['fname'] = $this->session->userdata('fname');
		$_POST['sname'] = $this->session->userdata('sname');

		$response = $this->model_service_providers->update_service_provider_staff_details($user_id);

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($response));
	}


}
