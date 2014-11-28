<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/presenters/Mmf_presenter.php');
require_once(APPPATH . '/presenters/Mmd_presenter.php');

class Mail_merge extends MY_Controller
{

	private $_sample_mf = array(
		'id' => 80863,
		'service_provider_id' => 99,
		'date_created' => '16/04/2012',
		'marketing' => array('GP','Other health professional','Friend or relative','Advertising','Pharmacy','Other'),		// @TODO: If mail merge is ever re-enabled, use marketing_sources_model.
		'marketing_other' => array('Youth worker', 'Initial assessment', 'Self Aware', 'Friend of a friend'),
		'agreed_quit_date' => '16/05/2012',
		'date_of_last_tobacco_use' => '10/04/2012',
		'date_of_4_week_follow_up' => '09/05/2012',
		'date_of_12_week_follow_up' => '08/07/2012',
		'intervention_type' => array('Closed group','Open (rolling) group','One-to-one support','Telephone support','Couple/family','Drop-in clinic','Other'),
		'intervention_type_other' => array('Couple/Family', '1-2-1 support'),
		'support_1' => array('', 'NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban'),
		'support_2' => array('', 'NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban'),
		'mf_gp_code' => array('A85006', 'A89011', 'A85016'),
		'treatment_outcome' => array('', 'Not quit','Lost to follow-up','Referred to GP','Refer to tier 3','Quit self-reported','Quit CO verified'),
		'notes' => array('Wants champix tablets', 'give inhalator as worried about what to do with his hands at night', 'Wants to stop for his children'),
		'referral_source' => array('', 'Advertising','Friend/Relative','General referrals from health professionals or T2 advisors/community','NHS Helpline referrals','Self','Other'),
		'function' => array('', 'Community','Pregnancy','Secondary Care','Workplace'),
		'previously_treated' => array('', 0, 1),
		'advisor_name' => array('Amber McAmberson', 'Violet Vitale', 'Jade Jefferies', 'Ruby Ryckman'),
		'advisor_number' => array('2047165938', '01189991234'),
	);

	private $_sample_client = array(
		'monitoring_form_id' => 80863,
		'nhs_number' => 'C10H14N2',
		'title' => 'Mr',
		'title_other' => '',
		'fname' => 'Nick',
		'sname' => 'Ateen',
		'gender' => 'Male',
		'date_of_birth' => '1987-04-16',
		'address' => array("1 Tabby Road\nNewcastle upon Tyne"),
		'post_code' => array('SM0K 1NG'),
		'tel_daytime' => '0191 7778123',
		'tel_mobile' => '07444 156798',
		'tel_alt' => '555 1234',
		'exempt_from_prescription_charge' => array(0, 1),
		'pregnant' => array(0, 1),
		'breastfeeding' => array(0, 1),
		'occupation_code' => array('Full-time student','Never worked/long-term unemployed','Retired','Home carer','Sick/disabled and unable to work','Managerial/professional','Intermediate','Routine manual','Prisoner'),
		'ethnic_group' => array('British','Irish','Other white background','White and Black Caribbean','White and Black African','White and Asian','Other mixed groups','Indian','Pakistani','Bangladeshi','Other Asian background','Caribbean','African','Other Black background','Chinese','Other ethnic group','Not stated'),
		'gp_personal_name' => array('Dr Nick', 'Dr Watson', 'Dr Seuss', 'Dr Who', 'Dr Evil'),
		'gp_code' => array('A5014', 'A85001'),
		'sms' => array(0, 1),
		'consent' => array(0, 1),
	);


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->layout->set_title('Mail Merge');
		$this->layout->set_breadcrumb('Mail Merge', '/service-providers/mail-merge');

