<?php

class Claims extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->load->model('model_claims');

		$this->layout->set_title('Claims');
		$this->layout->set_breadcrumb('Claims', '/service-providers/claims');
	}


	function index($page = 0)
	{
		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		$total = $this->model_claims->get_total_claims($this->session->userdata('sp_id'));

		$this->load->library('pagination');

		$config['base_url'] = '/service-providers/claims/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$config['suffix'] = '?' . http_build_query($_GET);

		$this->pagination->initialize($config);

		$this->data['claims'] = $this->model_claims->get_claims($this->session->userdata('sp_id'), 0, $page, $config['per_page']);

		$this->data['total'] = ($this->data['claims'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['claims'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;date_from=' . $_GET['date_from'] . '&amp;date_to=' . $_GET['date_to'] . '&amp;claim_type=' . @$_GET['claim_type'] . '&amp;status=' . @$_GET['status'];
		$this->data['claim_types'] = array('Initial','4 week','12 week');

		$this->data['status'] = array('Pending','Passed to finance','Rejected');

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_javascript('/views/service_providers/claims/index.js');
		$this->layout->set_view_script('service_providers/claims/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function csv()
	{
		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		if(@$_GET['export'])
		{
			if($claims = $this->model_claims->get_csv($this->session->userdata('sp_id')))
			{
				$csv[] = array("Monitoring form ID", "Date of claim", "Service provider", "Advisor code", "Provider code", "Cost code", "Claim type", "Cost", "Status");

				foreach($claims as $claim)
				{
					$csv[] = array($claim['monitoring_form_id'], $claim['date_of_claim_format'], $claim['sp_name'], $claim['advisor_code'], $claim['provider_code'], $claim['cost_code'], $claim['claim_type'], $claim['cost'], $claim['status']);
				}

				$file_name = md5($this->session->userdata('sps_id') . time()) . '.csv';

				$file_dir = $this->config->config['csv_dir'] . $file_name;

				$file = fopen($file_dir, "w");

				foreach ($csv as $line)
				{
					fputcsv($file, $line);
				}

				header('Content-Transfer-Encoding: none');
				header('Content-Type: text/csv;');
				header('Content-Disposition: attachment; filename="' . $file_name . '"');

				echo file_get_contents($file_dir);

				fclose($file);

				unlink($file_dir);
			}

		}
	}

}
