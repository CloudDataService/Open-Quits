<?php

class Help extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->layout->set_title('Help');
		$this->layout->set_breadcrumb('Help', '/service-providers/help');
	}


	function index()
	{
		$this->layout->set_view_script('service_providers/help/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function resources($page = 0)
	{
		$this->load->model('model_resources');

		$total_resources = $this->model_resources->get_total_resources();

		$this->load->library('pagination');

		$config['base_url'] = '/service-providers/help/resources/';

		$config['total_rows'] = $total_resources;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$this->data['resources'] = $this->model_resources->get_resources($page, $config['per_page']);

		$this->data['total'] = ($this->data['resources'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['resources'])) . ' of ' . $total_resources . '.' : '0 results');

		$this->data['sort'] = '&amp;sort=' . ($_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'];

		$this->data['page'] = $page;

		$this->layout->set_title('Resources');
		$this->layout->set_breadcrumb('Resources');
		$this->layout->set_view_script('service_providers/help/resources');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	// support function to list zen desk tickets
	function support() {
		// load model
		$this->load->model('model_zendesk');

		// set data
		$this->layout->set_title('Support');
		$this->layout->set_breadcrumb('Support');

		// set view
		$this->layout->set_view_script('service_providers/help/zendesk');

		// grab ticket results
		$this->data['tickets'] = $this->model_zendesk->grab_tickets();

		// initialise
		$this->load->vars($this->data);

		// load view
		$this->load->view('/layouts/default');
	}

	// view ticket
	function viewticket($tid) {
		// is tid empty?
		if(empty($tid)) {
			// redirect
			redirect('/service-providers/help/support');
		}

		// load model
		$this->load->model('model_zendesk');

		// do i own this ticket?
		if(!$this->model_zendesk->is_my_ticket($tid)) {
			// isn't, redirect
			redirect('/service-providers/help/support');
		}

		// set data
		$this->layout->set_title('Support');
		$this->layout->set_breadcrumb('Support');

		// set view
		$this->layout->set_view_script('service_providers/help/zendesk_ticket');

		// grab ticket results
		$this->data['ticket'] = $this->model_zendesk->grab_ticket($tid);
		$this->data['ticket_id'] = $tid;
		$this->data['ticket_status'] = @$this->session->userdata['tickets'][$tid]['status'];

		// set javascript
		$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/help/viewticket.js'));

		// initialise
		$this->load->vars($this->data);

		// load view
		$this->load->view('/layouts/default');
	}

	// add new ticket
	function newticket() {
		// load model
		$this->load->model('model_zendesk');

		// set data
		$this->layout->set_title('New ticket > Support');
		$this->layout->set_breadcrumb('Support');

		// are we posting this ticket?
		if(!empty($_POST)) {
			// is posting - are all the required fields set?
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('problem', 'Problem', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
			$this->form_validation->set_rules('subject', 'Subject', 'required');

			// did validation work?
			if($this->form_validation->run() == false) {
				// failed, show form again
				// set view
				$this->layout->set_view_script('service_providers/help/zendesk_newticket');

				// load view
				$this->load->view('/layouts/default');
			} else {
				// success
				// append userdata
				$_POST['ud_email'] 		= $this->session->userdata['email'];
				$_POST['ud_name'] 		= $this->session->userdata['fname'] . ' ' . $this->session->userdata['sname'];
				$_POST['ud_provider'] 	= $this->session->userdata['sp_name'];

				// post ticket
				$this->model_zendesk->add_ticket($_POST);
			}
		} else {
			$this->layout->set_javascript(array('/plugins/jquery.validate.js', '/views/service_providers/help/newticket.js'));
			// isn't posting, show form
			// set view
			$this->layout->set_view_script('service_providers/help/zendesk_newticket');

			// load view
			$this->load->view('/layouts/default');
		}

	}

	// update ticket
	public function postticket($tid) {
		// is it being posted?
		$this->load->model('model_zendesk');

		// do i own this ticket?
		if(!$this->model_zendesk->is_my_ticket($tid)) {
			// isn't, redirect
			redirect('/service-providers/help/support');
		}

		// is post set?
		if(!empty($_POST)) {
			// is set
			$this->load->library('form_validation');

			// set ruels
			$this->form_validation->set_rules('ticket_response', 'Ticket Response', 'required');

			// are they valid ?
			if($this->form_validation->run() == false) {
				// failed, set flash
				$this->session->set_flashdata('action', 'Unable to update ticket');
			} else {
				// post
				$this->model_zendesk->update_ticket($tid, $_POST['ticket_response']);

				// set flashdata
				$this->session->set_flashdata('action', 'Updated support ticket');

				// redirect
				redirect('/service-providers/help/viewticket/' . $tid);
			}
		} else {
			// redirect away
			redirect('/service-providers/help/support');
		}
	}
}
