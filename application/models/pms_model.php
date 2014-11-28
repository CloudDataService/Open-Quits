<?php

class Pms_model extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}




	function get_all_staff()
	{
		$sql = 'SELECT
					*,
					IF(ISNULL(pmss_datetime_last_login), "N/A", DATE_FORMAT(pmss_datetime_last_login, "%D %b %Y %l:%i%p")) AS pmss_datetime_last_login_format
				FROM pms_staff';

		return $this->db->query($sql)->result_array();
	}




	function get_staff($pmss_id = 0)
	{
		$sql = 'SELECT
					*,
					IF(ISNULL(pmss_datetime_last_login), "N/A", DATE_FORMAT(pmss_datetime_last_login, "%D %b %Y %l:%i%p")) AS pmss_datetime_last_login_format
				FROM pms_staff
				WHERE pmss_id = ?
				LIMIT 1';

		return $this->db->query($sql, array($pmss_id))->row_array();
	}




	function check_email_unique($email, $pmss_id)
	{
		if ($pmss_id)
		{
			$sql = 'SELECT pmss_email FROM pms_staff WHERE pmss_id = ?';
			if ($row = $this->db->query($sql, array($pmss_id))->row_array())
			{
				$sql = 'SELECT pmss_id FROM pms_staff WHERE pmss_email != ? AND pmss_email = ?';
				return ($this->db->query($sql, array($row['pmss_email'], $email))->row_array() ? FALSE : TRUE);
			}
		}
		else
		{
			$sql = 'SELECT pmss_id FROM pms_staff WHERE pmss_email = ?';
			return ($this->db->query($sql, $email)->row_array() ? FALSE : TRUE);
		}

		return FALSE;
	}




	function check_password_current($password = '', $pmss_id = 0)
	{
		$sql = 'SELECT pmss_id
				FROM pms_staff
				WHERE pmss_id = ?
				AND pmss_password = ?
				LIMIT 1';

		$password = md5($this->config->config['salt'] . $password . $this->config->config['salt']);

		return ($this->db->query($sql, $pmss_id, $password)->row_array() ? TRUE : FALSE);
	}




	public function insert_staff($data = array())
	{
		if (isset($data['pmss_password']))
			$data['pmss_password'] = md5($this->config->config['salt'] . $data['pmss_password'] . $this->config->config['salt']);

		$sql = $this->db->insert_string('pms_staff', $data);
		return ($this->db->query($sql)) ? $this->db->insert_id() : FALSE;
	}




	function update_staff($pmss_id = 0, $data = array())
	{
		if (isset($data['pmss_password']))
			$data['pmss_password'] = md5($this->config->config['salt'] . $data['pmss_password'] . $this->config->config['salt']);

		$sql = $this->db->update_string('pms_staff', $data, 'pmss_id = ' . (int) $pmss_id);
		return $this->db->query($sql);
	}




	function set_staff_password($pmss_id = 0, $password = '')
	{
		$password = md5($this->config->config['salt'] . $password . $this->config->config['salt']);
		$sql = 'UPDATE pms_staff SET pmss_password = ? WHERE pmss_id = ? LIMIT 1';
		return $this->db->query($sql, array($pmss_id, $password));
	}




	public function delete_staff($pmss_id = 0)
	{
		$sql = 'DELETE FROM pms_staff WHERE pmss_id = ? LIMIT 1';
		return $this->db->query($sql, array($pmss_id));
	}




}
