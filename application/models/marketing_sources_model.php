<?php

class Marketing_sources_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Get a single Marketing Source entry
	 *
	 * @param int  $ms_id		ID of marketing source
	 * @return array		DB row array
	 * @author CR
	 */
	public function get($ms_id = 0)
	{
		$sql = 'SELECT * FROM marketing_sources WHERE ms_id = ? LIMIT 1';
		return $this->db->query($sql, array($ms_id))->row_array();
	}




	/**
	 * Get list of active marketing sources
	 *
	 * @return array		DB result array
	 * @author CR
	 */
	public function get_active()
	{
		$sql = 'SELECT * FROM marketing_sources WHERE ms_active = 1 ORDER BY ms_title ASC';
		return $this->db->query($sql)->result_array();
	}




	/**
	 * Get ALL the marketing sources! (not just active ones)
	 *
	 * @return array		DB result array
	 * @author CR
	 */
	public function get_all()
	{
		$sql = 'SELECT * FROM marketing_sources ORDER BY ms_title ASC';
		return $this->db->query($sql)->result_array();
	}




	/**
	 * Update a marketing source
	 *
	 * @param int $ms_id		ID of marketing source
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 * @author CR
	 */
	public function update($ms_id = 0, $data = array())
	{
		$data['ms_updated'] = date('Y-m-d H:i:s');

		$sql = $this->db->update_string('marketing_sources', $data, 'ms_id = ' . (int) $ms_id);
		$this->db->query($sql);

		return ($query ? (int) $ms_id : $query);
	}




	/**
	 * Add a new marketing source
	 *
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 * @author CR
	 */
	public function insert($data = array())
	{
		$data['ms_created'] = date('Y-m-d H:i:s');

		$sql = $this->db->insert_string('marketing_sources', $data);
		$query = $this->db->query($sql);
		return ($query ? $this->db->insert_id() : $query);
	}




	/**
	 * Remove a marketing source
	 */
	public function delete($ms_id = 0)
	{
		$sql = 'DELETE FROM marketing_sources WHERE ms_id = ? LIMIT 1';
		return $this->db->query($sql, array($ms_id));
	}





}

/* End of file: ./application/models/marketing_sources_model.php */