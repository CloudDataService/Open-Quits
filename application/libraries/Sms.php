<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library to handle interaction with the TextMagic API to send text messages
 *
 * Custom version of the official wrapper at:
 * <http://code.google.com/p/textmagic-sms-api-php/>
 *
 * In the development environment, messages will ALWAYS be sent to the test number
 * which starts with 999 to save credits and to ensure that patients don't receive
 * undesired messages.
 *
 * @package Call it Quits
 * @subpackage Libraries
 * @author CR
 */

define('SMS_QUEUE', 'q');
define('SMS_SCHEDULED_QUEUE', 's');
define('SMS_SENDING_ERROR', 'e');
define('SMS_ENROUTE', 'r');
define('SMS_ACKED', 'a');
define('SMS_DELIVERED', 'd');
define('SMS_BUFFERED', 'b');
define('SMS_FAILED', 'f');
define('SMS_UNKNOWN', 'u');
define('SMS_REJECTED', 'j');

const LOW_BALANCE                  = 2;
const INVALID_USERNAME_PASSWORD    = 5;
const MESSAGE_WAS_NOT_SENT         = 6;
const TOO_LONG_MESSAGE             = 7;
const IP_ADDRESS_IS_NOT_ALLOWED    = 8;
const WRONG_PHONE_FORMAT           = 9;
const WRONG_PARAMETER_VALUE        = 10;
const DAILY_REQUESTS_LIMIT_EXCEEDED = 11;
const TOO_MANY_ITEMS               = 12;
const DISABLED_ACCOUNT             = 13;
const UNKNOWN_MESSAGE_ID           = 14;
const UNICODE_SYMBOLS_DETECTED     = 15;

class Sms
{

	private $error_codes = array(
		LOW_BALANCE => 'Low balance for the operation',
		INVALID_USERNAME_PASSWORD => 'API authentication error',
		MESSAGE_WAS_NOT_SENT => 'Message not sent',
		TOO_LONG_MESSAGE => 'Too long message',
		IP_ADDRESS_IS_NOT_ALLOWED => 'Not allowed IP address',
		WRONG_PHONE_FORMAT => 'Wrong phone number format',
		WRONG_PARAMETER_VALUE => 'Wrong parameter value',
		DAILY_REQUESTS_LIMIT_EXCEEDED => 'Daily requests limit exceeded',
		TOO_MANY_ITEMS => 'Too many items',
		DISABLED_ACCOUNT => 'Disabled account',
		UNKNOWN_MESSAGE_ID => 'Unknown message ID',
		UNICODE_SYMBOLS_DETECTED => 'Unicode symbols detected',
	);

	protected $_CI;		// CodeIgntier object

	private $_url;		// TextMagic API endpoint
	private $_username;		// TextMagic API username
	private $_password;		// TextMagic API password

	private $final_statuses = array(SMS_DELIVERED, SMS_FAILED, SMS_UNKNOWN, SMS_REJECTED);


	public function __construct()
	{
		$this->_CI =& get_instance();

		// Retrieve the username and password from config file and store locally
		$this->_url = $this->_CI->config->item('url', 'sms_api');
		$this->_username = $this->_CI->config->item('username', 'sms_api');
		$this->_password = $this->_CI->config->item('password', 'sms_api');

		// Number for testing
		$this->_test_number = '99942424242';
	}




	/**
	 * Get account balance
	 *
	 * This command is used to check the current SMS credits balance on your account.
	 *
	 * @return integer
	 */
	public function get_balance()
	{
		$params = array('cmd' => 'account');

		$json = $this->_make_http_request($params);

		return ($json !== FALSE) ? floor($json['balance'] / 0.8) : FALSE;
	}




	/**
	 * Retrieve the status of a delivered SMS by the textmagic ID
	 *
	 * @param int $id		TextMagic ID (e.g. 17585291)
	 * @return mixed 		Array or FALSE on failure
	 */
	function get_status($id = 0)
	{
		if ($id > 0)
		{
			$params = array(
				'username' => $this->_username,
				'password' => $this->_password,
				'cmd' => 'message_status',
				'ids' => $id
			);

			$response = $this->_make_http_request($params);

			if ($response !== FALSE)
			{
				// Got a readable response, return values
				return $response;
			}
			else
			{
				return FALSE;
			}
		}
	}




