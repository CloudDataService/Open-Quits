<style type="text/css">
	div#graph_div {
		width:870px;
		height:300px;
	}
	div.header {
		width:auto;
		line-height:20px;
	}
		div.header select {
			float:right;
		}
	select, select option {
		font-size:13px;
		padding-left:0px;
	}
</style>

<br><br>

<div class="panel_left">

	<div class="header">
		<h2>Recently made appointments</h2>
	</div>

	<div class="item results">

		<?php if ($recent_appointments): ?>

		<table class="results">

			<tr class="order">
				<th>Name</th>
				<th>Service provider</th>
				<th>&nbsp;</th>
			</tr>

			<?php foreach ($recent_appointments as $a): ?>

			<tr class="row">
				<td><?php echo $a['ac_fname'] ?> <?php echo $a['ac_sname'] ?></td>
				<td><?php echo $a['name']; ?></td>
				<td><a href="<?php echo site_url('pms/appointments/set/' . $a['a_id']) ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
			</td>

			<?php endforeach; ?>

		</table>

		<?php else: ?>

		<p class="no_results">None found</p>

		<?php endif; ?>

	</div>

</div>

<div class="panel_right">

	<div class="header">
		<h2>Search for service provider</h2>
	</div>

	<div class="item results" style="padding-left: 20px">

		<form method="get" action="/pms/service-providers/search">

			<table class="filter">
				<tr>
					<th><label for="post_code">Post code</label></th>
					<th></th>
				</tr>
				<tr>
					<td>
						<input type="text" class="text" name="post_code" size="10" style="text-transform: uppercase" />
					</td>
					<td>
						<input type="image" src="/img/btn/search.png" alt="Search" />
					</td>
				</tr>
			</table>

		</form>

	</div>

</div>

<div class="clear"></div>

<div class="header">

	<h2>Client Map</h2>

</div>

<div class="item">

	<div id="map_div" style="width: 100%; height: 320px;"></div>

</div>



<script type="text/javascript">
$(document).ready(function() {

	var the_map = L.map('map_div');
	the_map.setView([54.971104, -1.618502], 11);
	L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
		subdomains: '1234',
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>. Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a>.',
		maxZoom: 18
	}).addTo(the_map);

	var clients = null;

	<?php
	if ($map_appointments)
	{
		echo 'clients = ' . json_encode($map_appointments, JSON_NUMERIC_CHECK) . ';';
	}
	?>

	if (clients) {
		// Configure marker clustering
		markers = new L.MarkerClusterGroup({
			maxClusterRadius: 40
		});

		$.each(clients, function(idx, c) {
			var m = L.marker([c.ac_lat, c.ac_lng]);
			markers.addLayer(m);
		});

		the_map.addLayer(markers);
	}

});
</script>
