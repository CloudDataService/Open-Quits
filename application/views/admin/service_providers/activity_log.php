<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="" method="get">

        <table class="filter">

            <tr>
                <th><label for="date_from">Date from</label></th>
                <th><label for="date_to">Date to</label></th>
                <th><label for="sp_id">Service provider</label></th>
                <th><label for="pp">Results per page</label></th>
            </tr>

            <tr>
            	<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
                <td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
                <td>
					<select name="sp_id" id="sp_id" class="sp_id">

                        <option value="">-- All --</option>

                        <?php foreach($service_providers_select as $char => $sp_array) : ?>

                        <optgroup label="<?php echo $char; ?>">

                            <?php foreach($sp_array as $sp) : ?>

                            <option value="<?php echo $sp['id']; ?>" <?php if(@$_GET['sp_id'] == $sp['id']) echo 'selected="selected"'; ?>><?php echo $sp['name']; ?></option>

                            <?php endforeach; ?>

                        </optgroup>

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
                <td><a href="/admin/service-providers/activity-log"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
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

	<?php if($log) : ?>

	<table class="results">

    	<tr class="order">

            <th><a href="?order=datetime_log<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'datetime_log') echo ' class="' . $_GET['sort'] . '"'; ?>>Date and time</a></th>
            <th><a href="?order=sp.name<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sp.name') echo ' class="' . $_GET['sort'] . '"'; ?>>Service provider</th>
            <th><a href="?order=sps.sname<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sps.sname') echo ' class="' . $_GET['sort'] . '"'; ?>>Staff name</th>
            <th>Description</th>

        </tr>

    	<?php foreach($log as $log) : ?>

        <tr class="row no_click">

            <td><?php echo $log['datetime_log_format']; ?></td>
            <td><?php echo $log['sp_name']; ?></td>
            <td><?php echo $log['sps_name']; ?></td>
            <td><?php echo $log['description']; ?></td>

        </tr>

        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">Your search returned no activity.</p>

    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
