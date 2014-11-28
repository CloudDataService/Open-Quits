<?php if ($monitoring_forms) : ?>

<table class="results">

	<tr class="order">
		<th>ID</th>
		<th>Client name</th>
		<th>Due date</th>
	</tr>

	<?php foreach ($monitoring_forms as $mf) : ?>

	<tr class="row">
		<td><a href="/service-providers/monitoring-forms/info/<?php echo $mf['mf_id']; ?>">#<?php echo $mf['mf_id']; ?></a></td>
		<td><?php echo $mf['client_name']; ?></td>
		<td><?php echo $mf['date_of_follow_up']; ?></td>
	</td>

	<?php endforeach; ?>

</table>

<?php else : ?>

<p class="no_results">There are currently no clients approaching follow up.</p>

<?php endif; ?>
