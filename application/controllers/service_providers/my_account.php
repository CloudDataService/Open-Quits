<?php

class My_account extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->load->model('model_service_providers');

		$this->layout->set_title('My account');
		$this->layout->set_breadcrumb('My account', '/service-providers/account');
	}


	function index()
	{
		if(@$_POST['email'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_service_provider_staff_email_unique[' . $this->session->userdata('sps_id') . ']');
			$this->form_validation->set_rules('fname', '', 'required');
			$this->form_validation->set_rules('sname', '', 'required');

			if($this->form_validation->run())
			{
				$this->model_service_providers->update_service_provider_staff_details($this->session->userdata('sps_id'));

				$this->model_log->set_log('Personal account details updated.');

				redirect(current_url());
			}
		}
		elseif(@$_POST['current_password'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('current_password', '', 'required|check_service_provider_staff_current_password[' . $this->session->userdata('sps_id') . ']');
			$this->form_validation->set_rules('new_password', '', 'required|password_restrict|matches[new_password_confirmed]');

			if($this->form_validation->run())
			{
				$this->model_service_providers->update_service_provider_staff_password($this->session->userdata('sps_id'));

				/* unset change password notification */
				if($this->session->userdata('change_password'))
					$this->session->unset_userdata('change_password');

				$this->model_log->set_log('Personal account password changed.');

				redirect(current_url());
			}
		}

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/my_account.js'));
		$this->layout->set_view_script('service_providers/my_account');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function email()
	{
		$this->load->library('form_validation');
		$_POST['email'] = $_GET['email'];
		$this->form_validation->set_rules('email', '', 'check_service_provider_staff_email_unique[' . $this->session->userdata('sps_id') . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function check_password_current()
	{
		$this->load->library('form_validation');
		$_POST['current_password'] = $_GET['current_password'];
		$this->form_validation->set_rules('current_password', '', 'check_service_provider_staff_current_password[' . (int)$this->session->userdata('sps_id') . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function check_password_valid()
	{
		$this->load->library('form_validation');
		$_POST['new_password'] = $_GET['new_password'];
		$this->form_validation->set_rules('new_password', '', 'password_restrict');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}

}
