<?php

/**
 * TextMagic API callback controller
 */

class Callback extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Update the SMS table with the status of the message
	 */
	public function sms_callback()
	{
		if ($this->input->post('status') && $this->input->post('message_id'))
		{
			$data = array(
				's_status' => $this->input->post('status'),
				's_updated' => time(),
			);

			$message_id = (int) $this->input->post('message_id');

			$sql = $this->db->update_string('sms', $data, "s_message_id = '$message_id'");

			if ( ! $this->db->query($sql))
			{
				show_error('Cannot set status');
			}
			else
			{
				echo "Status of message ID $message_id updated to {$data['s_status']}.";
			}
		}
	}




}