<?php

class Model_pcts extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_pcts()
	{
		$sql = 'SELECT
					p.id,
					p.pct_name,
					COUNT(sp.id) AS total_service_providers
				FROM
					pcts p
				LEFT JOIN
					service_providers sp
						ON sp.active = 1 AND sp.pct = p.pct_name
				GROUP BY
					p.id
				ORDER BY
					p.pct_name ASC';

		return $this->db->query($sql)->result_array();
	}


	function get_pct($pct_id)
	{
		$sql = 'SELECT
					*
				FROM
					pcts
				WHERE
					id = "' . (int)$pct_id . '";';

		return $this->db->query($sql)->row_array();
	}


	function get_pcts_select()
	{
		$sql = 'SELECT
					id,
					pct_name,
					IF(lENGTH(pct_name) > 10, CONCAT(SUBSTR(pct_name, 1, 10), "..."), pct_name) AS pct_name_truncated
				FROM
					pcts
				ORDER BY
					pct_name ASC';

		return $this->db->query($sql)->result_array();
	}


	function set_pct($pct)
	{
		if($pct)
		{
			# change service provider pct to new pct name
			$sql = 'UPDATE
						service_providers
					SET
						pct = ?
					WHERE
						pct = ? ';

			# commit change
			$this->db->query($sql, array($this->input->post('pct_name'), $pct['pct_name']));

			# udpate pct name
			$sql = 'UPDATE
						pcts
					SET
						pct_name = ?
					WHERE
						id = "' . (int)$pct['id'] . '";';

			$this->session->set_flashdata('action', 'PCT updated');
		}
		else
		{
			# add new pct
			$sql = 'INSERT INTO
						pcts
					SET
						pct_name = ?';

			$this->session->set_flashdata('action', 'PCT added');
		}

		$this->db->query($sql, $this->input->post('pct_name'));
	}


	function delete_pct($pct)
	{
		$sql = 'UPDATE
					service_providers
				SET
					pct = ""
				WHERE
					pct = ' . $this->db->escape($pct['pct_name']) . ';';

		if($this->db->query($sql))
		{
			$sql = 'DELETE FROM
						pcts
					WHERE
						id = "' . (int)$pct['id'] . '";';

			if($this->db->query($sql))
			{
				$this->session->set_flashdata('action', 'PCT deleted');

				return TRUE;
			}
		}

		return FALSE;
	}


	function get_service_providers_select($pct_name)
	{
		$sql = 'SELECT
					id,
					name,
					pct
				FROM
					service_providers
				WHERE
					active = 1
				ORDER BY
					name ASC';

		$service_providers = $this->db->query($sql)->result_array();

		$service_providers_select = array('unassigned', 'assigned');

		foreach($service_providers as $sp)
		{
			$char = strtoupper(substr($sp['name'], 0, 1));

			if($sp['pct'] != $pct_name)
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


	function assign_to_pct($sp_id, $pct_name)
	{
		$sql = 'UPDATE
					service_providers
				SET
					pct = ' . $this->db->escape($pct_name) . '
				WHERE
					id = "' . (int)$sp_id . '";';

		if($this->db->query($sql))
			return TRUE;

		return FALSE;
	}


}
