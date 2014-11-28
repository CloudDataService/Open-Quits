<?php

// zendesk class - api reference
class Model_zendesk extends CI_Model {

	// add variables
	//private $base_url = 'https://vitalservice.zendesk.com/';
	public $author_id = '28764967';
	public $last_http_request_code;
	public $timed_out = false;
	public $status_lookup = array(0 => 'New',
								  1 => 'Open',
								  2 => 'Pending',
								  3 => 'Solved',
								  4 => 'Closed');

	// initialise
	function __construct() {
		// inherit
		parent::__construct();

		// any tickets set?
		if(!empty($this->session->userdata['tickets']) && !is_array($this->session->userdata['tickets'])) {
			// unserialize user data
			$tickets = unserialize($this->session->userdata['tickets']);

			// reverse the array (so last one is on top)
			$this->session->userdata['tickets'] = array_reverse($tickets, true);
		}
	}

	// produce curl header
	private function api_produce_header($header_array = false, $behalf_of = false) {
		// do we need to set header array?
		if($header_array !== false) {
			// add to new array
			$array1 = $header_array;
		}

		// do we need to set on behalf of array?
		if($behalf_of !== false) {
			// create array from email address
			$array2 = array("X-On-Behalf-Of: " . $behalf_of);
		}

		// are both of these set?
		if(isset($array1, $array2)) {
			// merge them together
			$array = array_merge($array1, $array2);
		} else {
			// send only 1
			if(isset($array1)) {
				// only send header array
				$array = $array1;
			} elseif(isset($array2)) {
				// send only behalf_of array
				$array = $array2;
			} else {
				// send padding header
				$array = array("No-Header: true");
			}
		}

		// return
		return $array;
	}

	// make an api request
	private function api_request($url, $on_behalf_of = false, $data = false, $put = false) {

		// CR 2012-10-31: Do not make API calls any more due to not using ZenDesk
		$this->timed_out = true;
		return array('error' => 'Not implemented');


		// initialise curl
		$curl = curl_init();

		// set url
		curl_setopt($curl, CURLOPT_URL, $url);

		// set authentication details
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "bot@sotw.nhs.uk:callitquits");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

		// config
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		// are we sending data
		if($data !== false) {
			// set headers
			// is this on behalf of someone?
			if($on_behalf_of !== false) {
				// use behalf of
				$header = $this->api_produce_header(array("Content-Type: application/xml", "Content-Length: " . strlen($data)), $on_behalf_of);
			} else {
				// just use a post header
				$header = $this->api_produce_header(array("Content-Type: application/xml", "Content-Length: " . strlen($data)));
			}

			// set header
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_HEADER, true);

			// pass data
			// are we using put or post?
			if($put) {
				// use put
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
			} else {
				// use post
				curl_setopt($curl, CURLOPT_POST, 1);
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		} else {
			if($on_behalf_of !== false) {
				// define normal headers
				$header = $this->api_produce_header(false, $on_behalf_of);

				// set header
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			}
		}

		// execute and return result
		$cdata = curl_exec($curl);

