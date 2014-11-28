<?php

class Service_providers extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_pms_logged_in();
		$this->layout->set_title('Service Providers');
		$this->layout->set_breadcrumb('Service Providers', '/pms/service-providers');

		$this->load->model(array('model_service_providers', 'postcode_model', 'appointments_model'));
		$this->load->helper('array');
		$this->load->config('datasets');

		// Remove expired reserved appointment bookings
		$this->appointments_model->prune();
	}




	/**
	 * Listing of all service providers
	 *
	 * @param int $page		Page number for pagination
	 */
	public function index($page = 0)
	{
		$total = $this->model_service_providers->get_total_service_providers();

		$this->load->library('pagination');

		$config['base_url'] = '/pms/service-providers/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$this->data['service_providers'] = $this->model_service_providers->get_service_providers($page, $config['per_page']);

		if ($total == 1)
		{
			redirect('/pms/service-providers/set/' . $this->data['service_providers'][0]['id']);
		}

		$this->data['total'] = ($this->data['service_providers'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['service_providers'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'];

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		# load pct model
		$this->load->model('model_pcts');

		# get list of pcts
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_view_script('pms/service_providers/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * Search page for searching for providers based on a postcode.
	 * Gateway to provider schedule and appointment booking
	 */
	function search()
	{
		$post_code = $this->input->get('post_code');

		$this->data['results'] = array();
		$this->data['total'] = 0;
		$this->data['lat'] = 0;
		$this->data['lng'] = 0;

		if ( ! empty($post_code))
		{
			// Get coords from this postcode
			$coords = $this->postcode_model->lookup($post_code);

			if (is_array($coords))
			{
				$lat = $coords['pc_lat'];
				$lng = $coords['pc_lng'];

				// Appointment options for all SPs
				$this->data['ao'] = $this->appointments_model->get_all_options();

				// Search results
				$this->data['results'] = $this->model_service_providers->geo_search($lat, $lng);

				$this->data['total'] = count($this->data['results']);

				// Centre point for map - postcode searched for
				$this->data['lat'] = $lat;
				$this->data['lng'] = $lng;

				// Store post code in session - retrieved later on other pages
				$this->session->set_userdata('search_post_code', $post_code);
			}

		}

		$this->layout->set_css(array('../scripts/leaflet/leaflet.css', '../scripts/leaflet/MarkerCluster.css', '../scripts/leaflet/MarkerCluster.Default.css'));
		$this->layout->set_javascript(array('/leaflet/leaflet-src.js', '/leaflet/leaflet.markercluster.js', '/views/pms/maps.js', '/views/pms/schedule.js'));

		$this->layout->set_view_script('pms/service_providers/search');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




	/**
	 * AJAX: response page for provider schedule
	 *
	 * @param int $sp_id		Service provider ID
	 * @param string $start		Now or next for 7 days from now, or the 7 days following 7 days from now
	 */
	function schedule($sp_id = 0, $start = 'now')
	{
		$this->data['sp'] = $this->model_service_providers->get_service_provider($sp_id);

		// Set start date as now or next week
		if ($start === 'now')
		{
			$start_date = time();
			$this->data['title'] = 'This week';
		}
		elseif ($start === 'next')
		{
			$start_date = strtotime('+7 days');
			$this->data['title'] = 'Next week';
		}

		// Get the options
		$this->data['ao'] = $this->appointments_model->get_options($sp_id);

		$ao_length = $this->data['ao'][1]['ao_length'];

		// Date range for week
		$start_date_format = date('Y-m-d', $start_date);
		$end_date_format = date('Y-m-d', strtotime('+7 days', $start_date));

		$week_start = new DateTime($start_date_format);
		$week_end = new DateTime($end_date_format);
		$period_interval = new DateInterval('P1D');
		$this->data['days'] = new DatePeriod($week_start, $period_interval, $week_end);

		// First & last appointment time across all days for SP
		$first_last_times = $this->appointments_model->get_first_last_appt_times($sp_id);

		// Time range during the day
		$first_appt_time = new DateTime($first_last_times['first']);
		$last_appt_time = new DateTime($first_last_times['last']);
		$last_appt_time = $last_appt_time->modify('+' . $ao_length . ' minutes');
		$period_interval = new DateInterval('PT' . $ao_length . 'M');

		// Available time slots
		$this->data['slots'] = new DatePeriod($first_appt_time, $period_interval, $last_appt_time);

		// Appointment count for times/dates within the range
		$this->data['appt_count'] = $this->appointments_model->count_within_range($sp_id, $start_date_format, $end_date_format);

		// Start (either now or next, 7 days)
		$this->data['start'] = $start;

		// Rescheduling appointment?
		if ($this->input->get('action') === 'reschedule')
		{
			// Rescheduling appointment. Get info about it first
			$reschedule_a_id = $this->input->get('a_id');
			$appointment = $this->appointments_model->get($reschedule_a_id);
			$this->data['reschedule'] = $appointment;
		}

		$this->load->vars($this->data);
		$this->load->view('pms/service_providers/schedule');
	}




	/**
	 * Update information for a service provider
	 */
	public function set($sp_id = 0)
	{
		$service_provider = $this->model_service_providers->get_service_provider($sp_id);

		if ( ! $service_provider)
		{
			show_error('Could not find service provider ID ' . $sp_id);
		}

		if ($this->input->post())
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', '', 'required');
			$this->form_validation->set_rules('post_code', '', 'required');
			$this->form_validation->set_rules('location', '', '');
			$this->form_validation->set_rules('location_other', '', '');
			$this->form_validation->set_rules('department', '', '');
			$this->form_validation->set_rules('venue', '', '');
			$this->form_validation->set_rules('telephone', '', '');

			if ($this->form_validation->run())
			{
				$data = array(
					'name' => $this->input->post('name'),
					'post_code' => format_postcode($this->input->post('post_code')),
					'location' => $this->input->post('location'),
					'location_other' => $this->input->post('location_other'),
					'department' => $this->input->post('department'),
					'venue'=> $this->input->post('venue'),
					'telephone' => $this->input->post('telephone'),
					'pct' => $this->input->post('pct'),
				);

				$this->model_service_providers->simple_update($sp_id, $data);

				// update cache
				$this->model_service_providers->get_service_providers_select(0);

				$this->session->set_flashdata('action', 'Service provider details have been updated.');
				redirect('/pms/service-providers/set/' . $sp_id);
			}
		}

		$title = 'Edit service provider';

		$this->data['groups'] = $this->model_service_providers->get_groups_select();

		$this->load->model('model_pcts');
		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		$this->data['service_provider'] = $service_provider;

		$this->data['title'] = $title;

		$this->data['locations'] = config_item('provider_locations');

		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/pms/service_providers/set.js'));
		$this->layout->set_title($title);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_view_script('pms/service_providers/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}




}