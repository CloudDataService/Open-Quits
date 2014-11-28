<?php

class Options extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->helper('array');

		$this->layout->set_title('Options');
		$this->layout->set_breadcrumb('Options', '/admin/options');
	}


	function index()
	{
		$this->layout->set_view_script('/admin/options/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function security_options()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model(array('model_auth', 'model_pcts'));

		$this->data['contact_details'] = $this->model_options->get_pct_option('contact_details', $this->input->get('pct_id'));
		//$this->data['security_options'] = $this->model_options->get_pct_option('security_options', $this->input->get('pct_id'));
		$this->data['support_options'] = $this->model_options->get_global_option('support_options');

		if(@$_GET['unblock'])
		{
			$this->model_auth->unblock_ip($_GET['unblock']);
			redirect('/admin/options/security-options');
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('organisation_name', '', 'strip_tags');
			$this->form_validation->set_rules('point_of_contact', '', 'strip_tags');
			$this->form_validation->set_rules('address', '', 'strip_tags');
			$this->form_validation->set_rules('email', '', 'valid_email');
			$this->form_validation->set_rules('telephone', '', 'strip_tags');
			$this->form_validation->set_rules('problems_logging_in', '', 'strip_tags');

			if($this->form_validation->run())
			{
				if ($this->input->post('problems_logging_in'))
				{
					$support_options = array(
						'problems_logging_in' => $this->input->post('problems_logging_in'),
						'tel_support_enabled' => (int) $this->input->post('tel_support_enabled'),
						'tel_support' => ($this->input->post('tel_support_enabled') ? $this->input->post('tel_support') : $this->data['support_options']['tel_support']),
					);

					$this->model_options->set_option('support_options', $support_options);
				}

				if ($this->input->post('organisation_name'))
				{
					$contact_details = array(
						'organisation_name' => $this->input->post('organisation_name'),
						'point_of_contact' => $this->input->post('point_of_contact'),
						'address' => $this->input->post('address'),
						'email' => $this->input->post('email'),
						'telephone' => $this->input->post('telephone'),
					);

					$this->model_options->set_option('contact_details', $contact_details, $this->input->post('pct_id'));
				}

				$this->session->set_flashdata('action', 'Options saved');

				redirect(current_url() . '?pct_id=' . $this->input->post('pct_id'));
			}
		}


		$this->data['blocked_ips'] = $this->model_auth->get_blocked_ips();

		$this->data['pcts'] = $this->model_pcts->get_pcts_select();
		$this->data['options_pct_id'] = $this->input->get('pct_id');

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/security_options.js'));
		$this->layout->set_title('Security options');
		$this->layout->set_breadcrumb('Security options');
		$this->layout->set_view_script('/admin/options/security_options');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function claim_options()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$claim_options = $this->model_options->get_pct_option('claim_options', $this->input->get('pct_id'));

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('initial', '', 'numeric');
			$this->form_validation->set_rules('follow_up_quit', '', 'numeric');
			$this->form_validation->set_rules('automatic_pass_to_finance_emails', '', 'valid_emails');
			$this->form_validation->set_rules('rejected_claims_email_note', '', 'max_length[500]');
			$this->form_validation->set_rules('pct_id', '', 'required');

			if($this->form_validation->run())
			{
				$claim_options = array('time_of_last_process' => $claim_options['time_of_last_process'],
									   'initial' => $this->input->post('initial'),
									   'claim_4_week' => $this->input->post('claim_4_week'),
									   'claim_12_week' => $this->input->post('claim_12_week'),
									   'interval' => $this->input->post('interval'),
									   'export_schema_id' => (int)$this->input->post('export_schema_id'),
									   'automatic_pass_to_finance' => $this->input->post('automatic_pass_to_finance'),
									   'automatic_email' => $this->input->post('automatic_email'),
									   'automatic_emails' => ($this->input->post('automatic_email') ? $this->input->post('automatic_emails') : $claim_options['automatic_emails']),
									   'rejected_claims_email' => array('enabled' => $this->input->post('rejected_claims_email_enabled'),
																		'note' => ($this->input->post('rejected_claims_email_enabled') ? $this->input->post('rejected_claims_email_note') : $claim_options['rejected_claims_email']['note']))
									   );

				$this->model_options->set_option('claim_options', $claim_options, $this->input->post('pct_id'));

				$this->session->set_flashdata('action', 'Claim options saved');

				redirect(current_url() . '?pct_id=' . $this->input->post('pct_id'));
			}
		}

		// Set default values for new 4/12 week options as the same value as follow up quit
		if ( ! element('claim_4_week', $claim_options)) $claim_options['claim_4_week'] = element('follow_up_quit', $claim_options);	//['follow_up_quit'];
		if ( ! element('claim_12_week', $claim_options)) $claim_options['claim_12_week'] = element('follow_up_quit', $claim_options);	//$claim_options['follow_up_quit'];

		$this->data['claim_options'] = $claim_options;

		$this->load->model(array('model_export_schemas', 'model_pcts'));

		$this->data['export_schemas'] = $this->model_export_schemas->get_export_schemas_select('monitoring_form_claims');

		$this->data['intervals'] = array('Every day' => '1', 'Every week' => '7', 'Every fortnight' => '14', 'Every month' => '30');

		$this->data['pcts'] = $this->model_pcts->get_pcts_select();
		$this->data['options_pct_id'] = $this->input->get('pct_id');

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/claim_options.js'));
		$this->layout->set_title('Claim options');
		$this->layout->set_breadcrumb('Claim options');
		$this->layout->set_view_script('/admin/options/claim_options');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function groups()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model(array('model_service_providers', 'model_pcts'));

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', '', 'required');
			$this->form_validation->set_rules('initial', '', 'required|numeric');
			$this->form_validation->set_rules('claim_4_week', '', 'required|numeric');
			$this->form_validation->set_rules('claim_12_week', '', 'required|numeric');
			$this->form_validation->set_rules('do_not_claim', '', '');
			$this->form_validation->set_rules('pct_id', '', '');

			if($this->form_validation->run())
			{
				$this->model_service_providers->set_group();

				redirect('/admin/options/groups');
			}
		}

		$this->data['groups'] = $this->model_service_providers->get_groups();
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/groups/index.js'));
		$this->layout->set_title('Groups');
		$this->layout->set_breadcrumb('Groups');
		$this->layout->set_view_script('/admin/options/groups/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function group($group_id)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model(array('model_service_providers', 'model_pcts'));

		if( ! $group = $this->model_service_providers->get_group($group_id))
			show_404();

		if(@$_POST['name'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', '', 'required');
			$this->form_validation->set_rules('initial', '', 'required|numeric');
			$this->form_validation->set_rules('claim_4_week', '', 'required|numeric');
			$this->form_validation->set_rules('claim_12_week', '', 'required|numeric');
			$this->form_validation->set_rules('do_not_claim', '', '');

			if($this->form_validation->run())
			{
				$this->model_service_providers->set_group($group['id']);

				redirect(current_url());
			}
		}
		elseif(@$_POST['unassigned_service_providers'])
		{
			foreach($_POST['unassigned_service_providers'] as $sp_id)
			{
				$this->model_service_providers->assign_to_group($sp_id, $group['id']);
			}

			$total_assigned = count($_POST['unassigned_service_providers']);

			$this->session->set_flashdata('action', ($total_assigned == 1 ? '1 service provider' : $total_assigned . ' service providers') . ' assigned to group');

			redirect(current_url());
		}
		elseif(@$_POST['assigned_service_providers'])
		{
			foreach($_POST['assigned_service_providers'] as $sp_id)
			{
				$this->model_service_providers->assign_to_group($sp_id, NULL);
			}

			$total_unassigned = count($_POST['assigned_service_providers']);

			$this->session->set_flashdata('action', ($total_unassigned == 1 ? '1 service provider' : $total_unassigned . ' service providers') . ' unassigned from group');

			redirect(current_url());
		}
		elseif(@$_GET['delete'])
		{
			$this->model_service_providers->delete_group($group['id']);

			redirect('/admin/options/groups');
		}

		$this->data['group'] = $group;

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select_group($group['id']);
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/groups/index.js'));
		$this->layout->set_title(array('Groups', $group['name']));
		$this->layout->set_breadcrumb(array('Groups' => '/admin/options/groups', $group['name'] => ''));
		$this->layout->set_view_script('/admin/options/groups/group');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function my_account()
	{
		$this->load->model('model_admins');

		if(@$_POST['email'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_admin_email_unique[' . $this->session->userdata('admin_id') . ']');
			$this->form_validation->set_rules('fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags');

			if($this->form_validation->run())
			{
				$this->model_admins->update_admin_details($this->session->userdata('admin_id'));

				redirect(current_url());
			}
		}
		elseif(@$_POST['current_password'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('current_password', '', 'required|check_admin_current_password[' . $this->session->userdata('admin_id') . ']');
			$this->form_validation->set_rules('new_password', '', 'required|password_restrict|matches[new_password_confirmed]');

			if($this->form_validation->run())
			{
				$this->model_admins->update_admin_password($this->session->userdata('admin_id'));

				redirect(current_url());
			}
		}

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/my_account.js'));
		$this->layout->set_title('My account');
		$this->layout->set_breadcrumb('My account');
		$this->layout->set_view_script('/admin/options/my_account');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function my_account_email()
	{
		$_POST['email'] = $_GET['email'];

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', '', 'check_admin_email_unique[' . $this->session->userdata('admin_id') . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function my_account_check_current_password()
	{
		$_POST['current_password'] = $_GET['current_password'];

		$this->load->library('form_validation');
		$this->form_validation->set_rules('current_password', '', 'check_admin_current_password[' . (int)$this->session->userdata('admin_id') . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function my_account_check_password_valid()
	{
		$_POST['new_password'] = $_GET['new_password'];

		$this->load->library('form_validation');
		$this->form_validation->set_rules('new_password', '', 'password_restrict');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function administrators($admin_id = 0)
	{
		$this->auth->check_master_logged_in();

		$this->load->model(array('model_admins', 'model_pcts'));

		if($admin = $this->model_admins->get_admin($admin_id))
		{
			$this->data['title'] = 'Update administrator';
		}
		else
		{
			$this->data['title'] = 'Add administrator';
		}

		if(@$_GET['delete'])
		{
			$this->model_admins->delete_admin(@$admin['id']);

			redirect('/admin/options/administrators');
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags');
			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_admin_email_unique[' . @$admin['id'] . ']');
			$this->form_validation->set_rules('password', '', 'password_restrict|matches[password_confirmed]');
			$this->form_validation->set_rules('pct_id', '', 'numeric');

			if($this->form_validation->run())
			{
				$admin_id = $this->model_admins->set_admin(@$admin['id']);

				if(@$_POST['master'])
				{
					$this->model_admins->set_master_admin($admin_id);

					redirect('/logout');
				}

				redirect('/admin/options/administrators');
			}
		}

		$this->data['admin'] = @$admin;

		$this->data['admins'] = $this->model_admins->get_admins();
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/administrators.js'));
		$this->layout->set_title('Administrators');
		$this->layout->set_breadcrumb('Administrators');
		$this->layout->set_view_script('/admin/options/administrators');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	function pms_staff($pmss_id = 0)
	{
		$this->auth->check_master_logged_in();

		$this->load->model('pms_model');

		if ($pmss = $this->pms_model->get_staff($pmss_id))
		{
			$this->data['title'] = 'Update PMS staff';
		}
		else
		{
			$this->data['title'] = 'Add PMS staff';
		}

		if ($this->input->get('delete'))
		{
			$this->pms_model->delete_pmss(@$pmss['pmss_id']);
			redirect('/admin/options/pms-staff');
		}

		if ($this->input->post())
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('pmss_fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('pmss_sname', '', 'required|strip_tags');
			$this->form_validation->set_rules('pmss_email', '', 'required|valid_email|matches[pmss_email_confirmed]|check_pmss_email_unique[' . @$pmss['pmss_id'] . ']');

			if ($pmss_id)
			{
				$this->form_validation->set_rules('pmss_password', '', 'password_restrict|matches[pmss_password_confirmed]');
			}
			else
			{
				$this->form_validation->set_rules('pmss_password', '', 'password_restrict|matches[pmss_password_confirmed]');
			}

			if ($this->form_validation->run())
			{
				$data = array(
					'pmss_fname' => $this->input->post('pmss_fname'),
					'pmss_sname' => $this->input->post('pmss_sname'),
					'pmss_email' => $this->input->post('pmss_email'),
				);

				// Setting password?
				if ($this->input->post('pmss_password'))
				{
					$data['pmss_password'] = $this->input->post('pmss_password');
				}

				if ($pmss_id)
				{
					// Update
					$this->pms_model->update_staff($pmss_id, $data);
					$this->session->set_flashdata('action', 'The PMS staff member has been updated.');
				}
				else
				{
					// Add
					$this->pms_model->insert_staff($data);
					$this->session->set_flashdata('action', 'New PMS staff member has been added.');
				}

				redirect('/admin/options/pms-staff');
			}
			else
			{
				echo validation_errors();
			}
		}

		$this->data['pmss'] = @$pmss;

		$this->data['all_staff'] = $this->pms_model->get_all_staff();

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/pms_staff.js'));
		$this->layout->set_title('PMS Staff');
		$this->layout->set_breadcrumb('PMS Staff');
		$this->layout->set_view_script('/admin/options/pms_staff');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	function pmss_email($pmss_id = 0)
	{
		$_POST['pmss_email'] = $_GET['pmss_email'];

		$this->load->library('form_validation');
		$this->form_validation->set_rules('pmss_email', '', 'check_pms_email_unique[' . $pmss_id . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}




	function advisors($page = 0)
	{
		$this->load->model('advisors_model');

		if ($this->input->get('a_id'))
		{
			if ($this->input->get('delete') == 1)
			{
				$this->advisors_model->delete($this->input->get('a_id'));
				$this->session->set_flashdata('action', 'Advisor has been removed.');
				redirect('/admin/options/advisors');
			}

			$this->data['advisor'] = $this->advisors_model->get_advisor($this->input->get('a_id'));
			$this->data['set_title'] = 'Update advisor';
		}
		else
		{
			$this->data['advisor'] = array();
			$this->data['set_title'] = 'Add new advisor';
		}

		if ($this->input->post())
		{
			// Data array for advisor
			$data = array(
				'a_number' => $this->input->post('a_number'),
				'a_fname' => $this->input->post('a_fname'),
				'a_sname' => $this->input->post('a_sname'),
			);

			// Add new or update existing advisor
			if ($this->input->post('a_id'))
			{
				$this->advisors_model->update($this->input->post('a_id'), $data);
				$this->session->set_flashdata('action', 'Advisor has been updated.');
				redirect('/admin/options/advisors');
			}
			else
			{
				$this->advisors_model->insert($data);
				$this->session->set_flashdata('action', 'New advisor has been added.');
				redirect('/admin/options/advisors');
			}
		}

		$total = $this->advisors_model->get_total_advisors();

		$this->load->library('pagination');

		$config['base_url'] = '/admin/options/advisors/';
		$config['total_rows'] = $total;
		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);
		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$this->data['advisors'] = $this->advisors_model->get_all_advisors($page, $config['per_page']);
		$this->data['total'] = ($this->data['advisors'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['advisors'])) . ' of ' . $total . '.' : '0 results');
		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'];
		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_title('Advisors');
		$this->layout->set_breadcrumb('Advisors');
		$this->layout->set_view_script('admin/options/advisors');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}



	function admin_email($admin_id = 0)
	{
		$_POST['email'] = $_GET['email'];

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', '', 'check_admin_email_unique[' . $admin_id . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function sms_options()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model('model_pcts');

		$sms_options = $this->model_options->get_pct_option('sms_options', $this->input->get('pct_id'));

		$this->data['total_sms_sent'] = $this->model_options->get_global_option('total_sms_sent');

		$this->load->library('sms');

		$this->data['total_sms_remaining'] = $this->sms->get_balance();

		if($_POST)
		{
			$sms_options = array('enabled' => $this->input->post('enabled'),
								 'texts'   => array('welcome' 		=> array('enabled' => @$_POST['texts']['welcome']['enabled'],
																  			 'value'   => (@$_POST['texts']['welcome']['enabled'] ? strip_tags(@$_POST['texts']['welcome']['value']) : $sms_options['texts']['welcome']['value'])),
											   'week_1' 			=> array('enabled' => @$_POST['texts']['week_1']['enabled'],
																             'value'   => (@$_POST['texts']['week_1']['enabled'] ? strip_tags(@$_POST['texts']['week_1']['value']) : $sms_options['texts']['week_1']['value'])),
											   'week_2' 			=> array('enabled' => @$_POST['texts']['week_2']['enabled'],
																  			 'value'   => (@$_POST['texts']['week_2']['enabled'] ? strip_tags(@$_POST['texts']['week_2']['value']) : $sms_options['texts']['week_2']['value'])),
											   'week_3' 			=> array('enabled' => @$_POST['texts']['week_3']['enabled'],
																  			 'value'   => (@$_POST['texts']['week_3']['enabled'] ? strip_tags(@$_POST['texts']['week_3']['value']) : $sms_options['texts']['week_3']['value'])),
											   'follow_up_reminder' => array('enabled' => @$_POST['texts']['follow_up_reminder']['enabled'],
																  			 'value'   => (@$_POST['texts']['follow_up_reminder']['enabled'] ? strip_tags(@$_POST['texts']['follow_up_reminder']['value']) : $sms_options['texts']['follow_up_reminder']['value'])),
                                               'follow_up_12_reminder' => array('enabled' => @$_POST['texts']['follow_up_12_reminder']['enabled'],
																			 'value'   => (@$_POST['texts']['follow_up_12_reminder']['enabled'] ? strip_tags(@$_POST['texts']['follow_up_12_reminder']['value']) : @$sms_options['texts']['follow_up_12_reminder']['value'])),
											   'quit' 				=> array('enabled' => @$_POST['texts']['quit']['enabled'],
																  			 'value'   => (@$_POST['texts']['quit']['enabled'] ? strip_tags(@$_POST['texts']['quit']['value']) : $sms_options['texts']['quit']['value'])),
											   'lost_to_follow_up'	=> array('enabled' => @$_POST['texts']['lost_to_follow_up']['enabled'],
																  			 'value'   => (@$_POST['texts']['lost_to_follow_up']['enabled'] ? strip_tags(@$_POST['texts']['lost_to_follow_up']['value']) : $sms_options['texts']['lost_to_follow_up']['value'])))
								 );

			$this->model_options->set_option('sms_options', $sms_options, $this->input->post('pct_id'));

			$this->session->set_flashdata('action', 'SMS options saved');

			redirect(current_url() . '?pct_id=' . $this->input->post('pct_id'));
		}

		$this->data['sms_options'] = $sms_options;
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();
		$this->data['options_pct_id'] = $this->input->get('pct_id');

		$this->layout->set_javascript(array('/views/admin/options/sms_options.js'));
		$this->layout->set_title('SMS options');
		$this->layout->set_breadcrumb('SMS options');
		$this->layout->set_view_script('/admin/options/sms_options');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Manage SMS templates
	 */
	function sms_templates($sms_t_id = 0)
	{
		$this->load->model(array('sms_templates_model'));
		$this->load->helper('text_helper');

		$this->data['template'] = array();

		if ($sms_t_id !== 0)
		{
			if ($sms_template = $this->sms_templates_model->get($sms_t_id))
			{
				$this->data['template'] = $sms_template;
			}

			if ($this->input->get('delete') == 1)
			{
				$this->sms_templates_model->delete($sms_t_id);
				$this->session->set_flashdata('action', 'SMS template deleted.');
				redirect('/admin/options/sms_templates');
			}
		}

		if ($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('sms_t_title', '', 'required');
			$this->form_validation->set_rules('sms_t_text', '', 'required|max_length[1024]');

			if ($this->form_validation->run())
			{
				$data = array(
					'sms_t_title' => $this->input->post('sms_t_title'),
					'sms_t_text' => $this->input->post('sms_t_text'),
					'sms_t_enabled' => (int) $this->input->post('sms_t_enabled'),
				);

				if ($this->input->post('sms_t_id'))
				{
					$this->sms_templates_model->update($this->input->post('sms_t_id'), $data);
					$this->session->set_flashdata('action', 'SMS template updated.');
				}
				else
				{
					$this->sms_templates_model->insert($data);
					$this->session->set_flashdata('action', 'New SMS template added.');
				}

				redirect('/admin/options/sms_templates');
			}
		}

		$this->data['templates'] = $this->sms_templates_model->get_all();

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/sms_templates.js'));
		$this->layout->set_title('SMS Templates');
		$this->layout->set_breadcrumb('SMS Templates');
		$this->layout->set_view_script('/admin/options/sms_templates');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Manage marketing sources
	 */
	function marketing_sources($ms_id = 0)
	{
		$this->load->model(array('marketing_sources_model'));

		$ms_id = (int) $ms_id;
		$this->data['ms'] = array();

		if ($ms_id !== 0)
		{
			if ($ms = $this->marketing_sources_model->get($ms_id))
			{
				$this->data['ms'] = $ms;
			}

			if ($this->input->get('delete') == 1)
			{
				$this->marketing_sources_model->delete($ms_id);
				$this->session->set_flashdata('action', 'Marketing source deleted.');
				redirect('/admin/options/marketing_sources');
			}
		}

		if ($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('ms_title', '', 'required|max_length[128]');
			$this->form_validation->set_rules('ms_active', '', 'required|integer|exact_length[1]');

			if ($this->form_validation->run())
			{
				$data = array(
					'ms_title' => $this->input->post('ms_title'),
					'ms_active' => (int) $this->input->post('ms_active'),
				);

				if ($this->input->post('ms_id'))
				{
					$this->marketing_sources_model->update($this->input->post('ms_id'), $data);
					$this->session->set_flashdata('action', 'Marketing source updated.');
				}
				else
				{
					$this->marketing_sources_model->insert($data);
					$this->session->set_flashdata('action', 'Marketing source added.');
				}

				redirect('/admin/options/marketing_sources');
			}
		}

		$this->data['marketing_sources'] = $this->marketing_sources_model->get_all();

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/marketing_sources.js'));
		$this->layout->set_title('Marketing Sources');
		$this->layout->set_breadcrumb('Marketing Sources');
		$this->layout->set_view_script('/admin/options/marketing_sources');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	function terms_and_conditions()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model('model_pcts');

		if($_POST)
		{
			$terms_and_conditions = array(
				'datetime_set' => time(),
				'value' => $this->input->post('value'),
				'last_changes' => $this->input->post('last_changes'),
				'on' => $this->input->post('on'),
			);

			$this->model_options->set_option('terms_and_conditions', $terms_and_conditions, $this->input->post('pct_id'));

			$this->session->set_flashdata('action', 'Terms and conditions saved');

			redirect(current_url() . '?pct_id=' . $this->input->post('pct_id'));
		}

		$this->data['terms_and_conditions'] = $this->model_options->get_pct_option('terms_and_conditions', $this->input->get('pct_id'));
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();
		$this->data['options_pct_id'] = $this->input->get('pct_id');

		$this->layout->set_javascript(array('/tiny_mce/jquery.tinymce.js', '/views/admin/options/terms_and_conditions.js'));
		$this->layout->set_title('Terms and conditions');
		$this->layout->set_breadcrumb('Terms and conditions');
		$this->layout->set_view_script('/admin/options/terms_and_conditions');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function resources($page = 0)
	{
		$this->load->model('model_resources');

		if(@$_GET['resource_id'])
		{
			$resource = $this->model_resources->get_resource(@$_GET['resource_id']);
		}

		if(@$_GET['cat_id'])
		{
			$resource_category = $this->model_resources->get_resource_category(@$_GET['cat_id']);
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			if(@$_POST['cat_title'])
			{
				$this->form_validation->set_rules('cat_title', '', 'required|strip_tags');

				if($this->form_validation->run())
				{
					$this->model_resources->set_resource_category(@$resource_category['id']);

					redirect('/admin/options/resources');
				}
			}
			else
			{

				$this->form_validation->set_rules('title', '', 'required|strip_tags');
				$this->form_validation->set_rules('cat_id', '', 'required');
				$this->form_validation->set_rules('description', '', 'required|strip_tags');

				$link = $this->input->post('link');
				if (!empty($link)) {
					$this->form_validation->set_rules('link', 'link', 'required|strip_tags|prep_url');
				}

				if($this->form_validation->run())
				{
					if(@$_FILES['userfile']['name'])
					{
						$config['upload_path'] = $this->config->config['base_dir'] . '/resources';
						$config['allowed_types'] = 'pdf|doc|docx';

						$this->load->library('upload', $config);

						if ( ! $this->upload->do_upload())
						{
							$this->data['upload_errors'] = $this->upload->display_errors('<label class="error">', '</label>');
						}
						else
						{
							$data = $this->upload->data();
						}
					}

					if (@$resource && ! isset($data))
					{
						$data = array();
						$data['file_name'] = (isset($resource['file_name']) && !empty($resource['file_name'])) ? $resource['file_name'] : null;
						$data['file_size'] = (isset($resource['file_size']) && !empty($resource['file_size'])) ? $resource['file_size'] : null;
						$data['file_size'] = preg_replace("/([^0-9.])/ui", "", $data['file_size']);
					}

					if (!isset($data))
					{
						$data = array();
						$data['file_name'] = null;
						$data['file_size'] = null;
					}

					$set_resource = $this->model_resources->set_resource(@$resource['id'], $data);
					redirect('/admin/options/resources');
				}

			}

		}

		if(@$_GET['restore'])
		{
			if ($this->model_resources->restore_resource($_GET['restore']) !== false)
			{
				redirect('/admin/options/resources');
			}
		}

		if(@$_GET['delete'])
		{
			if ($this->model_resources->delete_resource($_GET['delete']) !== false)
			{
				redirect('/admin/options/resources');
			}
		}

		if(@$_GET['delete_cat'])
		{
			if ($this->model_resources->delete_resource_category(@$_GET['delete_cat']) !== false)
			{
				redirect('/admin/options/resources');
			}
		}

		$total_resources = $this->model_resources->get_total_resources();

		$this->load->library('pagination');
		$config['base_url'] = '/admin/options/resources/';
		$config['total_rows'] = $total_resources;
		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);

		$this->data['resources'] = $this->model_resources->get_resources($page, $config['per_page']);
		$this->data['deleted_resources'] = $this->model_resources->get_deleted_resources();

		$this->data['total'] = ($this->data['resources'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['resources'])) . ' of ' . $total_resources . '.' : '0 results');
		$this->data['sort'] = '&amp;sort=' . ($_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'];
		$this->data['page'] = $page;
		$this->data['pp'] = array('10', '20', '50', '75', '100', '200');
		$this->data['resource'] = @$resource;
		$this->data['resource_category'] = @$resource_category;
		$this->data['resource_categories'] = $this->model_resources->get_resource_categories();

		$this->layout->set_title('Resources');
		$this->layout->set_breadcrumb('Resources');
		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/resources.js'));
		$this->layout->set_view_script('admin/options/resources');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function export_schemas()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model(array('model_export_schemas', 'model_pcts'));

		if(@$_GET['mf_schema_id'])
		{
			$schema = $this->model_export_schemas->get_export_schema(@$_GET['mf_schema_id'], 'monitoring_forms');

			$this->data['mf_schema'] = $schema;
		}
		elseif(@$_GET['claim_schema_id'])
		{
			$schema = $this->model_export_schemas->get_export_schema(@$_GET['claim_schema_id'], 'monitoring_form_claims');

			$this->data['claim_schema'] = $schema;
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('type', '', 'required');
			$this->form_validation->set_rules('pct_id', '', '');
			$this->form_validation->set_rules('title', '', 'required|strip_tags');
			$this->form_validation->set_rules('fields', '', 'required');

			if($this->form_validation->run())
			{
				$this->model_export_schemas->set_export_schema(@$schema['id']);

				redirect('/admin/options/export-schemas');
			}

		}

		if(@$_GET['delete_schema'])
		{
			$this->model_export_schemas->delete_schema($_GET['delete_schema']);

			redirect('/admin/options/export-schemas');
		}

		$this->data['monitoring_form_schemas'] = $this->model_export_schemas->get_export_schemas('monitoring_forms');
		$this->data['claim_schemas'] = $this->model_export_schemas->get_export_schemas('monitoring_form_claims');

		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		echo validation_errors();

		$this->layout->set_title('Export schemas');
		$this->layout->set_breadcrumb('Export schemas');
		$this->layout->set_javascript(array('/jquery-ui.min.js', '/views/admin/options/export_schemas.js'));
		$this->layout->set_view_script('admin/options/export_schemas');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function pcts()
	{
		$this->load->model('model_pcts');

		# user selects pct
		if(@$_GET['pct_id'])
		{
			# does the PCT exist?
			if($pct = $this->model_pcts->get_pct(@$_GET['pct_id']))
			{
				# user is trying to delete pct
				if(@$_GET['delete'])
				{
					# delete pct
					$this->model_pcts->delete_pct($pct);

					# redirect back to pct page
					redirect('/admin/options/pcts');
				}

				if(@$_POST['unassigned_service_providers'])
				{
					foreach($_POST['unassigned_service_providers'] as $sp_id)
					{
						$this->model_pcts->assign_to_pct($sp_id, $pct['pct_name']);
					}

					$total_assigned = count($_POST['unassigned_service_providers']);

					$this->session->set_flashdata('action', ($total_assigned == 1 ? '1 service provider' : $total_assigned . ' service providers') . ' assigned to PCT');

					redirect('/admin/options/pcts?pct_id=' . $pct['id']);
				}
				elseif(@$_POST['assigned_service_providers'])
				{
					foreach($_POST['assigned_service_providers'] as $sp_id)
					{
						$this->model_pcts->assign_to_pct($sp_id, NULL);
					}

					$total_unassigned = count($_POST['assigned_service_providers']);

					$this->session->set_flashdata('action', ($total_unassigned == 1 ? '1 service provider' : $total_unassigned . ' service providers') . ' unassigned from PCT');

					redirect('/admin/options/pcts?pct_id=' . $pct['id']);
				}

				# get list of service providers that are assigned and unassigned to this pct
				$this->data['service_providers_select'] = $this->model_pcts->get_service_providers_select($pct['pct_name']);
			}

		}

		# adding or updating a new pct
		if(@$_POST['pct_name'])
		{
			# load form validation library
			$this->load->library('form_validation');

			# pct_name is required
			$this->form_validation->set_rules('pct_name', '', 'required');

			# check to see if validation works
			if($this->form_validation->run() )
			{
				# add or update pct
				$this->model_pcts->set_pct(@$pct);

				# redirect back to pct page
				redirect('/admin/options/pcts');
			}
		}

		# get all listed pcts
		$this->data['pcts'] = $this->model_pcts->get_pcts();

		# assign pct to view
		$this->data['selected_pct'] = @$pct;

		$this->layout->set_title('PCTs');
		$this->layout->set_breadcrumb('PCTs');
		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/admin/options/pcts.js'));
		$this->layout->set_view_script('admin/options/pcts');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


}
