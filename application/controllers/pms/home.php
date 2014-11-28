<?php

class Home extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_pms_logged_in();
		$this->layout->set_title('Dashboard');
		$this->layout->set_breadcrumb('Dashboard', '/pms');

		$this->load->model('appointments_model');
		$this->load->helper('array');
	}


	function index()
	{
		$_GET['order'] = 'a_created_datetime';
		$_GET['sort'] = 'asc';

		$this->data['recent_appointments'] = $this->appointments_model->get_all_appointments(0, 5);

		$filter = array(
			'date_from' => date('d/m/Y'),
			'date_field' => 'a_datetime',
		);
		$total = $this->appointments_model->count_all_appointments($filter);
		$this->data['map_appointments'] = $this->appointments_model->get_all_appointments(0, $total, $filter);

		$this->layout->set_css(array('../scripts/leaflet/leaflet.css', '../scripts/leaflet/MarkerCluster.css', '../scripts/leaflet/MarkerCluster.Default.css'));
		$this->layout->set_javascript(array('/leaflet/leaflet.js', '/leaflet/leaflet.markercluster.js'));

		$this->layout->set_view_script('pms/home');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


}
