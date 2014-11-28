<?php

class Sms_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Get all SMS by the communication ID
	 */
	public function get_by_communication($c_id = 0)
	{
		$sql = 'SELECT *
				FROM sms
				WHERE s_c_id = ?
				ORDER BY sms_created ASC';

		return $this->db->query($sql, array($c_id))->result_array();
	}




	/**
	 * Get all SMS by the communication ID
	 */
	public function get_by_mf($mf_id = 0)
	{
		$sql = 'SELECT sms.*,
					CONCAT(admins.fname, " ", admins.sname) AS admin_name,
					CONCAT(service_provider_staff.fname, " ", service_provider_staff.sname) AS sps_name
				FROM sms
				LEFT JOIN
					admins ON s_a_id = admins.id
				LEFT JOIN
					service_provider_staff ON s_sps_id = service_provider_staff.id
				WHERE s_mf_id = ?
				ORDER BY s_created ASC';

		return $this->db->query($sql, array($mf_id))->result_array();
	}




	/**
	 * Get a number of unsent messages, for use in processing the queue
	 *
	 * @param int $limit		Number of messages to get
	 * @return array		DB result array of messages
	 */
	public function get_unsent($limit = 5)
	{
		$limit = (int) $limit;

		$sql = "SELECT *
				FROM sms
				WHERE s_status IS NULL
				ORDER BY s_created ASC
				LIMIT 0, $limit";

		return $this->db->query($sql)->result_array();
	}




	/**
	 * Update an SMS by its ID
	 *
	 * @param int $s_id		ID of template to update
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 */
	public function update($s_id = 0, $data = array())
	{
		$data['s_updated'] = time();

		$sql = $this->db->update_string('sms', $data, 's_id = ' . (int) $s_id);
		$query = $this->db->query($sql);

		return ($query ? (int) $s_id : $query);
	}




	/**
	 * Add a new SMS
	 *
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 */
	public function insert($data = array())
	{
		$data['s_created'] = time();

		$sql = $this->db->insert_string('sms', $data);
		$query = $this->db->query($sql);
		return ($query ? $this->db->insert_id() : $query);
	}




	public function add_bulk($data = array())
	{
		return $this->db->insert_batch('sms', $data);
	}





}

/* End of file: ./application/models/sms_model.php */