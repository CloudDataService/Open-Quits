<?php

class Home extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->load->model(array('model_dashboard', 'appointments_model'));

		$this->layout->set_title('Dashboard');
		$this->layout->set_breadcrumb('Dashboard', '/service-providers');
	}


	function index()
	{
		$sp_id = $this->session->userdata('sp_id');

		$this->data['approaching_follow_up'][4] = $this->model_dashboard->get_approaching_follow_up($sp_id, 4);
		$this->data['approaching_follow_up'][12] = $this->model_dashboard->get_approaching_follow_up($sp_id, 12);

		$this->data['appointment_options'] = $this->appointments_model->get_options($sp_id);

		$this->layout->set_javascript('/views/service_providers/home.js');
		$this->layout->set_view_script('service_providers/home');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function get_graph($range = 'month')
	{
		echo $this->model_dashboard->get_graph($range, $this->session->userdata('sp_id'));
	}


}
