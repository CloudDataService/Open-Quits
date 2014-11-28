<?php

class Model_statistics extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	function get_treatment_outcome_graph()
	{
		$sql = 'SELECT
					SUM(IF(treatment_outcome = "Not quit", 1, 0)) AS "Not quit",
					SUM(IF(treatment_outcome = "Lost to follow-up", 1, 0)) AS "Lost to follow up",
					SUM(IF(treatment_outcome = "Referred to GP", 1, 0)) AS "Referred to GP",
					SUM(IF(treatment_outcome = "Refer to tier 3", 1, 0)) AS "Refer to tier 3",
					SUM(IF(treatment_outcome = "Quit self-reported", 1, 0)) AS "Quit self-reported",
					SUM(IF(treatment_outcome = "Quit CO verified", 1, 0)) AS "Quit CO verified"
				FROM
					monitoring_forms mf
				LEFT JOIN
					service_providers sp
					ON mf.service_provider_id = sp.id
				WHERE
					1 = 1 ';

		if(@$_GET['pct_id'])
			$sql .= ' AND sp.pct_id = ' . (int) $_GET['pct_id'] . ' ';

		$treatment_outcomes = $this->db->query($sql)->row_array();

		$url = 'http://chart.apis.google.com/chart?';

		# chart type
		$url .= 'cht=bvs';

		# chart size
		$url .= '&chs=750x400';

		# bar colour
		# $url .= '&chco=000000';

		# chart data
		$url .= '&chd=t:' . implode(',', $treatment_outcomes);

		# chart axis
		$url .= '&chxt=x,y';

		# x axis labels
		$x_axis = '0:|' . implode('|', array_flip($treatment_outcomes)) . '|';

		$max = max($treatment_outcomes);

		$scale = ceil($max/100) * 100;

		for($i = 0; $i <= $scale; $i += ($scale/10))
		{
			$y_axis[] = $i;
		}

		$y_axis = '1:|' . implode('|', $y_axis);

		# axis labels
		$url .= '&chxl=' . $x_axis . $y_axis;

		# axis scale
		$url .= '&chds=0,' . $scale;

		# bar spacing
		$url .= '&chbh=a,15,15';

		return $url;
	}


}
