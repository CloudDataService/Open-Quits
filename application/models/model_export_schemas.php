<?php

class Model_export_schemas extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function set_export_schema($export_schema_id = 0)
	{
		if($export_schema_id)
		{
			$sql = 'UPDATE
						export_schemas
					SET
						pct_id = ?,
						type = ?,
						title = ?,
						export_schema = ?
					WHERE
						id = "' . (int)$export_schema_id . '";';

			$this->session->set_flashdata('action', 'Export schema updated');
		}
		else
		{
			$sql = 'INSERT INTO
						export_schemas
					SET
						pct_id = ?,
						type = ?,
						title = ?,
						export_schema = ?;';

			$this->session->set_flashdata('action', 'Export schema added');
		}



		$this->db->query($sql, array(($this->input->post('pct_id') ? $this->input->post('pct_id') : NULL),
									 $this->input->post('type'),
									 $this->input->post('title'),
									 serialize($this->input->post('fields')))
						 );
	}


	function delete_schema($schema_id)
	{
		$sql = 'DELETE FROM
					export_schemas
				WHERE
					id = "' . (int)$schema_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Export schema deleted');
	}


	function get_export_schemas($type)
	{
		$sql = 'SELECT
					id,
					pct_id,
					title
				FROM
					export_schemas
				WHERE
					type = ? ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $this->input->get('pct_id') . ' ';

		return $this->db->query($sql, array($type))->result_array();
	}


	function get_export_schemas_select($type = '')
	{
		$sql = 'SELECT
					id,
					pct_id,
					title
				FROM
					export_schemas
				WHERE
					type = ? ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id IS NULL OR pct_id = ' . (int) $this->input->get('pct_id') . ' ';

		return $this->db->query($sql, array($type))->result_array();
	}


	function get_export_schema($export_schema_id = 0, $type = '')
	{
		if($export_schema_id == 0)
		{
			$schema = $this->get_default($type);
		}
		else
		{
			$sql = 'SELECT
						id,
						pct_id,
						title,
						export_schema
					FROM
						export_schemas
					WHERE
						id = "' . (int)$export_schema_id . '";';

			if($schema =  $this->db->query($sql)->row_array())
			{
				$schema['export_schema'] = unserialize($schema['export_schema']);
			}
			else
			{
				$schema = $this->get_default($type);
			}
		}

		return $schema;
	}


	function get_default($type)
	{
		if($type == 'monitoring_form_claims')
		{
			$schema = $this->get_default_monitoring_form_claims();
		}
		else
		{
			$schema = $this->get_default_monitoring_forms();
		}

		return $schema;
	}

	function get_default_monitoring_form_claims()
	{
		return array('export_schema' => array(
			'monitoring_form_id' => 'Monitoring form ID',
			'date_of_claim_format' => 'Date of claim',
			'sp_name' => 'Service provider',
			'advisor_code' => 'Advisor code',
			'provider_code' => 'Provider code',
			'cost_code' => 'Cost code',
			'group_name' => 'Service provider group',
			'pct_name' => 'Local Authority',
			'claim_type' => 'Claim type',
			'cost' => 'Cost',
			'status' => 'Status',
			'agreed_quit_date_format' => 'Agreed Quit Date'
		));
	}

	function get_default_monitoring_forms()
	{
		return array('export_schema' => array(
			'id' => 'Monitoring form ID',
			'date_created_format' => 'Date created',
			'advisor_code' => 'Advisor code',
			'provider_code' => 'Provider code',
			'cost_code' => 'Cost code',
			'sp_name' => 'Service provider',
			'sp_post_code' => 'Post code',
			'group_name' => 'Group',
			'pct_name' => 'Local Authority',
			'department' => 'Department/ward',
			'location' => 'Location/setting',
			'venue' => 'Venue',
			'telephone' => 'Telephone',
			'nhs_number' => 'NHS number',
			'client_name' => 'Client name',
			'fname' => 'Client forename',
			'sname' => 'Client surname',
			'gender' => 'Gender',
			'date_of_birth' => 'Date of birth',
			'address' => 'Address',
			'post_code' => 'Post code',
			'tel_daytime' => 'Daytime telephone',
			'tel_mobile' => 'Mobile telephone',
			'tel_alt' => 'Alternative telephone',
			'email' => 'Email address',
			'exempt_from_prescription_charge' => 'Exempt from prescription charge',
			'pregnant' => 'Pregnant',
			'breastfeeding' => 'Breastfeeding',
			'occupation_code' => 'Occupation code',
			'ethnic_group' => 'Ethnic group',
			'gp_name' => 'GP name',
			'gp_address' => 'GP address',
			'gp_code' => 'GP code',
			'marketing' => 'How client heard about service',
			'date_of_last_tobacco_use_format' => 'Date of last tobacco use',
			'agreed_quit_date_format' => 'Agreed quit date',
			'date_of_4_week_follow_up_format' => 'Date of 4 week follow up',
			'date_of_12_week_follow_up_format' => 'Date of 12 week follow up',
			'intervention_type' => 'Type of intervention delivered',
			'support_1' => 'Pharmacological support 1',
			'support_2' => 'Pharmacological support 2',
			'support_none' => 'No product prescribed',
			'uncp' => 'Unlicensed NCP',
			//'treatment_outcome' => 'Treatment outcome',
			'treatment_outcome_4' => 'Treatment outcome (4 weeks)',
			'treatment_outcome_12' => 'Treatment outcome (12 weeks)',
			'referral_source' => 'Referral source',
			'function' => 'Function',
			'previously_treated' => 'Previously treated',
			'notes' => 'Notes',
			'consent' => 'Client consents to pass on of data to GP',
			'a_number' => 'Advisor number',
			'advisor' => 'Advisor name',
			'health_problems' => 'Health problems',
			'alcohol' => 'Drinks alcohol',
		));
	}


}