		$this->load->model(array('mail_merge_fields_model', 'mail_merge_documents_model'));
		$this->load->helper('array');
	}




	public function index()
	{
		$this->load->helper('text');

		$this->data['mmds'] = $this->mail_merge_documents_model->get_all();
		$this->data['mmfs'] = $this->mail_merge_fields_model->get_all($this->session->userdata('sp_id'), 'custom');

		$this->layout->set_view_script('/service_providers/mail_merge/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');

	}




	/**
	 * Add or edit a custom field
	 *
	 * @param int $mmf_id		ID of field to update
	 */
	public function set_field($mmf_id = NULL)
	{
		if ($mmf_id)
		{
			if ($mmf = $this->mail_merge_fields_model->get($mmf_id))
			{
				$this->layout->set_title('Edit mail merge field');
				$this->layout->set_breadcrumb('Edit mail merge field');
			}
			else
			{
				show_error('Could not load mail merge field.', 404);
			}
		}
		else
		{
			$mmf = array();
			$this->layout->set_title('Add mail merge field');
			$this->layout->set_breadcrumb('Add mail merge field');
		}


		if ($this->input->get('delete') == 1)
		{
			if ($this->mail_merge_fields_model->delete($mmf_id))
			{
				$this->session->set_flashdata('action', 'The mail merge field has been deleted.');
				redirect('service-providers/mail-merge');
			}
			else
			{
				$this->session->set_flashdata('action', 'Could not delete the mail merge field.');
				redirect(current_url());
			}
		}


		if ($this->input->post())
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('mmf_name', 'Field name', 'required|max_length[32]|alpha_dash|strtolower')
								  ->set_rules('mmf_description', 'Description', 'max_length[255]')
								  ->set_rules('mmf_format', 'Format', 'required')
								  ->set_rules('mmf_value', 'Value', 'required|max_length[40000]');

			if ($this->form_validation->run())
			{
				$data = array(
					'mmf_sp_id' => $this->session->userdata('sp_id'),
					'mmf_name' => $this->input->post('mmf_name'),
					'mmf_description' => $this->input->post('mmf_description'),
					'mmf_format' => $this->input->post('mmf_format'),
					'mmf_value' => $this->input->post('mmf_value'),
					'mmf_type' => 'custom',
				);

				if ($mmf_id)
				{
					// Update
					if ($this->mail_merge_fields_model->update($mmf_id, $data))
					{
						$this->session->set_flashdata('action', 'The mail merge field has been updated.');
					}
					else
					{
						$this->session->set_flashdata('action', 'The mail merge field could not be updated.');
					}
				}
				else
				{
					// Insert
					if ($this->mail_merge_fields_model->insert($data))
					{
						$this->session->set_flashdata('action', 'The new mail merge field has been added.');
					}
					else
					{
						$this->session->set_flashdata('action', 'The new mail merge field could not be added.');
					}
				}

				redirect('service-providers/mail-merge');

			}		// end form validation check
			else
			{
				$this->session->set_flashdata('action', 'Some errors were detected in the form. Please check and try again.');
			}

		}

		$this->data['mmf'] =& $mmf;

		$this->layout->set_view_script('/service_providers/mail_merge/set_field');
		$this->layout->set_javascript(array('/tiny_mce/jquery.tinymce.js', '/plugins/jquery.validate.js', '/views/service_providers/mail_merge/set_field.js'));
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');

	}




	/**
	 * Add or update a document
	 */
	public function set_document($mmd_id = NULL)
	{
		if ($mmd_id)
		{
			if ($mmd = $this->mail_merge_documents_model->get($mmd_id))
			{
				$this->layout->set_title('Edit mail merge document');
				$this->layout->set_breadcrumb('Edit mail merge document');
			}
			else
			{
				$mmd = array();
				show_error('Could not load mail merge document.', 404);
			}
		}
		else
		{
			$this->layout->set_title('Create mail merge document');
			$this->layout->set_breadcrumb('Created mail merge document');
		}


		if ($this->input->get('delete') == 1)
		{
			if ($this->mail_merge_documents_model->delete($mmd_id))
			{
				$this->session->set_flashdata('action', 'The mail merge document has been deleted.');
				redirect('service-providers/mail-merge');
			}
			else
			{
				$this->session->set_flashdata('action', 'Could not delete the mail merge document.');
				redirect(current_url());
			}
		}


		if ($this->input->post())
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('mmd_id', 'Document ID', '')
								  ->set_rules('mmd_title', 'Document title', 'required|max_length[128]')
								  ->set_rules('mmd_content', 'Document content', 'required|max_length[40000]');

			if ($this->form_validation->run())
			{

				$data = array(
					'mmd_title' => $this->input->post('mmd_title'),
					'mmd_content' => $this->input->post('mmd_content'),
				);

				if ($mmd_id)
				{
					// Update
					if ($this->mail_merge_documents_model->update($mmd_id, $data))
					{
						$this->session->set_flashdata('action', 'The mail merge document has been updated.');
					}
					else
					{
						$this->session->set_flashdata('action', 'The mail merge document could not be updated.');
					}
				}
				else
				{
					// Insert
					if ($this->mail_merge_documents_model->insert($data))
					{
						$this->session->set_flashdata('action', 'The new mail merge document has been added.');
					}
					else
					{
						$this->session->set_flashdata('action', 'Error updating document');
					}
				}

				redirect('service-providers/mail-merge');

			}		// end form validation check
			else
			{
				$this->session->set_flashdata('action', 'Some errors were detected in the form. Please check and try again.');
			}

		}

		// Get all available mail merge fields for display
		$fields = $this->mail_merge_fields_model->get_all($this->session->userdata('sp_id'));
		$all_fields = array();

		// Arrange by type
		foreach ($fields as $field)
		{
			$mmf = new Mmf_presenter($field);
			$all_fields[$mmf->get('mmf_type')][] = $mmf;
		}

		$this->data['fields'] =& $all_fields;
		$this->data['mmd'] =& $mmd;

		$this->layout->set_view_script('/service_providers/mail_merge/set_document');
		$this->layout->set_javascript(array('/tiny_mce/jquery.tinymce.js', '/plugins/jquery.validate.js', '/views/service_providers/mail_merge/set_document.js'));
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');



	}




	public function preview()
	{
		$this->view = FALSE;

		// Sample data

		foreach ($this->_sample_mf as $k => $v)
		{
			if (is_array($v))
			{
				$this->_sample_mf[$k] = $v[array_rand($v)];
			}
		}

		foreach ($this->_sample_client as $k => $v)
		{
			if (is_array($v))
			{
				$this->_sample_client[$k] = $v[array_rand($v)];
			}
		}

		$document_content = $this->input->post('mmd_content');
		$output = $this->mail_merge_documents_model->process($this->_sample_mf, $this->_sample_client, $document_content);

		echo $output;
	}




	/**
	 * Get the final page count of the PDF version of the document.
	 */
	public function get_page_count()
	{
		// Sample data
		foreach ($this->_sample_mf as $k => $v)
		{
			if (is_array($v))
			{
				$this->_sample_mf[$k] = $v[array_rand($v)];
			}
		}

		foreach ($this->_sample_client as $k => $v)
		{
			if (is_array($v))
			{
				$this->_sample_client[$k] = $v[array_rand($v)];
			}
		}

		$template = $this->input->post('mmd_content');

		// Convert document
		$this->data['document_content'] = $this->mail_merge_documents_model->process($this->_sample_mf, $this->_sample_client, $template);
		$this->data['sp_name'] = $this->session->userdata('sp_name');

		// Load HTML to pass into dompdf
		$document = $this->load->view('service_providers/monitoring_forms/mail_merge', $this->data, TRUE);

		// Include the PDF library and generate it
		require_once(APPPATH . 'third_party/dompdf/dompdf_config.inc.php');
		$dompdf = new DOMPDF();
		$dompdf->load_html($document);
		$dompdf->render();

		// Page count
		$c = $dompdf->get_canvas()->get_page_count();

		if (is_int($c))
		{
			$json = array(
				'status' => 'ok',
				'page_count' => $c,
			);
		}
		else
		{
			$json = array('status' => 'err');
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($json, JSON_NUMERIC_CHECK));
	}




}

/* End of file: ./application/controllers/service-providers/mail_merge.php */