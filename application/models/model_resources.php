<?php

class Model_resources extends CI_Model {


	function __construct()
	{
		parent::__construct();
	}


	function get_total_resources($include_deleted = false)
	{
		$sql = 'SELECT
					COUNT(id) AS total
				FROM
					resources';

		if ($include_deleted !== false){
			$sql .= ' WHERE deleted_at IS NULL';
		}

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}

	function get_deleted_resources($start = 0, $limit = false)
	{
		return $this->get_resources($start, $limit, true);
	}

	function get_resources($start = 0, $limit = false, $deleted = false)
	{
		if( ! in_array(@$_GET['order'], array('r.title', 'rc.title')))
		{
			$_GET['order'] = 'rc.title ASC, r.title';
		}

		(isset($_GET['sort']) ? '' : $_GET['sort'] = 'desc');

		if($_GET['sort'] != 'asc' && $_GET['sort'] != 'desc')
		{
			$_GET['sort'] = 'asc';
		}

		$sql = 'SELECT
					r.id,
					r.title,
					r.description,
					r.file_name,
					r.link,
					IF(r.file_size != "", CONCAT(r.file_size,"kb"), "") AS file_size,
					IF(r.file_name != "", SUBSTRING_INDEX(r.file_name, ".", -1), "link") AS file_ext,

					IF(rc.title IS NOT NULL, rc.title, "N/A") AS cat_title,

					r.deleted_at
				FROM
					resources r
				LEFT JOIN
					resource_categories rc
						ON (rc.id = r.cat_id)
				WHERE
					deleted_at IS ' . (($deleted !== false) ? 'NOT NULL' : 'NULL') . '
				ORDER BY
					' . $this->db->escape($_GET['order']) . ' ' . $this->db->escape($_GET['sort']);

		if ($limit)
		{
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
		}

		return $this->db->query($sql)->result_array();
	}


	function get_resource($resource_id)
	{
		$sql = 'SELECT
					r.*,
					IF(r.file_size != "", CONCAT(r.file_size,"kb"), "") AS file_size,
					IF(r.file_name != "", SUBSTRING_INDEX(r.file_name, ".", -1), "link") AS file_ext
				FROM
					resources r
				WHERE
					id = "' . (int)$resource_id . '";';

		return $this->db->query($sql)->row_array();
	}

	function set_resource($resource_id = 0, $data)
	{
		// header('Content-Type: application/json');
		// print json_encode($this->input->post());
		// exit;

		if($resource_id)
		{
			$sql = 'UPDATE
						resources
					SET
						title = ?,
						cat_id = ?,
						description = ?,
						file_name = ?,
						file_size = ?,
						link = ?
					WHERE
						id = "' . (int)$resource_id . '"';

			$this->session->set_flashdata('action', 'Resource updated');
		}
		else
		{
			$sql = 'INSERT INTO
						resources
					SET
						datetime_uploaded = NOW(),
						title = ?,
						cat_id = ?,
						description = ?,
						file_name = ?,
						file_size = ?,
						link = ?';

			$this->session->set_flashdata('action', 'Resource added');
		}

		$this->db->query($sql, array(
				$this->input->post('title'),
				$this->input->post('cat_id'),
				$this->input->post('description'),
				$data['file_name'],
				$data['file_size'],
				$this->input->post('link'),
			)
		);

	}


	function restore_resource($resource_id)
	{
		$sql = "UPDATE resources SET deleted_at = NULL WHERE id = ?";
		$query = $this->db->query($sql, array($resource_id));

		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('action', 'Resources restored');
			return true;
		}

		return false;
	}


	function delete_resource($resource_id)
	{
		$sql = "UPDATE resources SET deleted_at = NOW() WHERE id = ?";
		$query = $this->db->query($sql, array($resource_id));

		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('action', 'Resources deleted');
			return true;
		}

		return false;

		/*
		$sql = 'SELECT
					file_name
				FROM
					resources
				WHERE
					id = "' . (int)$resource_id . '";';

		if($file = $this->db->query($sql)->row_array())
		{
			$sql = 'DELETE FROM
						resources
					WHERE
						id = "' . (int)$resource_id . '";';

			if($this->db->query($sql))
			{
				unlink($this->config->config['base_dir'] . '/public_html/sotw/resources/' . $file['file_name']);
			}

			$this->session->set_flashdata('action', 'Resources deleted');
		}
		*/
	}


	function get_resource_categories()
	{
		$sql = 'SELECT
					id,
					title
				FROM
					resource_categories
				ORDER BY
					title ASC';

		return $this->db->query($sql)->result_array();
	}


	function get_resource_category($cat_id)
	{
		$sql = 'SELECT
					id,
					title
				FROM
					resource_categories
				WHERE
					id = "' . (int)$cat_id . '";';

		return $this->db->query($sql)->row_array();
	}


	function delete_resource_category($cat_id)
	{
		$sql = 'UPDATE
					resources
				SET
					cat_id = NULL
				WHERE
					cat_id = "' . (int)$cat_id . '";';

		$this->db->query($sql);

		$sql = 'DELETE FROM
					resource_categories
				WHERE
					id = "' . (int)$cat_id . '";';

		$this->db->query($sql);

		if ($this->db->affected_rows() > 0)
		{
			$this->session->set_flashdata('action', 'Resource category deleted');
			return true;
		}

		return false;
	}


	function set_resource_category($cat_id = 0)
	{
		if($cat_id)
		{
			$sql = 'UPDATE
						resource_categories
					SET
						title = ?
					WHERE
						id = "' . (int)$cat_id . '";';

			$this->session->set_flashdata('action', 'Resource category updated');
		}
		else
		{
			$sql = 'INSERT INTO
						resource_categories
					SET
						title = ?';

			$this->session->set_flashdata('action', 'Resource category added');
		}

		$this->db->query($sql, $this->input->post('cat_title'));
	}

}
