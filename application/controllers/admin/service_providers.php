<?php

class Service_providers extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->model('model_service_providers');
		$this->load->helper('array');
		$this->load->config('datasets');

		$this->layout->set_title('Service providers');
		$this->layout->set_breadcrumb('Service providers', '/admin/service-providers');
	}


	function index($page = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$total = $this->model_service_providers->get_total_service_providers();

		$this->load->library('pagination');

		$config['base_url'] = '/admin/service-providers/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$config['suffix'] = '?' . http_build_query($_GET);

		$this->pagination->initialize($config);

		$this->data['service_providers'] = $this->model_service_providers->get_service_providers($page, $config['per_page']);

		if($total == 1)
		{
			redirect('/admin/service-providers/info/' . $this->data['service_providers'][0]['id']);
		}

		$this->data['total'] = ($this->data['service_providers'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['service_providers'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'];

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		# load pct model
		$this->load->model('model_pcts');

		# get list of pcts
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_view_script('admin/service_providers/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function info($sp_id)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! $service_provider = $this->model_service_providers->get_service_provider($sp_id))
			show_404();

		/* change active status */
		if(isset($_GET['active_status']))
		{
			$this->model_service_providers->set_active_status($service_provider['id'], $_GET['active_status']);

			// update cache
			$this->model_service_providers->get_service_providers_select(0);

			redirect(current_url());
		}

		if(@$_GET['delete'])
		{
			$this->auth->check_master_logged_in();

			$this->model_service_providers->delete_service_provider($service_provider['id']);

			// update cache
			$this->model_service_providers->get_service_providers_select(0);

			redirect('/admin/service-providers');
		}

		$this->data['service_provider'] = $service_provider;

		$this->data['service_provider_staff'] = $this->model_service_providers->get_service_provider_staff($sp_id);

		$this->data['default_password'] = substr(md5($this->config->config['salt'] . 'foo' . $service_provider['id'] . 'bar' . $this->config->config['salt']), 0, 8);

		$this->layout->set_title($service_provider['name']);
		$this->layout->set_breadcrumb($service_provider['name']);
		$this->layout->set_view_script('admin/service_providers/info');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function set($sp_id = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$service_provider = $this->model_service_providers->get_service_provider($sp_id);

		if($sp_id && $service_provider)
		{
			$title = 'Update service provider';
		}
		else
		{
			$title = 'Add new service provider';
			$sp_id = 0;
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', '', 'required');
			$this->form_validation->set_rules('post_code', '', 'required');
			$this->form_validation->set_rules('location', '', '');
			$this->form_validation->set_rules('location_other', '', '');
			$this->form_validation->set_rules('department', '', '');
			$this->form_validation->set_rules('venue', '', '');
			$this->form_validation->set_rules('telephone', '', '');
			$this->form_validation->set_rules('advisor_code', '', '');
			$this->form_validation->set_rules('provider_code', '', '');
			$this->form_validation->set_rules('cost_code', '', '');
			$this->form_validation->set_rules('group_id', '', 'integer');
			$this->form_validation->set_rules('claim_options_initial', '', 'numeric');
			$this->form_validation->set_rules('claim_options_follow_up_quit', '', 'numeric');
			$this->form_validation->set_rules('tier_3', '', 'integer');
			$this->form_validation->set_rules('pct_id', '', 'integer');

			if($this->form_validation->run())
			{
				$sp_id = $this->model_service_providers->set_service_provider(@$service_provider['id']);

				// update cache
				$this->model_service_providers->get_service_providers_select(0);

				redirect('/admin/service-providers/info/' . $sp_id);
			}
		}

		$this->data['groups'] = $this->model_service_providers->get_pct_groups_select();

		# load pct model
		$this->load->model('model_pcts');

		# get listed pcts
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		$this->data['service_provider'] = $service_provider;

		$this->data['title'] = $title;

		$this->data['locations'] = config_item('provider_locations');

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/service_providers/set.js'));
		$this->layout->set_title($title);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_view_script('admin/service_providers/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function staff($page = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$total = $this->model_service_providers->get_total_service_providers_staff();

		$this->load->library('pagination');

		$config['base_url'] = '/admin/service-providers/staff/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$this->data['service_providers_staff'] = $this->model_service_providers->get_all_service_providers_staff($page, $config['per_page']);

		if($total == 1)
		{
			$sp_id = $this->data['service_providers_staff'][0]['service_provider_id'];
			$sps_id = $this->data['service_providers_staff'][0]['id'];
			redirect("/admin/service-providers/set-staff/{$sp_id}/{$sps_id}");
		}

		$this->data['total'] = ($this->data['service_providers_staff'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['service_providers_staff'])) . ' of ' . $total . '.' : '0 results');

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'];

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		# load pct model
		$this->load->model('model_pcts');

		# get list of pcts
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_view_script('admin/service_providers/staff');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function set_staff($sp_id, $sps_id = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! $service_provider = $this->model_service_providers->get_service_provider($sp_id))
			show_404();

		if($service_provider_staff = $this->model_service_providers->get_service_provider_staff_member($service_provider['id'], $sps_id))
		{
			if(@$_GET['delete'])
			{
				$this->model_service_providers->delete_service_provider_staff($service_provider_staff['id']);

				redirect('/admin/service-providers/info/' . $service_provider['id']);
			}

			$title = 'Update staff member';

			$this->data['service_provider_staff'] = $service_provider_staff;
		}
		else
		{
			$title = 'Add new staff member';
		}

		$default_password = substr(md5($this->config->config['salt'] . 'foo' . $service_provider['id'] . 'bar' . $this->config->config['salt']), 0, 8);

		if(@$_POST['fname'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags');
			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_service_provider_staff_email_unique[' . @$service_provider_staff['id'] . ']');

			if($this->form_validation->run())
			{
				if( ! @$service_provider_staff)
				{
					/* add a new staff member */
					$sps_id = $this->model_service_providers->add_service_provider_staff($service_provider['id'], $default_password);

					$contact_details = $this->model_options->get_pct_option('contact_details', $service_provider['pct_id']);

					/* email vars */
					$email['subject'] = 'You now have an account with Call it Quits';

					$email['message'] = '<p>Dear, ' . $this->input->post('fname') . '.</p>
										<p>We are pleased to let you know that you now have your own Call it Quits account.</p>
										<p>For security reasons, we cannot send you your login details over email, we ask that you get in contact with <strong>' . $contact_details['organisation_name'] . '</strong> on <strong>' . $contact_details['telephone'] . '</strong> and ask to speak to <strong>' . $contact_details['point_of_contact'] . '.</strong></p>
										<p>' . $contact_details['organisation_name'] . ' will be able to provide you with the information you need to login and begin using the system.</p>';
				}
				else
				{
					/* update an existing staff member */
					$this->model_service_providers->update_service_provider_staff_details($service_provider_staff['id']);

					$contact_details = $this->model_options->get_pct_option('contact_details', $service_provider['pct_id']);

					/* emails vars */
					$email['subject'] = 'Your Call it Quits account information has changed';

					$email['message'] = '<p>Dear, ' . $this->input->post('fname') . '.</p>
										<p>Your Call it Quits account information has changed.</p>
										<p>For security reasons, we cannot send you your login details over email, we ask that you get in contact with <strong>' . $contact_details['organisation_name'] . '</strong> on <strong>' . $contact_details['telephone'] . '</strong> and ask to speak to <strong>' . $contact_details['point_of_contact'] . '.</strong></p>
										<p>' . $contact_details['organisation_name'] . ' will be able to provide you with the information you need to login and begin using the system.</p>';
				}

				if(@$_POST['master'])
				{
					/* make this staff member the master */
					$this->model_service_providers->set_master($service_provider['id'], $sps_id);
				}

				if(@$_POST['send_email'])
				{
					/* send an email notification */
					$this->load->library('email');
					$this->email->from('no-reply@openquits.net', 'Call it Quits');
					$this->email->to($this->input->post('email'));
					$this->email->subject($email['subject']);
					$this->email->message($email['message']);
					$this->email->send();
				}

				redirect('/admin/service-providers/info/' . $service_provider['id']);
			}
		}
		elseif(@$_POST['new_password'] && $service_provider_staff)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('new_password', '', 'required|password_restrict|matches[new_password_confirmed]');

			if($this->form_validation->run())
			{
				$this->model_service_providers->update_service_provider_staff_password($service_provider_staff['id']);

				if(@$_POST['send_email'])
				{
					$email['subject'] = 'Your Call it Quits account information has changed';

					$email['message'] = '<p>Dear, ' . $this->input->post('fname') . '.</p>
										<p>Your Call it Quits account password has changed.</p>
										<p>For security reasons, we cannot send you your login details over email, we ask that you get in contact with <strong>' . $contact_details['organisation_name'] . '</strong> on <strong>' . $contact_details['telephone'] . '</strong> and ask to speak to <strong>' . $contact_details['point_of_contact'] . '.</strong></p>
										<p>' . $contact_details['organisation_name'] . ' will be able to provide you with the information you need to login and begin using the system.</p>';

					/* send an email notification */
					$this->load->library('email');
					$this->email->from('no-reply@openquits.net', 'Call it Quits');
					$this->email->to($service_provider_staff['email']);
					$this->email->subject($email['subject']);
					$this->email->message($email['message']);
					$this->email->send();
				}

				redirect('/admin/service-providers/info/' . $service_provider['id']);
			}
		}

		$this->data['service_provider'] = $service_provider;

		$this->data['title'] = $title;

		$this->data['default_password'] = $default_password;

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/service_providers/set_staff.js'));
		$this->layout->set_title(array($service_provider['name'], $title));
		$this->layout->set_breadcrumb(array($service_provider['name'] => '/admin/service-providers/info/' . $service_provider['id'], $title => ''));
		$this->layout->set_view_script('admin/service_providers/set_staff');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function staff_email($sps_id = 0)
	{
		$this->load->library('form_validation');
		$_POST['email'] = $_GET['email'];
		$this->form_validation->set_rules('email', '', 'check_service_provider_staff_email_unique[' . $sps_id . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function check_password_valid()
	{
		$this->load->library('form_validation');
		$_POST['new_password'] = $_GET['new_password'];
		$this->form_validation->set_rules('new_password', '', 'password_restrict');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function activity_log($page = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		$total = $this->model_log->get_total_log(@$_GET['sp_id']);

		$this->load->library('pagination');

		$config['base_url'] = '/admin/service-providers/activity-log/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$this->data['log'] = $this->model_log->get_log($page, $config['per_page'], @$_GET['sp_id']);

		$this->data['total'] = ($this->data['log'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['log'])) . ' of ' . $total . '.' : '0 results');

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;date_from=' . $_GET['date_from'] . '&amp;date_to=' . $_GET['date_to'] . '&amp;sp_id=' . @$_GET['sp_id'];

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_javascript('/views/admin/service_providers/activity_log.js');
		$this->layout->set_title('Activity log');
		$this->layout->set_breadcrumb('Activity log');
		$this->layout->set_view_script('admin/service_providers/activity_log');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');

	}


	function csv()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		if(@$_GET['export'])
		{
			if($service_providers = $this->model_service_providers->get_csv())
			{
				$csv[] = array("Service provider ID", "Status", "Name", "Post code", "Location/setting", "Department/ward", "Venue", "Telephone", "Provider code", "Advisor code", "Cost code", "Local AUthority", "Group");

				foreach($service_providers as $sp)
				{
					$status = ($sp['active'] == 1 ? "Active" : "Inactive");
					$csv[] = array($sp['id'], $status, $sp['name'], $sp['post_code'], $sp['location'], $sp['department'], $sp['venue'], $sp['telephone'], $sp['provider_code'], $sp['advisor_code'], $sp['cost_code'], $sp['pct_name'], $sp['group_name']);
				}

				$file_name = md5($this->session->userdata('admin_id') . time()) . '.csv';

				$file_dir = APPPATH . '/csv/' . $file_name;

				$file = fopen($file_dir, "w");

				foreach ($csv as $line)
				{
					fputcsv($file, $line);
				}

				header('Content-Transfer-Encoding: none');
				header('Content-Type: text/csv;');
				header('Content-Disposition: attachment; filename="' . $file_name . '"');

				echo file_get_contents($file_dir);

				fclose($file);

				unlink($file_dir);
			}
		}
	}
}