	/**
	 * Send a text to a phone number.
	 *
	 * @deprecated Kept to maintain compatibility, but will be removed soon. Replaced by send_sms().
	 * @param string $number		Number to send message to
	 * @param string $text		Message text
	 */
	public function send($number, $text)
	{
		$response = $this->send_sms($number, $text);

		if ($response !== FALSE)
		{
			$sql = 'UPDATE options
					SET option_value = (option_value + 1)
					WHERE option_name = "total_sms_sent"';
			$this->_CI->db->query($sql);

			return TRUE;
		}

		return FALSE;
	}




	/**
	 * Send a text message to the supplied number
	 *
	 * @param int $number		Mobile number to send message to (should start with 07)
	 * @param string $text		Text of the message to send
	 * @return array		Array containing response data (message id, text, etc)
	 */
	public function send_sms($number, $text)
	{
		if (empty($number))
		{
			$this->_err = 'Phone number is empty';
			return FALSE;
		}

		if (empty($text))
		{
			$this->_err = 'Message text is empty';
			return FALSE;
		}

		// Set number based on environment. Only the production environment will use real numbers.
		if (ENVIRONMENT === 'production')
		{
			// Live environment will format the nubmer properly
			$original_number = $number;
			$number = preg_replace('/^07/', '447', trim($number));
		}
		else
		{
			$number = $this->_test_number;
			$original_number = $number;
		}

		// set a list of parameters
		$params = array(
			'username' => $this->_username,
			'password' => $this->_password,
			'cmd' => 'send',
			'text' => rawurlencode($text),
			'phone' => rawurlencode($number),
			'unicode' => 0,
		);

		$response = $this->_make_http_request($params);

		if ($response !== FALSE)
		{
			// Log the raw data of the response
			$data = array(
				'sal_num' => $number,
				'sal_data' => json_encode($response),
				'sal_sent' => $text,
			);
			$sql = $this->_CI->db->insert_string('sms_api_log', $data);
			$query = $this->_CI->db->query($sql);

			log_message('debug', 'send_sms(): log data: ' . $data['sal_data']);

			foreach ($response['message_id'] as $message_id => $number)
			{
				$message_id_array[preg_replace('/^447/', '07', $number, 1)] = $message_id;
			}

			log_message('debug', 'send_sms(): message id array: ' . var_export($message_id_array, TRUE));

			$response['message_id'] = $message_id_array[$original_number];
			$response['sent_to'] = $number;

			log_message('debug', 'send_sms(): response array: ' . var_export($response, TRUE));

			return (array) $response;
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Retrive the last error that occurred
	 *
	 * @return string
	 */
	public function get_error()
	{
		return $this->_err;
	}




	/**
	 * HTTP abstract request wrapper
	 *
	 * @param array $params associative array of request parameters
	 *
	 * @return string
	*/
	private function _make_http_request($params = array())
	{
		$params['username'] = $this->_username;
		$params['password'] = $this->_password;

		if ( ! extension_loaded('curl'))
		{
			$this->_err = 'cURL extension not loaded.';
			return FALSE;
		}

		$ch = curl_init($this->_url);

		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 3,
			CURLOPT_POSTFIELDS  => $params,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			//CURLOPT_VERBOSE => 1
		));

		$raw_data  = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code != 200)
		{
			if ($http_code)
			{
				$this->_err = "Bad response: HTTP code $http_code";
			}
			else
			{
				$this->_err = "Couldn't connect to remote server";
			}
			return FALSE;
		}

		log_message('debug', 'SMS HTTP request response: ' . $raw_data);

		// TRUE ensures return value is array
		$json = json_decode($raw_data, TRUE);

		// Return false on error and set error variable
		if (array_key_exists('error_code', $json))
		{
			log_message('debug', '_make_http_request(): ' . var_dump($json, TRUE));
			$this->_err = @$this->error_codes[$json['error_code']];
			return FALSE;
		}

		return $json;
	}




	/**
	 * Crude message templating for messages.
	 *
	 * Replaces [fname] with the same key value in supplied array.
	 *
	 * @deprecated Function remains for backwards-compability reasons
	 * @param string $string		Message text
	 * @param array $txt		Array of data to replace
	 * @return string		 String, with [fname] replaced with the value from $txt['fname']
	 */
	function str_replace($string, $txt)
	{
		return str_replace(array('[fname]'), array($txt['fname']), $string);
	}


}

/* End of file: ./application/libaries/Sms.php */