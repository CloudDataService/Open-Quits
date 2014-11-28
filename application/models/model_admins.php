<?php

class Model_admins extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_admins()
	{
		$sql = 'SELECT
					admins.*,
					IF(ISNULL(datetime_last_login), "N/A", DATE_FORMAT(datetime_last_login, "%D %b %Y %l:%i%p")) AS datetime_last_login_format,
					pct_name
				FROM
					admins
				LEFT JOIN
					pcts
					ON pcts.id = admins.pct_id
				WHERE
					master = 0;';

		return $this->db->query($sql)->result_array();
	}

	function get_admin($admin_id)
	{
		if($admin_id)
		{
			$sql = 'SELECT
						admins.id AS id,
						fname,
						sname,
						email,
						master,
						pct_id,
						pct_name
					FROM
						admins
					LEFT JOIN
						pcts
						ON pcts.id = admins.pct_id
					WHERE
						admins.id = "' . (int)$admin_id . '";';

			return $this->db->query($sql)->row_array();
		}

		return FALSE;
	}


	function check_email_unique($email, $admin_id)
	{
		if($admin_id)
		{
			$sql = 'SELECT
						email
					FROM
						admins
					WHERE
						id = "' . (int)$admin_id . '";';

			if($row = $this->db->query($sql)->row_array())
			{
				$sql = 'SELECT
							id
						FROM
							admins
						WHERE
							email != ?
							AND email = ?';

				return ($this->db->query($sql, array($row['email'], $email))->row_array() ? FALSE : TRUE);
			}
		}
		else
		{
			$sql = 'SELECT
						id
					FROM
						admins
					WHERE
						email = ?;';

			return ($this->db->query($sql, $email)->row_array() ? FALSE : TRUE);
		}

		return FALSE;
	}


	function check_password_current($password, $admin_id)
	{
		$sql = 'SELECT
					id
				FROM
					admins
				WHERE
					id = "' . (int)$admin_id . '"
					AND password = ?;';

		$password = md5($this->config->config['salt'] . $password . $this->config->config['salt']);

		return ($this->db->query($sql, $password)->row_array() ? TRUE : FALSE);
	}


	function update_admin_details($sps_id)
	{
		$sql = 'UPDATE
					admins
				SET
					email = ?,
					fname = ?,
					sname = ?
				WHERE
					id = "' . (int)$sps_id . '";';

		$this->db->query($sql, array($this->input->post('email'),
									 $this->input->post('fname'),
									 $this->input->post('sname'))
						 );

		$this->session->set_userdata(array('email' => $this->input->post('email'),
									       'fname' => $this->input->post('fname'),
									       'sname' => $this->input->post('sname')));

		$this->session->set_flashdata('action', 'Details updated');
	}


	function update_admin_password($sps_id)
	{
		$sql = 'UPDATE
					admins
				SET
					password = ?
				WHERE
					id = "' . (int)$sps_id . '";';

		$password = md5($this->config->config['salt'] . $this->input->post('new_password') . $this->config->config['salt']);

		$this->db->query($sql, $password);

		$this->session->set_flashdata('action', 'Password changed');
	}


	function set_admin($admin_id = 0)
	{
		$data = array(
			'fname' => $this->input->post('fname'),
			'sname' => $this->input->post('sname'),
			'email' => $this->input->post('email'),
			'pct_id' => ($this->input->post('pct_id') ? (int) $this->input->post('pct_id') : NULL),
		);

		if ($this->input->post('password') !== '')
		{
			$data['password'] = md5($this->config->config['salt'] . $this->input->post('password') . $this->config->config['salt']);
		}

		if($admin_id)
		{
			$sql = $this->db->update_string('admins', $data, 'id = ' . (int) $admin_id);
			$this->session->set_flashdata('action', 'Administrator updated');
		}
		else
		{
			$sql = $this->db->insert_string('admins', $data);
			$this->session->set_flashdata('action', 'Administrator added');
		}

		$query = $this->db->query($sql);
		return ($admin_id && $query ? $admin_id : $this->db->insert_id());
	}


	function set_master_admin($admin_id)
	{
		$sql = 'UPDATE
					admins
				SET
					master = 1
				WHERE
					id = "' . (int)$admin_id . '";';

		$this->db->query($sql);

		$sql = 'UPDATE
					admins
				SET
					master = 0
				WHERE
					id = ' . $this->session->userdata('admin_id') . ';';

		$this->db->query($sql);
	}


	function delete_admin($admin_id)
	{
		$sql = 'DELETE FROM
					admins
				WHERE
					id = "' . (int)$admin_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Staff member deleted');
	}

}
