<?php

class Monitoring_forms extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->load->model('model_monitoring_forms');
		$this->load->helper('array');

		$this->load->config('datasets');

		$this->layout->set_title('Clients');
		$this->layout->set_breadcrumb('Clients', '/service-providers/monitoring-forms');
	}


	function index($page = 0)
	{
		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-3 months'));

		// Set default follow up to 4
		if ( ! isset($_GET['follow_up']))
			$_GET['follow_up'] = 4;

		$total = $this->model_monitoring_forms->get_total_monitoring_forms($this->session->userdata('sp_id'));

		$this->load->library('pagination');

		$config['base_url'] = '/service-providers/monitoring-forms/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$config['suffix'] = '?' . http_build_query($_GET);

		$this->pagination->initialize($config);

		$this->data['monitoring_forms'] = $this->model_monitoring_forms->get_monitoring_forms($page, $config['per_page'], $this->session->userdata('sp_id'));

		$this->data['total'] = ($this->data['monitoring_forms'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['monitoring_forms'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;date_from=' . $_GET['date_from'] . '&amp;date_to=' . $_GET['date_to'] . '&amp;sname=' . @$_GET['sname'] . '&amp;treatment_outcome=' . @$_GET['treatment_outcome'] . '&amp;date_type=' . @$_GET['date_type'];

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['treatment_outcomes'] = array('Follow up required', 'Not quit', 'Lost to follow-up', 'Referred to GP', 'Quit self-reported', 'Quit CO verified');

		$this->layout->set_javascript('/views/service_providers/monitoring_forms/index.js');
		$this->layout->set_view_script('/service_providers/monitoring_forms/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	function info($mf_id = 0)
	{
		if( ! $monitoring_form = $this->model_monitoring_forms->get_monitoring_form($mf_id, $this->session->userdata('sp_id')))
			show_404();

		$this->load->model(array('model_claims', 'mail_merge_documents_model'));

		$this->data['claims'] = $this->model_claims->get_claims($this->session->userdata('sp_id'), $monitoring_form['id']);

		$this->data['monitoring_form'] = $monitoring_form;

		// Available mail merge documents (to generate)
		$this->data['mail_merge_documents'] = $this->mail_merge_documents_model->get_all_dropdown($this->session->userdata('sp_id'));

		// History of mail merge documents already generated for this monitoring form
		$this->data['mail_merges'] = $this->model_monitoring_forms->get_mail_merges($mf_id);

		if ($this->input->get('delete_mfmm_id'))
		{
			if ($this->model_monitoring_forms->delete_mail_merge($this->input->get('delete_mfmm_id')))
			{
				$this->session->set_flashdata('action', 'Mail merge entry has been deleted.');
			}
			redirect('service-providers/monitoring-forms/info/' . $mf_id);
		}

		if($this->input->post('sms_t_id'))
		{
			//SMS sending...
			$this->load->library('sms');
			$this->load->model(array('sms_model', 'sms_templates_model'));
			$template = $this->sms_templates_model->get($this->input->post('sms_t_id'));

			// Need to get names and numbers of client
			$clients = $this->model_monitoring_forms->get_by_ids(array($mf_id => $mf_id));
			$client = $clients[0];

			$created = time();

			$messages = array(array(
				's_c_id' => NULL,		// not part of a bulk communication
				's_mf_id' => $mf_id,		// monitoring form attached to
				's_sms_t_id' => $this->input->post('sms_t_id'),		// template ID
				's_message' => $this->sms->str_replace($template['sms_t_text'], $client),		// name replacement
				's_to_number' => str_replace(' ', '', $client['tel_mobile']),		// format number
				's_a_id' => NULL, // sender
				's_sps_id' => $this->session->userdata('sps_id'), // wasnt sent by staff
				's_status' => NULL,		// initial status
				's_created' => $created,		// timestamp
			));

			//now send it
			if ($this->sms_model->add_bulk($messages))
			{
				$this->session->set_flashdata('action', 'The message has been added to the queue and will be delivered soon.');
			}
			else
			{
				$this->session->set_flashdata('action', 'There was an error adding the message to the queue.');
			}

			redirect('service-providers/monitoring_forms/info/'. $mf_id);
			return;
		}

		//get messages
		$this->load->model('sms_model');
		$this->data['smses'] = $this->sms_model->get_by_mf($monitoring_form['id']);

		//templates for sending messages
		$this->load->model('sms_templates_model');
		$this->data['sms_templates'] =  $this->sms_templates_model->get_all();

		$this->layout->set_title('#' . $monitoring_form['id']);
		$this->layout->set_breadcrumb('#' . $monitoring_form['id']);

		if(@$_GET['export'])
		{
			$this->layout->set_view_script('/service_providers/monitoring_forms/print');
			$this->load->vars($this->data);
			$this->load->view('/layouts/blank');
		}
		else
		{
			$this->layout->set_view_script('/service_providers/monitoring_forms/info');
			$this->load->vars($this->data);
			$this->load->view('/layouts/default');
		}

	}




	public function get_gps()
	{
		$this->load->model('model_gps');
		$data = $this->model_gps->get_gps_ajax();

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

		foreach ($data as $advisor)
		{
			$advisors[] = array('label' => $advisor['advisor'], 'value' => $advisor['a_id']);
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($advisors));
	}




	function set($mf_id = 0)
	{
		$this->load->model(array('model_service_providers', 'health_problems_model', 'marketing_sources_model'));

		if($monitoring_form = $this->model_monitoring_forms->get_monitoring_form($mf_id))
		{
			$title = 'Update';

			// if($_POST)
			// {
			// 	$_POST['date_of_4_week_follow_up']
			// 	$_POST['date_of_12_week_follow_up']
			// }
		}
		else
		{
			$title = 'Add';

			// Check: is this being added via an appointment?
			if ($this->input->get('a_id'))
			{
				// Load appointment as provided by GET param
				$this->load->model('appointments_model');
				$appointment = $this->appointments_model->get($this->input->get('a_id'));

				if ($appointment)
				{
					// Create "blank" monitoring form but with client details populated from appointment
					$monitoring_form = array(
						'title' => $appointment['ac_title'],
						'title_other' => $appointment['ac_title_other'],
						'fname' => $appointment['ac_fname'],
						'sname' => $appointment['ac_sname'],
						'address' => $appointment['ac_address'],
						'post_code' => $appointment['ac_post_code'],
						'tel_daytime' => $appointment['ac_tel_daytime'],
						'tel_mobile' => $appointment['ac_tel_mobile'],
					);

					$this->data['appointment'] = $appointment;
				}
			}
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('nhs_number', '', 'exact_length[10]|strtoupper|strip_tags');
			$this->form_validation->set_rules('title', '', 'required');
			$this->form_validation->set_rules('title_other', '', 'ucfirst' . $this->form_validation->other('title') . '|strip_tags');
			$this->form_validation->set_rules('fname', '', 'required|ucfirst|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|ucfirst|strip_tags');
			$this->form_validation->set_rules('gender', '', 'required|strip_tags');
			$this->form_validation->set_rules('date_of_birth', '', 'required');
			$this->form_validation->set_rules('address', '', 'required|strip_tags');
			$this->form_validation->set_rules('post_code', '', 'required|strtoupper|strip_tags');
			$this->form_validation->set_rules('email', '', 'valid_email|max_length[128]');
			$this->form_validation->set_rules('occupation_code', '', 'required|strip_tags');
			$this->form_validation->set_rules('ethnic_group', '', 'required|strip_tags');
			$this->form_validation->set_rules('gp_personal_name', '', 'strip_tags');
			$this->form_validation->set_rules('ms_id', '', 'required');
			$this->form_validation->set_rules('marketing_other', '', $this->form_validation->other('ms_id') . '|strip_tags');
			$this->form_validation->set_rules('agreed_quit_date', '', 'required|parse_date');
			$this->form_validation->set_rules('date_of_last_tobacco_use', '', 'required|parse_date');
			$this->form_validation->set_rules('date_of_4_week_follow_up', '', 'required|parse_date');
			$this->form_validation->set_rules('date_of_12_week_follow_up', '', 'parse_date');
			$this->form_validation->set_rules('intervention_type', '', 'required|strip_tags');
			$this->form_validation->set_rules('intervention_type_other', '', $this->form_validation->other('intervention_type') . '|strip_tags');
			$this->form_validation->set_rules('support_method', '', 'integer|exact_length[1]');
			$this->form_validation->set_rules('support_none', '', 'required|integer|exact_length[1]');
			$this->form_validation->set_rules('uncp', '', 'required|integer|exact_length[1]');
			$this->form_validation->set_rules('uncp_method', '', 'integer');
			$this->form_validation->set_rules('co_quit', '', 'integer');
			$this->form_validation->set_rules('health_problems[]', '');
			$this->form_validation->set_rules('health_problems_other', '', 'max_length[255]');
			$this->form_validation->set_rules('health_problems_not_reported', '', 'required|integer');
			$this->form_validation->set_rules('alcohol', '', 'required|integer');
			$this->form_validation->set_rules('alcohol_not_reported', '', 'required|integer');

			// find the gp code
			preg_match_all('^\#([A-Za-z0-9]+)^', $this->input->post('ci_gp'), $gp_code);

			// set gp code in post array
			$_POST['ci_gp'] = @$gp_code[1][0];

			// find the mf gp code
			preg_match_all('^\#([A-Za-z0-9]+)^', $this->input->post('mf_gp_code'), $mf_gp_code);

			// set gp code in post array
			$_POST['mf_gp_code'] = (@$mf_gp_code[1][0] ? $mf_gp_code[1][0] : NULL);

			if($this->form_validation->run())
			{
				# if tier 3, check to see if setting on behalf of tier 2
				if($this->input->post('sp_id'))
				{
					$this->auth->check_tier_3_logged_in();

					$service_provider = $this->model_service_providers->get_service_provider($this->input->post('sp_id'));

					# sets flag which will let log know later on what to say.
					$service_provider['on_behalf_of_tier_2'] = TRUE;
				}
				else
				{
					$service_provider = $this->model_service_providers->get_service_provider($this->session->userdata('sp_id'));
					$service_provider['on_behalf_of_tier_2'] = FALSE;
				}

				// If the 4 week outcome is lost-to-followup or not-quit, and the 12 week value is not set, the 12 week value should be populated with the 4 week value that was previously set or via the new POST value.
				$outcome_4_notquit_post = ($this->input->post('treatment_outcome_4') === 'Not quit' || $this->input->post('treatment_outcome_4') === 'Lost to follow-up');
				$outcome_4_notquit_current = (@$monitoring_form['treatment_outcome_4'] === 'Not quit' || @$monitoring_form['treatment_outcome_4'] === 'Lost to follow-up');
				$outcome_12 = $this->input->post('treatment_outcome_12');

				if ($outcome_4_notquit_post && empty($outcome_12))
				{
					$_POST['treatment_outcome_12'] = $this->input->post('treatment_outcome_4');
				}

				if ($outcome_4_notquit_current && empty($outcome_12))
				{
					$_POST['treatment_outcome_12'] = @$monitoring_form['treatment_outcome_4'];
				}

				# save monitoring form
				$mf_id = $this->model_monitoring_forms->set_monitoring_form($service_provider, @$monitoring_form['id']);

				# set any claims required
				$this->load->model('model_claims');

				// Check the claims that have already been submitted
				$current_4_claim = $this->model_claims->get_claim(@$monitoring_form['id'], '4 week');
				$current_12_claim = $this->model_claims->get_claim(@$monitoring_form['id'], '12 week');

				// Get SMS options that could be needed
				$sms_options = $this->model_options->get_pct_option('sms_options', $service_provider['pct_id']);

				if( ! @$monitoring_form['id'])
				{
					if ($claim_cost = $this->model_claims->get_claim_cost($service_provider['id'], 'initial'))
					{
						# add initial claim
						$this->model_claims->add_claim($mf_id, 'Initial', $claim_cost);
						$this->model_log->set_log('Initial claim set for monitoring form #' . $mf_id);
					}
				}

				if (empty($current_4_claim) && ($this->input->post('treatment_outcome_4') == 'Quit CO verified' || $this->input->post('treatment_outcome_4') == 'Quit self-reported'))
				{
					// add follow up quit claim if no previous 4-week claims submitted
					log_message('debug', "Monitoring form $mf_id does not have an existing 4 week claim. Adding new " . $this->input->post('treatment_outcome_4') . " claim...");

					if ($claim_cost = $this->model_claims->get_claim_cost($service_provider['id'], 'claim_4_week'))
					{
						$this->model_claims->add_claim($mf_id, '4 week', $claim_cost);
						$this->model_log->set_log('4 week quit claim set for monitoring form #' . $mf_id);
					}

					if ($this->input->post('sms'))
					{
						if ($sms_options['enabled'] && $sms_options['texts']['quit']['enabled'])
						{
							$this->load->library('sms');
							$this->sms->send($this->input->post('tel_mobile'), $this->sms->str_replace($sms_options['texts']['quit']['value'], $_POST));
						}
					}
				}

				if ( ! @$monitoring_form['treatment_outcome_4'] && $this->input->post('treatment_outcome_4') == 'Lost to follow-up')
				{
					if ($this->input->post('sms'))
					{
						if ($sms_options['enabled'] && $sms_options['texts']['lost_to_follow_up']['enabled'])
						{
							$this->load->library('sms');
							$this->sms->send($this->input->post('tel_mobile'), $this->sms->str_replace($sms_options['texts']['lost_to_follow_up']['value'], $_POST));
						}
					}
				}

				if (empty($current_12_claim) && ($this->input->post('treatment_outcome_12') == 'Quit CO verified' || $this->input->post('treatment_outcome_12') == 'Quit self-reported'))
				{
					// add follow up quit claim if no claim previously submitted
					if ($claim_cost = $this->model_claims->get_claim_cost($service_provider['id'], 'claim_12_week'))
					{
						$this->model_claims->add_claim($mf_id, '12 week', $claim_cost);
						$this->model_log->set_log('12 week quit claim set for monitoring form #' . $mf_id);
					}

					if ($this->input->post('sms'))
					{
						if ($sms_options['enabled'] && $sms_options['texts']['quit']['enabled'])
						{
							$this->load->library('sms');
							$this->sms->send($this->input->post('tel_mobile'), $this->sms->str_replace($sms_options['texts']['quit']['value'], $_POST));
						}
					}
				}

				if ( ! @$monitoring_form['treatment_outcome_12'] && $this->input->post('treatment_outcome_12') == 'Lost to follow-up')
				{
					if ($this->input->post('sms'))
					{
						if ($sms_options['enabled'] && $sms_options['texts']['lost_to_follow_up']['enabled'])
						{
							$this->load->library('sms');
							$this->sms->send($this->input->post('tel_mobile'), $this->sms->str_replace($sms_options['texts']['lost_to_follow_up']['value'], $_POST));
						}
					}
				}



				// Check if the monitoring form was created via an appointment
				if ($this->input->get('a_id'))
				{
					// Update the appointment with the monitoring form ID.
					$this->load->model('appointments_model');
					$this->appointments_model->update($this->input->get('a_id'), array('a_mf_id' => $mf_id));
				}

				redirect(($service_provider['id'] == $this->session->userdata('sp_id') ? '/service-providers/monitoring-forms/info/' . $mf_id : '/service-providers/monitoring-forms'));
			}

		}



		$this->data['form_elements']['titles'] = array('Mr', 'Mrs', 'Miss', 'Ms', 'Other');

		$this->data['form_elements']['genders'] = array('Male', 'Female');

		$this->data['form_elements']['months'] = array("January","February","March","April","May","June","July","August","September","October","November","December");

		$this->data['form_elements']['occupation_codes'] = array('Full-time student','Never worked/long-term unemployed','Retired','Home carer','Sick/disabled and unable to work','Managerial/professional','Intermediate','Routine manual','Prisoner','Unable to code');

		$this->data['form_elements']['ethnic_groups'] = array('White' => array('British','Irish','Other white background'),
															  'Mixed' => array('White and Black Caribbean','White and Black African','White and Asian','Other mixed groups'),
															  'Asian or Asian British' => array('Indian','Pakistani','Bangladeshi','Other Asian background'),
															  'Black or Black British' => array('Caribbean','African','Other Black background'),
															  'Other ethnic groups' => array('Chinese','Other ethnic group'),
															  'Other' => array('Not stated'));

		$this->data['form_elements']['intervention_types'] = array('Closed group', 'Open (rolling) group', 'One-to-one support', 'Telephone support', 'Couple/family', 'Drop-in clinic', 'Other');

		// $this->data['form_elements']['support'] = array('NRT - lozenge', 'NRT - microtab', 'NRT - inhalator', 'NRT - spray', 'NRT - gum', 'NRT - patch', 'Champix', 'Zyban', 'NRT - Quickmist');

		$this->data['form_elements']['treatment_outcomes'] = config_item('treatment_outcomes');

		$this->data['form_elements']['health_problems'] = $this->health_problems_model->get_all();

		$this->data['form_elements']['marketing_sources'] = result_assoc($this->marketing_sources_model->get_active(), 'ms_id', 'ms_title', '-- Please Select --');

		$this->data['form_elements']['support_methods'] = config_item('support_methods');

		$this->data['form_elements']['uncp_methods'] = config_item('uncp_methods');

		$this->data['monitoring_form'] = $monitoring_form;

		if($this->session->userdata('tier_3'))
		{
			$this->data['form_elements']['referral_sources'] = array('Advertising','Friend/Relative','General referrals from health professionals or T2 advisors/community','NHS Helpline referrals','Self','Other');

			$this->data['form_elements']['functions'] = array('Community','Pregnancy','Secondary Care','Workplace');

			$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();
		}

		$js = array(
			'/plugins/moment.min.js',
			'/plugins/jquery.validate.js',
			'/plugins/jquery.fancybox.js',
			'/views/service_providers/autosave.js',
			'/views/service_providers/dupe_search.js',
			'/views/service_providers/monitoring_forms/set.js'
		);

		$this->layout->set_css(array('fancybox.css'));
		$this->layout->set_javascript($js);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_view_script('/service_providers/monitoring_forms/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function ic_reports()
	{
		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-1 months'));

		$this->data['ic'] = $this->model_monitoring_forms->get_ic($this->session->userdata('sp_id'));

		$this->layout->set_javascript('/views/service_providers/monitoring_forms/ic_reports.js');
		$this->layout->set_title('IC reports');
		$this->layout->set_breadcrumb('IC reports');
		$this->layout->set_view_script('/service_providers/monitoring_forms/ic_reports');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function csv()
	{
		$_GET['sp_id'] = $this->session->userdata('sp_id');

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		// Set default follow up to 4
		if ( ! isset($_GET['follow_up']))
			$_GET['follow_up'] = 4;

		if(@$_GET['export'])
		{
			$this->load->model('model_export_schemas');

			$export_schema = $this->model_export_schemas->get_export_schema(0, 'monitoring_forms');

			$i = 0;

			$csv[$i] = array();

			foreach($export_schema['export_schema'] as $field_name => $description)
			{
				$csv[$i][] = $description;
			}

			if($monitoring_forms = $this->model_monitoring_forms->get_csv(@$_GET['sp_id']))
			{
				foreach($monitoring_forms as $mf)
				{
					$i++;
					foreach($export_schema['export_schema'] as $field_name => $description)
					{
						// Newlines and commas in addresses mess up CSV output
						if ($field_name == 'address' OR $field_name == 'gp_address')
						{
							$mf[$field_name] = preg_replace('/,|\s+/', ' ', $mf[$field_name]);
						}
						$csv[$i][] = element($field_name, $mf);
					}
				}
			}

			$file_name = md5($this->session->userdata('admin_id') . time()) . '.csv';

			$file_dir = $this->config->config['csv_dir'] . $file_name . '.csv';

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


	function statistics()
	{
		$this->layout->set_title('Statistics');
		$this->layout->set_breadcrumb('Statistics');
		$this->layout->set_view_script('/service_providers/monitoring_forms/statistics');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	# allows tier 3 service provider to upload/download CSV file of any monitoring forms that contain missing information
	function missing_information()
	{
		$this->auth->check_tier_3_logged_in();

		if($_FILES)
		{
			$config['upload_path'] = $this->config->config['csv_dir'];
			$config['allowed_types'] = 'csv';

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload())
			{
				$this->data['upload_errors'] = $this->upload->display_errors('<label class="error">', '</label>');
			}
			else
			{
				$data = $this->upload->data();

				$file = fopen($config['upload_path'] . '/' . $data['file_name'], "r");

				$updated = 0;

				while ($client = fgetcsv($file, 5000, ",")) {

					$client['status_code'] = @$client[0];
					$client['monitoring_form_id'] = @$client[1];
					$client['nhs_number' ] = @$client[3];
					$client['gp_code'] = @$client[59];

					if($client['status_code'] == '20')
					{
						$this->model_monitoring_forms->set_missing_information($client['monitoring_form_id'], $client['nhs_number'], $client['gp_code']);

						$updated++;
					}
				}

				fclose($file);

				unlink($config['upload_path'] . $data['file_name']);

				$this->session->set_flashdata('action', ($updated == 1 ? '1 row' : $updated . ' rows') . ' of missing information imported');

				redirect('/service-providers/monitoring-forms/missing-information');
			}
		}

		if(@$_GET['export'])
		{
			$clients = $this->model_monitoring_forms->get_missing_information();

			$csv = array();

			foreach($clients as $client)
			{
				$csv[] = array('10', $client['monitoring_form_id'], '', $client['nhs_number'], $client['date_of_birth_format'], '', '', '', $client['fname'], '', $client['sname'], '', $client['gender'], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $client['gp_code']);
			}

			$file_name = 'missing_information_' . md5($this->session->userdata('admin_id') . time()) . '.csv';

			$file_dir = $this->config->config['csv_dir'] . $file_name . '.csv';

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

			exit;
		}

		$this->data['total'] = $this->model_monitoring_forms->get_total_missing_information();

		$this->layout->set_title('Missing information');
		$this->layout->set_breadcrumb('Missing information');
		$this->layout->set_view_script('/service_providers/monitoring_forms/missing_information');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	public function mail_merge()
	{
		$err = FALSE;

		$this->load->model(array('mail_merge_documents_model', 'mail_merge_fields_model'));

		$mmd_id = (int) $this->input->post('mmd_id');
		$mf_id = (int) $this->input->post('mf');

		$monitoring_form = $this->model_monitoring_forms->get_monitoring_form($mf_id, $this->session->userdata('sp_id'));
		$mmd = $this->mail_merge_documents_model->get($mmd_id);

		if ( ! $monitoring_form OR ! $mmd)
		{
			redirect('service-providers/monitoring-forms/info/' . $mf_id);
		}

		// Log the creation of the document against the monitoring form
		$this->model_monitoring_forms->log_mail_merge(array(
			'mfmm_mf_id' => $mf_id,
			'mfmm_mmd_id' => $mmd_id,
		));

		// Convert document
		$this->data['document_content'] = $this->mail_merge_documents_model->process($monitoring_form, $monitoring_form, $mmd_id);
		$this->data['sp_name'] = $this->session->userdata('sp_name');

		// Load HTML to pass into dompdf
		$document = $this->load->view('service_providers/monitoring_forms/mail_merge', $this->data, TRUE);

		// Define filename
		$filename = sprintf('CiQ Mail Merge %s MF#%s %s',
			element('mmd_title', $mmd),
			element('id', $mf),
			date('Y-m-d His')
		);

		// Include the PDF library and generate it
		require_once(APPPATH . 'third_party/dompdf/dompdf_config.inc.php');
		$dompdf = new DOMPDF();
		$dompdf->load_html($document);
		$dompdf->render();

		// Page count
		//$c = $dompdf->get_canvas()->get_page_count();

		$pdf = $dompdf->output();

		// Set header to dorce download
		$this->output->set_content_type('application/pdf');
		$this->output->set_header("Content-Disposition: attachment; filename={$filename}.pdf");
		$this->output->set_header("Cache-Control: private");
		$this->output->set_header("Pragma: token");
		$this->output->set_header("Expires: 0");
		$this->output->set_output($pdf);
	}




	public function dupe_search()
	{
		$params = $this->input->post();

		$results = $this->model_monitoring_forms->dupe_search($params);

		echo $this->load->view('service_providers/monitoring_forms/dupe_search', array(
			'results' => $results,
			'sp_id' => $this->session->userdata('sp_id'),
		));
	}


}
