<div class="functions">

    <a href="/service-providers/claims/csv/?export=1<?php echo '&amp;order=' . @$_GET['order'] . '&amp;sort=' . @$_GET['sort'] . '&amp;date_from=' . $_GET['date_from'] . '&amp;date_to=' . $_GET['date_to'] . '&amp;claim_type=' . @$_GET['claim_type'] . '&amp;status=' . @$_GET['status']; ?>" class="action" title="Click OK to export <?php echo $total_export . ($total_export == 1 ? ' claim' : ' claims'); ?> to CSV."><img src="/img/btn/export-to-csv.png" alt="Export to csv" /></a>

    <div class="clear"></div>

</div>

<div class="header">
	<h2>Filter</h2>
</div>
<div class="item">

	<form action="/service-providers/claims" method="get">

        <table class="filter">

            <tr>
                <th><label for="date_from">Date from</label></th>
                <th><label for="date_to">Date to</label></th>
                <th><label for="claim_type">Claim type</label></th>
                <th><label for="status">Status</label></th>
                <th><label for="pp">Results per page</label></th>
            </tr>

            <tr>
            	<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
                <td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
                <td>
                	<select name="claim_type" id="claim_type">

                        <option value="">-- All --</option>
                        <?php foreach($claim_types as $claim_type) : ?>
                        <option value="<?php echo $claim_type; ?>" <?php if(@$_GET['claim_type'] == $claim_type) echo 'selected="selected"'; ?>><?php echo $claim_type; ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                <td>
                	<select name="status" id="status">

                        <option value="">-- All --</option>
                        <?php foreach($status as $status) : ?>
                        <option value="<?php echo $status; ?>" <?php if(@$_GET['status'] == $status) echo 'selected="selected"'; ?>><?php echo $status; ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                <td>
                	<select name="pp" id="pp">
                   		<?php foreach($pp as $pp) : ?>
                        <option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="image" src="/img/btn/filter.png" alt="Filter" /></td>
                <td><a href="<?php echo current_url(); ?>"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
            </tr>

        </table>

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($claims) : ?>

    <table class="results">

        <tr class="order">

        	<th><a href="?order=monitoring_form_id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'monitoring_form_id') echo ' class="' . $_GET['sort'] . '"'; ?>>Monitoring form ID</a></th>
        	<th><a href="?order=date_of_claim<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'date_of_claim') echo ' class="' . $_GET['sort'] . '"'; ?>>Date of claim</a></th>
        	<th><a href="?order=claim_type<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'claim_type') echo ' class="' . $_GET['sort'] . '"'; ?>>Claim type</a></th>
        	<th><a href="?order=cost<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'cost') echo ' class="' . $_GET['sort'] . '"'; ?>>Cost</a></th>
        	<th><a href="?order=status<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'status') echo ' class="' . $_GET['sort'] . '"'; ?>>Status</a></th>
            <th>View monitoring form</th>

        </tr>

        <?php foreach($claims as $claim) : ?>

        <tr class="row">

        	<td>#<?php echo $claim['monitoring_form_id']; ?></td>
            <td><?php echo $claim['date_of_claim_format']; ?></td>
            <td><?php echo $claim['claim_type']; ?></td>
            <td><?php echo $claim['cost']; ?></td>
            <td>
            <?php
			switch($claim['status'])
			{
				case 'Pending' :
					echo '<span style="color:#555;">' . $claim['status'] . '</span>';
				break;
				case 'Passed to finance' :
					echo '<span style="color:#2e841a;">' . $claim['status'] . '</span>';
				break;
				case 'Rejected' :
					echo '<span style="color:#f00;">' . $claim['status'] . '</span>';
				break;
			}
			?>
            </td>
            <td><a href="/service-providers/monitoring-forms/info/<?php echo $claim['monitoring_form_id']; ?>#claims"><img src="/img/icons/magnifier.png" alt="View monitoring form" /></a></td>

        </tr>

        <?php endforeach; ?>

    </table>

    <?php else : ?>

   	<p class="no_results">Your search returned no claims.</p>

    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
