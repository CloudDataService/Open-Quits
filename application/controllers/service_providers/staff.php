<?php

class Staff extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();
		$this->auth->check_master_logged_in();

		$this->load->model('model_service_providers');

		$this->layout->set_title('Staff');
		$this->layout->set_breadcrumb('Staff', '/service-providers/staff');
	}


	function index()
	{
		$this->data['service_provider_staff'] = $this->model_service_providers->get_service_provider_staff($this->session->userdata('sp_id'));

		$this->layout->set_view_script('service_providers/staff/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function update($sps_id = 0)
	{
		if( ! $service_provider_staff = $this->model_service_providers->get_service_provider_staff_member($this->session->userdata('sp_id'), $sps_id))
			show_404();

		if(@$_GET['delete'])
		{
			if(@$_GET['delete'])
			{
				$this->model_service_providers->delete_service_provider_staff($service_provider_staff['id']);

				redirect('/service-providers/staff');
			}
		}

		if(@$_POST['fname'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags');
			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_service_provider_staff_email_unique[' . @$service_provider_staff['id'] . ']');

			if($this->form_validation->run())
			{
				/* update an existing staff member */
				$this->model_service_providers->update_service_provider_staff_details($service_provider_staff['id']);

				$this->model_log->set_log('Changed ' . $service_provider_staff['fname'] . ' ' . $service_provider_staff['sname'] . '\'s account details.');

				if(@$_POST['master'])
				{
					/* make this staff member the master */
					$this->model_service_providers->set_master($this->session->userdata('sp_id'), $sps_id);

					redirect('/logout');
				}

				redirect(current_url());
			}
		}
		elseif($this->input->post('spst_date') && $service_provider_staff)
		{
			//training
			$this->load->library('form_validation');

			$this->form_validation->set_rules('spst_date', '', 'required|parse_date');
			$this->form_validation->set_rules('spst_title', '', 'strip_tags');

			if($this->form_validation->run())
			{
				/* add new training date */
				$this->model_service_providers->add_staff_training($service_provider_staff['id'], $this->input->post());

				$this->model_log->set_log('Recorded training date for ' . $service_provider_staff['fname'] . ' ' . $service_provider_staff['sname'] . '.');
			}

			redirect(current_url());
		}
		elseif(@$_POST['new_password'] && $service_provider_staff)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('new_password', '', 'required|password_restrict|matches[new_password_confirmed]');

			if($this->form_validation->run())
			{
				$this->model_service_providers->update_service_provider_staff_password($service_provider_staff['id']);

				$this->model_log->set_log('Changed ' . $service_provider_staff['fname'] . ' ' . $service_provider_staff['sname'] . '\'s account password.');

				redirect(current_url());
			}
		}

		$this->data['service_provider_staff'] = $service_provider_staff;

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/staff/update.js'));
		$this->layout->set_title('Update staff member');
		$this->layout->set_breadcrumb('Update staff member');
		$this->layout->set_view_script('service_providers/staff/update');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function add()
	{
		if($_POST)
		{
		$this->load->library('form_validation');

			$this->form_validation->set_rules('fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags');
			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_service_provider_staff_email_unique[' . @$service_provider_staff['id'] . ']');
			$this->form_validation->set_rules('password', '', 'required|password_restrict|matches[password_confirmed]');

			if($this->form_validation->run())
			{
				/* update an existing staff member */
				$this->model_service_providers->add_service_provider_staff($this->session->userdata('sp_id'), $this->input->post('password'));

				$this->model_log->set_log($this->input->post('fname') . ' ' . $this->input->post('sname') . ' added to staff members.');

				if(@$_POST['master'])
				{
					/* make this staff member the master */
					$this->model_service_providers->set_master($this->session->userdata('sp_id'), $sps_id);

					redirect('/logout');
				}

				redirect('/service-providers/staff');
			}

		}

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/staff/add.js'));
		$this->layout->set_title('Add staff member');
		$this->layout->set_breadcrumb('Add staff member');
		$this->layout->set_view_script('service_providers/staff/add');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function staff_email($sps_id = 0)
	{
		$this->load->library('form_validation');
		$_POST['email'] = $this->input->get('email');
		$this->form_validation->set_rules('email', '', 'check_service_provider_staff_email_unique[' . $sps_id . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


}
