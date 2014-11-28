<?php

class Claims extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->model('model_claims');
		$this->load->model('model_service_providers');
		$this->load->model('model_export_schemas');

		$this->load->config('datasets');

		$this->layout->set_title('Claims');
		$this->layout->set_breadcrumb('Claims', '/admin/claims');
	}


	function index($page = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if(@$_POST['set_status'])
		{
			if(@$_POST['claims'])
			{

				foreach($_POST['claims'] as $claim)
				{
					/* explode pipes to get monitoring form id and claim type */
					$claim = explode('|', $claim);

					$this->model_claims->set_claim_status($claim[0], $claim[1], $this->input->post('set_status'));
				}

				$this->_rejected_claims();

				$total_claims = count($_POST['claims']);

				$this->session->set_flashdata('action', $total_claims . ($total_claims == 1 ? ' claim' : ' claims') . ' set as "' . $this->input->post('set_status') . '"');
			}

			redirect('/admin/claims');
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		$total = $this->model_claims->get_total_claims(@$_GET['sp_id']);

		$this->load->library('pagination');
		$config['base_url'] = '/admin/claims/index/';
		$config['total_rows'] = $total;
		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);
		$config['uri_segment'] = 4;
		$config['suffix'] = '?' . @http_build_query($_GET);

		$this->pagination->initialize($config);

		$this->data['claims'] = $this->model_claims->get_claims(@$_GET['sp_id'], 0, $page, $config['per_page']);

		$this->data['total'] = ($this->data['claims'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['claims'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;date_from=' . $_GET['date_from'] . '&amp;date_to=' . $_GET['date_to'] . '&amp;claim_type=' . @$_GET['claim_type'] . '&amp;status=' . @$_GET['status'] . '&amp;sp_id=' . @$_GET['sp_id'] . '&amp;pct_id=' . @$_GET['pct_id'] . '&amp;location=' . @$_GET['location'];

		$this->data['claim_types'] = array('Initial','4 week','12 week');

		$this->data['status'] = array('Pending','Passed to finance','Rejected');

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['service_providers_select'] = $this->model_service_providers->get_service_providers_select();

		# load pct model
		$this->load->model('model_pcts');

		# get list of pcts
		$this->data['pcts_select'] = $this->model_pcts->get_pcts_select();

		$this->data['locations'] = config_item('provider_locations');

		$this->data['export_schemas'] = $this->model_export_schemas->get_export_schemas('monitoring_form_claims');

		$this->layout->set_javascript('/views/admin/claims/index.js');
		$this->layout->set_view_script('/admin/claims/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function csv()
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		if( ! parse_date(@$_GET['date_to']))
			$_GET['date_to'] = date('d/m/Y', time());

		if( ! parse_date(@$_GET['date_from']))
			$_GET['date_from'] = date('d/m/Y', strtotime('-7 days'));

		if(@$_GET['export'])
		{
			$export_schema = $this->model_export_schemas->get_export_schema(@$_GET['schema_id'], 'monitoring_form_claims');

			$i = 0;

			$csv[$i] = array();

			foreach($export_schema['export_schema'] as $field_name => $description)
			{
				$csv[$i][] = $description;
			}

			if($claims = $this->model_claims->get_csv(@$_GET['sp_id']))
			{
				//print_r($claims); die();
				foreach($claims as $claim)
				{
					$i++;

					foreach($export_schema['export_schema'] as $field_name => $description)
					{
						$csv[$i][] = $claim[$field_name];
					}
				}
			}

			$file_name = md5($this->session->userdata('admin_id') . time()) . '.csv';

			$file_dir = $this->config->config['csv_dir'] . $file_name;

			if($file = fopen($file_dir, "w"))
			{
				foreach ($csv as $line)
				{
					fputcsv($file, $line);
				}

				if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
				{
					header('Content-Type: text/csv;');
					header('Content-Disposition: attachment; filename="'.$file_name.'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header("Content-Transfer-Encoding: none");
					header('Pragma: public');
				}
				else
				{
					header('Content-Type: text/csv;');
					header('Content-Disposition: attachment; filename="'.$file_name.'"');
					header("Content-Transfer-Encoding: none");
					header('Expires: 0');
					header('Pragma: no-cache');
				}

				echo file_get_contents($file_dir);

				fclose($file);

				unlink($file_dir);
			}
		}
	}


	protected function _rejected_claims()
	{
		/* if rejecting claims, check to see if an email has to be sent */
		if($this->input->post('set_status') == 'Rejected')
		{
			$claim_options = $this->model_options->get_pct_option('claim_options', $this->pct_id);

			if($claim_options['rejected_claims_email']['enabled'])
			{
				foreach($_POST['claims'] as $claim)
				{
					/* explode pipes to get monitoring form id and claim type */
					$claim = explode('|', $claim);

					/* retrieves details information about claim */
					$claim_information = $this->model_claims->get_claim($claim[0], $claim[1]);

					$service_provider_claims[$claim_information['sp_id']][] = $claim_information;
				}
			}

			$this->load->library('email');

			foreach($service_provider_claims as $sp_id => $claims)
			{
				if($service_provider = $this->model_claims->get_service_provider($sp_id))
				{
					/* construct email */
					$total_claims = count($claims);

					$email['subject'] = ($total_claims == 1 ? '1 claim has' : $total_claims . ' claims have') . ' been rejected.';

					$email['message'] = '<p>Dear ' . $service_provider['fname'] . '.</p>
										<p>' . nl2br($claim_options['rejected_claims_email']['note']) . '</p>
										<table class="results">
											<tr class="order">
												<th>Monitoring form ID</th>
												<th>Claim type</th>
												<th>Date of claim</th>
												<th>Date of rejection</th>
											</tr>';

					foreach($claims as $claim)
					{
						$email['message'] .= '<tr>
												<td>#' . $claim['mf_id'] . '</td>
												<td>' . $claim['claim_type'] . '</td>
												<td>' . $claim['date_of_claim_format'] . '</td>
												<td>' . $claim['status_datetime_set_format'] . '</td>
											</tr>';
					}

					$email['message'] .= '</table>';

					$this->email->clear();
					$this->email->from('no-reply@openquits.net', 'Call it Quits');
					$this->email->to($service_provider['email']);
					$this->email->subject($email['subject']);
					$this->email->message($email['message']);
					$this->email->send();
				}
			}

		}
	}

}
