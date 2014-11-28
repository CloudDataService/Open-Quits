<style type="text/css">
	table.form tr th {
        width:200px;
    }
    tr.contact {
        display:none;
    }
</style>
<?php
// any tickets?
if(empty($ticket)) {
	// no tickets
	?>

    <div class="header">
        <h2>View ticket</h2>
    </div>

    <div class="item">

        <p class="no_results">Ticket doesn't exist</p>

    </div>

	<?php
} else {
	// some tickets
	?>

    <div class="header">
        <h2>View ticket (<?php echo((count($ticket) - 1)); ?> replies)</h2>
    </div>

    <div class="item results">

        <table class="results">
            <?php
			// loop through
			foreach($ticket as $key=>$comment) {
				// display
			?>
            <tr class="order">
                <th><?php echo '<strong>' . $comment['author'] . '</strong> ' . date('jS F Y g:ia', strtotime($comment['date'])); ?></th>
            </tr>
            <tr>
            	<td class="comments_box"><?php echo($comment['comment']); ?></a></td>
            </tr>
            	<?php
			}
			?>
		</table>

    </div>

    <div class="header">
    	<h2>Post reply</h2>
    </div>

    <div class="item">
    	<?php
		// is it open?
		if($ticket_status == 'Solved' || $ticket_status == 'Closed') {
			// is closed
			echo('Ticket solved! You can no longer write a response.');
		} else {
			// show form
		?>
    	<form action="../postticket/<?php echo($ticket_id); ?>" method="post" id="reply_form">

            <table class="form">

                <tr class="vat">
                    <td>
                    	<textarea name="ticket_response" id="ticket_response" cols="80" rows="4"></textarea>
                    </td>
                    <td class="e"></td>
                </tr>

                <tr>
                	<td style="text-align:right;"><input type="image" src="/img/btn/submit.png" alt="Save" style="padding-left: 10px;" /></td>
                </tr>

            </table>

        </form>
        <?php
		}
		?>
    </div>
    <?php
}
?>
