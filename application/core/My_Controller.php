<?php

class My_controller extends CI_Controller {

	public $data = array();
	protected $pct_id = FALSE;

	public function __construct()
	{
		parent::__construct();

		// SSL check
		if(ENVIRONMENT == "production" && $_SERVER['SERVER_PORT'] != 443)
		{
			redirect($this->config->config['base_url']);
		}

		// Set the version number for layout/display - stores in config
		$this->_set_version();
		// Load library AFTER we've set the version
		$this->load->library('layout');

		if ($this->input->get('export'))
		{
			header("cache-Control: no-store, no-cache, must-revalidate");
			header("cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header('Content-Type: text/html; charset=utf-8');
		}

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		foreach ($_GET as $key => $value)
		{
			$_GET[$key] = xss_clean($value);
		}


		// If user is an admin and the PCT ID is present, store it globally for use in various places
		if ($this->session->userdata('pct_id'))
		{
			$this->pct_id = $this->session->userdata('pct_id');
		}

		$this->data['pct_id'] = $this->pct_id;

		$this->layout->set_title($this->config->config['site_name']);

		$this->layout->set_css(array('screen.css', 'jquery-ui-1.8.23.custom.css'));

		if($this->uri->segment(1) == 'service-providers')
		{
			//$this->layout->set_javascript(array('/jquery-1.3.2.min.js', '/views/default.js'));
			$this->layout->set_javascript(array('/jquery-1.8.2.min.js', '/jquery-ui-1.8.23.custom.min.js', '/views/default.js'));
			//$this->layout->set_javascript(array('/jquery-1.8.0.min.js', '/jquery-ui-1.8.23.custom.min.js', '/views/default.js'));
			//$this->layout->set_javascript(array('/jquery-1.7.2.min.js', '/jquery-ui-1.8.23.custom.min.js', '/views/default.js'));

			$nav = array(
				'Dashboard' => '/service-providers',
				'Client Records' => NULL,
				'Claims' => '/service-providers/claims',
				'News' . ($this->session->userdata('total_new_news_1') ? ' (<strong>' . $this->session->userdata('total_new_news_1') . '</strong>)' : '') => '/service-providers/news/index/1',
				'Training' . ($this->session->userdata('total_new_news_2') ? ' (<strong>' . $this->session->userdata('total_new_news_2') . '</strong>)' : '') => '/service-providers/news/index/2',
				'My account' => '/service-providers/my-account',
				'Help' => NULL,
				'Appointments' => '/service-providers/appointments'
			);

			if($this->session->userdata('master'))
			{
				$nav['Staff'] = '/service-providers/staff';
			}

			$sub_nav = array(
				'Client Records' => array(
					'Index' => '/service-providers/monitoring-forms',
					'IC reports' => '/service-providers/monitoring-forms/ic-reports',
					'Statistics' => '/service-providers/monitoring-forms/statistics',
				),
				'Help' => array(
					'Documentation' => '/service-providers/help',
					'Resources' => '/service-providers/help/resources',
					'Support' => '/service-providers/help/support',
				),
			);

			# tier 3 monitoring form missing information
			if($this->session->userdata('tier_3'))
			{
				$sub_nav['Client Records']['Missing information'] = '/service-providers/monitoring-forms/missing-information';
			}
		}
		elseif($this->uri->segment(1) == 'admin')
		{
			//$this->layout->set_javascript(array('/jquery-1.3.2.min.js', '/views/default.js'));
			$this->layout->set_javascript(array('/jquery-1.8.2.min.js', '/jquery-ui-1.8.23.custom.min.js', '/views/default.js'));

			$nav = array(
				'Dashboard' => '/admin',
				'Service providers' => NULL,
				'Client Records'  => NULL,
				'Claims' => '/admin/claims',
				'News &amp; Training' => '/admin/news',
				'Options' => NULL,
			);

			$sub_nav = array(
				'Options' => array(
					'Quick links' => '/admin/options',
					'Claim options' => '/admin/options/claim-options',
					'Export schemas' => '/admin/options/export-schemas',
					'Groups' => '/admin/options/groups',
					'My account' => '/admin/options/my-account',
					'Local Authorities' => '/admin/options/pcts',
					'Resources' => '/admin/options/resources',
					'Security options' => '/admin/options/security-options',
					'SMS options' => '/admin/options/sms-options',
					'SMS templates' => '/admin/options/sms-templates',
					'Marketing sources' => '/admin/options/marketing-sources',
					'Terms and conditions' => '/admin/options/terms-and-conditions',
					'Advisors' => '/admin/options/advisors',
				),

				'Client Records' => array(
					'Index' => '/admin/monitoring-forms',
					'IC reports' => '/admin/monitoring-forms/ic-reports',
					'IC reports (providers)' => '/admin/monitoring-forms/ic-reports-providers',
					'Statistics' => '/admin/monitoring-forms/statistics',
				),

				'Service providers' => array(
					'Index' => '/admin/service-providers',
					'Staff' => '/admin/service-providers/staff',
					'Activity log' => '/admin/service-providers/activity-log',
				)
			);

			if($this->session->userdata('master'))
			{
				$sub_nav['Options']['Administrators'] = '/admin/options/administrators';
				$sub_nav['Options']['PMS Staff'] = '/admin/options/pms-staff';
			}

			if ($this->pct_id)
			{
				unset($sub_nav['Options']['Local Authorities']);
			}

			// Get list of any blocked IPs to show notification
			$this->load->model('model_auth');
			$this->data['blocked_ips'] = $this->model_auth->get_blocked_ips();
		}
		elseif ($this->uri->segment(1) == 'pms')
		{
			$this->layout->set_javascript(array('/jquery-1.8.2.min.js', '/jquery-ui-1.8.23.custom.min.js', '/views/default.js'));

			$nav = array(
				'Dashboard' => '/pms',
				'Service Providers' => NULL,
				'Appointments' => '/pms/appointments',
			);

			$sub_nav = array(
				'Service Providers' => array(
					'Search' => '/pms/service-providers/search',
					'Index' => '/pms/service-providers/index',
				),
			);
		}
		else
		{
			$nav = array();
			$sub_nav = array();
		}

		$this->layout->set_nav($nav);
		$this->layout->set_sub_nav($sub_nav);

		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));

		$this->output->enable_profiler(ENVIRONMENT == 'development' && $this->input->get('profiler'));
	}

	/**
	 * Get the SVN revision deployed, or generate ID on development. Stores data in config
	 */
	private function _set_version()
	{
		if (ENVIRONMENT === 'development')
		{
			// Locally, just use time
			$num = time() . "D";
		}
		else
		{
			// Get version number from file
			$contents = file_get_contents('../.site_revision', FALSE, NULL, -1, 8);
			$num = $contents;
			$num = (strlen($num) < 8) ? time() : $num;
		}

		$this->config->set_item('version', $num);
	}
}
