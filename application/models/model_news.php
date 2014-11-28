<?php

class Model_news extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_total_news()
	{
		$sql = 'SELECT
					COUNT(id) AS total
				FROM
					news
				LEFT JOIN
					news_pcts
					ON id = news_id
				WHERE
					id = id ';

		if(@$_GET['date_from'])
			$sql .= ' AND DATE(datetime_created) >= ' . $this->db->escape(parse_date($_GET['date_from'])) . ' ';

		if(@$_GET['date_to'])
			$sql .= ' AND DATE(datetime_created) <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if(@$_GET['pct_id'])
			$sql .= ' AND (all_areas = 1 OR pct_id = ' . (int) $_GET['pct_id'] . ') ';

		if ($this->input->get('nc_id'))
			$sql .= ' AND news.nc_id = ' . (int) $this->input->get('nc_id') . ' ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	function get_news($start = 0, $limit = 0)
	{
		if( ! in_array(@$_GET['order'], array('datetime_created', 'title')) ) $_GET['order'] = 'datetime_created';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
					id,
					news.nc_id,
					nc_title,
					DATE_FORMAT(datetime_created, "%D %b %Y") AS datetime_created_format,
					title,
					body_excerpt
				FROM
					news
				LEFT JOIN
					news_pcts
					ON id = news_id
				LEFT JOIN
					news_categories
					ON news.nc_id = news_categories.nc_id
				WHERE
					id = id ';

		if(@$_GET['date_from'])
			$sql .= ' AND DATE(datetime_created) >= ' . $this->db->escape(parse_date($_GET['date_from'])) . ' ';

		if(@$_GET['date_to'])
			$sql .= ' AND DATE(datetime_created) <= ' . $this->db->escape(parse_date($_GET['date_to'])) . ' ';

		if(@$_GET['pct_id'])
			$sql .= ' AND (all_areas = 1 OR pct_id = ' . (int) $_GET['pct_id'] . ') ';

		if ($this->input->get('nc_id'))
			$sql .= ' AND news.nc_id = ' . (int) $this->input->get('nc_id') . ' ';

		$sql .= ' GROUP BY news.id ';

		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}


	function get_news_item($news_id)
	{
		if($news_id)
		{
			$sql = 'SELECT
						id,
						news.nc_id,
						nc_title,
						DATE_FORMAT(datetime_created, "%D %b %Y") AS datetime_created_format,
						title,
						body,
						all_areas,
						GROUP_CONCAT(pct_id) AS pct_ids
					FROM
						news
					LEFT JOIN
						news_pcts
						ON news.id = news_id
					LEFT JOIN
						news_categories
						ON news.nc_id = news_categories.nc_id
					WHERE
						id = "' . (int)$news_id . '";';

			return $this->db->query($sql)->row_array();
		}

		return FALSE;
	}


	function set_news($news_id)
	{
		$sql = 'DELETE FROM news_pcts WHERE news_id = ?';
		$this->db->query($sql, array($news_id));

		if($news_id)
		{
			$sql = 'UPDATE
						news
					SET
						nc_id = ?,
						title = ?,
						body = ?,
						body_excerpt = ?,
						all_areas = ?
					WHERE
						id = "' . (int)$news_id . '";';

			$this->session->set_flashdata('action', 'News updated');
		}
		else
		{
			$sql = 'INSERT INTO
						news
					SET
						datetime_created = NOW(),
						nc_id = ?,
						title = ?,
						body = ?,
						body_excerpt = ?,
						all_areas = ?;';

			$this->session->set_flashdata('action', 'News added');
		}

		$body_excerpt = strip_tags($this->input->post('body'));

		$body_excerpt = (strlen($body_excerpt) > 250 ? substr($body_excerpt, 0, 250) . '...' : substr($body_excerpt, 0, 250));

		$this->db->query($sql, array(
			$this->input->post('nc_id'),
			$this->input->post('title'),
			clean_html($this->input->post('body')),
			$body_excerpt,
			$this->input->post('all_areas'),
		));

		$news_id = ($news_id ? $news_id : $this->db->insert_id());

		// If all areas is true, then no need to set the selected PCTs explicitly
		if ( (int) $this->input->post('all_areas') === 1)
			return;

		// Array of PCTs the news is valid for
		$news_pcts = array();

		foreach ($this->input->post('news_pcts') as $pct_id)
		{
			$news_pcts[] = array('news_id' => $news_id, 'pct_id' => $pct_id);
		}

		if ( ! empty($news_pcts))
		{
			$this->db->insert_batch('news_pcts', $news_pcts);
		}

	}


	function delete_news($news_id)
	{
		$sql = 'DELETE FROM
					news
				WHERE
					id = "' . (int)$news_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'News deleted');
	}


	function get_total_new_news($datetime_last_login, $nc_id = 0)
	{
		$nc_id = (int) $nc_id;

		$sql = 'SELECT
					COUNT(id) AS total
				FROM
					news
				WHERE
					datetime_created > ? ';

		if ($nc_id !== 0)
		{
			$sql .= ' AND nc_id = ' . (int) $nc_id . ' ';
		}

		$row = $this->db->query($sql, $datetime_last_login)->row_array();

		return $row['total'];
	}
}
