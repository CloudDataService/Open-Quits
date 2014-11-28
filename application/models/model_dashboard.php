<?php

class Model_dashboard extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_approaching_follow_up($sp_id, $follow_up = 4)
	{
		$follow_up_fields = array(
			4 => 'date_of_4_week_follow_up',
			12 => 'date_of_12_week_follow_up',
		);

		$follow_up_field = (array_key_exists($follow_up, $follow_up_fields) ? $follow_up_fields[$follow_up] : 'date_of_4_week_follow_up');

		$sql = 'SELECT
					mf.id AS mf_id,
					CONCAT(IF(ISNULL(c.title_other), c.title, c.title_other), " ", c.fname, " ", c.sname) AS client_name,
					DATE_FORMAT(mf.' . $follow_up_field . ', "%D %b %Y") AS date_of_follow_up,
					"' . $follow_up . '" AS follow_up_week
				FROM
					monitoring_forms mf,
					clients c
				WHERE
					c.monitoring_form_id = mf.id
					AND ' . $follow_up_field . ' >= CURDATE() AND ' . $follow_up_field . ' <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) ';

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '" ';

		$sql .= ' ORDER BY ' . $follow_up_field . ' DESC ';

		return $this->db->query($sql)->result_array();
	}


	function get_latest_claims()
	{
		$sql = 'SELECT
					mfc.monitoring_form_id,
					mfc.claim_type,
					DATE_FORMAT(mfc.date_of_claim, "%D %b %Y") AS date_of_claim_format,
					CONCAT("&pound;", FORMAT(mfc.cost, 2)) AS cost,
					mfc.status,
					sp.name AS sp_name
				FROM
					monitoring_form_claims mfc,
					monitoring_forms mf,
					service_providers sp
				WHERE
					mf.id = mfc.monitoring_form_id
					AND sp.id = mf.service_provider_id
				ORDER BY
					date_of_claim DESC
				LIMIT 0, 5';

		return $this->db->query($sql)->result_array();
	}


	function get_graph($range, $sp_id = 0)
	{
		$current_week = date('W', time());
		$current_month = date('F', time());
		$current_year = date('Y', time());

		$url = 'http://chart.apis.google.com/chart?';

		# chart type
		$url .= 'cht=lc';

		# chart size
		$url .= '&chs=870x300';

		# background color
		$url .= '&chf=bg,s,fbfbfb';

		switch($range)
		{

			case 'year' :

				# chart title
				#$url .= '&chtt=Total+claims+of+' . $current_year;

				$sql = 'SELECT
							SUM(IF(MONTH(date_of_claim) = 1, 1, 0)) AS "Jan",
							SUM(IF(MONTH(date_of_claim) = 2, 1, 0)) AS "Feb",
							SUM(IF(MONTH(date_of_claim) = 3, 1, 0)) AS "Mar",
							SUM(IF(MONTH(date_of_claim) = 4, 1, 0)) AS "Apr",
							SUM(IF(MONTH(date_of_claim) = 5, 1, 0)) AS "May",
							SUM(IF(MONTH(date_of_claim) = 6, 1, 0)) AS "Jun",
							SUM(IF(MONTH(date_of_claim) = 7, 1, 0)) AS "Jul",
							SUM(IF(MONTH(date_of_claim) = 8, 1, 0)) AS "Aug",
							SUM(IF(MONTH(date_of_claim) = 9, 1, 0)) AS "Sep",
							SUM(IF(MONTH(date_of_claim) = 10, 1, 0)) AS "Oct",
							SUM(IF(MONTH(date_of_claim) = 11, 1, 0)) AS "Nov",
							SUM(IF(MONTH(date_of_claim) = 12, 1, 0)) AS "Dec"
						FROM
							monitoring_form_claims mfc,
							monitoring_forms mf
						WHERE
							mf.id = mfc.monitoring_form_id
							AND YEAR(date_of_claim) = YEAR(CURDATE()) ';

					$x_label = 'Months';

			break;

			case 'month' :

				# chart title
				#$url .= '&chtt=Total+claims+of+' . $current_month . '+' . $current_year;

				switch($current_month)
				{
					case 'February' :
						# is leap year?
						$days = date('L', time()) ? '29' : '28';
					break;

					case 'September' || 'April' || 'June' || 'November' :
						$days = 30;
					break;

					default:
						$days = 31;
					break;
				}

				$sql = 'SELECT ';

				for($i = 1; $i <= $days; $i++)
				{
					$sql .= ' SUM(IF(DAY(date_of_claim) = ' . $i . ', 1, 0)) AS "' . $i . '"';
					if($i != $days)
						$sql .= ',';
				}

				$sql .= ' FROM
							monitoring_form_claims mfc,
							monitoring_forms mf
						WHERE
							mf.id = mfc.monitoring_form_id
							AND MONTH(date_of_claim) = MONTH(CURDATE()) ';

				$x_label = 'Days+of+the+month';

			break;

			case 'week' :

				$sql = 'SELECT
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Mon", 1, 0)) AS "Mon",
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Tue", 1, 0)) AS "Tue",
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Wed", 1, 0)) AS "Wed",
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Thu", 1, 0)) AS "Thu",
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Fri", 1, 0)) AS "Fri",
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Sat", 1, 0)) AS "Sat",
							SUM(IF(DATE_FORMAT(date_of_claim, "%a") = "Sun", 1, 0)) AS "Sun"
						FROM
							monitoring_form_claims mfc,
							monitoring_forms mf
						WHERE
							mf.id = mfc.monitoring_form_id
							AND WEEK(date_of_claim, 1) = WEEK(CURDATE(), 1) ';

				$x_label = 'Days+of+the+week';

			break;
		}

		if($sp_id)
			$sql .= ' AND mf.service_provider_id = "' . (int)$sp_id . '"';

		$data = $this->db->query($sql)->row_array();

		$keys = '|';
		$values = '';


		foreach($data as $month => $total)
		{
			$keys .= $month . '|';
			$values .= $total . ',';
		}

		$values = substr($values, 0, (strlen($values) - 1));

		# chart axis
		$url .= '&chxt=x,y';

		$max = max($data);

		$scale = ceil($max/100) * 100;

		$y_axis = array();

		if($scale)
		{
			for($i = 0; $i <= $scale; $i += ($scale/10))
			{
				$y_axis[] = $i;
			}
		}

		$y_axis = '1:|' . implode('|', $y_axis);

		$url .= '&chxl=0:' . $keys . $y_axis;

		$url .= '&chds=0,' . $scale;

		# chart values
		$url .= '&chd=t:' . $values;

		return $url;
	}


}
