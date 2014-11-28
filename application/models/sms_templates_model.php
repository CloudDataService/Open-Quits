<?php

class Sms_templates_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Get a single SMS template by ID
	 *
	 * @param int $sms_t_id		ID of template to get
	 */
	public function get($sms_t_id = 0)
	{
		$sql = 'SELECT * FROM sms_templates WHERE sms_t_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($sms_t_id));
		return $query->row_array();
	}




	/**
	 * Get all SMS templates
	 */
	public function get_all()
	{
		$sql = 'SELECT * FROM sms_templates ORDER BY sms_t_title ASC';
		return $this->db->query($sql)->result_array();
	}




	/**
	 * Update an SMS template by its ID
	 *
	 * @param int $sms_t_id		ID of template to update
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 */
	public function update($sms_t_id = 0, $data = array())
	{
		$data['sms_t_updated'] = time();

		$sql = $this->db->update_string('sms_templates', $data, 'sms_t_id = ' . (int) $sms_t_id);
		$this->db->query($sql);

		return ($query ? (int) $sms_t_id : $query);
	}




	/**
	 * Add a new SMS templtae
	 *
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 */
	public function insert($data = array())
	{
		$data['sms_t_created'] = time();

		$sql = $this->db->insert_string('sms_templates', $data);
		$query = $this->db->query($sql);
		return ($query ? $this->db->insert_id() : $query);
	}




	/**
	 * Remove an SMS template
	 */
	public function delete($sms_t_id = 0)
	{
		$sql = 'DELETE FROM sms_templates WHERE sms_t_id = ? LIMIT 1';
		return $this->db->query($sql, array($sms_t_id));
	}




}

/* End of file: ./application/models/sms_templates_model.php */