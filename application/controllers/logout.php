<?php

class Logout extends My_controller
{


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		// Cancel timeout logout in development mode and just go back to previous page
		if (ENVIRONMENT === 'development' && $this->input->get('timeout'))
		{
			redirect($_SERVER['HTTP_REFERER']);
		}

		$this->session->sess_destroy();

		if(@$_GET['timeout'])
		{
			redirect('/?timeout=1');
		}
		else
		{
			redirect('/?logged_out=1');
		}
	}

}
