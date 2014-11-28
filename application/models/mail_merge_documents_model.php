<?php

require_once(APPPATH . '/presenters/Mmf_presenter.php');
require_once(APPPATH . '/presenters/Mmd_presenter.php');

class Mail_merge_documents_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	public function get($mmd_id)
	{
		$sql = 'SELECT * FROM mail_merge_documents WHERE mmd_id = ? AND mmd_sp_id = ? LIMIT 1 ';
		$query = $this->db->query($sql, array($mmd_id, $this->session->userdata('sp_id')));
		return $query->row_array();
	}




	public function get_all($sp_id = 0)
	{
		$sql_where = '';
		if ($sp_id !== 0)
		{
			$sql_where = ' AND mmd_sp_id = ' . (int) $sp_id . ' ';
		}

		$sql = 'SELECT *
				FROM mail_merge_documents
				WHERE 1 = 1
				' . $sql_where  . '
				ORDER BY mmd_title ASC';

		return $this->db->query($sql)->result_array();
	}




	public function get_all_dropdown($sp_id = 0)
	{
		$results = $this->get_all($sp_id);
		$dd = array();
		foreach ($results as $row)
		{
			$dd[$row['mmd_id']] = $row['mmd_title'];
		}
		return $dd;
	}




	public function update($mmd_id, $data = array())
	{
		$data['mmd_updated_sps_id'] = $this->session->userdata('sps_id');
		$data['mmd_updated_timestamp'] = date('Y-m-d H:i:s');

		$sp_id = $this->session->userdata('sp_id');
		$sql_where = 'mmd_id = ' . (int) $mmd_id . ' AND mmd_sp_id = ' . (int) $sp_id;

		$sql = $this->db->update_string('mail_merge_documents', $data, $sql_where);
		return $this->db->query($sql);
	}




	public function insert($data = array())
	{
		$data['mmd_sp_id'] = $this->session->userdata('sp_id');
		$data['mmd_created_sps_id'] = $this->session->userdata('sps_id');
		$data['mmd_created_timestamp'] = date('Y-m-d H:i:s');

		$sql = $this->db->insert_string('mail_merge_documents', $data);
		$query = $this->db->query($sql);
		return ($query) ? $this->db->insert_id() : $query;
	}




	public function delete($mmd_id)
	{
		$sql = 'DELETE FROM mail_merge_documents WHERE mmd_id = ? AND mmd_sp_id = ?';
		return $this->db->query($sql, array($mmd_id, $this->session->userdata('sp_id')));
	}




	/**
	 * Process a mail merge document with request form and patient data
	 *
	 * @param object $mf		Monitoring form array
	 * @param object $patient		Client array
	 * @param mixed $doc		Integer: document ID to retrieve; String: document in HTML format
	 * @return string		Final outputted document
	 */
	public function process($mf, $client, $doc = NULL)
	{
		if (is_integer($doc))
		{
			// ID - get document from DB (generating)
			$mmd = $this->get($doc);
			$doc = $mmd['mmd_content'];
		}
		elseif (is_string($doc))
		{
			// HTML - use this as template (preview)
			$doc = $doc;
		}

		// Get all fields
		$this->load->model('mail_merge_fields_model');
		$fields = $this->mail_merge_fields_model->get_all($this->session->userdata('sp_id'));

		$search = array();
		$replace = array();

		// Loop through all the possible fields
		foreach ($fields as $field)
		{
			$mmf = new Mmf_presenter($field);
			$field_name = $mmf->get('mmf_name');
			$field_type = $mmf->get('mmf_type');
			// The tag to find in the document
			$search[] = "[$field_name]";
			// Determine the value to replace it with
			switch ($field_type)
			{
				case 'monitoring_form':
					$value = element($field_name, $mf, 'N/A');
				break;
				case 'client':
					$value = element($field_name, $client, 'N/A');
				break;
				case 'custom':
					$value = $mmf->get('mmf_value');
				break;
				default:
					$value = '';
				break;
			}

			if ($mmf->get('mmf_format') == 'multi')
			{
				$value = nl2br($value);
			}

			$replace[] = $value;
		}

		return str_replace($search, $replace, $doc);
	}


}

/* End of file: ./application/models/mail_merge_documents_model.php */
