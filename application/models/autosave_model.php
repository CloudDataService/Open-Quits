<?php

class Autosave_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Get all autosave entries for a given user and URI
	 *
	 * @param int $sps_id		Service provider staff ID
	 * @param string $uri		URI string for the autosave data to get
	 * @return array		Result array
	 */
	public function get($sps_id = 0, $uri = '')
	{
		$sql = 'SELECT
					autosave.*,
					DATE_FORMAT(as_created_datetime, "%d/%m/%Y %l:%i %p") AS as_created_datetime_format
				FROM autosave
				WHERE as_sps_id = ? AND as_uri_string = ?
				ORDER BY as_created_datetime ASC';
		$result = $this->db->query($sql, array($sps_id, $uri))->result_array();

		$autosaves = array();

		foreach ($result as $row)
		{
			$row['as_data'] = json_decode($row['as_data']);
			$autosaves[$row['as_id']] = $row;
		}

		return $autosaves;
	}




	public function insert($data = array())
	{
		$sql = $this->db->insert_string('autosave', $data);
		return ($this->db->query($sql)) ? $this->db->insert_id() : FALSE;
	}




	public function delete($as_id, $sps_id)
	{
		$sql = 'DELETE FROM autosave WHERE as_id = ? AND as_sps_id = ? LIMIT 1';
		return $this->db->query($sql, array($as_id, $sps_id));
	}


}

/* End of file: ./application/models/health_problems_model.php */
