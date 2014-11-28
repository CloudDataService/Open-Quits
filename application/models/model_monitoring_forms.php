<?php

class Model_monitoring_forms extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_total_monitoring_forms($sp_id = 0)
	{
		if($this->session->userdata('admin_id'))
		{
			$sql = 'SELECT
						COUNT(mf.id) AS total
					FROM
						monitoring_forms mf
					LEFT JOIN
						service_providers sp ON mf.service_provider_id = sp.id
					LEFT JOIN
						clients c ON mf.id = c.monitoring_form_id
					LEFT JOIN
						advisors a ON mf.a_id = a.a_id
					WHERE
						1 = 1 ';

			if(@$_GET['location'])
				$sql .= ' AND sp.location = ' . $this->db->escape($_GET['location']) . ' ';
		}
		else
		{
			$sql = 'SELECT
						COUNT(mf.id) AS total
					FROM
						monitoring_forms mf
					LEFT JOIN
						clients c ON mf.id = c.monitoring_form_id
					LEFT JOIN
						advisors a ON mf.a_id = a.a_id
					WHERE
						1 = 1  ';
		}

		// are we using date created or quit date set?
		if(@$_GET['date_type'] == 'qds')
		{
			// sql
			$sql .= ' AND mf.agreed_quit_date  >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					  AND mf.agreed_quit_date  <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
		} else {
			// sql
			$sql .= ' AND mf.date_created >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					  AND mf.date_created <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
		}

		if(@$_GET['sname'])
			$sql .= ' AND c.sname = ' . $this->db->escape($_GET['sname']) . ' ';

		/* if(@$_GET['treatment_outcome'])
			$sql .= ($_GET['treatment_outcome'] == 'Follow up required' ? ' AND ISNULL(treatment_outcome) ' : ' AND treatment_outcome = ' . $this->db->escape($_GET['treatment_outcome']) . ' '); */

		switch ( (int) $this->input->get('follow_up'))
		{
			case 4:
				if ($this->input->get('treatment_outcome'))
				{
					$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
						? ' AND ISNULL(treatment_outcome_4) '
						: ' AND treatment_outcome_4 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
				}
				break;

			case 12:
				if ($this->input->get('treatment_outcome'))
				{
					$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
						? ' AND ISNULL(treatment_outcome_12) '
						: ' AND treatment_outcome_12 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
				}
				break;

			default:
				$sql .= ' AND (treatment_outcome_4 IS NULL AND treatment_outcome_12 IS NULL) ';
			break;
		}

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

		if ($this->input->get('fname_like'))
			$sql .= ' AND (c.fname LIKE "%' . $this->db->escape_like_str($this->input->get('fname_like')) . '%" OR SOUNDEX(c.fname) = SOUNDEX(' . $this->db->escape($this->input->get('fname_like')) . ') ) ';

		if ($this->input->get('sname_like'))
			$sql .= ' AND (c.sname LIKE "%' . $this->db->escape_like_str($this->input->get('sname_like')) . '%" OR SOUNDEX(c.sname) = SOUNDEX(' . $this->db->escape($this->input->get('sname_like')) . ') ) ';

		if ($this->input->get('advisor_code'))
		{
			$advisor = $this->input->get('advisor_code');
			$advisor = $this->db->escape($advisor);

			$sql .= ' AND (a.a_number = ' . $advisor . ' OR mf.advisor_code = ' . $advisor . ') ';
		}

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_monitoring_forms($start = 0, $limit = 0, $sp_id = 0)
	{
		if( ! in_array(@$_GET['order'], array('id', 'date_created', 'sp_name', 'sname', 'date_of_4_week_follow_up', 'date_of_12_week_follow_up', 'treatment_outcome_4', 'treatment_outcome_12')) ) $_GET['order'] = 'id';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		if($this->session->userdata('admin_id'))
		{
			$sql = 'SELECT
						mf.id,
						CONCAT(IF(ISNULL(c.title_other), c.title, c.title_other), " ", c.fname, " ", c.sname) AS client_name,
						c.fname,
						c.sname,
						DATE_FORMAT(mf.date_created, "%D %b %Y") AS date_created_format,
						DATE_FORMAT(mf.date_of_4_week_follow_up, "%D %b %Y") AS date_of_4_week_follow_up_format,
						IF(ISNULL(mf.date_of_12_week_follow_up), "N/A", DATE_FORMAT(mf.date_of_12_week_follow_up, "%D %b %Y")) AS date_of_12_week_follow_up_format,
						IF(ISNULL(treatment_outcome_4), "Follow up required", treatment_outcome_4) AS treatment_outcome_4,
						IF(ISNULL(treatment_outcome_12), "Follow up required", treatment_outcome_12) AS treatment_outcome_12,
						sp.name AS sp_name,
						DATE_FORMAT(mf.agreed_quit_date, "%D %b %Y") AS quit_date_set_format
					FROM
						monitoring_forms mf
					LEFT JOIN
						service_providers sp ON mf.service_provider_id = sp.id
					LEFT JOIN
						clients c ON mf.id = c.monitoring_form_id
					LEFT JOIN
						advisors a ON mf.a_id = a.a_id
					WHERE
						1 = 1 ';

			// are we using date created or quit date set?
			if(@$_GET['date_type'] == 'qds')
			{
				// sql
				$sql .= ' AND mf.agreed_quit_date  >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
						  AND mf.agreed_quit_date  <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
			} else {
				// sql
				$sql .= ' AND mf.date_created >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
						  AND mf.date_created <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
			}

			/*
			if(@$_GET['treatment_outcome_4'])
				$sql .= ($_GET['treatment_outcome_4'] == 'Follow up required' ? ' AND ISNULL(treatment_outcome_4) ' : ' AND treatment_outcome_4 = ' . $this->db->escape($_GET['treatment_outcome_4']) . ' ');

			if(@$_GET['treatment_outcome_12'])
				$sql .= ($_GET['treatment_outcome_12'] == 'Follow up required' ? ' AND ISNULL(treatment_outcome_12) ' : ' AND treatment_outcome_12 = ' . $this->db->escape($_GET['treatment_outcome_12']) . ' ');
			*/

			switch ( (int) $this->input->get('follow_up'))
			{
				case 4:
					if ($this->input->get('treatment_outcome'))
					{
						$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
							? ' AND ISNULL(treatment_outcome_4) '
							: ' AND treatment_outcome_4 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
					}
					break;

				case 12:
					if ($this->input->get('treatment_outcome'))
					{
						$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
							? ' AND ISNULL(treatment_outcome_12) '
							: ' AND treatment_outcome_12 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
					}
					break;

				default:
					$sql .= ' AND (treatment_outcome_4 IS NULL AND treatment_outcome_12 IS NULL) ';
				break;
			}

			if($sp_id)
				$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

			if(@$_GET['pct_id'])
				$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

			if(@$_GET['location'])
				$sql .= ' AND sp.location = ' . $this->db->escape($_GET['location']) . ' ';

			if ($this->input->get('fname_like'))
				$sql .= ' AND (c.fname LIKE "%' . $this->db->escape_like_str($this->input->get('fname_like')) . '%" OR SOUNDEX(c.fname) = SOUNDEX(' . $this->db->escape($this->input->get('fname_like')) . ') ) ';

			if ($this->input->get('sname_like'))
				$sql .= ' AND (c.sname LIKE "%' . $this->db->escape_like_str($this->input->get('sname_like')) . '%" OR SOUNDEX(c.sname) = SOUNDEX(' . $this->db->escape($this->input->get('sname_like')) . ') ) ';

			if ($this->input->get('sms_valid') == 1)
				$sql .= ' AND c.sms = 1 AND CHAR_LENGTH(c.tel_mobile) >= 11 ';

			if ($this->input->get('advisor_code'))
			{
				$advisor = $this->input->get('advisor_code');
				$advisor = $this->db->escape($advisor);

				$sql .= ' AND (a.a_number = ' . $advisor . ' OR mf.advisor_code = ' . $advisor . ') ';
			}

			$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

			if($limit)
				$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

			//echo $sql;
			//die();
		}
		else
		{
			$sql = 'SELECT
						mf.id,
						DATE_FORMAT(mf.date_created, "%D %b %Y") AS date_created_format,
						CONCAT(IF(ISNULL(c.title_other), c.title, c.title_other), " ", c.fname, " ", c.sname) AS client_name,
						IF(ISNULL(treatment_outcome_4) AND date_of_4_week_follow_up < CURDATE(), CONCAT("<span style=\"color:#f00\">", DATE_FORMAT(mf.date_of_4_week_follow_up, "%D %b %Y"), " (overdue)</span>"), DATE_FORMAT(mf.date_of_4_week_follow_up, "%D %b %Y")) AS date_of_4_week_follow_up_format,
						IF(ISNULL(treatment_outcome_12) AND date_of_12_week_follow_up < CURDATE(), CONCAT("<span style=\"color:#f00\">", DATE_FORMAT(mf.date_of_12_week_follow_up, "%D %b %Y"), " (overdue)</span>"), DATE_FORMAT(mf.date_of_12_week_follow_up, "%D %b %Y")) AS date_of_12_week_follow_up_format,
						IF(ISNULL(treatment_outcome_4), "Follow up required", treatment_outcome_4) AS treatment_outcome_4,
						IF(ISNULL(treatment_outcome_12), "Follow up required", treatment_outcome_12) AS treatment_outcome_12,
						DATE_FORMAT(mf.agreed_quit_date, "%D %b %Y") AS quit_date_set_format
					FROM
						monitoring_forms mf
					LEFT JOIN
						clients c ON mf.id = c.monitoring_form_id
					LEFT JOIN
						advisors a ON mf.a_id = a.a_id
					WHERE
						1 = 1 ';

			// are we using date created or quit date set?
			if(@$_GET['date_type'] == 'qds')
			{
				// sql
				$sql .= ' AND mf.agreed_quit_date  >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
						  AND mf.agreed_quit_date  <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
			} else {
				// sql
				$sql .= ' AND mf.date_created >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
						  AND mf.date_created <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
			}

			if(@$_GET['sname'])
				$sql .= ' AND sname = ' . $this->db->escape($_GET['sname']) . ' ';

			switch ( (int) $this->input->get('follow_up'))
			{
				case 4:
					if ($this->input->get('treatment_outcome'))
					{
						$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
							? ' AND ISNULL(treatment_outcome_4) '
							: ' AND treatment_outcome_4 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
					}
					break;

				case 12:
					if ($this->input->get('treatment_outcome'))
					{
						$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
							? ' AND ISNULL(treatment_outcome_12) '
							: ' AND treatment_outcome_12 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
					}
					break;

				default:
					$sql .= ' AND (treatment_outcome_4 IS NULL AND treatment_outcome_12 IS NULL) ';
				break;
			}

			if($sp_id)
				$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

			if(@$_GET['pct_id'])
				$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

			//$sql .= ' GROUP BY mf.id ';

			$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

			if($limit)
				$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
		}

		return $this->db->query($sql)->result_array();
	}


	function get_csv($sp_id = 0)
	{
		if( ! in_array(@$_GET['order'], array('id', 'date_created', 'sp_name', 'sname', 'date_of_4_week_follow_up', 'date_of_12_week_follow_up', 'treatment_outcome')) ) $_GET['order'] = 'id';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
						mf.*,
						IF(ISNULL(mf.previously_treated), "", IF(mf.previously_treated = 1, "Yes", "No")) AS previously_treated,
						DATE_FORMAT(mf.date_created, "%d/%m/%Y") AS date_created_format,
						DATE_FORMAT(mf.agreed_quit_date, "%d/%m/%Y") AS agreed_quit_date_format,
						DATE_FORMAT(mf.agreed_quit_date, "%d/%m/%Y") AS quit_date_set,
						DATE_FORMAT(mf.date_of_last_tobacco_use, "%d/%m/%Y") AS date_of_last_tobacco_use_format,
						DATE_FORMAT(mf.date_of_4_week_follow_up, "%d/%m/%Y") AS date_of_4_week_follow_up_format,
						IF(ISNULL(mf.date_of_12_week_follow_up), "N/A", DATE_FORMAT(mf.date_of_12_week_follow_up, "%d/%m/%Y")) AS date_of_12_week_follow_up_format,
						c.*,
						IF(exempt_from_prescription_charge = 1, "Yes", "No") AS exempt_from_prescription_charge,
						IF(pregnant = 1, "Yes", "No") AS pregnant,
						IF(breastfeeding = 1, "Yes", "No") AS breastfeeding,
						IF(consent = 1, "Yes", "No") AS consent,
						CONCAT(IF(ISNULL(c.title_other), c.title, c.title_other), " ", c.fname, " ", c.sname) AS client_name,
						DATE_FORMAT(c.date_of_birth, "%d/%m/%Y") AS date_of_birth_format,
						sp.name AS sp_name,
						sp.advisor_code,
						sp.provider_code,
						sp.cost_code,
						sp.post_code AS sp_post_code,
						sp.location,
						sp.department,
						sp.venue,
						sp.telephone,
						IF(pcts.pct_name = "", "Unassigned", pcts.pct_name) AS pct,
						IF(spg.id, spg.name, "Unassigned") AS group_name,
						gps.gp_name,
						gps.gp_surgery AS gp_address,
						IF(ISNULL(mf.alcohol), "N/A", IF(alcohol = 1, "Yes", "No")) AS alcohol,
						GROUP_CONCAT(hp_name SEPARATOR ";") AS health_problems,
						a_number,
						CONCAT(a_fname, " ", a_sname) AS advisor,
						pct_name AS pct_name,
						ms_title,
						IF(ms_title IS NULL, marketing_other, ms_title) AS marketing,
						IF(support_none = 1, "Yes", "") AS support_none,
						IF(uncp = 1, "Yes", "") AS uncp
					FROM
						monitoring_forms mf
					LEFT JOIN
						clients c
							ON mf.id = c.monitoring_form_id
					LEFT JOIN
						service_providers sp
							ON mf.service_provider_id = sp.id

					/*	clients c,
						service_providers sp */

					LEFT JOIN
						service_provider_groups spg
							ON (spg.id = sp.group_id)
					LEFT JOIN
						gps
							ON c.gp_code = gps.gp_code
					LEFT JOIN
						advisors USING (a_id)
					LEFT JOIN
						mf2hp
							ON mf2hp_mf_id = mf.id
					LEFT JOIN
						health_problems
							ON mf2hp_hp_id = hp_id
					LEFT JOIN
						pcts
						ON sp.pct_id = pcts.id
					LEFT JOIN
						marketing_sources ms
						ON mf.ms_id = ms.ms_id

					WHERE
						1 = 1
						/*c.monitoring_form_id = mf.id
						/*AND sp.id = mf.service_provider_id

					WHERE 1=1*/';

		// are we using date created or quit date set?
		if($_GET['date_type'] == 'qds')
		{
			// sql
			$sql .= ' AND mf.agreed_quit_date  >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					  AND mf.agreed_quit_date  <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
		} else {
			// sql
			$sql .= ' AND mf.date_created >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					  AND mf.date_created <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
		}

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

		if(@$_GET['location'])
			$sql .= ' AND sp.location = ' . $this->db->escape($_GET['location']) . ' ';

		if(@$_GET['sname'])
			$sql .= ' AND sname = ' . $this->db->escape($_GET['sname']) . ' ';

		/* if(@$_GET['treatment_outcome'])
			$sql .= ($_GET['treatment_outcome'] == 'Follow up required' ? ' AND ISNULL(treatment_outcome) ' : ' AND treatment_outcome = ' . $this->db->escape($_GET['treatment_outcome']) . ' '); */

		switch ( (int) $this->input->get('follow_up'))
		{
			case 4:
				if ($this->input->get('treatment_outcome'))
				{
					$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
						? ' AND ISNULL(treatment_outcome_4) '
						: ' AND treatment_outcome_4 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
				}
				break;

			case 12:
				if ($this->input->get('treatment_outcome'))
				{
					$sql .= ($_GET['treatment_outcome'] == 'Follow up required')
						? ' AND ISNULL(treatment_outcome_12) '
						: ' AND treatment_outcome_12 = ' . $this->db->escape($this->input->get('treatment_outcome')) . ' ';
				}
				break;

			default:
				$sql .= ' AND (treatment_outcome_4 IS NULL AND treatment_outcome_12 IS NULL) ';
			break;
		}

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		$sql .= ' GROUP BY mf.id ';
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		return $this->db->query($sql)->result_array();
	}


	function get_monitoring_form($mf_id, $sp_id = 0)
	{
		if($mf_id)
		{
			$sql = 'SELECT
						mf.*,
						DAY(c.date_of_birth) AS date_of_birth_day,
						MONTH(c.date_of_birth) AS date_of_birth_month,
						YEAR(c.date_of_birth) AS date_of_birth_year,
						DATE_FORMAT(c.date_of_birth, "%d/%m/%Y") AS date_of_birth_format,
						DATE_FORMAT(mf.agreed_quit_date, "%d/%m/%Y") AS agreed_quit_date_format,
						DATE_FORMAT(mf.agreed_quit_date, "%d/%m/%Y") AS quit_date_set,
						DATE_FORMAT(mf.date_of_last_tobacco_use, "%d/%m/%Y") AS date_of_last_tobacco_use_format,
						DATE_FORMAT(mf.date_of_4_week_follow_up, "%d/%m/%Y") AS date_of_4_week_follow_up_format,
						IF(ISNULL(mf.date_of_12_week_follow_up), "N/A", DATE_FORMAT(mf.date_of_12_week_follow_up, "%d/%m/%Y")) AS date_of_12_week_follow_up_format,
						c.*,
						gps.*,
						CONCAT(gps.gp_name, " (#", gps.gp_code, ")") AS ci_gp,
						CONCAT(prescribing_gps.gp_name, " (#", prescribing_gps.gp_code, ")") AS mf_prescribing_gp,
						GROUP_CONCAT(hp_name) AS health_problems,
						GROUP_CONCAT(hp_id) AS health_problems_ids,
						CONCAT(a_fname, " ", a_sname, " (", a_number, ")") AS advisor,
						ms_title
					FROM
						monitoring_forms mf
					LEFT JOIN
						service_providers sp
						ON mf.service_provider_id = sp.id
					LEFT JOIN
						clients c
							ON c.monitoring_form_id = mf.id
					LEFT JOIN
						gps
							ON gps.gp_code = c.gp_code
					LEFT JOIN
						gps AS prescribing_gps
							ON prescribing_gps.gp_code = mf_gp_code
					LEFT JOIN
						advisors USING (a_id)
					LEFT JOIN
						mf2hp
							ON mf2hp_mf_id = mf.id
					LEFT JOIN
						health_problems
							ON mf2hp_hp_id = hp_id
					LEFT JOIN
						marketing_sources ms
						ON mf.ms_id = ms.ms_id
					WHERE
						mf.id = "' . (int)$mf_id . '"';

			if($sp_id)
				$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '";';

			if(@$_GET['pct_id'])
				$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

			$row = $this->db->query($sql)->row_array();

			// Extract the health problem details and put into array format
			$row['health_problems_array'] = explode(',', $row['health_problems']) ?: array();
			$row['health_problems_ids_array'] = explode(',', $row['health_problems_ids']) ?: array();

			return $row;
		}

		return FALSE;
	}




	/**
	 * Get basic monitoring form information by IDs
	 */
	public function get_by_ids($ids = array())
	{
		$id_str = implode(',', array_values($ids));

		$sql = 'SELECT
						monitoring_form_id,
						CONCAT(IF(ISNULL(c.title_other), c.title, c.title_other), " ", c.fname, " ", c.sname) AS client_name,
						c.fname,
						c.sname,
						c.tel_mobile
					FROM
						clients c
					WHERE
						c.monitoring_form_id IN(' . $id_str . ') ';

		return $this->db->query($sql)->result_array();
	}




	function set_monitoring_form($service_provider, $mf_id)
	{
		$monitoring_form = array(
			($this->input->post('ms_id') == 'Other' ? NULL : $this->input->post('ms_id')),
			$this->input->post('agreed_quit_date'),
			$this->input->post('date_of_last_tobacco_use'),
			$this->input->post('date_of_4_week_follow_up'),
			$this->input->post('intervention_type'),
			($this->input->post('support_1') && $this->input->post('support_2') ? $this->input->post('support_method') : NULL),
			(int) $this->input->post('support_none'),
			(int) $this->input->post('uncp'),
			($this->input->post('uncp') == 1 ? (int) $this->input->post('uncp_method') : NULL),
			$this->input->post('mf_gp_code'),
			$this->input->post('notes'),
			(int) $this->input->post('a_id'),
			$this->input->post('health_problems_other'),
			(int) $this->input->post('health_problems_not_reported'),
			(int) $this->input->post('alcohol'),
			(int) $this->input->post('alcohol_not_reported'),
			$this->input->post('advisor_code'),
			(strlen($this->input->post('co_quit')) === 0 ? NULL : (int) $this->input->post('co_quit')),
		);

		$client = array(
			$this->input->post('title'),
			$this->input->post('fname'),
			$this->input->post('sname'),
			$this->input->post('gender'),
			((int)@$_POST['date_of_birth']['year'] . '-' . (int)@$_POST['date_of_birth']['month'] . '-' . (int)@$_POST['date_of_birth']['day']),
			$this->input->post('address'),
			$this->input->post('post_code'),
			$this->input->post('tel_daytime'),
			$this->input->post('tel_mobile'),
			$this->input->post('tel_alt'),
			$this->input->post('email'),
			$this->input->post('exempt_from_prescription_charge'),
			$this->input->post('pregnant'),
			$this->input->post('breastfeeding'),
			$this->input->post('occupation_code'),
			$this->input->post('ethnic_group'),
			$this->input->post('ci_gp'),
			$this->input->post('gp_personal_name'),
			$this->input->post('sms'),
			$this->input->post('consent'),
		);


		if($mf_id)
		{
			$sql = 'UPDATE
						monitoring_forms
					SET
						service_provider_id = "' . (int)$service_provider['id'] . '",
						marketing_other = ' . ($this->input->post('ms_id') == 'Other' ? $this->db->escape($this->input->post('marketing_other')) : 'NULL') . ',
						intervention_type_other = ' . ($this->input->post('intervention_type') == 'Other' ? $this->db->escape($this->input->post('intervention_type_other')) : 'NULL') . ',
						treatment_outcome_4 = ' . ($this->input->post('treatment_outcome_4') ? $this->db->escape($this->input->post('treatment_outcome_4')) : 'NULL') . ',
						treatment_outcome_12 = ' . ($this->input->post('treatment_outcome_12') ? $this->db->escape($this->input->post('treatment_outcome_12')) : 'NULL') . ',
						ms_id = ?,
						agreed_quit_date = ?,
						date_of_last_tobacco_use = ?,
						date_of_4_week_follow_up = ?,
						date_of_12_week_follow_up = ' . ($this->input->post('date_of_12_week_follow_up') ? $this->db->escape($this->input->post('date_of_12_week_follow_up')) : 'NULL') . ',
						intervention_type = ?,
						support_1 = ' . ($this->input->post('support_1') ? $this->db->escape($this->input->post('support_1')) : 'NULL') . ',
						support_2 = ' . ($this->input->post('support_2') ? $this->db->escape($this->input->post('support_2')) : 'NULL') . ',
						support_method = ?,
						support_none = ?,
						uncp = ?,
						uncp_method = ?,
						mf_gp_code = ?,
						notes = ?,
						referral_source = ' . ($this->input->post('referral_source') ? $this->db->escape($this->input->post('referral_source')) : 'NULL') . ',
						function = ' . ($this->input->post('function') ? $this->db->escape($this->input->post('function')) : 'NULL') . ',
						previously_treated = ' . ($this->input->post('previously_treated') ? (int)$this->input->post('previously_treated') : 'NULL') . ',
						a_id = ?,
						health_problems_other = ?,
						health_problems_not_reported = ?,
						alcohol = ?,
						alcohol_not_reported = ?,
						advisor_code = ?,
						co_quit = ?
					WHERE
						id = "' . (int)$mf_id . '"';

			$this->db->query($sql, $monitoring_form);

			$sql = 'UPDATE
						clients
					SET
						title_other = ' . ($this->input->post('title') == 'Other' ? $this->db->escape($this->input->post('title_other')) : 'NULL') . ',
						nhs_number = ' . ($this->input->post('nhs_number') ? $this->db->escape($this->input->post('nhs_number')) : 'NULL') . ',
						title = ?,
						fname = ?,
						sname = ?,
						gender = ?,
						date_of_birth = ?,
						address = ?,
						post_code = ?,
						tel_daytime = ?,
						tel_mobile = ?,
						tel_alt = ?,
						email = ?,
						exempt_from_prescription_charge = ?,
						pregnant = ?,
						breastfeeding = ?,
						occupation_code = ?,
						ethnic_group = ?,
						gp_code = ?,
						gp_personal_name = ?,
						sms = ?,
						consent = ?
					WHERE
						monitoring_form_id = "' . (int)$mf_id .'"';

			$this->db->query($sql, $client);

			$this->session->set_flashdata('action', 'Monitoring form updated');

			$log = ($service_provider['on_behalf_of_tier_2'] ? 'Monitoring form #' . $mf_id . ' updated on behalf of ' . $service_provider['name'] . '.' : 'Monitoring form #' . $mf_id . ' updated.');
		}
		else
		{
			$sps_id = (int) ($this->input->post('sps_id') ?: $this->session->userdata('sps_id'));

			$sql = 'INSERT INTO
						monitoring_forms
					SET
						service_provider_id = "' . (int)$service_provider['id'] . '",
						date_created = NOW(),
						marketing_other = ' . ($this->input->post('ms_id') == 'Other' ? $this->db->escape($this->input->post('marketing_other')) : 'NULL') . ',
						intervention_type_other = ' . ($this->input->post('intervention_type') == 'Other' ? $this->db->escape($this->input->post('intervention_type_other')) : 'NULL') . ',
						treatment_outcome_4 = ' . ($this->input->post('treatment_outcome_4') ? $this->db->escape($this->input->post('treatment_outcome_4')) : 'NULL') . ',
						treatment_outcome_12 = ' . ($this->input->post('treatment_outcome_12') ? $this->db->escape($this->input->post('treatment_outcome_12')) : 'NULL') . ',
						ms_id = ?,
						agreed_quit_date = ?,
						date_of_last_tobacco_use = ?,
						date_of_4_week_follow_up = ?,
						date_of_12_week_follow_up = ' . ($this->input->post('date_of_12_week_follow_up') ? $this->db->escape($this->input->post('date_of_12_week_follow_up')) : 'NULL') . ',
						intervention_type = ?,
						support_1 = ' . ($this->input->post('support_1') ? $this->db->escape($this->input->post('support_1')) : 'NULL') . ',
						support_2 = ' . ($this->input->post('support_2') ? $this->db->escape($this->input->post('support_2')) : 'NULL') . ',
						support_method = ?,
						support_none = ?,
						uncp = ?,
						uncp_method = ?,
						mf_gp_code = ?,
						notes = ?,
						referral_source = ' . ($this->input->post('referral_source') ? $this->db->escape($this->input->post('referral_source')) : 'NULL') . ',
						function = ' . ($this->input->post('function') ? $this->db->escape($this->input->post('function')) : 'NULL') . ',
						previously_treated = ' . ($this->input->post('previously_treated') ? (int)$this->input->post('previously_treated') : 'NULL') . ',
						a_id = ?,
						health_problems_other = ?,
						health_problems_not_reported = ?,
						alcohol = ?,
						alcohol_not_reported = ?,
						sps_id = ' . ($sps_id === 0 ? 'NULL' : $sps_id) . ',
						advisor_code = ?,
						co_quit = ?';

			$this->db->query($sql, $monitoring_form);

			$mf_id = $this->db->insert_id();

			$sql = 'INSERT INTO
						clients
					SET
						monitoring_form_id = "' . (int)$mf_id . '",
						title_other = ' . ($this->input->post('title') == 'Other' ? $this->db->escape($this->input->post('title_other')) : 'NULL') . ',
						nhs_number = ' . ($this->input->post('nhs_number') ? $this->db->escape($this->input->post('nhs_number')) : 'NULL') . ',
						title = ?,
						fname = ?,
						sname = ?,
						gender = ?,
						date_of_birth = ?,
						address = ?,
						post_code = ?,
						tel_daytime = ?,
						tel_mobile = ?,
						tel_alt = ?,
						email = ?,
						exempt_from_prescription_charge = ?,
						pregnant = ?,
						breastfeeding = ?,
						occupation_code = ?,
						ethnic_group = ?,
						gp_code = ?,
						gp_personal_name = ?,
						sms = ?,
						consent = ?';

			$this->db->query($sql, $client);

			$this->session->set_flashdata('action', 'Monitoring form added');

			$log = ($service_provider['on_behalf_of_tier_2'] ? 'Monitoring form #' . $mf_id . ' added on behalf of ' . $service_provider['name'] . '.' : 'Monitoring form #' . $mf_id . ' added.');
		}

		// Update the health proplems list for the monitoring form
		$this->_set_health_problems($mf_id, $this->input->post('health_problems'));

		$this->model_log->set_log($log);

		return $mf_id;
	}




	/**
	 * Update the health problems entries for the provided monitoring form
	 *
	 * @param int $mf_id		Monitoring form ID
	 * @param array $health_proplems		Array of health problem IDs from POST array. Array values will be health problem IDs.
	 * @return bool
	 * @author CR
	 */
	private function _set_health_problems($mf_id = 0, $health_problems = array())
	{
		// Remove current entries (if any)
		$sql = 'DELETE FROM mf2hp WHERE mf2hp_mf_id = ?';
		$this->db->query($sql, array($mf_id));

		if (empty($health_problems)) return TRUE;

		foreach ($health_problems as $hp_id)
		{
			$data[] = array('mf2hp_mf_id' => $mf_id, 'mf2hp_hp_id' => $hp_id);
		}

		return $this->db->insert_batch('mf2hp', $data);
	}




	function get_ic($sp_id = 0)
	{
		/*
		$sql = "CREATE FUNCTION client_age (date_created DATE, date_of_birth DATE) RETURNS int DETERMINISTIC RETURN DATE_FORMAT(date_created, '%Y') - DATE_FORMAT(date_of_birth, '%Y') - (DATE_FORMAT(date_created, '00-%m-%d') < DATE_FORMAT(date_of_birth, '00-%m-%d'));";

		# $sql = "DROP FUNCTION IF EXISTS client_age;";

		$this->db->query($sql); exit;
		*/

		// Determine which date point to use - 4 week or 12.
		$follow_up = (int) element('follow_up', $_GET, 4);
		// Set which treatment outcome field should be used in query
		$to_field = 'treatment_outcome_' . $follow_up;

		$sql = 'SELECT

				SUM(IF(monitoring_form_id IS NOT NULL, 1, 0)) AS total,
				SUM(IF(mf.treatment_outcome_4 = "Quit CO verified" OR mf.treatment_outcome_4 = "Quit self-reported" OR mf.treatment_outcome_12 = "Quit CO verified" OR mf.treatment_outcome_12 = "Quit self-reported", 1, 0)) AS total_quit_overall,
				SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS total_male_quit_overall,
				SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS total_female_quit_overall,
				SUM(IF(mf.treatment_outcome_4 = "Quit CO verified" OR mf.treatment_outcome_4 = "Quit self-reported", 1, 0)) AS total_quit_4,
				SUM(IF(mf.treatment_outcome_4 = "Quit CO verified", 1, 0)) AS total_quit_co_4,
				SUM(IF(mf.treatment_outcome_12 = "Quit CO verified" OR mf.treatment_outcome_12 = "Quit self-reported", 1, 0)) AS total_quit_12,
				SUM(IF(mf.treatment_outcome_12 = "Quit CO verified", 1, 0)) AS total_quit_co_12,

				/* persons setting a quit date */

					/* males */

						SUM(IF(c.gender = "Male", 1,0)) AS total_males_setting_a_quit_date,

						/* white */

							SUM(IF(c.gender = "Male" AND c.ethnic_group = "British" , 1,0)) AS british_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Irish" , 1,0)) AS irish_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Other white background" , 1,0)) AS other_white_background_males_setting_a_quit_date,

						/* mixed */

							SUM(IF(c.gender = "Male" AND c.ethnic_group = "White and Black Caribbean" , 1,0)) AS white_and_black_caribbean_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "White and Black African" , 1,0)) AS white_and_black_african_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "White and Asian" , 1,0)) AS white_and_asian_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Other mixed groups" , 1,0)) AS other_mixed_groups_males_setting_a_quit_date,

						/* asian or asian british */

							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Indian" , 1,0)) AS indian_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Pakistani" , 1,0)) AS pakistani_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Bangladeshi" , 1,0)) AS bangladeshi_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Other Asian background" , 1,0)) AS other_asian_background_males_setting_a_quit_date,

						/* black or black british */

							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Caribbean" , 1,0)) AS caribbean_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "African" , 1,0)) AS african_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Other Black background" , 1,0)) AS other_black_background_males_setting_a_quit_date,

						/* other ethnic groups */

							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Chinese" , 1,0)) AS chinese_males_setting_a_quit_date,
							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Other ethnic group" , 1,0)) AS other_ethnic_group_males_setting_a_quit_date,

						/* not stated */

							SUM(IF(c.gender = "Male" AND c.ethnic_group = "Not stated" , 1,0)) AS not_stated_males_setting_a_quit_date,

					/* females */

					SUM(IF(c.gender = "Female", 1,0)) AS total_females_setting_a_quit_date,

						/* white */

							SUM(IF(c.gender = "Female" AND c.ethnic_group = "British" , 1,0)) AS british_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Irish" , 1,0)) AS irish_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Other white background" , 1,0)) AS other_white_background_females_setting_a_quit_date,

						/* mixed */

							SUM(IF(c.gender = "Female" AND c.ethnic_group = "White and Black Caribbean" , 1,0)) AS white_and_black_caribbean_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "White and Black African" , 1,0)) AS white_and_black_african_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "White and Asian" , 1,0)) AS white_and_asian_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Other mixed groups" , 1,0)) AS other_mixed_groups_females_setting_a_quit_date,

						/* asian or asian british */

							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Indian" , 1,0)) AS indian_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Pakistani" , 1,0)) AS pakistani_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Bangladeshi" , 1,0)) AS bangladeshi_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Other Asian background" , 1,0)) AS other_asian_background_females_setting_a_quit_date,

						/* black or black british */

							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Caribbean" , 1,0)) AS caribbean_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "African" , 1,0)) AS african_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Other Black background" , 1,0)) AS other_black_background_females_setting_a_quit_date,

						/* other ethnic groups */

							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Chinese" , 1,0)) AS chinese_females_setting_a_quit_date,
							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Other ethnic group" , 1,0)) AS other_ethnic_group_females_setting_a_quit_date,

						/* not stated */

							SUM(IF(c.gender = "Female" AND c.ethnic_group = "Not stated" , 1,0)) AS not_stated_females_setting_a_quit_date,

				/* persons successfully quit */

						/* males */

							SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") , 1,0)) AS total_males_successfully_quit,

							/* white */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "British" , 1,0)) AS british_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Irish" , 1,0)) AS irish_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other white background" , 1,0)) AS other_white_background_males_successfully_quit,

							/* mixed */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "White and Black Caribbean" , 1,0)) AS white_and_black_caribbean_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "White and Black African" , 1,0)) AS white_and_black_african_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "White and Asian" , 1,0)) AS white_and_asian_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other mixed groups" , 1,0)) AS other_mixed_groups_males_successfully_quit,

							/* asian or asian british */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Indian" , 1,0)) AS indian_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Pakistani" , 1,0)) AS pakistani_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Bangladeshi" , 1,0)) AS bangladeshi_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other Asian background" , 1,0)) AS other_asian_background_males_successfully_quit,

							/* black or black british */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Caribbean" , 1,0)) AS caribbean_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "African" , 1,0)) AS african_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other Black background" , 1,0)) AS other_black_background_males_successfully_quit,

							/* other ethnic groups */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Chinese" , 1,0)) AS chinese_males_successfully_quit,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other ethnic group" , 1,0)) AS other_ethnic_group_males_successfully_quit,

							/* not stated */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Not stated" , 1,0)) AS not_stated_males_successfully_quit,

						/* females */

							SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") , 1,0)) AS total_females_successfully_quit,

							/* white */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "British" , 1,0)) AS british_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Irish" , 1,0)) AS irish_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other white background" , 1,0)) AS other_white_background_females_successfully_quit,

							/* mixed */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "White and Black Caribbean" , 1,0)) AS white_and_black_caribbean_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "White and Black African" , 1,0)) AS white_and_black_african_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "White and Asian" , 1,0)) AS white_and_asian_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other mixed groups" , 1,0)) AS other_mixed_groups_females_successfully_quit,

							/* asian or asian british */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Indian" , 1,0)) AS indian_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Pakistani" , 1,0)) AS pakistani_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Bangladeshi" , 1,0)) AS bangladeshi_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other Asian background" , 1,0)) AS other_asian_background_females_successfully_quit,

							/* black or black british */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Caribbean" , 1,0)) AS caribbean_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "African" , 1,0)) AS african_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other Black background" , 1,0)) AS other_black_background_females_successfully_quit,

							/* other ethnic groups */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Chinese" , 1,0)) AS chinese_females_successfully_quit,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Other ethnic group" , 1,0)) AS other_ethnic_group_females_successfully_quit,

							/* not stated */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported") AND c.ethnic_group = "Not stated" , 1,0)) AS not_stated_females_successfully_quit,

				/* quit date by age, gender and treatment outcome */

						/* males */

							/* setting a quit date */

								SUM(IF(c.gender = "Male" AND client_age(mf.date_created, c.date_of_birth) < 18, 1,0)) AS males_under_18_setting_a_quit_date,
								SUM(IF(c.gender = "Male" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_setting_a_quit_date,
								SUM(IF(c.gender = "Male" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_setting_a_quit_date,
								SUM(IF(c.gender = "Male" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_setting_a_quit_date,
								SUM(IF(c.gender = "Male" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_setting_a_quit_date,
								SUM(IF(c.gender = "Male" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_setting_a_quit_date,

							/* quit self reported */

								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported", 1,0)) AS total_males_quit_self_reported,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS males_under_18_quit_self_reported,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_quit_self_reported,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_quit_self_reported,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_quit_self_reported,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_quit_self_reported,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_quit_self_reported,

							/* not quit */

								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit", 1,0)) AS total_males_not_quit,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS males_under_18_not_quit,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_not_quit,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_not_quit,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_not_quit,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_not_quit,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_not_quit,


							/* lost to follow-up */

								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */), 1,0)) AS total_males_lost_to_follow_up,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS males_under_18_lost_to_follow_up,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_lost_to_follow_up,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_lost_to_follow_up,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_lost_to_follow_up,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_lost_to_follow_up,
								SUM(IF(c.gender = "Male" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_lost_to_follow_up,

							/* CO Verified */

								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified", 1,0)) AS total_males_co_verified,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS males_under_18_co_verified,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_co_verified,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_co_verified,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_co_verified,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_co_verified,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_co_verified,

							/* Referred to GP */

								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP", 1,0)) AS total_males_referred_to_gp,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS males_under_18_referred_to_gp,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_referred_to_gp,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_referred_to_gp,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_referred_to_gp,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_referred_to_gp,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_referred_to_gp,

							/* Refer to tier 3 */

								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3", 1,0)) AS total_males_refer_to_tier_3,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS males_under_18_refer_to_tier_3,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS males_18_34_refer_to_tier_3,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS males_35_44_refer_to_tier_3,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_45_59_refer_to_tier_3,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS males_18_59_refer_to_tier_3,
								SUM(IF(c.gender = "Male" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS males_60_and_over_refer_to_tier_3,


						/* females */

							/* setting a quit date */

								SUM(IF(c.gender = "Female" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_setting_a_quit_date,
								SUM(IF(c.gender = "Female" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_setting_a_quit_date,
								SUM(IF(c.gender = "Female" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_setting_a_quit_date,
								SUM(IF(c.gender = "Female" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_setting_a_quit_date,
								SUM(IF(c.gender = "Female" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_setting_a_quit_date,
								SUM(IF(c.gender = "Female" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_setting_a_quit_date,

							/* quit self reported */

								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported", 1,0)) AS total_females_quit_self_reported,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_quit_self_reported,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_quit_self_reported,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_quit_self_reported,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_quit_self_reported,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_quit_self_reported,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit self-reported" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_quit_self_reported,


							/* not quit */

								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit", 1,0)) AS total_females_not_quit,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_not_quit,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_not_quit,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_not_quit,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_not_quit,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_not_quit,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Not quit" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_not_quit,


							/* lost to follow-up */

								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */), 1,0)) AS total_females_lost_to_follow_up,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_lost_to_follow_up,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_lost_to_follow_up,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_lost_to_follow_up,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_lost_to_follow_up,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_lost_to_follow_up,
								SUM(IF(c.gender = "Female" AND (mf.' . $to_field . ' = "Lost to follow-up" /* OR ' . $to_field . ' IS NULL */) AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_lost_to_follow_up,


							/* CO Verified */

								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified", 1,0)) AS total_females_co_verified,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_co_verified,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_co_verified,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_co_verified,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_co_verified,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_co_verified,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Quit CO verified" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_co_verified,

							/* Referred to GP */

								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP", 1,0)) AS total_females_referred_to_gp,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_referred_to_gp,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_referred_to_gp,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_referred_to_gp,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_referred_to_gp,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_referred_to_gp,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Referred to GP" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_referred_to_gp,

							/* Refer to tier 3 */

								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3", 1,0)) AS total_females_refer_to_tier_3,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) < 18 , 1,0)) AS females_under_18_refer_to_tier_3,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 35, 1,0)) AS females_18_34_refer_to_tier_3,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 34 AND client_age(mf.date_created, c.date_of_birth) < 45, 1,0)) AS females_35_44_refer_to_tier_3,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 44 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_45_59_refer_to_tier_3,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 17 AND client_age(mf.date_created, c.date_of_birth) < 60, 1,0)) AS females_18_59_refer_to_tier_3,
								SUM(IF(c.gender = "Female" AND mf.' . $to_field . ' = "Refer to tier 3" AND client_age(mf.date_created, c.date_of_birth) > 59, 1,0)) AS females_60_and_over_refer_to_tier_3,

					/* pregnant women */

						SUM(IF(c.gender = "Female" AND pregnant = 1, 1,0)) AS pregnant_women_setting_a_quit_date,
						SUM(IF(c.gender = "Female" AND pregnant = 1 AND mf.' . $to_field . ' = "Quit self-reported", 1,0)) AS pregnant_women_quit_self_reported,
						SUM(IF(c.gender = "Female" AND pregnant = 1 AND mf.' . $to_field . ' = "Not quit", 1,0)) AS pregnant_women_not_quit,
						SUM(IF(c.gender = "Female" AND pregnant = 1 AND mf.' . $to_field . ' = "Lost to follow-up", 1,0)) AS pregnant_women_lost_to_follow_up,
						SUM(IF(c.gender = "Female" AND pregnant = 1 AND mf.' . $to_field . ' = "Quit CO verified", 1,0)) AS pregnant_women_co_verified,

					/* free prescriptions */

							SUM(IF(c.exempt_from_prescription_charge = 1, 1,0)) AS free_prescriptions_setting_a_quit_date,
							SUM(IF(c.exempt_from_prescription_charge = 1 AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS free_prescriptions_successfully_quit,

					/* socio-economic classifications */

						/* students */

							SUM(IF(c.occupation_code = "Full-time student", 1,0)) AS students_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Full-time student" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS students_successfully_quit,

						/* uemployed */

							SUM(IF(c.occupation_code = "Never worked/long-term unemployed", 1,0)) AS unemployed_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Never worked/long-term unemployed" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS unemployed_successfully_quit,

						/* retired */

							SUM(IF(c.occupation_code = "Retired", 1,0)) AS retired_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Retired" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS retired_successfully_quit,

						/* disabled */

							SUM(IF(c.occupation_code = "Sick/disabled and unable to work", 1,0)) AS disabled_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Sick/disabled and unable to work" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS disabled_successfully_quit,

						/* home carers */

							SUM(IF(c.occupation_code = "Home carer", 1,0)) AS home_carers_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Home carer" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS home_carers_successfully_quit,

						/* managerial */

							SUM(IF(c.occupation_code = "Managerial/professional", 1,0)) AS managerial_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Managerial/professional" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS managerial_successfully_quit,

						/* intermediate */

							SUM(IF(c.occupation_code = "Intermediate", 1,0)) AS intermediate_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Intermediate" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS intermediate_successfully_quit,

						/* manual */

							SUM(IF(c.occupation_code = "Routine manual", 1,0)) AS manual_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Routine manual" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS manual_successfully_quit,

						/* prisoners */

							SUM(IF(c.occupation_code = "Prisoner", 1,0)) AS prisoner_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Prisoner" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS prisoner_successfully_quit,

						/* unable to code */

							SUM(IF(c.occupation_code = "Unable to code", 1,0)) AS unable_to_code_setting_a_quit_date,
							SUM(IF(c.occupation_code = "Unable to code" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS unable_to_code_successfully_quit,

					/* pharmacotherapy treatment received */

						/* nrt only */
							SUM(IF((SUBSTRING(support_1, 1, 3) = "NRT" AND SUBSTRING(support_2, 1, 3) = "NRT") OR (SUBSTRING(support_1, 1, 3) = "NRT" AND ISNULL(support_2)) OR (ISNULL(support_1) AND SUBSTRING(support_2, 1, 3) = "NRT"), 1, 0)) AS nrt_only_setting_a_quit_date,
							SUM(IF(((SUBSTRING(support_1, 1, 3) = "NRT" AND SUBSTRING(support_2, 1, 3) = "NRT") OR (SUBSTRING(support_1, 1, 3) = "NRT" AND ISNULL(support_2)) OR (ISNULL(support_1) AND SUBSTRING(support_2, 1, 3) = "NRT")) AND(mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS nrt_only_successfully_quit,

						/* zyban only */

							SUM(IF((support_1 = "Zyban" AND support_2 = "Zyban") OR (support_1 = "Zyban" AND ISNULL(support_2)) OR (ISNULL(support_1) AND support_2 = "Zyban"), 1, 0)) AS zyban_only_setting_a_quit_date,
							SUM(IF(( (support_1 = "Zyban" AND support_2 = "Zyban") OR (support_1 = "Zyban" AND ISNULL(support_2)) OR (ISNULL(support_1) AND support_2 = "Zyban") ) AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS zyban_only_successfully_quit,

						/* champix only */

							SUM(IF((support_1 = "Champix" AND support_2 = "Champix") OR (support_1 = "Champix" AND ISNULL(support_2)) OR (ISNULL(support_1) AND support_2 = "Champix"), 1, 0)) AS champix_only_setting_a_quit_date,
							SUM(IF( ( (support_1 = "Champix" AND support_2 = "Champix") OR (support_1 = "Champix" AND ISNULL(support_2)) OR (ISNULL(support_1) AND support_2 = "Champix") ) AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS champix_only_successfully_quit,

						/* nrt and zyban concurrently or consecutively */

							SUM(IF((SUBSTRING(support_1, 1, 3) = "NRT" AND support_2 = "Zyban") OR (support_1 = "Zyban" AND SUBSTRING(support_2, 1, 3) = "NRT"), 1, 0)) AS nrt_zyban_setting_a_quit_date,
							SUM(IF( ( (SUBSTRING(support_1, 1, 3) = "NRT" AND support_2 = "Zyban") OR (support_1 = "Zyban" AND SUBSTRING(support_2, 1, 3) = "NRT") ) AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS nrt_zyban_successfully_quit,

						/* nrt and champix consecutively */

							SUM(IF(SUBSTRING(support_1, 1, 3) = "NRT" AND support_2 = "Champix", 1, 0)) AS nrt_champix_setting_a_quit_date,
							SUM(IF(SUBSTRING(support_1, 1, 3) = "NRT" AND support_2 = "Champix" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS nrt_champix_successfully_quit,

						/* no_pharmacotherapy */

							SUM(IF(ISNULL(support_1) AND ISNULL(support_2), 1, 0)) AS no_pharmacotherapy_setting_a_quit_date,
							SUM(IF(ISNULL(support_1) AND ISNULL(support_2) AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1, 0)) AS no_pharmacotherapy_successfully_quit,

					/* intervention type */

						/* closed group */

						SUM(IF(mf.intervention_type = "Closed group", 1,0)) AS closed_group_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "Closed group" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS closed_group_successfully_quit,

						/* open group */

						SUM(IF(mf.intervention_type = "Open (rolling) group", 1,0)) AS open_group_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "Open (rolling) group" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS open_group_successfully_quit,

						/* drop in */

						SUM(IF(mf.intervention_type = "Drop-in clinic", 1,0)) AS drop_in_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "Drop-in clinic" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS drop_in_successfully_quit,

						/* one to one */

						SUM(IF(mf.intervention_type = "One-to-one support", 1,0)) AS one_to_one_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "One-to-one support" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS one_to_one_successfully_quit,

						/* couple/family */

						SUM(IF(mf.intervention_type = "Couple/Family", 1,0)) AS family_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "Couple/Family" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS family_successfully_quit,

						/* telephone */

						SUM(IF(mf.intervention_type = "Telephone support", 1,0)) AS telephone_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "Telephone support" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS telephone_successfully_quit,

						/* other */

						SUM(IF(mf.intervention_type = "Other", 1,0)) AS other_support_setting_a_quit_date,
						SUM(IF(mf.intervention_type = "Other" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS other_support_successfully_quit,

					/* setting */

						/* stop smoking setting */

						SUM(IF(sp.location = "Stop smoking services", 1,0)) AS stop_smoking_services_setting_a_quit_date,
						SUM(IF(sp.location = "Stop smoking services" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS stop_smoking_services_successfully_quit,

						/* pharmacy */

						SUM(IF(sp.location = "Pharmacy", 1,0)) AS pharmacy_setting_a_quit_date,
						SUM(IF(sp.location = "Pharmacy" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS pharmacy_successfully_quit,

						/* prison */

						SUM(IF(sp.location = "Prison", 1,0)) AS prison_setting_a_quit_date,
						SUM(IF(sp.location = "Prison" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS prison_successfully_quit,

						/* primary care */

						SUM(IF(sp.location = "Primary care", 1,0)) AS primary_care_setting_a_quit_date,
						SUM(IF(sp.location = "Primary care" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS primary_care_successfully_quit,

						/* hospital ward */

						SUM(IF(sp.location = "Hospital ward", 1,0)) AS hospital_ward_setting_a_quit_date,
						SUM(IF(sp.location = "Hospital ward" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS hospital_ward_successfully_quit,

						/* hospital ward */

						SUM(IF(sp.location = "Hospital ward", 1,0)) AS hospital_ward_setting_a_quit_date,
						SUM(IF(sp.location = "Hospital ward" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS hospital_ward_successfully_quit,

						/* dental practice */

						SUM(IF(sp.location = "Dental practice", 1,0)) AS dental_practice_setting_a_quit_date,
						SUM(IF(sp.location = "Dental practice" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS dental_practice_successfully_quit,

						/* military base setting */

						SUM(IF(sp.location = "Military base setting", 1,0)) AS military_base_setting_setting_a_quit_date,
						SUM(IF(sp.location = "Military base setting" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS military_base_setting_successfully_quit,

						/* other */

						SUM(IF(sp.location = "Other", 1,0)) AS other_setting_setting_a_quit_date,
						SUM(IF(sp.location = "Other" AND (mf.' . $to_field . ' = "Quit CO verified" OR mf.' . $to_field . ' = "Quit self-reported"), 1,0)) AS other_setting_successfully_quit

				FROM
					monitoring_forms mf,
					clients c,
					service_providers sp
				WHERE
					c.monitoring_form_id = mf.id
					AND sp.id = mf.service_provider_id ';

		// are we using date created or quit date set?
		if(@$_GET['date_type'] == 'qds')
		{
			// sql
			$sql .= ' AND mf.agreed_quit_date  >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					  AND mf.agreed_quit_date  <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
		} else {
			// sql
			$sql .= ' AND mf.date_created >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					  AND mf.date_created <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';
		}

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

		return $this->db->query($sql)->row_array();
	}




	/**
	 * Function to get data for the overall quit by providers report
	 *
	 * Provider: total, successful, lost.
	 *
	 */
	public function get_ic_providers()
	{
		// Gather params

		$date_from = $this->db->escape(parse_date($this->input->get('date_from')));
		$date_to = $this->db->escape(parse_date($this->input->get('date_to')));
		$date_type = $this->input->get('date_type');
		$pct_id = $this->input->get('pct_id');

		// Determine which date point to use - 4 week or 12.
		$follow_up = (int) element('follow_up', $_GET, 4);

		// Set which treatment outcome field should be used in query
		$to_field = 'treatment_outcome_' . $follow_up;

		$where = '';

		// Date field to use for range
		if ($date_type === FALSE OR $date_type === 'qds')
		{
			$where .= " AND mf.agreed_quit_date  >= $date_from AND mf.agreed_quit_date  <= $date_to ";
		}
		elseif ($date_type === 'dc')
		{
			$where .= "AND mf.date_created >= $date_from AND mf.date_created <= $date_to ";
		}

		// PCT (Local Authority) ID
		if ($pct_id)
		{
			$where .= ' AND sp.pct_id = ' . (int) $pct_id . ' ';
		}

		$sql = 'SELECT
					pcts.pct_name,
					sp.name,
					SUM(1) AS total,
					SUM(IF(' . $to_field . ' = "Quit self-reported" OR ' . $to_field . ' = "Quit CO verified", 1, 0)) AS successful_quit_' . $follow_up . ',
					SUM(IF(' . $to_field . ' = "Quit CO verified", 1, 0)) AS successful_quit_co_' . $follow_up . ',
					SUM(IF(' . $to_field . ' = "Lost to follow-up", 1, 0)) AS lost_' . $follow_up . '
				FROM
					service_providers sp
				LEFT JOIN
					monitoring_forms mf on mf.service_provider_id = sp.id
				LEFT JOIN
					pcts ON sp.pct_id = pcts.id
				WHERE
					1 = 1
				' . $where . '
				GROUP BY
					sp.id
				ORDER BY
					pcts.pct_name ASC,
					sp.name ASC
				';

		$result = $this->db->query($sql)->result_array();
		$pcts = array();

		foreach ($result as $row)
		{
			$pcts[$row['pct_name']][] = $row;
		}

		return $pcts;
	}


	function get_total_missing_information()
	{
		$sql = 'SELECT
					COUNT(monitoring_form_id) AS total
				FROM
					clients
				WHERE
					nhs_number = ""
					OR gp_code = ""';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_missing_information()
	{
		$sql = 'SELECT
					c.monitoring_form_id,
					c.nhs_number,
					DATE_FORMAT(c.date_of_birth, "%d/%m/%Y") AS date_of_birth_format,
					c.fname,
					c.sname,
					c.gender,
					c.gp_code
				FROM
					clients c
				WHERE
					c.nhs_number = ""
					OR c.gp_code = ""';

		return $this->db->query($sql)->result_array();
	}

	function set_missing_information($mf_id, $nhs_number, $gp_code)
	{
		$sql = 'UPDATE
					clients
				SET
					nhs_number = ?,
					gp_code = ?
				WHERE
					monitoring_form_id = ?';

		$this->db->query($sql, array($nhs_number, $gp_code, $mf_id));
	}


	function delete_monitoring_form($mf_id)
	{
		$sql = 'DELETE FROM
					monitoring_forms
				WHERE
					id = "' . (int)$mf_id . '";';

		$this->db->query($sql);

		$sql = 'DELETE FROM
					monitoring_form_claims
				WHERE
					monitoring_form_id = "' . (int)$mf_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Monitoring form and claims deleted');
	}




	/**
	 * Get all the mail merge documents that have been generated for the specified monitoring form
	 *
	 * @param int $mf		Monitoring form ID
	 * @return array		Result array of document and mail merge generation info
	 */
	public function get_mail_merges($mf = 0)
	{
		$sql = 'SELECT
					mfmm.*,
					mmd.*,
					DATE_FORMAT(mfmm_datetime, "%D %b %Y") AS mfmm_datetime_format,
					CONCAT(sps.fname, " ", sps.sname) AS sps_name
				FROM
					monitoring_forms_mail_merges mfmm
				LEFT JOIN
					monitoring_forms mf
						ON mfmm_mf_id = mf.id
				LEFT JOIN
					service_provider_staff sps
						ON mfmm_sps_id = sps.id
				LEFT JOIN
					mail_merge_documents mmd
						ON mfmm_mmd_id = mmd_id
				WHERE
					mfmm_mf_id = ?
				AND
					mfmm_sp_id = ?
				ORDER BY
					mfmm_datetime DESC';

		return $this->db->query($sql, array($mf, $this->session->userdata('sp_id')))->result_array();
	}




	/**
	 * Log the production of a mail merge document against a monitoring form
	 *
	 * @param array $data		Array of values for the DB columns to set (mfmm_mf_id and mfmm_mmd_id are required)
	 * @return mixed		New AUTO_INCREMENT ID on success, result of $query on failure (probably FALSE?)
	 */
	public function log_mail_merge($data = array())
	{
		$data['mfmm_datetime'] = date('Y-m-d H:i:s');
		$data['mfmm_sps_id'] = $this->session->userdata('sps_id');
		$data['mfmm_sp_id'] = $this->session->userdata('sp_id');

		$sql = $this->db->insert_string('monitoring_forms_mail_merges', $data);
		$query = $this->db->query($sql);
		return ($query) ? $this->db->insert_id() : $query;
	}




	/**
	 * Delete a monitoring form mail merge entry
	 *
	 * @param int $mfmm_id		Monitoring form mail merge row ID to remove
	 * @return bool
	 */
	public function delete_mail_merge($mfmm_id = 0)
	{
		$sql = 'DELETE FROM monitoring_forms_mail_merges WHERE mfmm_id = ? AND mfmm_sp_id = ? LIMIT 1';
		$sp_id = $this->session->userdata('sp_id');
		return $this->db->query($sql, array($mfmm_id, $sp_id));
	}




	/**
	 * Possible duplicate client record search
	 *
	 */
	public function dupe_search($params = array())
	{
		// Get fields supplied
		$fname = element('fname', $params);
		$sname = element('sname', $params);
		$gender = element('gender', $params);
		$dob_date = (int) element('dob_date', $params);
		$dob_month = element('dob_month', $params);
		$dob_year = (int) element('dob_year', $params);
		$post_code = format_postcode(element('post_code', $params));
		$address = element('address', $params);
		$tel_daytime = str_replace(' ', '', element('tel_daytime', $params));
		$tel_mobile = str_replace(' ', '', element('tel_mobile', $params));

		// Post code parts
		$post_code_last = substr($post_code, -3);
		$post_code_most = substr($post_code, 0, -2);

		// Address line 1
		$addr1 = '';
		if ($address)
		{
			$addr1 = strtolower(current(explode("\n", $address)));
			$addr1 = preg_replace('/^[0-9]+\s/', '', $addr1);
			$addr1 = preg_replace('/\s+(road|street|way|avenue|drive|grove|lane|gardens|place|crescent|close|square|hill|circus|mews|vale|rise|row|mead|wharf|end|court|cross|side|view|walk|park|meadow|ct|st|ave|ln|rd|cres)/', '', $addr1);
		}

		// Range for date of birth date
		$dob_date_min = ($dob_date - 5 < 1 ? 1 : $dob_date - 5);
		$dob_date_max = ($dob_date + 5 > 31 ? 31 : $dob_date + 5);

		// Phones
		$tel_day_most = substr($tel_daytime, 0, -2);
		$tel_mob_most = substr($tel_mobile, 0, -2);

		// Like mail merge for database queries.
		// The main query ANDs all of these tags, which initially contain the match queries.
		// If any of the required fields for these are empty, they get changed later to a simple 1=1

		$wheres = array(
			'[fname_like]' => ' (fname LIKE "%' . $this->db->escape_like_str($fname) . '%" OR SOUNDEX(fname) = SOUNDEX(' . $this->db->escape($fname) . ')) ',
			'[sname_like]' => ' (sname LIKE "%' . $this->db->escape_like_str($sname) . '%" OR SOUNDEX(sname) = SOUNDEX(' . $this->db->escape($sname) . ')) ',

			'[gender]' => ' gender = ' . $this->db->escape($gender) . ' ',
			'[birth_year]' => ' YEAR(date_of_birth) BETWEEN ' . ($dob_year - 5) . ' AND ' . ($dob_year + 5) . ' ',
			'[birth_date]' => ' DAY(date_of_birth) BETWEEN ' . $dob_date_min . ' AND ' . $dob_date_max . ' ',

			'[post_code]' => ' ( c.post_code LIKE "%' . $this->db->escape_like_str($post_code_last) . '" OR c.post_code LIKE "' . $this->db->escape_like_str($post_code_most) . '%") ',
			'[addr1]' => ' ( address LIKE "%' . $this->db->escape_like_str($addr1) . '%" OR SOUNDEX(address) = SOUNDEX(' . $this->db->escape($addr1) . ') ) ',

			'[tel_daytime]' => ' (tel_daytime LIKE "' . $this->db->escape_like_str($tel_day_most) . '%") ',
			'[tel_mobile]' => ' (tel_mobile LIKE "' . $this->db->escape_like_str($tel_mob_most) . '%") ',
		);

		// Track the number of empty items we have
		$empty = 0;

		// Check if the data required to perform the matches is present.
		// If not, update the tag to remove the match query

		if (empty($fname))
		{
			$wheres['[fname_like]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($sname))
		{
			$wheres['[sname_like]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($post_code_last) || strlen($post_code) < 6)
		{
			$wheres['[post_code]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($dob_year) || $dob_year === 0)
		{
			$wheres['[birth_year]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($dob_date) || $dob_date === 0)
		{
			$wheres['[birth_date]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($gender))
		{
			$wheres['[gender]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($addr1))
		{
			$wheres['[addr1]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($tel_daytime))
		{
			$wheres['[tel_daytime]'] = ' 1 = 1';
			$empty++;
		}

		if (empty($tel_mobile))
		{
			$wheres['[tel_mobile]'] = ' 1 = 1';
			$empty++;
		}

		// Make sure we have enough pieces of the puzzle
		$num_present = count($wheres) - $empty;
		if ($num_present < 2)
		{
			return FALSE;
		}

		$sql = "SELECT
					mf.id,
					service_provider_id,
					date_created,
					title,
					fname,
					sname,
					gender,
					date_of_birth,
					address,
					c.post_code,
					tel_daytime,
					tel_mobile
				FROM
					monitoring_forms mf
				LEFT JOIN
					clients c
					ON monitoring_form_id = mf.id
				LEFT JOIN
					service_providers sp
					ON mf.service_provider_id = sp.id
				WHERE

					/* Gender */
					[gender]

				AND

					/* Date */
					date_created >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)

				AND

					(treatment_outcome_4 IS NULL OR treatment_outcome_4 NOT IN ('Quit CO verified', 'Quit self-reported') )

				AND

					(treatment_outcome_12 IS NULL OR treatment_outcome_12 NOT IN ('Quit CO verified', 'Quit self-reported') )

				AND

				(

					( [fname_like] AND [sname_like] AND [post_code] AND [birth_year] AND [birth_date] AND [addr1] AND [tel_daytime] AND [tel_mobile] )

				)

				LIMIT 0, 10";

		$sql = str_replace(array_keys($wheres), array_values($wheres), $sql);
		//echo $sql;

		$query = $this->db->query($sql);
		return $query->result_array();

	}




}
