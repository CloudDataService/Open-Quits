<?php

// model clinics
class Model_api extends CI_Model
{
	// variables
	public $storage = null;

	// initialise
	public function __construct()
	{
		// inherit
		parent::__construct();
	}

	// http auth digest parser
	public function http_digest_parse($txt)
	{
		// protect against missing data
		$needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));

		// match
		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		// loop through
		foreach($matches as $m)
		{
			// assign
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];

			// unset
			unset($needed_parts[$m[1]]);
		}

		// return?
		return $needed_parts ? false : $data;
	}

	// grab the contact data
	public function grab_contact_data($start = 0)
	{
		// perform query
		$sql = 'SELECT `monitoring_form_id`, `fname`, `sname`, `gender`, `date_of_birth`, `post_code`, `treatment_outcome`, `address`, `tel_daytime`, `tel_mobile`
				FROM `clients`
				LEFT JOIN `monitoring_forms`
					ON `monitoring_forms`.`id` = `clients`.`monitoring_form_id`
				WHERE `monitoring_form_id` > ' . $this->db->escape($start) . '
				ORDER BY `monitoring_form_id` ASC
				LIMIT 1000;';

		// execute query
		$query = $this->db->query($sql);

		// calculate num rows
		$rows = $query->num_rows();

		// are there no rows?
		if($rows == 0)
		{
			// update storage
			$this->storage = array('error' => 'no records to return');
		} else {
			// update storage
			$this->storage = array('records' => $rows,
								   'data'	 => $query->result_array());
		}

		// return storage
		return $this->storage;
	}

	/**
	 * Get all postcodes from the system along with their lat/lngs
	 */
	public function get_postcodes()
	{
		$sql = 'SELECT `pc_postcode`, `pc_lat`, `pc_lng`
				FROM `postcodes`
				ORDER BY `pc_postcode`';

		$query = $this->db->query($sql);
		$rows = $query->num_rows();

		if ( ! $rows)
		{
			$this->storage = array('error' => 'no records to return');
		}
		else
		{
			$this->storage = array(
				'records' => $rows,
				'data' => $query->result_array()
			);
		}

		// Return storage
		return $this->storage;
	}

	/**
	 * Get all postcode sectors from the system along with their lat/lngs
	 */
	public function get_postcode_sectors()
	{
		$sql = 'SELECT `pc_postcode`, `pc_lat`, `pc_lng`
				FROM `postcodes_sectors`
				ORDER BY `pc_postcode`';

		$query = $this->db->query($sql);
		$rows = $query->num_rows();

		if ( ! $rows)
		{
			$this->storage = array('error' => 'no records to return');
		}
		else
		{
			$this->storage = array(
				'records' => $rows,
				'data' => $query->result_array()
			);
		}

		// Return storage
		return $this->storage;
	}

	/**
	 * Get all service providers from the system
	 */
	public function get_service_providers()
	{
		$sql = 'SELECT `id`,
					`name`,
					`post_code`,
					`location`,
					`department`,
					`venue`,
					`telephone`,
					`provider_code`,
					`advisor_code`,
					`cost_code`
				FROM `service_providers`
				ORDER BY `id`';

		$query = $this->db->query($sql);
		$rows = $query->num_rows();

		if ( ! $rows)
		{
			$this->storage = array('error' => 'no records to return');
		}
		else
		{
			$this->storage = array(
				'records' => $rows,
				'data' => $query->result_array()
			);
		}

		// Return storage
		return $this->storage;
	}

	/**
	 * Get all service provider appointments from the system
	 */
	public function get_service_provider_appointments()
	{
		$sql = 'SELECT `ao_id`,
					`ao_sp_id`,
					`ao_first_appt_time`,
					`ao_last_appt_time`,
					`ao_length`,
					`ao_capacity`,
					`ao_day_of_week`
				FROM `appointment_options`
				ORDER BY `ao_id`';

		$query = $this->db->query($sql);
		$rows = $query->num_rows();

		if ( ! $rows)
		{
			$this->storage = array('error' => 'no records to return');
		}
		else
		{
			$this->storage = array(
				'records' => $rows,
				'data' => $query->result_array()
			);
		}

		// Return storage
		return $this->storage;
	}
}
