<?php

class Postcode_model extends CI_Model
{


	private $_api_key = 'e182c8dcce46f0';
	private $_api_url = 'http://api1.nearby.org.uk/api/convert.php';


	function __construct()
	{
		parent::__construct();
	}




	public function lookup($post_code = '', $store = TRUE)
	{
		$post_code = trim($post_code);

		if (empty($post_code)) return FALSE;

		if (strlen($post_code) <= 4)
		{
			// Sector area only
			log_message('debug', 'Postcode_model: lookup(): Short postcode ' . $post_code . '. Checking sector table.');
			$sql = 'SELECT * FROM postcodes_sectors WHERE pc_postcode LIKE ' . $this->db->escape_like_str($post_code) . '%" LIMIT 1';
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0)
			{
				log_message('debug', 'Postcode_model: lookup(): Got sector postcode ' . $post_code . ' from local DB.');
				$row = $query->row_array();
				return $row;
			}
		}

		$post_code = format_postcode($post_code);

		// Attempt to get postcode from DB first
		$sql = 'SELECT * FROM postcodes WHERE pc_postcode = ? LIMIT 1';
		$query = $this->db->query($sql, array($post_code));

		if ($query->num_rows() === 1)
		{
			log_message('debug', 'Postcode_model: lookup(): Got ' . $post_code . ' from local DB.');
			$row = $query->row_array();
			return $row;
		}
		else
		{
			// Not in DB. Check API
			// Specify required API params
			$data = array(
				'p' => preg_replace('/\s|\s+/', '', trim(urldecode($post_code))),
				'in' => 'postcode-uk',
				'want' => 'll-wgs84',
			);

			log_message('debug', 'Postcode_model: lookup(): ' . $post_code . ' Not found.');

			$xml = $this->_make_request($data);

			// Invalid response? FAIL
			if ($xml === FALSE)
			{
				$this->lasterr = (string) $xml;
				log_message('debug', 'Postcode_model: lookup(): XML error: ' . $this->lasterr);
				return FALSE;
			}

			if (@$xml->input->postcode['error'])
			{
				$this->lasterr = (string) $xml->input->postcode['error'];
				log_message('debug', 'Postcode_model: lookup(): Postcode error: ' . $this->lasterr);
				return FALSE;
			}

			// OK - Get LatLng
			$lat = (float) $xml->output->ll['lat'];
			$lng = (float) $xml->output->ll['long'];

			log_message('debug', "Postcode_model: lookup(): Coords for $post_code: $lat, $lng.");

			if ($store)
			{
				// Store in DB
				$data = array(
					'pc_postcode' => format_postcode($post_code),
					'pc_lat' => $lat,
					'pc_lng' => $lng,
				);

				if ($this->db->insert('postcodes', $data))
				{
					log_message('debug', "Postcode_model: lookup(): Stored $post_code in database.");
				}
			}

			// Return raw data from API lookup
			return array(
				'pc_postcode' => format_postcode($post_code),
				'pc_lat' => $lat,
				'pc_lng' => $lng,
			);
		}
	}




	/**
	 * Make the actual HTTP request to Nearby
	 *
	 * @param array $data		Array of Nearby-named params (p, in, want, need, output)
	 * @return mixed		String of response or false if request failed
	 */
	private function _make_request($data = array())
	{
		if ( ! array_key_exists('p', $data))
		{
			log_message('debug', 'Postcode_model: _make_request(): No postcode value in parameters.');
			$this->lasterr = 'No coordinate specified - cannot continue.';
			return FALSE;
		}

		// Specify output format and build up the request URL
		$data['output'] = 'sxml';
		$url = $this->_build_url($data);

		log_message('debug', 'Postcode_model: _make_request(): Making HTTP request to ' . $url);

		// Make the HTTP request to the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'curl cloud-data-service/call-it-quits');
		$res = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		log_message('debug', 'Postcode_model: _make_request(): HTTP response code ' . @$info['http_code']);

		// Validate request
		if ($info['http_code'] === 200)
		{
			// Attempt to parse response as XML
			try
			{
				@$xml = new SimpleXMLElement($res);
			}
			catch (Exception $e)
			{
				// Couldn't load the response as an XML string - other Nearby error?
				$this->lasterr = $res;
				return FALSE;
			}

			// Valid XML - return the XML object
			return $xml;
		}
		else
		{
			// HTTP response code wasn't 200 OK - error
			$this->lasterr = 'HTTP response code was: ' . $info['http_code'];
			return FALSE;
		}
	}




	/**
	 * Build and encode the URL. Adds the API key
	 *
	 * @param array $data		2D array of Nearby parameters
	 * @return string		String. API URL with all params urlencoded
	 */
	private function _build_url($data)
	{
		$url = $this->_api_url . '?';
		$data['key'] = $this->_api_key;
		foreach ($data as $k => $v)
		{
			$url .= "&$k=" . urlencode(trim($v));
		}
		return $url;
	}


}
