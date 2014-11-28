<?php

class Autosave extends My_controller
{


	public function __construct()
	{
		parent::__construct();
		$this->auth->check_service_provider_logged_in();
		$this->load->model('autosave_model');
	}


	public function get()
	{
		$sps_id = $this->session->userdata('sps_id');
		$uri = $this->input->get('uri');
		$data = (object) $this->autosave_model->get($sps_id, $uri);

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($data));
	}




	/**
	 * Take data from POST and save it to the DB
	 */
	public function save()
	{
		$data = array(
			'as_sps_id' => $this->session->userdata('sps_id'),
			'as_uri_string' => $this->input->post('uri'),
			'as_data' => json_encode($this->input->post('form_data')),
			'as_created_datetime' => date('Y-m-d H:i:s'),
		);

		if ($this->autosave_model->insert($data))
		{
			$res = array('status' => 'ok');
		}
		else
		{
			$res = array('status' => 'err');
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($res));
	}




	/**
	 * Delete an autosave entry
	 */
	public function delete()
	{
		$as_id = $this->input->post('as_id');
		$sps_id = $this->session->userdata('sps_id');

		if ($this->autosave_model->delete($as_id, $sps_id))
		{
			$res = array('status' => 'ok');
		}
		else
		{
			$res = array('status' => 'err');
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($res));
	}


}