		// update http request code
		$this->last_http_request_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		// success?
		if(!curl_errno($curl)) {
			// return result
			return $cdata;
		} else {
			// api failed
			$this->timed_out = true;

			// failed, return error array
			return array('error' => curl_error($curl));
		}
	}

	// get tickets
	function grab_tickets() {
		// are the tickets an array?
		if(@is_array($this->session->userdata['tickets'])) {
			// is - loop through
			foreach($this->session->userdata['tickets'] as $key=>$ticket) {
				// is the ticket more than 30 days old?
				if((time() - $ticket['date_created']) >= 2592000) {
					// remove it
					unset($this->session->userdata['tickets'][$key]);

					// set has changed
					$has_changed = true;
				} else {
					// continue processing
					// is the last update more than 30 mins ago?
					if((((time() - $ticket['date_last_updated']) >= 1800) && (($ticket['status'] != 'Solved') || ($ticket['status'] != 'Closed')) && !$this->timed_out)) {
						// has changed - get new data
						$api_ticket = $this->api_request($this->base_url . 'requests/' . $ticket['ticket_id'] . '.xml');

						// did we get a success request?
						if($this->last_http_request_code == 404) {
							// delete
							unset($this->session->userdata['tickets'][$key]);

							// has changed
							$has_changed = true;
						} else {
							// continue processing
							// load into simplexml
							$array_ticket = (array)new SimpleXMLElement($api_ticket);

							// compare against current ticket
							if($ticket['status'] != $this->status_lookup[$array_ticket['status-id']]) {
								// has changed
								$this->session->userdata['tickets'][$key]['status'] = $this->status_lookup[$array_ticket['status-id']];

								// set has changed
								$has_changed = true;
							}

							// many more replies?
							$replies = (count($array_ticket['comments']) - 1);

							// has this figure changed?
							if($ticket['replies'] != $replies) {
								// has changed
								$this->session->userdata['tickets'][$key]['replies'] = $replies;

								// update updated time stamp
								$this->session->userdata['tickets'][$key]['date_last_updated'] = time();

								// set has changed
								$has_changed = true;
							}

							// has been more than 30 mins anyway, auto update time
							$this->session->userdata['tickets'][$key]['date_last_updated'] = time();

							// set has changed
							$has_changed = true;
						}
					}
				}

				// has it changed?
				if(@$has_changed) {
					// update ticket
					$this->session->set_userdata('tickets', serialize(array_reverse($this->session->userdata['tickets'], true)));

					// now set the data to be unserialized
					$tickets = unserialize($this->session->userdata['tickets']);

					// reverse the array (so last one is on top)
					$this->session->userdata['tickets'] = array_reverse($tickets, true);

					// update in database
					$this->update_service_provider_tickets($this->session->userdata['sps_id'], array_reverse($this->session->userdata['tickets'], true));
				}

				// reset data
				unset($api_ticket, $has_changed, $array_ticket, $replies);
			}

			// count total tickets
			$request['data']['total_tickets'] = count($this->session->userdata['tickets']);

			// add tickets back into view
			$request['data']['tickets'] = $this->session->userdata['tickets'];

			// how many tickets
			if($request['data']['total_tickets'] < 1) {
				// remove whole ticket array
				$request['data']['tickets'] = false;
			}

			// return
			return $request;
		} else {
			// false
			return false;
		}
	}

	// add ticket
	function add_ticket($data) {
		// build description
		$final_desc = $data['description'];

		// add divider
		$final_desc .= "\n\n -------------- \n\n";

		// add provider information
		$final_desc .= 'Telephone: ' . @$data['contact_telephone'] . "\n" .
					   'Contact time: ' . @$data['contact_time'] . "\n" .
					   'Problem category: ' . $data['problem'] . "\n" .
					   'Name: ' . $data['ud_name'] . "\n" .
					   'Email: ' . $data['ud_email'] . "\n" .
					   'Service provider: ' . $data['ud_provider'] . "\n" .
					   'Browser: ' . $this->input->user_agent() . "\n";

		// TEMPORARY: Send email instead of logging ticket via the API
		$this->load->library('email');
		$this->email->from($data['ud_email'], $data['ud_name']);
		$this->email->to('support@vitalservice.co.uk');
		$this->email->subject('Ticket: Call it Quits: ' . $data['subject']);
		$this->email->message($final_desc);
		$sent = $this->email->send();


		// build up xml
		$xml = '<ticket>
					<subject>' . $data['subject'] . '</subject>
					<description>' . $final_desc . '</description>
				</ticket>';

		// call api to add
		$response = $this->api_request($this->base_url . 'requests.xml', false, $xml);

		// get a ticket id
		//if(preg_match("/Location: .*\/requests\/(\d*).xml/", $response, $matches)) {
		if ($sent) {
			// worked
			$ticket_id = uniqid();

			// save to the database
			if(@is_array($this->session->userdata['tickets'])) {
				// already has tickets in
				$array[$ticket_id] = array('ticket_id' 			=> $ticket_id,
										   'date_created' 		=> time(),
										   'date_last_updated' 	=> time(),
										   'status' 			=> 'New',
										   'replies' 			=> 0,
										   'subject' 			=> $data['subject']);

				// merge them together
				# $ar2 = array_merge($array, $this->session->userdata['tickets']);- doesn't preserve index keys
				$ar2 = $array + $this->session->userdata['tickets'];

				// pass to service provider
				//$this->update_service_provider_tickets($this->session->userdata['sps_id'], $ar2);

				// set flashdata
				$this->session->set_flashdata('action', 'Support ticket sent');

				// update session data
				$this->session->set_userdata('tickets', serialize($ar2));

				// redirect
				redirect('/service-providers/help/support');
			} else {
				// build new one
				$array = array();

				// add item
				$array[$ticket_id] = array('ticket_id' 			=> $ticket_id,
										   'date_created' 		=> time(),
										   'date_last_updated' 	=> time(),
										   'status' 			=> 'New',
										   'replies' 			=> 0,
										   'subject' 			=> $data['subject']);

				// pass to service provider
				$this->update_service_provider_tickets($this->session->userdata['sps_id'], $array);

				// set flashdata
				$this->session->set_flashdata('action', 'Support ticket sent');

				// update session data
				$this->session->set_userdata('tickets', serialize($array));

				// redirect
				redirect('/service-providers/help/support');
			}
		} else {
			// didn't work - no matches?
			$this->session->set_flashdata('action', 'Support ticket was unsuccessful');

			// redirect
			redirect('/service-providers/help/support');
		}
	}

	// update service provider tickets
	public function update_service_provider_tickets($pid, $array) {
		// generate sql
		$sql = 'UPDATE
					service_provider_staff
				SET
					tickets = ' . $this->db->escape(serialize($array)) . '
				WHERE
					id = ' . $this->db->escape((int)$pid) . '
				LIMIT
					1;';

		// execute
		$this->db->query($sql);
	}

	// grab ticket
	public function grab_ticket($tid) {
		// comments array
		$comments_array = array();

		// call api
		$api_ticket = $this->api_request($this->base_url . 'requests/' . $tid . '.xml');

		// is it an array
		if($this->last_http_request_code != '404' && !$this->timed_out) {
			// load into simplexml
			$array_ticket = (array)new SimpleXMLElement($api_ticket);

			// mainly produce comments
			foreach($array_ticket['comments'] as $comment) {
				// determine author
				if($comment->{'author-id'} == $this->author_id) {
					// is me
					$author = 'You';
				} else {
					// someone else
					$author = 'Support';
				}

				// rebuild date
				$t_date = explode('T', $comment->{'created-at'});

				// explode date
				$t_date_d = explode('-', $t_date[0]);

				// reverse and implode
				$f_tdate = implode('-', array_reverse($t_date_d));

				// define actual comment
				$ac_comment = (array)$comment->{'value'};

				// build new array
				$comments_array[] = array('author' 	=> $author,
										  'date' 	=> $f_tdate . ' ' . $t_date[1],
										  'comment' => nl2br($ac_comment[0]));
			}

			// is comments array blank?
			if(empty($comments_array)) {
				// false
				return false;
			} else {
				// return that
				return $comments_array;
			}
		} else {
			// failed, automatically return false
			return false;
		}
	}

	// update ticket
	public function update_ticket($tid, $comment = false) {
		// is it false?
		if($comment !== false) {
			// proceed
			$xml = '<comment>
						<value>' . $comment . '</value>
					</comment>';

			// send response
			$response = $this->api_request($this->base_url . 'requests/' . $tid . '.xml', false, $xml, true);

			// set flashdata
			$this->session->set_flashdata('action', 'Updated support ticket');

			// redirect
			redirect('/service-providers/help/viewticket/' . $tid);
		} else {
			// not valid
			return false;
		}
	}

	// is my ticket
	public function is_my_ticket($tid) {
		// check to see if it's their ticket
		if(isset($this->session->userdata['tickets'][$tid])) {
			// yes
			return true;
		} else {
			// not their ticket
			return false;
		}
	}
}
