<?php

class Model_claims extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_total_claims($sp_id = 0, $mf_id = 0)
	{
		if($this->session->userdata('admin_id'))
		{
			$sql = 'SELECT
						COUNT(mfc.monitoring_form_id) AS total
					FROM
						monitoring_form_claims mfc,
						monitoring_forms mf,
						service_providers sp
					WHERE
						mf.id = mfc.monitoring_form_id
						AND sp.id = mf.service_provider_id ';

			if(@$_GET['pct_id'])
				$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

			if(@$_GET['location'])
				$sql .= ' AND sp.location = ' . $this->db->escape($_GET['location']) . ' ';
		}
		else
		{
			$sql = 'SELECT
						COUNT(mfc.monitoring_form_id) AS total
					FROM
						monitoring_form_claims mfc,
						monitoring_forms mf
					WHERE
						mf.id = mfc.monitoring_form_id ';
		}

		if(@$_GET['date_from'])
			$sql .= ' AND mfc.date_of_claim >= ' . $this->db->escape(parse_date($_GET['date_from'])) . ' ';

		if(@$_GET['date_to'])
			$sql .= ' AND mfc.date_of_claim <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if(@$_GET['claim_type'])
			$sql .= ' AND mfc.claim_type = ' . $this->db->escape($_GET['claim_type']) . ' ';

		if(@$_GET['status'])
			$sql .= ' AND mfc.status = ' . $this->db->escape($_GET['status']) . ' ';

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		if($mf_id)
			$sql .= ' AND mfc.monitoring_form_id = "' . (int)$mf_id . '" ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_claims($sp_id = 0, $mf_id = 0, $start = 0, $limit = 0)
	{
		if( ! in_array(@$_GET['order'], array('monitoring_form_id', 'service_provider_id', 'date_of_claim', 'claim_type', 'cost', 'status')) ) $_GET['order'] = 'monitoring_form_id';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		if($this->session->userdata('admin_id'))
		{
			$sql = 'SELECT
						mfc.monitoring_form_id,
						mfc.claim_type,
						DATE_FORMAT(mfc.date_of_claim, "%D %b %Y") AS date_of_claim_format,
						CONCAT("&pound;", FORMAT(mfc.cost, 2)) AS cost,
						mfc.status,
						sp.name AS sp_name
					FROM
						monitoring_form_claims mfc,
						monitoring_forms mf,
						service_providers sp
					WHERE
						mf.id = mfc.monitoring_form_id
						AND sp.id = mf.service_provider_id ';

			if(@$_GET['pct_id'])
				$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

			if(@$_GET['location'])
				$sql .= ' AND sp.location = ' . $this->db->escape($_GET['location']) . ' ';
		}
		else
		{
			$sql = 'SELECT
						mfc.monitoring_form_id,
						mfc.claim_type,
						DATE_FORMAT(mfc.date_of_claim, "%D %b %Y") AS date_of_claim_format,
						CONCAT("&pound;", FORMAT(mfc.cost, 2)) AS cost,
						mfc.status
					FROM
						monitoring_form_claims mfc,
						monitoring_forms mf
					WHERE
						mf.id = mfc.monitoring_form_id ';
		}

		if(@$_GET['date_from'])
			$sql .= ' AND mfc.date_of_claim >= ' . $this->db->escape(parse_date($_GET['date_from'])) . ' ';

		if(@$_GET['date_to'])
			$sql .= ' AND mfc.date_of_claim <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if(@$_GET['claim_type'])
			$sql .= ' AND mfc.claim_type = ' . $this->db->escape($_GET['claim_type']) . ' ';

		if(@$_GET['status'])
			$sql .= ' AND mfc.status = ' . $this->db->escape($_GET['status']) . ' ';

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		if($mf_id)
			$sql .= ' AND mfc.monitoring_form_id = "' . (int)$mf_id . '" ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}


	function get_claim($mf_id, $claim_type)
	{
		$sql = 'SELECT
					mfc.monitoring_form_id AS mf_id,
					mfc.claim_type,
					DATE_FORMAT(mfc.date_of_claim, "%D %b %Y") AS date_of_claim_format,
					DATE_FORMAT(mfc.status_datetime_set, "%D %b %Y") AS status_datetime_set_format,
					sp.id AS sp_id
				FROM
					monitoring_form_claims mfc,
					monitoring_forms mf,
					service_providers sp
				WHERE
					mfc.monitoring_form_id = ?
					AND mfc.claim_type = ?
					AND mf.id = mfc.monitoring_form_id
					AND sp.id = mf.service_provider_id';

		return $this->db->query($sql, array($mf_id, $claim_type))->row_array();
	}


	function get_service_provider($sp_id)
	{
		$sql = 'SELECT
					sp.name AS sp_name,
					sps.fname,
					sps.email
				FROM
					service_providers sp,
					service_provider_staff sps
				WHERE
					sp.id = "' . (int)$sp_id . '"
					AND sps.id = sp.master_id';

		return $this->db->query($sql)->row_array();
	}


	function get_csv($sp_id = 0)
	{
		if( ! in_array(@$_GET['order'], array('monitoring_form_id', 'service_provider_id', 'date_of_claim', 'claim_type', 'cost', 'status')) ) $_GET['order'] = 'date_of_claim';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
					sp.name AS sp_name,
					sp.advisor_code,
					sp.provider_code,
					sp.cost_code,
					sp.location,
					sp.department,
					sp.venue,
					sp.post_code AS sp_post_code,
					sp.telephone,
					IF(sp.pct = "", "Unassigned", pct) AS pct,
					IF(sp.pct = "", "Unassigned", pct) AS pct_name,
					IF(spg.id, spg.name, "Unassigned") AS group_name,
					mfc.monitoring_form_id,
					DATE_FORMAT(mfc.date_of_claim, "%d/%m/%Y") AS date_of_claim_format,
					DATE_FORMAT(mf.agreed_quit_date, "%d/%m/%Y") AS agreed_quit_date_format,
					mfc.claim_type,
					CONCAT("", FORMAT(mfc.cost, 2)) AS cost,
					mfc.status
				FROM
					monitoring_form_claims mfc,
					monitoring_forms mf,
					service_providers sp
				LEFT JOIN
					service_provider_groups spg
						ON (spg.id = sp.group_id)
				WHERE
					mf.id = mfc.monitoring_form_id
					AND sp.id = mf.service_provider_id ';

		if(@$_GET['pct_id'])
				$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

		if(@$_GET['location'])
			$sql .= ' AND sp.location = ' . $this->db->escape($_GET['location']) . ' ';

		if(@$_GET['date_from'])
			$sql .= ' AND mfc.date_of_claim >= ' . $this->db->escape(parse_date($_GET['date_from'])) . ' ';

		if(@$_GET['date_to'])
			$sql .= ' AND mfc.date_of_claim <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if(@$_GET['claim_type'])
			$sql .= ' AND mfc.claim_type = ' . $this->db->escape($_GET['claim_type']) . ' ';

		if(@$_GET['status'])
			$sql .= ' AND mfc.status = ' . $this->db->escape($_GET['status']) . ' ';

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'];

		return $this->db->query($sql)->result_array();
	}


	function add_claim($mf_id, $claim_type, $cost)
	{
		$sql = 'INSERT INTO
					monitoring_form_claims
				SET
					monitoring_form_id = "' . (int)$mf_id . '",
					claim_type = ' . $this->db->escape($claim_type) . ',
					date_of_claim = NOW(),
					cost = "' . (float)$cost . '",
					status = "Pending",
					status_datetime_set = NOW();';

		$this->db->query($sql);
	}


	function set_claim_status($mf_id, $claim_type, $status)
	{
		$sql = 'UPDATE
					monitoring_form_claims
				SET
					status = ' . $this->db->escape($status) . ',
					status_datetime_set = NOW()
				WHERE
					monitoring_form_id = "' . (int)$mf_id . '"
					AND claim_type = ' . $this->db->escape($claim_type) . ';';

		$this->db->query($sql);
	}


	function get_claim_cost($sp_id, $claim_type = NULL)
	{
		$sql = 'SELECT
					group_id,
					claim_options
				FROM
					service_providers
				WHERE
					id = "' . (int)$sp_id . '";';

		$service_provider = $this->db->query($sql)->row_array();

		/* if the service provider has it's own personal claim costs use them */
		if($claim_costs = unserialize($service_provider['claim_options']))
		{
		}
		elseif($service_provider['group_id'])
		{
			/* else if they are part of a group, use the claim costs of that group */
			$sql = 'SELECT
						claim_options
					FROM
						service_provider_groups
					WHERE
						id = "' . (int)$service_provider['group_id'] . '";';

			$service_provider_group = $this->db->query($sql)->row_array();

			$claim_costs = unserialize($service_provider_group['claim_options']);
		}
		else
		{
			/* else if they don't have their own claim costs assigned or are not part of any group, use the default claim costs */
			$sql = 'SELECT
						option_value
					FROM
						options
					WHERE
						option_name = "default_claim_options";';

			$claim_options = $this->db->query($sql)->row_array();

			$claim_costs = unserialize($claim_options['option_value']);
		}

		if($claim_costs)
		{
			// Set default values for new 4/12 week options as the same value as follow up quit
			if ( ! element('claim_4_week', $claim_costs)) $claim_costs['claim_4_week'] = element('follow_up_quit', $claim_costs);
			if ( ! element('claim_12_week', $claim_costs)) $claim_costs['claim_12_week'] = element('follow_up_quit', $claim_costs);

			if($claim_type)
			{
				/* if this provider is set to not submit claims return false */
				if(@$claim_costs['do_not_claim'] == TRUE)
				{
					return false;
				}
				else
				{
					return $claim_costs[$claim_type];
				}
			}
			else
			{
				return $claim_costs;
			}
		}
		else
		{
			return false;
		}

	}


	function get_cron_csv($group_id = 0, $old_csv=false)
	{
		$sql = 'SELECT
					sp.name AS sp_name,
					sp.advisor_code,
					sp.provider_code,
					sp.cost_code,
					sp.location,
					sp.department,
					sp.venue,
					sp.post_code AS sp_post_code,
					sp.telephone,
					IF(spg.id, spg.name, "Unassigned") AS group_name,
					mfc.monitoring_form_id,
					DATE_FORMAT(mfc.date_of_claim, "%d/%m/%Y") AS date_of_claim_format,
					DATE_FORMAT(mf.agreed_quit_date, "%d/%m/%Y") AS agreed_quit_date_format,
					mfc.claim_type,
					CONCAT("", FORMAT(mfc.cost, 2)) AS cost,
				';
		if($old_csv)
		{
			$sql .= ' "Pending" AS status, ';
		}
		else
		{
			$sql .= ' mfc.status, ';
		}
		$sql .= '
					pct_name AS pct,
					pct_name
				FROM
					monitoring_form_claims mfc,
					monitoring_forms mf,
					service_providers sp
				LEFT JOIN
					service_provider_groups spg
						ON (spg.id = sp.group_id)
				LEFT JOIN
					pcts
					ON sp.pct_id = pcts.id
				WHERE
					mf.id = mfc.monitoring_form_id
					AND sp.id = mf.service_provider_id
					';
		if($old_csv)
		{
			$sql .= '  ';
		}
		else
		{
			$sql .= ' AND mfc.status = "Pending" ';
		}

		if($group_id)
			$sql .= ' AND sp.group_id = "' . (int)$group_id . '" ';

		if(@$_GET['date_from'])
			$sql .= ' AND mfc.date_of_claim >= ' . $this->db->escape(parse_date($_GET['date_from'])) . ' ';

		if(@$_GET['date_to'])
			$sql .= ' AND mfc.date_of_claim <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . $this->db->escape($_GET['pct_id']) . ' ';

		$sql .= ' ORDER BY date_of_claim DESC ';

		$claims = $this->db->query($sql)->result_array();

		$claims_by_location = array();

		foreach($claims as $claim)
		{
			$claims_by_location[$claim['location']][] = $claim;
		}

		return $claims_by_location;
	}

}
