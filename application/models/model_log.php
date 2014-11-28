<?php

class Model_log extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function set_log($description)
	{
		$sql = 'INSERT INTO
					log
				SET
					datetime_log = NOW(),
					service_provider_staff_id = ' . (int)$this->session->userdata('sps_id') . ',
					admin_id = ' . (int)$this->session->userdata('admin_id') . ',
					description = ?;';

		$this->db->query($sql, $description);
	}


	function get_total_log($sp_id = 0)
	{
		$sql = 'SELECT
					COUNT(l.id) AS total
				FROM
					log l,
					service_provider_staff sps,
					service_providers sp
				WHERE
					sps.id = l.service_provider_staff_id
					AND sps.service_provider_id = sp.id
					AND DATE(l.datetime_log) >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					AND DATE(l.datetime_log) <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if($sp_id)
			$sql .= ' AND sps.service_provider_id = "' . (int)$sp_id . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_log($start = 0, $limit = 0, $sp_id = 0)
	{
		if( ! in_array(@$_GET['order'], array('datetime_log', 'sp.name', 'sps.sname')) ) $_GET['order'] = 'datetime_log';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
					l.id,
					DATE_FORMAT(l.datetime_log, "%D %b %Y %l:%i%p") AS datetime_log_format,
					l.description,
					IF(sp.active, sp.name, CONCAT(sp.name, " <span style=\"color:#f00;\">(inactive)</span>")) AS sp_name,
					CONCAT(sps.fname, " ", sps.sname) AS sps_name
				FROM
					log l,
					service_provider_staff sps,
					service_providers sp
				WHERE
					sps.id = l.service_provider_staff_id
					AND sp.id = sps.service_provider_id
					AND DATE(l.datetime_log) >= ' . $this->db->escape(parse_date($_GET['date_from'])) . '
					AND DATE(l.datetime_log) <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if($sp_id)
			$sql .= ' AND sps.service_provider_id = "' . (int)$sp_id . '" ';

		if(@$_GET['pct_id'])
			$sql .= ' AND pct_id = ' . (int) $_GET['pct_id'] . ' ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}


}
