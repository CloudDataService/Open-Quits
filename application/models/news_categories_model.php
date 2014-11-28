<?php

class News_categories_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Get a single news category
	 *
	 * @param int  $nc_id		ID of category to get
	 * @return array		DB row array
	 * @author CR
	 */
	public function get($nc_id = 0)
	{
		$sql = 'SELECT * FROM news_categories WHERE nc_id = ? LIMIT 1';
		return $this->db->query($sql, array($nc_id))->row_array();
	}




	/**
	 * Get list of active news categories
	 *
	 * @return array		DB result array
	 * @author CR
	 */
	public function get_active()
	{
		$sql = 'SELECT * FROM news_categories WHERE nc_active = 1 ORDER BY nc_title ASC';
		return $this->db->query($sql)->result_array();
	}




	/**
	 * Get ALL the categories! (not just active ones)
	 *
	 * @return array		DB result array
	 * @author CR
	 */
	public function get_all()
	{
		$sql = 'SELECT * FROM news_categories ORDER BY nc_title ASC';
		return $this->db->query($sql)->result_array();
	}




	/**
	 * Update a news category
	 *
	 * @param int $c_id		ID of news category to update
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 * @author CR
	 */
	public function update($nc_id = 0, $data = array())
	{
		$data['nc_updated'] = date('Y-m-d H:i:s');

		$sql = $this->db->update_string('news_categories', $data, 'nc_id = ' . (int) $nc_id);
		$this->db->query($sql);

		return ($query ? (int) $nc_id : $query);
	}




	/**
	 * Add a new news category
	 *
	 * @param array $data		Array of DB columns => values to set
	 * @return mixed		ID on success, FALSE on failure
	 * @author CR
	 */
	public function insert($data = array())
	{
		$data['nc_created'] = date('Y-m-d H:i:s');

		$sql = $this->db->insert_string('news_categories', $data);
		$query = $this->db->query($sql);
		return ($query ? $this->db->insert_id() : $query);
	}





}

/* End of file: ./application/models/news_categories_model.php */