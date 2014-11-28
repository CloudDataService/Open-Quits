<?php

class Monitoring_forms extends My_controller
{
	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->model('model_monitoring_forms');
		$this->load->model('model_service_providers');
		$this->load->model('model_export_schemas');
		$this->load->model('model_claims');

		$this->load->helper(array('array', 'html'));

		$this->load->config('datasets');

		$this->layout->set_title('Monitoring forms');
		$this->layout->set_breadcrumb('Monitoring forms', '/admin/monitoring-forms');
	}

	function index($page = 0)
	{
		$this->load->model('sms_templates_model');

		// If filtering for an ID, just go straight to it.
		$mf_id = $this->input->get('id');
		if ($mf_id && ! $this->pct_id)
		{
			// Check it exists first.
			$form = $this->model_monitoring_forms->get_monitoring_form($mf_id);
			if (is_array($form) && ! empty($form['id']))
			{
				redirect('admin/monitoring-forms/info/' . $mf_id);
			}
		}

		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
		{
			// 1st April start date
			$month = (int) date('m');
			$year = ($month >= 4 ? date('Y') : (int) date('Y') - 1);
			$_GET['date_from'] = '01/04/' . $year;
		}

		// Set default follow up to 4
		if ( ! isset($_GET['follow_up']))
			$_GET['follow_up'] = 4;

		if ( ! isset($_GET['date_type']))
			$_GET['date_type'] = 'qds';

		$total = $this->model_monitoring_forms->get_total_monitoring_forms(@$_GET['sp_id']);

		$this->load->library('pagination');

		$config['base_url'] = '/admin/monitoring-forms/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$config['suffix'] = '?' . http_build_query($_GET);

		$this->pagination->initialize($config);

		$this->data['monitoring_forms'] = $this->model_monitoring_forms->get_monitoring_forms($page, $config['per_page'], @$_GET['sp_id']);

		$this->data['total'] = ($this->data['monitoring_forms'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['monitoring_forms'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;date_from=' . $_GET['date_from'] . '&amp;date_to=' . $_GET['date_to'] . '&amp;sp_id=' . @$_GET['sp_id'] . '&amp;treatment_outcome=' . @$_GET['treatment_outcome'] . '&amp;pct_id=' . @$_GET['pct_id'] . '&amp;location=' . @$_GET['location'] . '&amp;location=' . @$_GET['location'];

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		# load pct model
		$this->load->model('model_pcts');

		# get list of pcts
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['locations'] = config_item('provider_locations');

		$this->data['treatment_outcomes'] = array('Follow up required', 'Not quit', 'Lost to follow-up', 'Referred to GP', 'Quit self-reported', 'Quit CO verified');

		$this->data['export_schemas'] = $this->model_export_schemas->get_export_schemas_select('monitoring_forms');

		$this->data['sms_templates'] = $this->sms_templates_model->get_all();

		$this->load->model('advisors_model');
		$this->data['advisors'] = $this->advisors_model->get_advisors_filter();

		$this->layout->set_javascript('/views/admin/monitoring_forms/index.js');
		$this->layout->set_view_script('/admin/monitoring_forms/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function info($mf_id = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! $monitoring_form = $this->model_monitoring_forms->get_monitoring_form($mf_id))
			show_404();

		if (empty($monitoring_form['id']))
			show_404();

		if(@$_GET['delete'])
		{
			$this->model_monitoring_forms->delete_monitoring_form($monitoring_form['id']);

			redirect('/admin/monitoring-forms');
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
				's_a_id' => $this->session->userdata('admin_id'), // sender
				's_sps_id' => NULL, // wasnt sent by staff
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

			redirect('admin/monitoring_forms/info/'. $mf_id);
			return;
		}

		$this->data['service_provider'] = $this->model_service_providers->get_service_provider($monitoring_form['service_provider_id']);

		$this->data['claims'] = $this->model_claims->get_claims(0, $monitoring_form['id']);

		//get messages
		$this->load->model('sms_model');
		$this->data['smses'] = $this->sms_model->get_by_mf($monitoring_form['id']);

		//templates for sending messages
		$this->load->model('sms_templates_model');
		$this->data['sms_templates'] =  $this->sms_templates_model->get_all();

		$this->data['monitoring_form'] = $monitoring_form;

		$this->layout->set_title('#' . $monitoring_form['id']);
		$this->layout->set_breadcrumb('#' . $monitoring_form['id']);

		if(@$_GET['export'])
		{
			$this->layout->set_view_script('/admin/monitoring_forms/print');
			$this->load->vars($this->data);
			$this->load->view('/layouts/blank');
		}
		else
		{
			$this->layout->set_view_script('/admin/monitoring_forms/info');
			$this->load->vars($this->data);
			$this->load->view('/layouts/default');
		}
	}



	/**
	 * Allow non-LA administrators to update forms.
	 *
	 * Almost a carbon-copy of the service provier's version, with the following differences:
	 *
	 * - No dupe search, no autosave.
	 * - Tier 2 "On behalf of..." used for logging.
	 * - If a LA admin, error thrown.
	 * - No adding, just updating.
	 */
	function set($mf_id = 0)
	{
		if ($this->pct_id)
		{
			show_error('This page is not currently available.');
		}

		$this->load->model(array('model_service_providers', 'health_problems_model', 'marketing_sources_model'));

		if ($monitoring_form = $this->model_monitoring_forms->get_monitoring_form($mf_id))
		{
			$title = 'Update';
		}
		else
		{
			show_error('Could not find record ID #' . $mf_id);
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
				$sp_id = $monitoring_form['service_provider_id'];
				$service_provider = $this->model_service_providers->get_service_provider($sp_id);
				// flag for "updated on behalf of..." in logging.
				$service_provider['on_behalf_of_tier_2'] = TRUE;

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

				redirect('/admin/monitoring-forms/info/' . $mf_id);
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

		$this->data['form_elements']['support_methods'] = config_item('support_methods');

		$this->data['form_elements']['uncp_methods'] = config_item('uncp_methods');

		$this->data['form_elements']['treatment_outcomes'] = config_item('treatment_outcomes');

		$this->data['form_elements']['health_problems'] = $this->health_problems_model->get_all();

		$this->data['form_elements']['marketing_sources'] = result_assoc($this->marketing_sources_model->get_active(), 'ms_id', 'ms_title', '-- Please Select --');

		$this->data['monitoring_form'] = $monitoring_form;

		$js = array(
			'/plugins/moment.min.js',
			'/plugins/jquery.validate.js',
			'/plugins/jquery.fancybox.js',
			'/views/service_providers/monitoring_forms/set.js'
		);

		$this->layout->set_css(array('fancybox.css'));
		$this->layout->set_javascript($js);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_view_script('/service_providers/monitoring_forms/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function csv()
	{
		// Fields that have patient identifable information in them, for reference.
		$patient_info_fields = array(
			'telephone',
			'nhs_number',
			'client_name',
			'fname',
			'sname',
			'date_of_birth',
			'gender',
			'address',
			'post_code',
			'tel_daytime',
			'tel_mobile',
			'tel_alt',
			'email',
		);

		$this->load->helper('form');

		// Set up GET variable defaults

		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		// Set default follow up to 4
		if ( ! isset($_GET['follow_up']))
			$_GET['follow_up'] = 4;

		if ( ! isset($_GET['date_type']))
			$_GET['date_type'] = 'qds';

		if(@$_GET['export'])
		{
			$export_schema = $this->model_export_schemas->get_export_schema(@$_GET['schema_id'], 'monitoring_forms');

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
						if ($this->pct_id && in_array($field_name, $patient_info_fields))
						{
							if ($field_name == 'post_code')
							{
								$csv[$i][] = current(explode(' ', format_postcode($mf[$field_name])));
							}
							else
							{
								$csv[$i][] = "N/A";
							}
						}
						else
						{
							if ($field_name == 'gp_address' || $field_name == 'notes')
							{
								$csv[$i][] = str_replace(array("\r", "\r\n", "\n"), ', ', $mf[$field_name]);
							}
							else
							{
								$csv[$i][] = $mf[$field_name];
							}
						}
					}
				}
			}

			$file_name = md5($this->session->userdata('admin_id') . time()) . '.csv';

			$file_dir = $this->config->config['csv_dir'] . $file_name;

			$file = fopen($file_dir, "w");

			foreach ($csv as $line)
			{
				fputcsv($file, $line);
			}

			header("cache-Control: must-revalidate");
			header("Pragma: must-revalidate");
			header('Content-type: application/vnd.ms-excel');
			header('Content-disposition: attachment; filename=' . $file_name);

			echo file_get_contents($file_dir);

			fclose($file);

			unlink($file_dir);
		}
	}


	function ic_reports()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-1 months'));

		if ( ! isset($_GET['date_type']))
			$_GET['date_type'] = 'qds';

		// Set default follow up to 4
		if ( ! isset($_GET['follow_up']))
			$_GET['follow_up'] = 4;

		$this->data['follow_up'] = $this->input->get('follow_up');

		$this->load->helper('number_helper');

		# load pct model
		$this->load->model('model_pcts');

		# get list of pcts
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['ic'] = $this->model_monitoring_forms->get_ic(@$_GET['sp_id']);

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		$this->layout->set_javascript('/views/admin/monitoring_forms/ic_reports.js');
		$this->layout->set_title('IC reports');
		$this->layout->set_breadcrumb('IC reports');
		$this->layout->set_view_script('/admin/monitoring_forms/ic_reports');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}



	public function ic_reports_providers()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-1 months'));

		if ( ! isset($_GET['date_type']))
			$_GET['date_type'] = 'qds';

		// Set default follow up to 4
		if ( ! isset($_GET['follow_up']))
			$_GET['follow_up'] = 4;

		$this->load->helper('number_helper');

		$this->load->model('model_pcts');
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['ic'] = $this->model_monitoring_forms->get_ic_providers();

		$this->layout->set_javascript('/views/admin/monitoring_forms/ic_reports.js');
		$this->layout->set_title('IC Providers Report');
		$this->layout->set_breadcrumb('IC reports', 'admin/monitoring_forms/ic-reports');
		$this->layout->set_breadcrumb('Providers Report');
		$this->layout->set_view_script('/admin/monitoring_forms/ic_reports_providers');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function statistics()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$this->load->model('model_statistics');

		$this->data['treatment_outcome_graph'] = $this->model_statistics->get_treatment_outcome_graph();

		$this->layout->set_title('Statistics');
		$this->layout->set_breadcrumb('Statistics');
		$this->layout->set_view_script('/admin/monitoring_forms/statistics');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


}
