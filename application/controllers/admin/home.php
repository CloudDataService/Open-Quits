<?php

class Home extends My_controller
{

	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->model('model_dashboard');

		$this->layout->set_title('Dashboard');
		$this->layout->set_breadcrumb('Dashboard', '/admin');
	}

	function index()
	{
		$this->data['latest_claims'] = $this->model_dashboard->get_latest_claims();
		$this->layout->set_javascript('/views/admin/home.js');
		$this->layout->set_view_script('admin/home');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}

	function get_graph($range = 'month')
	{
		echo $this->model_dashboard->get_graph($range);
	}
}
