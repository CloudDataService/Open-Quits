<?php

class Auth {

	protected $_CI;

	function __construct()
	{
		$this->_CI =& get_instance();
	}

	function check_logged_in()
	{
		if($this->_CI->session->userdata('sps_id') && $this->_CI->session->userdata('logged_in'))
		{
			redirect('/service-providers');
		}
		elseif($this->_CI->session->userdata('admin_id') && $this->_CI->session->userdata('logged_in'))
		{
			redirect('/admin');
		}
		elseif ($this->_CI->session->userdata('pmss_id') && $this->_CI->session->userdata('logged_in'))
		{
			redirect('/pms');
		}

		return $this;
	}

	function check_service_provider_logged_in()
	{
		if( ! $this->_CI->session->userdata('sps_id') || ! $this->_CI->session->userdata('logged_in'))
		{
			$this->_CI->session->set_userdata('sp_request_uri', $_SERVER['REQUEST_URI']);

			redirect('/?auth_failed=1');
		}

		return $this;
	}

	function check_admin_logged_in()
	{
		if( ! $this->_CI->session->userdata('admin_id') || ! $this->_CI->session->userdata('logged_in'))
		{
			$this->_CI->session->set_userdata('admin_request_uri', $_SERVER['REQUEST_URI']);

			redirect('/?auth_failed=1');
		}

		return $this;
	}

	function check_pms_logged_in()
	{
		if( ! $this->_CI->session->userdata('pmss_id') || ! $this->_CI->session->userdata('logged_in'))
		{
			$this->_CI->session->set_userdata('pms_request_uri', $_SERVER['REQUEST_URI']);
			redirect('/?auth_failed=1');
		}

		return $this;
	}

	function check_master_logged_in()
	{
		if( ! $this->_CI->session->userdata('master'))
		{
			show_404();
		}

		return $this;
	}

	function check_tier_3_logged_in()
	{
		if( ! $this->_CI->session->userdata('tier_3'))
		{
			show_404();
		}

		return $this;
	}
}
