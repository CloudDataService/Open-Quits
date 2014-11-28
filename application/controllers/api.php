<?php

// API controller
class Api extends My_controller
{
	// set variables
	public $users = array(
        'api_acl' => '5b2e446e24a8694e0145c3c175969dec',
        'smokefree' => 'd019eb089e65903455cc52308f00b997'
    );
	public $realm = 'CallItQuits API Access';

	// initialise
	public function __construct()
	{
		// inherit
		parent::__construct();

		// load the request forms model
		$this->load->model('model_api');

		// send auth headers if digest doesn't exist
		if(empty($_SERVER['PHP_AUTH_DIGEST']))
		{
			// send headers
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="' . $this->realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($realm) . '"');
		}

	}

	// analyse data
	public function _analyse_auth_data()
	{
		// need to analyse auth data
		if(!($data = $this->model_api->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($this->users[$data['username']]))
		{
			// show error
			echo(json_encode(array('error' => 'authentication failed')));
		} else {
			// generate response
			$a1 = md5($data['username'] . ':' . $this->realm . ':' . $this->users[$data['username']]);
			$a2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
			$valid_response = md5($a1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $a2);

			// did it match what they sent?
			if($data['response'] != $valid_response)
			{
				// failed
				echo(json_encode(array('error' => 'authentication failed')));
			} else {
				// success, return
				return true;
			}
		}
	}

	// data request
	public function fetch_data($start = 0)
	{
		// is it a valid authentication header?
		if($this->_analyse_auth_data())
		{
			// continue
			// grab data
			$data = $this->model_api->grab_contact_data($start);

			// return json encoded
			echo(json_encode($data));
		}
	}

    /**
     * Return a full JSON stream of available postcodes and their lat/lng
     */
    public function postcodes()
    {
        if ($this->_analyse_auth_data())
        {
            $data = $this->model_api->get_postcodes();

            header('Content-type: application/json');
            echo json_encode($data);
        }
    }

    /**
     * Return a full JSON stream of available postcode sectors and their lat/lng
     */
    public function postcode_sectors()
    {
        if ($this->_analyse_auth_data())
        {
            $data = $this->model_api->get_postcode_sectors();

            header('Content-type: application/json');
            echo json_encode($data);
        }
    }

    /**
     * Return a full JSON stream of all service providers stored in the system
     */
    public function service_providers()
    {
        if ($this->_analyse_auth_data())
        {
            $data = $this->model_api->get_service_providers();

            header('Content-type: application/json');
            echo json_encode($data);
        }
    }

    /**
     * Return a full JSON stream of available service provider appointment times
     */
    public function service_provider_appointments()
    {
        if ($this->_analyse_auth_data())
        {
            $data = $this->model_api->get_service_provider_appointments();

            header('Content-type: application/json');
            echo json_encode($data);
        }
    }

}
