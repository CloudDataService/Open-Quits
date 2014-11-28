<div class="functions">

    <a href="/service-providers/monitoring-forms/set"><img src="/img/btn/add-new.png" alt="Add new" /></a>

    <?php
    $get_data = array();
    $get_fields = array(
		'order',
		'sort',
		'date_from',
		'date_to',
		'date_type',
		'sname',
		'treatment_outcome',
		'follow_up',
	);
    foreach ($get_fields as $key)
    {
    	$get_data[$key] = $this->input->get($key);
    }
    $get_data['export'] = 1;
    $csv_url = site_url('service-providers/monitoring-forms/csv') . '?' . http_build_query($get_data);
    ?>
    <a href="<?php echo $csv_url ?>" class="action" title="Click OK to export <?php echo $total_export . ($total_export == 1 ? ' monitoring form' : ' monitoring forms'); ?> to CSV."><img src="/img/btn/export-to-csv.png" alt="Export to csv" /></a>

    <div class="clear"></div>

</div>

<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/service-providers/monitoring-forms" method="get">

        <table class="filter" style="width: 100%">

            <tr>
                <th><label for="date_from">Date from</label></th>
                <th><label for="date_to">Date to</label></th>
                <th><label for="sname">Client surname</label></th>
                <th><label for="pp">Results per page</label></th>
                <th>&nbsp;</th>
            </tr>

            <tr>
            	<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
                <td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
                <td><input type="text" name="sname" id="sname" value="<?php echo @$_GET['sname']; ?>" class="text" /></td>
                <td>
                	<select name="pp" id="pp">
                   		<?php foreach($pp as $pp) : ?>
                        <option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="image" src="/img/btn/filter.png" alt="Filter" />
                    <a href="<?php echo current_url(); ?>"><img src="/img/btn/clear.png" alt="Clear" /></a>
                </td>
            </tr>

            <tr>
                <th><label for="follow_up">Follow up outcome</label></th>
                <th><label for="date_type">Date type</label></th>
                <th><label for="treatment_outcome">Treatment outcome</label></th>
                <th colspan="2"></th>
            </tr>

            <tr>
                <td>
                    <select name="follow_up">
                        <option value="" <?php echo (element('follow_up', $_GET, NULL) === NULL) ? 'selected="selected"' : ''; ?>>(Not set)</option>
                        <option value="4" <?php echo (@$_GET['follow_up'] == 4) ? 'selected="selected"' : ''; ?>>4 week</option>
                        <option value="12" <?php echo (@$_GET['follow_up'] == 12) ? 'selected="selected"' : ''; ?>>12 week</option>
                    </select>
                </td>
                <td><select name="date_type">
                      <option value="qds">Agreed Quit Date</option>
                      <option value="dc" <?php if(@$_GET['date_type'] == 'dc') { echo('selected="selected"'); } ?>>Date Created</option>
                  </select></td>
                <td>
                    <select name="treatment_outcome" id="treatment_outcome">
                        <option value="">-- Please select --</option>
                        <?php foreach($treatment_outcomes as $treatment_outcome) : ?>
                        <option value="<?php echo $treatment_outcome; ?>" <?php if($treatment_outcome == @$_GET['treatment_outcome']) echo 'selected="selected"'; ?>><?php echo $treatment_outcome; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td colspan="2"></td>
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

	<?php if($monitoring_forms) : ?>

    <?php
    // Set which follow up date data point to use
    $follow_up = element('follow_up', $_GET, 4);
    ?>

	<table class="results">

    	<tr class="order">
        	<th><a href="?order=id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'id') echo ' class="' . $_GET['sort'] . '"'; ?>>ID</a></th>
    		<th><a href="?order=date_created<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'date_created') echo ' class="' . $_GET['sort'] . '"'; ?>>Date created</a></th>
    		<th><a href="?order=sname<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sname') echo ' class="' . $_GET['sort'] . '"'; ?>>Client name</a></th>
            <th>Agreed Quit Date</th>
            <th><a href="?order=date_of_<?php echo $follow_up ?>_week_follow_up<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'date_of_' . $follow_up . '_week_follow_up') echo ' class="' . $_GET['sort'] . '"'; ?>>Follow up date (<?php echo $follow_up ?>)</a></th>
            <th><a href="?order=treatment_outcome_<?php echo $follow_up ?><?php echo $sort; ?>"<?php if(@$_GET['order'] == 'treatment_outcome_' . $follow_up) echo ' class="' . $_GET['sort'] . '"'; ?>>Outcome (<?php echo $follow_up ?>)</a></th>
            <th>View more</th>
        </tr>

        <?php foreach($monitoring_forms as $mf) : ?>
        <tr class="row">
        	<td>#<?php echo $mf['id']; ?></td>
        	<td><?php echo $mf['date_created_format']; ?></td>
            <td><?php echo $mf['client_name']; ?></td>
            <td><?php if(empty($mf['quit_date_set_format'])) { echo('-'); } else { echo($mf['quit_date_set_format']); } ?></td>
            <td><?php echo $mf['date_of_' . $follow_up . '_week_follow_up_format']; ?></td>
            <td><?php echo $mf['treatment_outcome_' . $follow_up]; ?></td>
            <td><a href="/service-providers/monitoring-forms/info/<?php echo $mf['id']; ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">Your search returned no monitoring forms.</p>

    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
