<?php

class Model_auth extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function login_service_provider_staff()
	{
		$sql = 'SELECT
					sps.id AS sps_id,
					sps.service_provider_id AS sp_id,
					sps.datetime_last_login,
					sps.email,
					sps.password,
					sps.fname,
					sps.sname,
					sps.advisor_code,
					sps.tickets,
					sps.datetime_tc_agree,
					IF(sp.master_id = sps.id, true, false) AS master,
					sp.name AS sp_name,
					sp.tier_3,
					sp.pct_id AS pct_id
				FROM
					service_provider_staff sps,
					service_providers sp
				WHERE
					sps.email = ?
					AND sps.password = ?
					AND sp.id = sps.service_provider_id
					AND sp.active = 1
					AND sps.active = 1';

		$password = md5($this->config->config['salt'] . $this->input->post('password') . $this->config->config['salt']);

		$sps = $this->db->query($sql, array($this->input->post('email'),
											$password)
								)->row_array();

		if($sps && $sps['password'] == $password)
		{
			$default_password = substr(md5($this->config->config['salt'] . 'foo' . $sps['sp_id'] . 'bar' . $this->config->config['salt']), 0, 8);

			if($this->input->post('password') == $default_password)
			{
				/* set notification to change default password */
				$this->session->set_userdata('change_password', TRUE);
			}

			unset($sps['password']);

			return $sps;
		}

		return false;
	}


	function update_sps_last_login($sps_id)
	{
		$sql = 'UPDATE
					service_provider_staff
				SET
					datetime_last_login = NOW(),
					ip = ?
				WHERE
					id = "' . (int)$sps_id . '";';

		$this->db->query($sql, getenv('REMOTE_ADDR'));
	}


	function login_admin()
	{
		$sql = 'SELECT
					admins.id AS admin_id,
					email,
					password,
					fname,
					sname,
					master,
					"Administrator" AS sp_name,
					pcts.id AS pct_id,
					pct_name
				FROM
					admins
				LEFT JOIN
					pcts
					ON admins.pct_id = pcts.id
				WHERE
					email = ?
					AND password = ?';

		$password = md5($this->config->config['salt'] . $this->input->post('password') . $this->config->config['salt']);

		$admin = $this->db->query($sql, array($this->input->post('email'),
											$password)
								)->row_array();

		if($admin && $admin['password'] == $password)
		{
			unset($admin['password']);

			$sql = 'UPDATE
						admins
					SET
						datetime_last_login = NOW(),
						ip = ?
					WHERE
						id = "' . (int)$admin['admin_id'] . '";';

			$this->db->query($sql, getenv('REMOTE_ADDR'));

			return $admin;
		}

		return false;
	}




	/**
	 * Handle login for PMS staff accounts
	 */
	function login_pmss()
	{
		$email = $this->input->post('email');
		$password = md5($this->config->config['salt'] . $this->input->post('password') . $this->config->config['salt']);
		$sql = 'SELECT * FROM pms_staff WHERE pmss_email = ? AND pmss_password = ? LIMIT 1';

		$pmss = $this->db->query($sql, array($email, $password))->row_array();

		if ($pmss && $pmss['pmss_password'] === $password)
		{
			unset($pmss['pmss_password']);

			$sql = 'UPDATE pms_staff
					SET pmss_datetime_last_login = NOW(), pmss_ip = ?
					WHERE pmss_id = ?
					LIMIT 1';

			$this->db->query($sql, array($this->input->ip_address(), $pmss['pmss_id']));

			return $pmss;
		}

		return FALSE;
	}




	function check_for_ban($ip)
	{
		$sql = 'SELECT
					ip,
					TIME_FORMAT(datetime_set, "%h:%i%p") AS datetime_set_format
				FROM
					logins
				WHERE
					ip = ?
					AND attempts = 3
					AND NOW() < DATE_ADD(datetime_set, INTERVAL 10 MINUTE);';

		return $this->db->query($sql, $ip)->row_array();
	}


	function set_failed_login($ip)
	{
		# get number of failed login attempts for ip
		$sql = 'SELECT
					attempts
				FROM
					logins
				WHERE
					ip = ?';

		# if failed login attempts exist, must update row
		if($row = $this->db->query($sql, $ip)->row_array())
		{
			# if attempts = 3, we know this ip has been banned before, but 10 mins have passed
			# because it wasn't picked up before by check_for_ban(), in that case set the
			# number of attempts to 1, or if it doesn't = 3, set it to incrememnt as normal
			$attempts = ($row['attempts'] == 3 ? 1 : $row['attempts'] + 1);

			$sql = 'UPDATE
						logins
					SET
						attempts = "' . (int)$attempts . '",
						datetime_set = NOW()
					WHERE
						ip = ?';

			$this->db->query($sql, $ip);

			# if a ban has just been set, then redirect to start to invoke check_for_ban()
			if($attempts == 3)
				redirect('/');
		}
		else
		{
			# else we insert a new row
			$sql = 'INSERT INTO
						logins
					SET
						ip = ?,
						attempts = 1,
						datetime_set = NOW();';

			$this->db->query($sql, $ip);
		}
	}


	function clear_failed_logins($ip)
	{
		$sql = 'DELETE FROM
					logins
				WHERE
					ip = ?';

		$this->db->query($sql, $ip);
	}


	function get_blocked_ips()
	{
		$sql = 'SELECT
					ip,
					TIME_FORMAT(datetime_set, "%h:%i%p") AS datetime_set_format
				FROM
					logins
				WHERE
					attempts = 3
					AND NOW() < DATE_ADD(datetime_set, INTERVAL 10 MINUTE);';

		return $this->db->query($sql)->result_array();
	}


	function unblock_ip($ip)
	{
		$sql = 'DELETE FROM
					logins
				WHERE
					ip = ?';

		$this->db->query($sql, $ip);

		$this->session->set_flashdata('action', 'IP unblocked');
	}


	function set_datetime_tc_agree($sps_id)
	{
		$sql = 'UPDATE
					service_provider_staff
				SET
					datetime_tc_agree = NOW()
				WHERE
					id = "' . (int)$sps_id . '";';

		$this->db->query($sql);
	}


}
