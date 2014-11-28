<style type="text/css">
	table.form tr th {
        width:200px;
    }
    tr.contact {
        display:none;
    }
</style>

<a href="/service-providers/help/newticket"><img src="/img/btn/newticket.png"  style="padding-bottom: 15px;" /></a>

<?php
// any tickets?
if($tickets === false || $tickets['data']['total_tickets'] == 0) {
	// no tickets
	?>
    <div class="header">
        <h2>Support tickets (0)</h2>
    </div>

    <div class="item">

		<p class="no_results">You have no listed support tickets.</p>

    </div>
	<?php
} else {
	// some tickets
	?>
    <div class="header">
        <h2>Support tickets (<?php echo($tickets['data']['total_tickets']); ?>)</h2>
    </div>

    <div class="item results">

        <table class="results">
            <tr class="order">
                <th>Subject</th>
                <th>Replies</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <?php
			// loop through
			foreach($tickets['data']['tickets'] as $key=>$ticket) {
				// display
				?>
            <tr class="row no_click">
            	<td><?php echo($ticket['subject']); ?></td>
                <td><?php echo($ticket['replies']); ?></td>
                <td><?php echo(date('jS F Y', $ticket['date_created'])); ?></td>
                <td><?php echo($ticket['status']); ?></td>
            </tr>
            	<?php
			}
			?>
		</table>

    </div>
    <?php
}
?>
