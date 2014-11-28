<?php

class Communications_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Update a communication entry
	 *
	 * @param int $c_id		ID of communication entry to update
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 */
	public function update($c_id = 0, $data = array())
	{
		$data['c_updated'] = time();

		$sql = $this->db->update_string('communications', $data, 'c_id = ' . (int) $c_id);
		$this->db->query($sql);

		return ($query ? (int) $c_id : $query);
	}




	/**
	 * Add a new communication
	 *
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 */
	public function insert($data = array())
	{
		$data['c_created'] = time();

		$sql = $this->db->insert_string('communications', $data);
		$query = $this->db->query($sql);
		return ($query ? $this->db->insert_id() : $query);
	}





}

/* End of file: ./application/models/communications_model.php */