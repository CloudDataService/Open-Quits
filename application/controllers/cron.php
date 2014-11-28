<?php

class Cron extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		// if this is not the same server as the one that call it quits is on.
		/*if($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'])
		{
			// quit now
			exit;
		}*/

		$this->load->model('model_cron');
	}


	function csv()
	{
		$this->load->model(array('model_pcts', 'model_export_schemas', 'model_service_providers', 'model_claims'));
		$this->load->library('email');

		//$config['protocol'] = 'smtp';
		//$config['smtp_host'] = 'localhost';
		//$config['smtp_port'] = 25;
		//$this->email->initialize($config);

		// Get all PCTs
		$pcts = $this->model_pcts->get_pcts_select();

		// Timestamp for comparisons
		$current_time = time();

		foreach ($pcts as $pct)
		{
			// Claim options for this PCT
			$claim_options = $this->model_options->get_pct_option('claim_options', $pct['id']);

			// Put PCT ID in GET array for model to retrieve
			$_GET['pct_id'] = $pct['id'];

			/* if either automatic pass to finance or email is on, get today's claim */
			if ( ! $claim_options['automatic_pass_to_finance'] || ! $claim_options['automatic_email'])
			{
				log_message('debug', "cron: csv(): PCT {$pct['id']}: Auto pass to finance or auto emails are off. Skipping.");
				continue;
			}

			$interval_time = strtotime('+' . (int) $claim_options['interval'] . ' day', $claim_options['time_of_last_process']);
			log_message('debug', "cron: csv(): PCT {$pct['id']}: Current: $current_time / Interval: $interval_time");

			if ($current_time <= $interval_time)
			{
				continue;
			}

			/* get the set export schema */
			$export_schema = $this->model_export_schemas->get_export_schema($claim_options['export_schema_id'], 'monitoring_form_claims');

			$_GET['date_to'] = date('d/m/Y', $current_time);
			$_GET['date_from'] = date('d/m/Y', $claim_options['time_of_last_process']);


			if ($claim_options['automatic_email'])
			{
				$this->email->clear(TRUE);
				$this->email->from('no-reply@openquits.net', 'Call it Quits');
				$this->email->to($claim_options['automatic_emails']);
				//$this->email->to('craig@clouddataservice.co.uk');
				$this->email->subject('Claims CSV ' . $_GET['date_from'] . ' - ' . $_GET['date_to']);
				$this->email->message('');

				log_message('debug', "cron: csv(): PCT {$pct['id']}: Email recipients: " . $claim_options['automatic_emails']);
			}

			$file_dirs = array();

			$groups = $this->model_service_providers->get_groups_select();

			foreach ($groups as $group)
			{
				$claims_by_location = $this->model_claims->get_cron_csv($group['id']);

				foreach ($claims_by_location as $location => $claims)
				{
					log_message('debug', "cron: csv(): PCT {$pct['id']}: Group {$group['name']}, Location {$location}, " . count($claims) . " claim(s).");
					$file_dirs[] = $this->_parse_csv($claims, $claim_options, $location, $group['name'], $export_schema, $pct['pct_name']);
				}

			}

			if ($claim_options['automatic_email'])
			{
				log_message('debug', "cron: csv(): PCT {$pct['id']}: Sending email!");
				$this->email->send();
				//echo $this->email->print_debugger();
			}

			foreach($file_dirs as $file_dir)
			{
				//unlink($file_dir);
			}

			log_message('debug', "cron: csv(): PCT {$pct['id']}: Updating last process time to: " . $claim_options['time_of_last_process']);

			// set time of last process to now
			$claim_options['time_of_last_process'] = time();

			// save options
			$this->model_options->set_option('claim_options', $claim_options, $pct['id']);

		}  // end foreach PCTs

	}  // end function csv


	function get_old_csv()
	{
		$this->load->model(array('model_pcts', 'model_export_schemas', 'model_service_providers', 'model_claims'));
		$this->load->library('email');

		//$config['protocol'] = 'smtp';
		//$config['smtp_host'] = 'localhost';
		//$config['smtp_port'] = 25;
		//$this->email->initialize($config);

		// Get all PCTs
		$pcts = $this->model_pcts->get_pcts_select();

		// Timestamp for comparisons
		$current_time = time();

		foreach ($pcts as $pct)
		{
			//only run for sunderland (for get_old)
			echo '<br />'; print_r($pct);
			if($pct['id'] != 7)
			{
				continue;
			}

			// Claim options for this PCT
			$claim_options = $this->model_options->get_pct_option('claim_options', $pct['id']);

			// Put PCT ID in GET array for model to retrieve
			$_GET['pct_id'] = $pct['id'];


			/* get the set export schema */
			$export_schema = $this->model_export_schemas->get_export_schema($claim_options['export_schema_id'], 'monitoring_form_claims');

			//$_GET['date_to'] = date('d/m/Y', $current_time);
			//$_GET['date_from'] = date('d/m/Y', $claim_options['time_of_last_process']);
			//Override the dates (for get_old)
			$_GET['date_to']   = '30/04/2013';
			$_GET['date_from'] = '30/03/2013';


				$this->email->clear(TRUE);
				$this->email->from('no-reply@openquits.net', 'Call it Quits');
				//$this->email->to($claim_options['automatic_emails']);
				//send it to a specified email (for get_old)
				$this->email->to('gregory@clouddataservice.co.uk');
				$this->email->subject('Claims CSV ' . $_GET['date_from'] . ' - ' . $_GET['date_to']);
				$this->email->message('Attached are the csv files.');


			//dont pass items to finance (theyll already be there), (for get_old)
			$claim_options['automatic_pass_to_finance'] = FALSE;
			$old_csv = TRUE; //alters the sql to pretend claims are pending

			$file_dirs = array();

			$groups = $this->model_service_providers->get_groups_select();
			foreach ($groups as $group)
			{
				$claims_by_location = $this->model_claims->get_cron_csv($group['id'], $old_csv);

				foreach ($claims_by_location as $location => $claims)
				{
					$file_dirs[] = $this->_parse_csv($claims, $claim_options, $location, $group['name'], $export_schema, $pct['pct_name']);
					echo '+attach+';
				}

			}

			if ($claim_options['automatic_email'])
			{
				$this->email->send();
				//echo $this->email->print_debugger();
			}

			foreach($file_dirs as $file_dir)
			{
				//unlink($file_dir);
			}

		}  // end foreach PCTs
		die('done');
	}  // end function recover_csv


	protected function _parse_csv($claims, $claim_options, $location, $group_name, $export_schema, $pct_name)
	{
		$i = 0;

		$csv[$i] = array();

		foreach ($export_schema['export_schema'] as $field_name => $description)
		{
			$csv[$i][] = $description;
		}

		if ($claims)
		{
			foreach ($claims as $claim)
			{
				$i++;

				foreach ($export_schema['export_schema'] as $field_name => $description)
				{
					$csv[$i][] = $claim[$field_name];
				}

				if($claim_options['automatic_pass_to_finance'])
					$this->model_claims->set_claim_status($claim['monitoring_form_id'], $claim['claim_type'], 'Passed to finance');
					//echo '';
			}
		}

		$file_name = $pct_name . '-' . $group_name . '-' . $location . '-' . time() . '.csv';

		$file_dir = $this->config->config['csv_dir'] . $file_name;

		$file = fopen($file_dir, "w");

		foreach ($csv as $line)
		{
			fputcsv($file, $line);
		}

		if($claim_options['automatic_email'])
		{
			$this->email->attach($file_dir);
		}

		fclose($file);

		return $file_dir;
	}


	function sms()
	{
		$this->load->model('model_pcts');
		$this->load->library('sms');

		// Get all PCTs
		$pcts = $this->model_pcts->get_pcts_select();

		foreach ($pcts as $pct)
		{
			log_message('debug', "cron: sms(): PCT {$pct['id']}: Processing.");

			$sms_options = $this->model_options->get_pct_option('sms_options', $pct['id']);

			if ( ! $sms_options['enabled'])
			{
				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS not enabled.");
				continue;
			}

			// Set PCT ID for model to retrieve
			$_GET['pct_id'] = $pct['id'];

			if ($sms_options['texts']['welcome']['enabled'])
			{
				$clients = $this->model_cron->get_sms_interval('0 DAY');

				foreach ($clients as $client)
				{
					$this->sms->send($client['tel_mobile'], $this->sms->str_replace($sms_options['texts']['welcome']['value'], $client));
				}
			}

			if ($sms_options['texts']['week_1']['enabled'])
			{
				$clients = $this->model_cron->get_sms_interval('1 WEEK');

				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: 1 week. SQL: " . $this->db->last_query());
				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: 1 week. " . count($clients) . " clients.");

				foreach ($clients as $client)
				{
					$this->sms->send($client['tel_mobile'], $this->sms->str_replace($sms_options['texts']['week_1']['value'], $client));
				}
			}

			if ($sms_options['texts']['week_2']['enabled'])
			{
				$clients = $this->model_cron->get_sms_interval('2 WEEK');

				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: 2 week. SQL: " . $this->db->last_query());
				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: 2 week. " . count($clients) . " clients.");

				foreach ($clients as $client)
				{
					$this->sms->send($client['tel_mobile'], $this->sms->str_replace($sms_options['texts']['week_2']['value'], $client));
				}
			}

			if ($sms_options['texts']['week_3']['enabled'])
			{
				$clients = $this->model_cron->get_sms_interval('3 WEEK');

				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: 3 week. SQL: " . $this->db->last_query());
				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: 3 week. " . count($clients) . " clients.");

				foreach ($clients as $client)
				{
					$this->sms->send($client['tel_mobile'], $this->sms->str_replace($sms_options['texts']['week_3']['value'], $client));
				}
			}

			if ($sms_options['texts']['follow_up_reminder']['enabled'])
			{
				$clients = $this->model_cron->get_sms_follow_up_reminder(4);

				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: follow up 4. SQL: " . $this->db->last_query());
				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: follow up 4. " . count($clients) . " clients.");

				foreach ($clients as $client)
				{
					$this->sms->send($client['tel_mobile'], $this->sms->str_replace($sms_options['texts']['follow_up_reminder']['value'], $client));
				}
			}

			if ($sms_options['texts']['follow_up_12_reminder']['enabled'])
			{
				$clients = $this->model_cron->get_sms_follow_up_reminder(12);

				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: follow up 12. SQL: " . $this->db->last_query());
				log_message('debug', "cron: sms(): PCT {$pct['id']}: SMS: follow up 12. " . count($clients) . " clients.");

				foreach ($clients as $client)
				{
					$this->sms->send($client['tel_mobile'], $this->sms->str_replace($sms_options['texts']['follow_up_12_reminder']['value'], $client));
				}
			}

		}  // endforeach for PCTs

	}  // end sms function


	function repeat_clients()
	{
		$repeat_clients_last_execute = $this->model_options->get_option('repeat_clients_last_execute');

		$repeat_clients = $this->model_cron->get_repeat_clients($repeat_clients_last_execute);

		$html = '<style type="text/css">
				table {
				}
				table tr th, table tr td {
					font-family:Arial, Helvetica, sans-serif;
					font-size:13px;
					text-align:center;
					padding:3px;
				}
				</style>';

		$html .= '<table>';

		$html .= '<tr><th>Client name</th><th>Date of birth</th><th>Total engagements</th></tr>';

		foreach($repeat_clients as $client)
		{
			$html .= '<tr><td>' . ucfirst(strtolower($client['fname'])) . ' ' . ucfirst(strtolower($client['sname'])) . '</td><td>' . $client['date_of_birth_format'] . '</td><td>' . $client['total_engagements'] . '</td></tr>';
		}

		$html .= '</table>';

		echo $html;
	}




	/**
	 * Handle the sending of all SMS messages from within the queue.
	 *
	 * The "queue" is all the messages in the
	 */
	public function bulk_sms()
	{
		// Load essential things
		$this->load->model('sms_model');
		$this->load->library('sms');

		// Find out how many messages to process
		$limit = $this->config->item('bulk_sms_limit');

		log_message('debug', "cron: bulk_sms(): Getting $limit unsent messages.");

		// Get messages to send
		$messages = $this->sms_model->get_unsent($limit);

		log_message('debug', "cron: bulk_sms(): Got " . count($messages) . " messages to be sent.");

		if (count($messages) === 0 )
		{
			echo "No messages to send.";
			return;
		}

		foreach ($messages as $message)
		{
			log_message('debug', "cron: bulk_sms(): SMS ID {$message['s_id']}.");

			$response = $this->sms->send_sms($message['s_to_number'], $message['s_message']);

			if ($response !== FALSE)
			{
				log_message('debug', "cron: bulk_sms(): Send successful. TextMagic Message ID {$response['message_id']}.");

				// Update status to marked as sent.
				// We just give it a status of 1 for now. The API will tell us more later.
				$this->sms_model->update($message['s_id'], array(
					's_message_id' => $response['message_id'],
					's_status' => 1,
				));
			}
			else
			{
				log_message('debug', "cron: bulk_sms(): Send failed.");
				$this->sms_model->update($message['s_id'], array('s_status' => 2));
			}
		}

		// Get the balance and update it
		$balance = $this->sms->get_balance();

		$sql = 'UPDATE options
				SET option_value = ?
				WHERE option_name = "total_sms_sent"';
		$this->_CI->db->query($sql, array($balance));

		echo "Sent " . count($messages) . " message(s).";
	}


}
