<?php

class Model_cron extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_sms_interval($interval)
	{
		$sql = 'SELECT
					c.fname,
					c.tel_mobile
				FROM
					clients c,
					monitoring_forms mf
				LEFT JOIN
					service_providers sp
					ON mf.service_provider_id = sp.id
				WHERE
					mf.id = c.monitoring_form_id
					AND c.sms = 1
					AND mf.date_created = DATE_SUB(CURDATE(), INTERVAL ' . $interval . ')
				';

		if ($this->input->get('pct_id'))
			$sql .= ' AND sp.pct_id = ' . (int) $this->input->get('pct_id');

		return $this->db->query($sql)->result_array();
	}


	function get_sms_follow_up_reminder($follow_up = 4)
	{
		switch ($follow_up)
		{
			case 4: $date_field = 'date_of_4_week_follow_up'; break;
			case 12: $date_field = 'date_of_12_week_follow_up'; break;
		}

		$sql = 'SELECT
					c.fname,
					c.tel_mobile
				FROM
					clients c,
					monitoring_forms mf
				LEFT JOIN
					service_providers sp
					ON mf.service_provider_id = sp.id
				WHERE
					mf.id = c.monitoring_form_id
					AND c.sms = 1
					AND mf.' . $date_field . ' = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
				';

		if ($this->input->get('pct_id'))
			$sql .= ' AND sp.pct_id = ' . (int) $this->input->get('pct_id');

		return $this->db->query($sql)->result_array();
	}


	function get_repeat_clients($repeat_clients_last_execute)
	{
		$sql = 'SELECT
					c.fname,
					c.sname,
					DATE_FORMAT(c.date_of_birth, "%d/%m/%Y") AS date_of_birth_format,
					COUNT(c.date_of_birth) AS total_engagements
				FROM
					monitoring_forms mf,
					clients c
				WHERE
					c.monitoring_form_id = mf.id
					AND	mf.date_created >= ' . $this->db->escape($repeat_clients_last_execute) . '
				GROUP BY
					c.fname, c.sname, c.date_of_birth
				HAVING total_engagements >= 2;';

		return $this->db->query($sql)->result_array();
	}


}
