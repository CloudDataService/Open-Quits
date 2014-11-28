<?php

class Home extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		/* check to see if user is logged in and redirect */
		$this->auth->check_logged_in();

		$this->load->model('model_auth');

		$this->layout->set_css('home.css');
	}


	function index()
	{
		$this->data['support_options'] = $this->model_options->get_global_option('support_options');

		if($banned = $this->model_auth->check_for_ban(getenv('REMOTE_ADDR')))
		{
			$this->data['banned'] = $banned;
		}
		elseif($_POST)
		{
			/* check to see if tokens match */
			$this->load->library('form_validation');

			$this->form_validation->set_rules('email', '', 'required|valid_email|callback__login');
			$this->form_validation->set_rules('password', '', 'required');

			if( ! $this->form_validation->run())
			{
				$this->model_auth->set_failed_login(getenv('REMOTE_ADDR'));

				$this->data['failed_login'] = true;
			}
		}

		$this->layout->set_view_script('default/login');
		$this->load->vars($this->data);
		$this->load->view('/layouts/home');
	}


	function terms_and_conditions()
	{
		$this->data['terms_and_conditions'] = $this->model_options->get_pct_option('terms_and_conditions', $this->input->get('pct_id'));

		if(isset($_GET['agree']) && @$_GET['token'] == $this->session->flashdata('token'))
		{
			if($_GET['agree'] == 1)
			{
				$this->model_auth->set_datetime_tc_agree($this->session->userdata('sps_id'));

				$this->session->set_userdata('logged_in', TRUE);

				/* set datetime_last_login to NOW() */
				$this->model_auth->update_sps_last_login($this->session->userdata('sps_id'));

				redirect('/service-providers');

			}
			elseif($_GET['agree'] == 0)
			{
				$this->session->sess_destroy();

				redirect('/?terms_and_conditions=1');
			}

		}

		/* generate token */
		$token = md5($this->config->config['salt'] . rand(0, 99999) . $this->config->config['salt']);

		/* set token */
		$this->session->set_flashdata('token', $token);

		$this->data['token'] = $token;

		$this->layout->set_title('Terms and Conditions');
		$this->layout->set_view_script('default/terms_and_conditions');
		$this->load->vars($this->data);
		$this->load->view('/layouts/home');
	}


	function javascript()
	{
		$this->layout->set_javascript('/views/default/javascript.js');
		$this->layout->set_title('Javacript');
		$this->layout->set_view_script('default/javascript');
		$this->load->vars($this->data);
		$this->load->view('/layouts/home');
	}


	function _login()
	{
		/* login service provider staff */
		if($sps = $this->model_auth->login_service_provider_staff())
		{
			/* clear any previous failed login attempts */
			$this->model_auth->clear_failed_logins(getenv('REMOTE_ADDR'));

			$this->session->set_userdata($sps);

			$terms_and_conditions = $this->model_options->get_pct_option('terms_and_conditions', $sps['pct_id']);

			/* check to see if service provider staff has agreed to the most recent terms and conditions */
			if($terms_and_conditions['on'] == TRUE && strtotime($sps['datetime_tc_agree']) < $terms_and_conditions['datetime_set'])
			{
				/* if they have not, redirect to terms and conditions page */
				redirect('/home/terms-and-conditions?new=' . ($sps['datetime_tc_agree'] ? '0' : '1') . '&pct_id=' . $sps['pct_id']);
			}
			else
			{
				/* log in and redirect */
				$this->session->set_userdata('logged_in', TRUE);

				/* set datetime_last_login to NOW() */
				$this->model_auth->update_sps_last_login($sps['sps_id']);

				if($request_uri = $this->session->userdata('sp_request_uri'))
					$this->session->unset_userdata('sp_request_uri');

				/* hook to set notifications */
				$this->_sps_login_hook($sps);

				redirect(($request_uri ? $request_uri : '/service-providers'));
			}
		}

		/* admin login */
		elseif($admin = $this->model_auth->login_admin())
		{
			$this->model_auth->clear_failed_logins(getenv('REMOTE_ADDR'));

			/* set tinymce session */
			session_start();
			$_SESSION['isLoggedIn'] = true;
			$_SESSION['user'] = 'foo';

			/* log in and redirect */
			$admin['logged_in'] = true;

			$this->session->set_userdata($admin);

			if($request_uri = $this->session->userdata('admin_request_uri'))
					$this->session->unset_userdata('admin_request_uri');

			redirect(($request_uri ? $request_uri : '/admin'));
		}

		/* PMS login */
		elseif ($pmss = $this->model_auth->login_pmss())
		{
			$this->model_auth->clear_failed_logins($this->input->ip_address());
			$pmss['logged_in'] = TRUE;
			$pmss['fname'] = $pmss['pmss_fname'];
			$pmss['sname'] = $pmss['pmss_sname'];

			$this->session->set_userdata($pmss);

			if ($request_uri = $this->session->userdata('pms_request_uri'))
				$this->session->unset_userdata('pms_request_uri');

			redirect(($request_uri) ? $request_uri : '/pms');
		}

		return FALSE;
	}




	protected function _sps_login_hook($sps)
	{
		$this->load->model('model_news');

		$this->session->set_userdata('total_new_news_1', $this->model_news->get_total_new_news($this->session->userdata('datetime_last_login'), 1));
		$this->session->set_userdata('total_new_news_2', $this->model_news->get_total_new_news($this->session->userdata('datetime_last_login'), 2));

		// Get support info
		$support_options = $this->model_options->get_global_option('support_options');

		if($support_options['tel_support_enabled'])
			$this->session->set_userdata('tel_support', $support_options['tel_support']);
	}


}