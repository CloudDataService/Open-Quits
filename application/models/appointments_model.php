<?php

class Appointments_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();

		// Ensure all expired appointments are removed
		$this->prune();
	}




	public function count_all_appointments($filter = array())
	{
		$date_field = element('date_field', $filter);

		$sql = 'SELECT
					COUNT(*) AS count
				FROM
					appointments
				LEFT JOIN
					appointment_clients ac
						ON a_ac_id = ac.ac_id
				LEFT JOIN
					service_providers sp
						ON a_sp_id = sp.id
				WHERE
					1 = 1 ';

		if (element('date_from', $filter))
			$sql .= " AND DATE(`$date_field`) >= " . $this->db->escape(parse_date(element('date_from', $filter))) . " ";

		if (element('date_to', $filter))
			$sql .= " AND DATE(`$date_field`) <= " . $this->db->escape(parse_date(element('date_to', $filter))) . " ";

		if (element('a_sp_id', $filter))
			$sql .= ' AND a_sp_id = ' . (int) element('a_sp_id', $filter);

		if (element('a_status', $filter))
			$sql .= ' AND a_status = "' . element('a_status', $filter) . '" ';

		if (element('ac_fname', $filter))
			$sql .= ' AND ac_fname LIKE "%' . $this->db->escape_like_str(element('ac_fname', $filter)) . '%"';

		if (element('ac_sname', $filter))
			$sql .= ' AND ac_sname LIKE "%' . $this->db->escape_like_str(element('ac_sname', $filter)) . '%" ';

		if (element('post_code', $filter))
			$sql .= ' AND (ac_post_code LIKE "%' . $this->db->escape_like_str(element('post_code', $filter)) . '%" OR sp.post_code LIKE "%' . $this->db->escape_like_str(element('post_code', $filter)) . '%") ';

		$row = $this->db->query($sql)->row_array();
		return $row['count'];
	}




	/**
	 * Get appointments for a given service provider for listing purposes
	 *
	 * @param int $sp_id		Service provider ID
	 * @param int $start		LIMIT start number
	 * @param int $limit		LIMIT row limit
	 * @return array
	 */
	public function get_all_appointments($start = 0, $limit = 20, $filter = array())
	{
		if ( ! in_array(@$_GET['order'], array('ac_fname', 'ac_sname', 'a_datetime', 'a_status', 'a_created_datetime')) ) $_GET['order'] = 'a_datetime';

		if ( @$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'asc';

		// Use datetime of appointment for range by default
		$date_field = element('date_field', $filter, 'a_datetime');

		$sql = 'SELECT
					a.*,
					ac.*,
					sp.*,
					pc_ac.pc_lat AS ac_lat,
					pc_ac.pc_lng AS ac_lng,
					pc_sp.pc_lat AS sp_lat,
					pc_sp.pc_lng AS sp_lat,
					DATE_FORMAT(a_datetime, "%a %d/%m/%Y %H:%i") AS a_datetime_format,
					DATE_FORMAT(a_created_datetime, "%a %d/%m/%Y %H:%i") AS a_created_datetime_format
				FROM
					appointments a
				LEFT JOIN
					appointment_clients ac
						ON a_ac_id = ac.ac_id
				LEFT JOIN
					service_providers sp
						ON a_sp_id = sp.id
				LEFT JOIN
					postcodes pc_ac
						ON ac.ac_post_code = pc_ac.pc_postcode
				LEFT JOIN
					postcodes pc_sp
						ON sp.post_code = pc_sp.pc_postcode
				WHERE 1 = 1';

		if (element('date_from', $filter))
			$sql .= " AND DATE(`$date_field`) >= " . $this->db->escape(parse_date(element('date_from', $filter))) . " ";

		if (element('date_to', $filter))
			$sql .= " AND DATE(`$date_field`) <= " . $this->db->escape(parse_date(element('date_to', $filter))) . " ";

		if (element('future', $filter))
			$sql .= " AND `$date_field` >= NOW() ";

		if (element('a_sp_id', $filter))
			$sql .= ' AND a_sp_id = ' . (int) element('a_sp_id', $filter);

		if (element('a_status', $filter))
			$sql .= ' AND a_status = "' . element('a_status', $filter) . '" ';

		if (element('ac_fname', $filter))
			$sql .= ' AND ac_fname LIKE "%' . $this->db->escape_like_str(element('ac_fname', $filter)) . '%"';

		if (element('ac_sname', $filter))
			$sql .= ' AND ac_sname LIKE "%' . $this->db->escape_like_str(element('ac_sname', $filter)) . '%" ';

		if (element('post_code', $filter))
			$sql .= ' AND (ac_post_code LIKE "%' . $this->db->escape_like_str(element('post_code', $filter)) . '%" OR sp.post_code LIKE "%' . $this->db->escape_like_str(element('post_code', $filter)) . '%") ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if ($limit)
			$sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;

		return $this->db->query($sql)->result_array();
	}




	/**
	 * Get details of a single appointment for updating
	 *
	 * @param int $a_id		ID of appointment to get
	 * @return mixed
	 */
	public function get($a_id = 0)
	{
		$sql = 'SELECT
					*,
					CONCAT(pmss_fname, " ", pmss_sname) AS pmss_name,
					CONCAT(sps.fname, " ", sps.sname) AS sps_name,
					DATE_FORMAT(a_datetime, "%Y%m%d%H%i") AS cell_id
				FROM
					appointments
				LEFT JOIN
					appointment_clients ac
						ON a_ac_id = ac_id
				LEFT JOIN
					service_providers sp
						ON a_sp_id = sp.id
				LEFT JOIN
					postcodes
						ON sp.post_code = pc_postcode
				LEFT JOIN
					pms_staff pmss
						ON a_created_pmss_id = pmss.pmss_id
				LEFT JOIN
					service_provider_staff sps
						ON a_created_sps_id = sps.id
				WHERE
					a_id = ?
				LIMIT 1';

		return $this->db->query($sql, array($a_id))->row_array();
	}




	/**
	 * Count the number of SP appointments within a given date range.
	 *
	 * Used to determine whether appointments in the schedule are available or not.
	 *
	 * @param int $sp_id		ID of service provider to check appointments are for
	 * @param string $start_date		Start date of range
	 * @param string $end_date		End date of range
	 * @return array
	 */
	public function count_within_range($sp_id = 0, $start_date = NULL, $end_date = NULL)
	{
		if ($start_date === NULL)
		{
			$start_date = date('Y-m-d');
		}

		if ($end_date === NULL)
		{
			$end_date = date('Y-m-d', strtotime('+7 days'));
		}

		$sql = 'SELECT
					DATE_FORMAT(a_datetime, "%Y%m%d%H%i") AS cell_id,
					COUNT(a_datetime) AS count
				FROM
					appointments
				WHERE
					a_sp_id = ?
				AND
					a_status IN ("Reserved", "Confirmed")
				AND
					a_datetime BETWEEN "' . $start_date . '" AND "' . $end_date . '"
				GROUP BY a_datetime';

		$result = $this->db->query($sql, array($sp_id))->result_array();

		$appointments = array();
		foreach ($result as $row)
		{
			$appointments[$row['cell_id']] = $row['count'];
		}

		return $appointments;
	}




	/**
	 * Checks availability for a given date and time against
	 * the service provider appointment options.
	 *
	 * @param int $sp_id		Service provider ID
	 * @param string $date		Date of the appouintment in Y-m-d format
	 * @param string $time		Time of the appointment in H:i format
	 * @return bool
	 */
	public function check_availability($sp_id = 0, $date = '', $time = '')
	{

		$sql = 'SELECT
					ao_capacity - COUNT(a_id) AS free
				FROM
					appointment_options
				LEFT JOIN
					appointments
						ON ao_sp_id = a_sp_id
						AND DATE(a_datetime) = "' . $date . '"
						AND TIME(a_datetime) = "' . $time . '"
				WHERE
					1 = 1
				AND
					ao_sp_id = ?
				AND
					ao_day_of_week = ?
				GROUP BY ao_day_of_week';

		$day_of_week = date('N', strtotime($date));

		$row = $this->db->query($sql, array($sp_id, $day_of_week))->row_array();

		if ($row)
		{
			return ( (int) $row['free'] > 0);
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Create an appointment for given service provider at given time.
	 *
	 * Checks availability before creating.
	 * Now row has status to 'reserved'.
	 *
	 * @param int $sp_id		Service provider ID
	 * @param string $date		Date of the appouintment in Y-m-d format
	 * @param string $time		Time of the appointment in H:i format
	 * @return int		New  record ID
	 */
	public function create($sp_id = 0, $date = '', $time = '')
	{
		if ($this->check_availability($sp_id, $date, $time))
		{
			$data = array(
				'a_sp_id' => $sp_id,
				'a_datetime' => "$date $time",
				'a_status' => 'Reserved',
				'a_created_datetime' => date('Y-m-d H:i:s'),
			);

			// Set the PMS staff ID if PMSS logged in
			if ($this->session->userdata('pmss_id'))
			{
				$data['a_created_pmss_id'] = $this->session->userdata('pmss_id');
			}

			// Set the SP staff ID if SPS logged in
			if ($this->session->userdata('sps_id'))
			{
				$data['a_created_sps_id'] = $this->session->userdata('sps_id');
			}

			$sql = $this->db->insert_string('appointments', $data);
			return ($this->db->query($sql)) ? $this->db->insert_id() : FALSE;
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Reschedule an appointment
	 *
	 * Checks availability before creating.
	 * Now row has status to 'confirmed'.
	 *
	 * @param int $a_id		ID of appointment to reschedule
	 * @param int $sp_id		Service provider ID
	 * @param string $date		Date of the appouintment in Y-m-d format
	 * @param string $time		Time of the appointment in H:i format
	 * @return bool
	 */
	public function reschedule($a_id = 0, $sp_id = 0, $date = '', $time = '')
	{
		if ($this->check_availability($sp_id, $date, $time))
		{
			$data = array(
				'a_datetime' => "$date $time",
				'a_status' => 'Confirmed',
			);

			return $this->update($a_id, $data);
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Update appointment
	 *
	 * @param int $a_id		ID of appointment to update
	 * @param array $data		Array of data to set
	 * @return mixed		Appointment ID on successful update, FALSE on failure
	 */
	public function update($a_id = 0, $data = array())
	{
		$sql = $this->db->update_string('appointments', $data, 'a_id = ' . (int) $a_id);
		return ($this->db->query($sql)) ? (int) $a_id : FALSE;
	}




	/**
	 * Permanently delete an appointment
	 *
	 * @param int $a_id		ID of appointment to remove
	 * @return bool
	 */
	public function delete($a_id = 0)
	{
		$sql = 'DELETE FROM appointments WHERE a_id = ? LIMIT 1';
		return $this->db->query($sql, array($a_id));
	}




	/**
	 * Remove half-booked "Reserved" entries that are older than 15 minutes
	 *
	 * @return bool
	 */
	public function prune()
	{
		$sql = 'DELETE FROM appointments
				WHERE a_status = "Reserved"
				AND a_created_datetime <= NOW() - INTERVAL 15 MINUTE';

		return $this->db->query($sql);
	}




	/**
	 * Add or update an appointment client details
	 */
	public function set_client($ac_id = 0, $data = array())
	{
		if ($ac_id)
		{
			// Update
			$sql = $this->db->update_string('appointment_clients', $data, 'ac_id = ' . (int) $ac_id);
			return ($this->db->query($sql)) ? (int) $ac_id : FALSE;
		}
		else
		{
			// Insert
			$sql = $this->db->insert_string('appointment_clients', $data);
			return ($this->db->query($sql)) ? $this->db->insert_id() : FALSE;
		}
	}




	/**
	 * Get options of all service providers
	 *
	 * @return array 		Array. Keys: [$sp_id][$day_of_week] = $row
	 */
	public function get_all_options()
	{
		$sql = 'SELECT
					*,
					DATE_FORMAT(ao_first_appt_time, "%H:%i") AS ao_first_appt_time_format,
					DATE_FORMAT(ao_last_appt_time, "%H:%i") AS ao_last_appt_time_format
				FROM appointment_options';

		$result = $this->db->query($sql)->result_array();

		$options = array();

		foreach ($result as $row)
		{
			$options[$row['ao_sp_id']][$row['ao_day_of_week']] = $row;
		}

		return $options;
	}




	/**
	 * Get the configuration options for the service provider
	 *
	 * @param int $sp_id		Service provider ID
	 * @return array
	 */
	public function get_options($sp_id = 0)
	{
		$sql = 'SELECT
					*,
					DATE_FORMAT(ao_first_appt_time, "%H:%i") AS ao_first_appt_time_format,
					DATE_FORMAT(ao_last_appt_time, "%H:%i") AS ao_last_appt_time_format
				FROM appointment_options
				WHERE ao_sp_id = ?
				ORDER BY ao_day_of_week ASC';

		$result = $this->db->query($sql, array($sp_id))->result_array();

		$options = array();

		foreach ($result as $row)
		{
			$options[$row['ao_day_of_week']] = $row;
		}

		return $options;
	}




	/**
	 * Set the appointment options for a given service provider.
	 *
	 * @param int $sp_id		Service provider ID
	 * @param array $data		Array of arrays of data to set. Batch insert is used for each array
	 * @return bool
	 */
	public function set_options($sp_id = 0, $data = array())
	{

		$sql = 'DELETE FROM appointment_options WHERE ao_sp_id = ?';
		$this->db->query($sql, array($sp_id));

		return $this->db->insert_batch('appointment_options', $data);
	}




	/**
	 * Get the first and last appointment times for a given service provider
	 *
	 * This is used on the schedule so that the full times
	 * throughout the week are generated, even if some days
	 * start later/finish earlier than others.
	 *
	 * @param int $sp_id		Service provider ID
	 * @return array
	 */
	public function get_first_last_appt_times($sp_id = 0)
	{
		$sql = 'SELECT
					MIN(ao_first_appt_time) AS first,
					MAX(ao_last_appt_time) AS last
				FROM appointment_options
				WHERE ao_sp_id = ?
				LIMIT 1';

		return $this->db->query($sql, array($sp_id))->row_array();
	}




	/**
	 * Send an email to the client with a reminder of their appointment
	 */
	public function send_email($a_id = 0)
	{
		$appointment = $this->get($a_id);

		if ( ! $appointment) return FALSE;
		if (empty($appointment['ac_email'])) return FALSE;

		// Load email message with appointment data
		$body = $this->load->view('emails/client_appointment', array('appointment' => $appointment), TRUE);

		$this->load->library('email');

		$this->email->from('no-reply@openquits.net', 'NHS South of Tyne and Wear')
					->to($appointment['ac_email'])
					->subject('Your stop smoking appointment')
					->message($body);

		return $this->email->send();
	}




}

/* End of file: ./application/models/appointments_model.php */
