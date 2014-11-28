<?php

class Mail_merge_fields_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}



	public function get_all($sp_id = 0, $type = NULL)
	{
		$sql_where = '';
		if ($type !== NULL)
		{
			$sql_where .= ' AND mmf_type = ' . $this->db->escape($type) . ' ';
		}

		$sql = 'SELECT
					*
				FROM
					mail_merge_fields
				WHERE
					(mmf_sp_id = ' . (int) $sp_id . '
					AND mmf_type = "custom")
				OR
					(mmf_sp_id IS NULL
					AND mmf_type != "custom")
				' . $sql_where . '
				ORDER BY mmf_name ASC';

		return $this->db->query($sql)->result_array();
	}



	/**
	 * Get a single custom mail merge field
	 */
	public function get($mmf_id)
	{
		$sql = 'SELECT * FROM mail_merge_fields WHERE mmf_id = ? AND mmf_sp_id = ? LIMIT 1 ';
		$query = $this->db->query($sql, array($mmf_id, $this->session->userdata('sp_id')));
		return $query->row_array();
	}




	public function update($mmf_id, $data = array())
	{
		$sp_id = $this->session->userdata('sp_id');
		$sql_where = 'mmf_id = ' . (int) $mmf_id . ' AND mmf_sp_id = ' . (int) $sp_id;

		$data['mmf_sp_id'] = $sp_id;

		$sql = $this->db->update_string('mail_merge_fields', $data, $sql_where);
		return $this->db->query($sql);
	}




	public function insert($data = array())
	{
		$data['mmf_sp_id'] = $this->session->userdata('sp_id');

		$sql = $this->db->insert_string('mail_merge_fields', $data);
		$query = $this->db->query($sql);
		return ($query) ? $this->db->insert_id() : $query;
	}




	public function delete($mmf_id)
	{
		$sql = 'DELETE FROM mail_merge_fields WHERE mmf_id = ? AND mmf_sp_id = ?';
		return $this->db->query($sql, array($mmf_id, $this->session->userdata('sp_id')));
	}


}

/* End of file: ./application/models/mail_merge_fields_model.php */
