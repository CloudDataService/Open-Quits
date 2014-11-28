<?php

class Model_service_providers extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_total_service_providers()
	{
		$sql = 'SELECT
					COUNT(id) AS total
				FROM
					service_providers
				WHERE
					active = "' . (@$_GET['inactive'] ? '0' : '1') . '" ';

		if(@$_GET['sp_id'])
			$sql .= ' AND id = ' . (int)$_GET['sp_id'] . ' ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		if ($this->input->get('post_code'))
			$sql .= ' AND post_code LIKE "%' . $this->db->escape_like_str($this->input->get('post_code')) . '%" ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_service_providers($start = 0, $limit = 0)
	{
		if( ! in_array(@$_GET['order'], array('advisor_code', 'name', 'location', 'department', 'pct')) ) $_GET['order'] = 'name';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'asc';

		$sql = 'SELECT
					id,
					IF(advisor_code = "", "N/A", advisor_code) AS advisor_code,
					IF(active, name, CONCAT(name, " <span style=\"color:#f00;\">(inactive)</span>")) AS name,
					IF(location = "", "N/A", location) AS location,
					location_other,
					IF(department = "", "N/A", department) AS department,
					IF(pct = "", "Unassigned", pct) AS pct,
					active
				FROM
					service_providers
				WHERE
					active = "' . (@$_GET['inactive'] ? '0' : '1') . '" ';

		if(@$_GET['sp_id'])
			$sql .= ' AND id = "' . (int)$_GET['sp_id'] . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		if ($this->input->get('post_code'))
			$sql .= ' AND post_code LIKE "%' . $this->db->escape_like_str($this->input->get('post_code')) . '%" ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}


	function get_service_providers_select($cache = 1)
	{
		// if we want to get cached version
		//if($cache)
		//{
		//	if($service_providers_select = $this->cache->get('service_providers_select'))
		//		return $service_providers_select;
		//}

		$sql = 'SELECT
					id,
					IF(lENGTH(name) > 35, CONCAT(SUBSTR(name, 1, 35), "..."), name) AS name
				FROM
					service_providers
				WHERE
					active = 1 ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		$sql .= ' ORDER BY name ASC ';

		$service_providers = $this->db->query($sql)->result_array();

		$service_providers_select = array();

		foreach($service_providers as $sp)
		{
			$char = strtoupper(substr($sp['name'], 0, 1));

			$service_providers_select[$char][] = $sp;
		}

		// update cache
		//$this->cache->save('service_providers_select', $service_providers_select, 999999999);

		return $service_providers_select;
	}


	function get_service_provider($sp_id)
	{
		if($sp_id)
		{
			$sql = 'SELECT
						sp.*,
						IF(sp.pct = "", "Unassigned", pct) AS pct,
						IF(spg.id, spg.name, "Unassigned") AS group_name,
						pct_name
					FROM
						service_providers sp
					LEFT JOIN
						service_provider_groups spg
							ON (spg.id = sp.group_id)
					LEFT JOIN
						pcts
						ON sp.pct_id = pcts.id
					WHERE
						sp.id = "' . (int)$sp_id . '" ';

			if ($this->input->get('pct_id'))
				$sql .= ' AND sp.pct_id = ' . (int) $_GET['pct_id'] . ' ';

			if ($service_provider = $this->db->query($sql)->row_array())
			{
				$service_provider['claim_options'] = unserialize($service_provider['claim_options']);

				if ($service_provider['claim_options'])
				{
					// Set default values for new 4/12 week options as the same value as follow up quit
					if ( ! element('claim_4_week', $service_provider['claim_options'])) $service_provider['claim_options']['claim_4_week'] = element('follow_up_quit', $service_provider['claim_options']);
					if ( ! element('claim_12_week', $service_provider['claim_options'])) $service_provider['claim_options']['claim_12_week'] = element('follow_up_quit', $service_provider['claim_options']);
				}
			}

			return $service_provider;
		}

		return FALSE;
	}


	function get_csv()
	{
		if( ! in_array(@$_GET['order'], array('advisor_code', 'name', 'location', 'department', 'venue')) ) $_GET['order'] = 'name';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'asc';

		$sql = 'SELECT
					sp.id,
					sp.name,
					sp.post_code,
					IF(sp.location = "", "N/A", sp.location) AS location,
					sp.location_other,
					sp.department,
					sp.venue,
					sp.telephone,
					sp.provider_code,
					sp.advisor_code,
					sp.cost_code,
					sp.active,
					IF(pct_name IS NULL, "Unassigned", pct_name) AS pct_name,
					IF(spg.id, spg.name, "Unassigned") AS group_name
				FROM
					service_providers sp
				LEFT JOIN
					service_provider_groups spg
					ON (spg.id = sp.group_id)
				LEFT JOIN
					pcts
					ON sp.pct_id = pcts.id
				WHERE
					1 = 1 ';

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . (int) $_GET['pct_id'] . ' ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		return $this->db->query($sql)->result_array();
	}


	function set_service_provider($sp_id = 0)
	{
		if($this->input->post('claim_options_enabled'))
		{
			/*$claim_options = $this->db->escape(serialize(array('initial' => $this->input->post('claim_options_initial'),
								   											'follow_up_quit' => $this->input->post('claim_options_follow_up_quit'),
																			'do_not_claim' => $this->input->post('claim_options_do_not_claim'))));*/
			$claim_options = $this->db->escape(serialize(array(
				'initial' => $this->input->post('claim_options_initial'),
				'claim_4_week' => $this->input->post('claim_4_week'),
				'claim_12_week' => $this->input->post('claim_12_week'),
			)));
		}
		else
		{
			$claim_options = 'NULL';
		}

		if($sp_id)
		{
			$sql = 'UPDATE
						service_providers
					SET
						group_id = ' . ($this->input->post('group_id') ? (int)$this->input->post('group_id') : 'NULL') . ',
						name = ?,
						post_code = ?,
						location = ?,
						location_other = ?,
						department = ?,
						venue = ?,
						telephone = ?,
						advisor_code = ?,
						provider_code = ?,
						cost_code = ?,
						pct_id = ?,
						tier_3 = ?,
						claim_options = ' . $claim_options . '
					WHERE
						id = "' . (int)$sp_id . '";';

			$this->session->set_flashdata('action', 'Service provider updated');
		}
		else
		{
			$sql = 'INSERT INTO
						service_providers
					SET
						group_id = ' . ($this->input->post('group_id') ? (int)$this->input->post('group_id') : 'NULL') . ',
						name = ?,
						post_code = ?,
						location = ?,
						location_other = ?,
						department = ?,
						venue = ?,
						telephone = ?,
						advisor_code = ?,
						provider_code = ?,
						cost_code = ?,
						pct_id = ?,
						tier_3 = ?,
						claim_options = ' . $claim_options . ';';

			$this->session->set_flashdata('action', 'Service provider added');
		}

		$this->db->query($sql, array($this->input->post('name'),
									 $this->input->post('post_code'),
									 $this->input->post('location'),
									 ($this->input->post('location') == 'Other' ? $this->input->post('location_other') : NULL),
									 $this->input->post('department'),
									 $this->input->post('venue'),
									 $this->input->post('telephone'),
									 $this->input->post('advisor_code'),
									 $this->input->post('provider_code'),
									 $this->input->post('cost_code'),
									 $this->input->post('pct_id'),
									 $this->input->post('tier_3'))
						 );

		if( ! $sp_id)
		{
			return $this->db->insert_id();
		}

		return $sp_id;
	}


	function set_active_status($sp_id, $active_status)
	{
		$sql = 'UPDATE
					service_providers
				SET
					active = "' . (int)$active_status . '"
				WHERE
					id = "' . (int)$sp_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', ($active_status ? 'Service provider set as active' : 'Service provider set as inactive'));
	}


	function delete_service_provider($sp_id)
	{
		$sql = 'DELETE FROM
					service_providers
				WHERE
					id = "' . (int)$sp_id . '";';

		$this->db->query($sql);

		$sql = 'DELETE FROM
					service_provider_staff
				WHERE
					service_provider_id = "' . (int)$sp_id . '";';

		$this->db->query($sql);

		$sql = 'SELECT
					id
				FROM
					monitoring_forms
				WHERE
					service_provider_id = "' . (int)$sp_id . '";';

		$monitoring_forms = $this->db->query($sql)->result_array();

		foreach($monitoring_forms as $mf)
		{
			$sql = 'DELETE FROM
						clients
					WHERE
						monitoring_form_id = "' . (int)$mf['id'] . '";';

			$this->db->query($sql);

			$sql = 'DELETE FROM
						monitoring_form_claims
					WHERE
						monitoring_form_id = "' . (int)$mf['id'] . '";';

			$this->db->query($sql);

			$sql = 'DELETE FROM
						monitoring_forms
					WHERE
						id = "' . (int)$mf['id'] . '";';

			$this->db->query($sql);
		}

		$this->session->set_flashdata('action', 'Service provider deleted');
	}


	function get_total_service_providers_staff()
	{
		$sql = 'SELECT
					COUNT(sps.id) AS total
				FROM
					service_provider_staff sps
				LEFT JOIN
					service_providers sp
					ON service_provider_id = sp.id
				WHERE
					sps.active = "' . (@$_GET['inactive'] ? '0' : '1') . '" ';

		if(@$_GET['sp_id'])
			$sql .= ' AND service_provider_id = "' . (int)$_GET['sp_id'] . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		if ($this->input->get('name'))
			$sql .= ' AND
				(fname LIKE "%' . $this->db->escape_like_str($this->input->get('name')) . '%") OR
				(sname LIKE "%' . $this->db->escape_like_str($this->input->get('name')) . '%") ';

		if ($this->input->get('email'))
			$sql .= ' AND email LIKE "%' . $this->db->escape_like_str($this->input->get('email')) . '%" ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_all_service_providers_staff($start = 0, $limit = 20)
	{
		if( ! in_array(@$_GET['order'], array('email', 'datetime_last_login', 'name', 'post_code', 'active')) ) $_GET['order'] = 'sname';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'asc';

		$sql = 'SELECT
					sps.id,
					sps.email,
					sps.fname,
					sps.sname,
					sps.advisor_code,
					IF(datetime_last_login, DATE_FORMAT(datetime_last_login, "%D %b %Y at %l:%S%p"), "N/A") AS datetime_last_login_format,
					IF(sp.master_id = sps.id, true, false) AS master,
					sps.service_provider_id,
					sps.active,
					sp.name,
					sp.post_code,
					IF(sp.location = "", "N/A", IF(sp.location = "Other" AND sp.location_other IS NOT NULL, CONCAT("Other", " (", sp.location_other, ")"), sp.location)) AS location,
					sp.venue,
					sp.telephone
				FROM
					service_provider_staff sps
				LEFT JOIN
					service_providers sp
					ON service_provider_id = sp.id
				WHERE
					sps.active = "' . (@$_GET['inactive'] ? '0' : '1') . '" ';

		if(@$_GET['sp_id'])
			$sql .= ' AND service_provider_id = "' . (int)$_GET['sp_id'] . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . (int) $_GET['pct_id'] . ' ';

		if ($this->input->get('name'))
			$sql .= ' AND
				(fname LIKE "%' . $this->db->escape_like_str($this->input->get('name')) . '%") OR
				(sname LIKE "%' . $this->db->escape_like_str($this->input->get('name')) . '%") ';

		if ($this->input->get('email'))
			$sql .= ' AND email LIKE "%' . $this->db->escape_like_str($this->input->get('email')) . '%" ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}


	function get_service_provider_staff($sp_id = 0)
	{
		$sql = 'SELECT
					sps.id,
					sps.email,
					sps.fname,
					sps.sname,
					sps.advisor_code,
					sps.active,
					IF(datetime_last_login, DATE_FORMAT(datetime_last_login, "%D %b %Y at %l:%S%p"), "N/A") AS datetime_last_login_format,
					IF(sp.master_id = sps.id, true, false) AS master,
					pct_name,
					spst.spst_date,
					IF(spst.spst_date, DATE_FORMAT(spst.spst_date, "%D %b %Y"), "N/A") AS spst_date_format
				FROM
					service_provider_staff sps
				LEFT JOIN
					service_providers sp
					ON sps.service_provider_id = sp.id
				LEFT JOIN
					pcts
					ON sp.pct_id = pcts.id
				LEFT JOIN sp_staff_training spst ON spst.spst_id =
					(SELECT spst_id FROM sp_staff_training tmp_training WHERE tmp_training.spst_sps_id = sps.id ORDER BY spst_date DESC LIMIT 1)
				WHERE
					sps.service_provider_id = "' . (int)$sp_id . '"
					/* AND sps.active = 1 */ ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		/* if service provider staff is logged in, do not include master in results */
		if($this->session->userdata('sps_id'))
			$sql .= ' AND sps.id != "' . (int)$this->session->userdata('sps_id') . '" ';

		$sql .= ' ORDER BY
					master DESC,
					sname ASC';

		return $this->db->query($sql)->result_array();
	}


	function get_service_provider_staff_member($sp_id, $sps_id = 0)
	{
		$sql = 'SELECT
					sps.id,
					sps.email,
					sps.fname,
					sps.sname,
					sps.advisor_code,
					IF(sp.master_id = sps.id, true, false) AS master,
					pct_name,
					spst.spst_date,
					IF(spst.spst_date, DATE_FORMAT(spst.spst_date, "%D %b %Y"), "N/A") AS spst_date_format,
					spst.spst_title
				FROM
					service_provider_staff sps,
					service_providers sp
				LEFT JOIN
					pcts
					ON sp.pct_id = pcts.id
				LEFT JOIN sp_staff_training spst ON spst.spst_id =
					(SELECT spst_id FROM sp_staff_training tmp_training WHERE tmp_training.spst_sps_id = "' . (int)$sps_id . '" ORDER BY spst_date DESC LIMIT 1)
				WHERE
					sps.id = "' . (int)$sps_id . '"
					AND sps.service_provider_id = "' . (int)$sp_id . '"
					AND sp.id = sps.service_provider_id
					AND sps.active = 1 ';

		return $this->db->query($sql)->row_array();
	}


	function add_service_provider_staff($sp_id, $password)
	{
		$sql = 'INSERT INTO
					service_provider_staff
				SET
					service_provider_id = "' . (int)$sp_id . '",
					fname = ?,
					sname = ?,
					email = ?,
					password = ?;';

		$this->db->query($sql, array($this->input->post('fname'),
									 $this->input->post('sname'),
									 $this->input->post('email'),
									 md5($this->config->config['salt'] . $password . $this->config->config['salt'])));

		$this->session->set_flashdata('action', 'Staff member added');

		return $this->db->insert_id();
	}


	function delete_service_provider_staff($sps_id)
	{
		$sql = 'UPDATE
					service_provider_staff
				SET
					active = 0
				WHERE
					id = "' . (int)$sps_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Staff member deleted');
	}


	function set_master($sp_id, $sps_id)
	{
		$sql = 'UPDATE
					service_providers
				SET
					master_id = "' . (int)$sps_id . '"
				WHERE
					id = "' . (int)$sp_id . '";';

		$this->db->query($sql);
	}


	function update_service_provider_staff_details($sps_id)
	{
		$sql = 'UPDATE
					service_provider_staff
				SET
					email = ?,
					fname = ?,
					sname = ?,
					advisor_code =?
				WHERE
					id = "' . (int)$sps_id . '";';

		$this->db->query($sql, array(
			$this->input->post('email'),
			$this->input->post('fname'),
			$this->input->post('sname'),
			strtoupper($this->input->post('advisor_code')) ?: null,
		));

		if ( ! $this->session->userdata('admin_id'))
		{
			$this->session->set_userdata(array(
				'email'        => $this->input->post('email'),
				'fname'        => $this->input->post('fname'),
				'sname'        => $this->input->post('sname'),
				'advisor_code' => strtoupper($this->input->post('advisor_code')) ?: null,
			));
		}

		$this->session->set_flashdata('action', 'Details updated');
	}


	/**
	 * Record an additional training session taken by staff.
	 * A full training record is kept, even though only the last one is of interest (in case they change their mind)
	 *
	 * @param sps_id the id of the staff member taking the training
	 * @param data the rest of the date of the training, usually from post input
	 * @author GM
	 **/
	function add_staff_training($sps_id, $data)
	{
		$sql = 'INSERT INTO
					sp_staff_training
				SET
					spst_sps_id = "' . (int)$sps_id . '",
					spst_date = ?,
					spst_title = ?;';

		$this->db->query($sql, array($this->input->post('spst_date'),
									 $this->input->post('spst_title')
									 ));

		$this->session->set_flashdata('action', 'Training record updated');

		return $this->db->insert_id();
	}


	function check_email_unique($email, $sps_id)
	{
		if($sps_id)
		{
			$sql = 'SELECT
						email
					FROM
						service_provider_staff
					WHERE
						id = "' . (int)$sps_id . '"
						/* AND active = 1; */ ';

			if($row = $this->db->query($sql)->row_array())
			{
				$sql = 'SELECT
							id
						FROM
							service_provider_staff
						WHERE
							email != ?
							AND email = ?
							/* AND active = 1; */ ';

				return ($this->db->query($sql, array($row['email'], $email))->row_array() ? FALSE : TRUE);
			}
		}
		else
		{
			$sql = 'SELECT
						id
					FROM
						service_provider_staff
					WHERE
						email = ?
						/* AND active = 1; */';

			return ($this->db->query($sql, $email)->row_array() ? FALSE : TRUE);
		}

		return FALSE;
	}


	function check_password_current($password, $sps_id)
	{
		$sql = 'SELECT
					id
				FROM
					service_provider_staff
				WHERE
					id = "' . (int)$sps_id . '"
					AND password = ?
					AND active = 1;';

		$password = md5($this->config->config['salt'] . $password . $this->config->config['salt']);

		return ($this->db->query($sql, $password)->row_array() ? TRUE : FALSE);
	}


	function update_service_provider_staff_password($sps_id)
	{
		$sql = 'UPDATE
					service_provider_staff
				SET
					password = ?
				WHERE
					id = "' . (int)$sps_id . '";';

		$password = md5($this->config->config['salt'] . $this->input->post('new_password') . $this->config->config['salt']);

		$this->db->query($sql, $password);

		$this->session->set_flashdata('action', 'Password changed');
	}


	function get_groups()
	{
		$sql = 'SELECT
					COUNT(sp.id) AS total_service_providers,
					spg.*,
					IF(pct_name IS NULL, "All", pct_name) AS pct_name
				FROM
					service_provider_groups spg
				LEFT JOIN
					service_providers sp
					ON (sp.group_id = spg.id) ';

		if(@$_GET['pct_id'])
			$sql .= ' AND (sp.pct_id = spg.pct_id) ';

		$sql .= 'LEFT JOIN
					pcts
					ON spg.pct_id = pcts.id
				WHERE
					1 = 1 ';

		if(@$_GET['pct_id'])
			$sql .= ' AND spg.pct_id = ' . (int) $_GET['pct_id'] . ' ';

		$sql .= ' GROUP BY spg.id ';

		$result = $this->db->query($sql)->result_array();
		foreach ($result as &$row)
		{
			$claim_options = unserialize($row['claim_options']);
			// Set default values for new 4/12 week options as the same value as follow up quit
			if ( ! element('claim_4_week', $claim_options)) $claim_options['claim_4_week'] = $claim_options['follow_up_quit'];
			if ( ! element('claim_12_week', $claim_options)) $claim_options['claim_12_week'] = $claim_options['follow_up_quit'];
			$claim_options['do_not_claim'] = element('do_not_claim', $claim_options);
			$row['claim_options'] = $claim_options;
		}

		return $result;
	}


	function get_groups_select()
	{
		$sql = 'SELECT
					id,
					IF(lENGTH(name) > 15, CONCAT(SUBSTR(name, 1, 15), "..."), name) AS name
				FROM
					service_provider_groups
				WHERE
					1 = 1 ';

		if(@$_GET['pct_id'])
			$sql .= ' AND (pct_id IS NULL OR pct_id = ' . (int) $_GET['pct_id'] . ') ';

		$sql .= 'ORDER BY name ASC';

		return $this->db->query($sql)->result_array();
	}




	/**
	 * Get the service provider groups, prefixed with the relevant local authority name
	 */
	function get_pct_groups_select()
	{
		$sql = 'SELECT
					spg.id,
					IF(pct_name IS NULL, CONCAT("ALL: ", name), CONCAT(pct_name, ": ", name)) AS name
				FROM
					service_provider_groups spg
				LEFT JOIN
					pcts
					ON spg.pct_id = pcts.id
				WHERE
					1 = 1 ';

		if(@$_GET['pct_id'])
			$sql .= ' AND (pct_id IS NULL OR pct_id = ' . (int) $_GET['pct_id'] . ') ';

		$sql .= 'ORDER BY name ASC';

		return $this->db->query($sql)->result_array();
	}




	function get_group($group_id)
	{
		if($group_id)
		{
			$sql = 'SELECT
						*
					FROM
						service_provider_groups
					WHERE
						id = "' . (int)$group_id . '";';

			if ($group = $this->db->query($sql)->row_array())
			{
				$group['claim_options'] = unserialize($group['claim_options']);
				// Set default values for new 4/12 week options as the same value as follow up quit
				if ( ! element('claim_4_week', $group['claim_options'])) $group['claim_options']['claim_4_week'] = $group['claim_options']['follow_up_quit'];
				if ( ! element('claim_12_week', $group['claim_options'])) $group['claim_options']['claim_12_week'] = $group['claim_options']['follow_up_quit'];
				$group['claim_options']['do_not_claim'] = element('do_not_claim', $group['claim_options']);

				return $group;
			}
		}

		return false;
	}


	function set_group($group_id = 0)
	{
		if($group_id)
		{
			$sql = 'UPDATE
						service_provider_groups
					SET
						name = ?,
						pct_id = ?,
						claim_options = ?
					WHERE
						id = "' . (int)$group_id . '";';

			$this->session->set_flashdata('action', 'Group updated');
		}
		else
		{
			$sql = 'INSERT INTO
						service_provider_groups
					SET
						name = ?,
						pct_id = ?,
						claim_options = ?;';

			$this->session->set_flashdata('action', 'Group added');
		}

		$claim_options = array(
			'initial' => $this->input->post('initial'),
			'claim_4_week' => $this->input->post('claim_4_week'),
			'claim_12_week' => $this->input->post('claim_12_week'),
			'do_not_claim' => (int) $this->input->post('do_not_claim'),
		);

		$this->db->query($sql, array(
			$this->input->post('name'),
			($this->input->post('pct_id') ? $this->input->post('pct_id') : NULL),
			serialize($claim_options),
		));
	}


	function delete_group($group_id)
	{
		$sql = 'UPDATE
					service_providers
				SET
					group_id = NULL
				WHERE
					group_id = "' . (int)$group_id . '";';

		$this->db->query($sql);

		$sql = 'DELETE FROM
					service_provider_groups
				WHERE
					id = "' . (int)$group_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Group deleted');
	}


	function get_service_providers_select_group($group_id)
	{
		$sql = 'SELECT
					id,
					group_id,
					name
				FROM
					service_providers
				WHERE
					id = id
				';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $this->input->get('pct_id') . ' ';

		$sql .= ' ORDER BY name ASC';

		$service_providers = $this->db->query($sql)->result_array();

		$service_providers_select = array('unassigned', 'assigned');

		foreach($service_providers as $sp)
		{
			$char = strtoupper(substr($sp['name'], 0, 1));

			if($sp['group_id'] != $group_id)
			{
				$service_providers_select['unassigned'][$char][] = $sp;
			}
			else
			{
				$service_providers_select['assigned'][$char][] = $sp;
			}
		}

		return $service_providers_select;
	}


	function assign_to_group($sp_id, $group_id)
	{
		$sql = 'UPDATE
					service_providers
				SET
					group_id = ' . ($group_id ? '"' . (int)$group_id . '"' : 'NULL') . '
				WHERE
					id = "' .(int)$sp_id . '";';

		$this->db->query($sql);
	}




	public function simple_update($sp_id = 0, $data = array())
	{
		$sql = $this->db->update_string('service_providers', $data, 'id = ' . (int) $sp_id);
		return $this->db->query($sql);
	}




	public function geo_search($lat = 0, $lng = 0)
	{
		$sql = 'SELECT
					id,
					name,
					post_code,
					IF(location = "", "N/A", IF(location = "Other" AND location_other IS NOT NULL, CONCAT("Other", " (", location_other, ")"), location)) AS location,
					department,
					venue,
					telephone,
					pc_lat,
					pc_lng,
					( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( pc_lat ) ) * cos( radians( pc_lng ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians( pc_lat ) ) ) ) AS distance
				FROM service_providers
				LEFT JOIN postcodes ON post_code = pc_postcode
				WHERE 1 = 1
				AND active = 1
				HAVING distance < 5
				ORDER BY distance ASC
				/* LIMIT 0, 50 */';

		return $this->db->query($sql)->result_array();
	}

}
