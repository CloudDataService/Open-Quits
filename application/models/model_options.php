<?php

class Model_options extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	public function get_global_option($option_name = '')
	{
		$sql = 'SELECT option_value FROM options WHERE option_name = ? AND pct_id = 0 LIMIT 1';
		$row = $this->db->query($sql, array($option_name))->row_array();

		if ($row)
		{
			if ($unserialized = @unserialize($row['option_value']))
			{
				return $unserialized;
			}
			else
			{
				return $row['option_value'];
			}
		}
		else
		{
			return FALSE;
		}
	}



	public function get_pct_option($option_name = '', $pct_id = 0)
	{
		$sql = 'SELECT option_value FROM options WHERE option_name = ? AND pct_id = ? LIMIT 1';
		$row = $this->db->query($sql, array($option_name, $pct_id))->row_array();

		if ($row)
		{
			if ($unserialized = @unserialize($row['option_value']))
			{
				return $unserialized;
			}
			else
			{
				return $row['option_value'];
			}
		}
		else
		{
			return FALSE;
		}
	}


	public function get_option($option_name)
	{
		echo "Invalid use of get_option()!";
		return FALSE;
	}


	/*
	function get_option($option_name)
	{
		$sql = 'SELECT
					option_value
				FROM
					options
				WHERE
					option_name = ? ';

		if (@$_GET['pct_id'])
		{
			$sql .= ' AND pct_id = ' . (int) $this->input->get('pct_id') . ' ';
		}
		else
		{
			$sql .= ' AND pct_id = 0 ';
		}

		if($option = $this->db->query($sql, $option_name)->row_array())
		{
			if($unserialized = @unserialize($option['option_value']))
			{
				return $unserialized;
			}
			else
			{
				return $option['option_value'];
			}
		}
		else
		{
			return false;
		}
	}*/



	function set_option($option_name = '', $option_value = '', $pct_id = 0)
	{
		$sql = 'UPDATE
					options
				SET
					option_value = ?
				WHERE
					option_name = ?
				AND
					pct_id = ? ';

		$this->db->query($sql, array(serialize($option_value), $option_name, $pct_id));
	}


}
