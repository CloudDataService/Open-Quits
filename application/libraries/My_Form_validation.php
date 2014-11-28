<?php

class My_Form_validation extends CI_Form_validation {


	public $CI;


	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->CI =& get_instance();
	}


	public function check_service_provider_staff_email_unique($email, $sps_id = 0)
	{
		if( ! isset($this->CI->model_service_providers))
			$this->CI->load->model('model_service_providers');

		return $this->CI->model_service_providers->check_email_unique($email, $sps_id);
	}


	public function check_service_provider_staff_current_password($password, $sps_id)
	{
		if( ! isset($this->CI->model_service_providers))
			$this->CI->load->model('model_service_providers');

		return $this->CI->model_service_providers->check_password_current($password, $sps_id);
	}


	public function check_admin_email_unique($email, $admin_id = 0)
	{
		if( ! isset($this->CI->model_admins))
			$this->CI->load->model('model_admins');

		return $this->CI->model_admins->check_email_unique($email, $admin_id);
	}


	public function check_pmss_email_unique($email, $pmss_id = 0)
	{
		if ( ! isset($this->CI->pms_model))
			$this->CI->load->model('pms_model');

		return $this->CI->pms_model->check_email_unique($email, $pmss_id);
	}


	public function check_admin_current_password($password, $admin_id)
	{
		if( ! isset($this->CI->model_admins))
			$this->CI->load->model('model_admins');

		return $this->CI->model_admins->check_password_current($password, $admin_id);
	}


	public function parse_date($date)
	{
		if( ! preg_match('/^[0-3]{1}[0-9]{1}\/[0-1]{1}[0-9]{1}\/[0-9]{4}$/', $date) )
			return false;

		$date = explode('/', $date);

		$mysql_date = $date[2] . '-' . $date[1] . '-' . $date[0];

		return $mysql_date;
	}


	function password_restrict($password)
	{
		return ( ! preg_match('/^(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', $password) ? FALSE : TRUE);
	}


	function other($index)
	{
		if(@$_POST[$index] == 'Other')
		{
			return '|required';
		}

		return false;
	}


	function numeric($str)
	{
		if((bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str))
		{
			return sprintf('%.2F', $str);
		}

		return false;
	}


}
