<?php

class Advisors_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	public function get_total_advisors()
	{
		$sql = 'SELECT COUNT(a_id) AS total
				FROM advisors
				WHERE 1 = 1 ';

		if ($this->input->get('a_fname'))
			$sql .= ' AND a_fname LIKE "%' . $this->db->escape_like_str($this->input->get('a_fname')) . '%" ';

		if ($this->input->get('a_sname'))
			$sql .= ' AND a_sname LIKE "%' . $this->db->escape_like_str($this->input->get('a_sname')) . '%" ';

		if ($this->input->get('a_number'))
			$sql .= ' AND a_number LIKE "%' . $this->db->escape_like_str($this->input->get('a_number')) . '%" ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}




	public function get_all_advisors($start = 0, $limit = 20)
	{
		if ( ! in_array(@$_GET['order'], array('a_fname', 'a_sname', 'a_number')) ) $_GET['order'] = 'a_sname';

		if ( @$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'asc';

		$sql = 'SELECT *
				FROM advisors
				WHERE 1 = 1';

		if ($this->input->get('a_fname'))
			$sql .= ' AND a_fname LIKE "%' . $this->db->escape_like_str($this->input->get('a_fname')) . '%" ';

		if ($this->input->get('a_sname'))
			$sql .= ' AND a_sname LIKE "%' . $this->db->escape_like_str($this->input->get('a_sname')) . '%" ';

		if ($this->input->get('a_number'))
			$sql .= ' AND a_number LIKE "%' . $this->db->escape_like_str($this->input->get('a_number')) . '%" ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if ($limit)
			$sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;

		return $this->db->query($sql)->result_array();
	}




	public function get_advisor($a_id = 0)
	{
		$sql = 'SELECT * FROM advisors WHERE a_id = ? LIMIT 1';
		return $this->db->query($sql, array($a_id))->row_array();
	}




	public function insert($data = array())
	{
		$sql = $this->db->insert_string('advisors', $data);
		return ($this->db->query($sql)) ? $this->db->insert_id() : FALSE;
	}




	public function update($a_id = 0, $data = array())
	{
		$sql = $this->db->update_string('advisors', $data, 'a_id = ' . (int) $a_id);
		return $this->db->query($sql);
	}




	public function delete($a_id = 0)
	{
		$sql = 'DELETE FROM advisors WHERE a_id = ? LIMIT 1';
		return $this->db->query($sql, array($a_id));
	}




	/**
	 * Search for a provider on their number or name using the query term provided
	 *
	 * @param string $q		Search query. Can be partial.
	 * @return array		Result array
	 */
	public function get_advisors_ajax()
	{
		$q = trim($this->input->get('term'));
		$parts = explode(' ', $q);
		$where = '';

		if (strpos(' ', $q) !== 0 && count($parts) === 2)
		{
			$first = $parts[0];
			$last = $parts[1];
			$where .= ' (a_fname LIKE "%' . $this->db->escape_like_str($first) . '%"
						AND a_sname LIKE "%' . $this->db->escape_like_str($last) . '%") ';
		}
		else
		{
			$where .= ' (a_fname LIKE "%' . $this->db->escape_like_str($q) . '%"
						OR a_sname LIKE "%' . $this->db->escape_like_str($q) . '%"
						OR a_number LIKE "%' . $this->db->escape_like_str($q) . '%") ';
		}

		$sql = 'SELECT
					advisors.*,
					CONCAT(a_fname, " ", a_sname, " (", a_number, ")") AS advisor,
					a_number AS code
				FROM
					advisors
				WHERE
					1 = 1
				AND ' . $where . '
				ORDER BY
					a_fname ASC
				LIMIT 25';

		return $this->db->query($sql)->result_array();
	}


	public function get_advisors_filter()
	{
		$sql = 'SELECT
					a_id,
					CONCAT(a_fname, " ", a_sname, " (", a_number, ")") AS advisor,
					a_number AS advisor_code
				FROM
					advisors
				WHERE
					a_number != ""
				ORDER BY
					a_fname ASC';
		$advisors_table = $this->db->query($sql)->result_array();

		$sql = 'SELECT
					id as sps_id,
					service_provider_id as sp_id,
					CONCAT(fname, " ", sname, " (", advisor_code, ")") AS advisor,
					advisor_code
				FROM
					service_provider_staff
				WHERE
					advisor_code != ""
				ORDER BY
					fname ASC';
		$staff_table = $this->db->query($sql)->result_array();

		$return = array_merge($advisors_table, $staff_table);

		function cust_sort($a, $b)
		{
			return strtolower($a['advisor']) > strtolower($b['advisor']);
		}

		usort($return, 'cust_sort');

		return $return;
	}
}

/* End of file: ./application/models/advisors_model.php */
