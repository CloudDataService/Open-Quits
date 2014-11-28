<?php

class Health_problems_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	public function get_all()
	{
		$sql = 'SELECT * FROM health_problems ORDER BY hp_name ASC';
		return $this->db->query($sql)->result_array();
	}


}

/* End of file: ./application/models/health_problems_model.php */
