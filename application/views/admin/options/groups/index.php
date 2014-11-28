<style type="text/css">
	table.form tr th {
        width:150px;
    }
</style>
<div class="header">
	<h2>Add new group</h2>
</div>

<div class="item">

	<form action="" method="post" id="group_form">

        <table class="form">

            <tr>
                <th><label for="name">Group name</label></th>
                <td><input type="text" name="name" id="name" class="text" maxlength="32" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="initial">Initial claim cost</label></th>
                <td>&pound; <input type="text" name="initial" id="initial" class="text" style="width:50px;" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="claim_4_week">4 week claim cost</label></th>
                <td>&pound; <input type="text" name="claim_4_week" id="claim_4_week" class="text" style="width:50px;" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="claim_12_week">12 week claim cost</label></th>
                <td>&pound; <input type="text" name="claim_12_week" id="claim_12_week" class="text" style="width:50px;" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="pct_id">Local Authority</label></th>
                <td>
                    <?php if ($pct_id) echo form_hidden('pct_id', $pct_id); ?>
                    <select name="pct_id" id="pct_id" <?php if ($pct_id): ?> disabled="disabled" <?php endif; ?>>

                        <option value="">-- All --</option>

                        <?php foreach($pcts as $pct) : ?>
                        <option value="<?php echo $pct['id']; ?>" <?php if(@$pct_id == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
            </tr>

           	<tr>
                <th>
                	<label for="do_not_claim">Do not claim</label>
                    <small>If this option is selected, any service providers in this group will not submit claims.</small>
                </th>
                <td><input type="checkbox" name="do_not_claim" id="do_not_claim" value="1" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>

        </table>

    </form>

</div>

<div class="header">
	<h2>Groups</h2>
</div>

<div class="item results">

	<?php if($groups) : ?>

    <table class="results">

    	<tr class="order">

        	<th>Group name</th>

            <th>Total providers</th>

            <th>Initial cost</th>

            <th>4 week cost</th>

            <th>12 week cost</th>

            <th>Local Authority</th>

            <th>Do not claim</th>

        </tr>

        <?php foreach($groups as $group) : ?>

        <tr class="row">
        	<td><a href="/admin/options/group/<?php echo $group['id']; ?>"><?php echo $group['name']; ?></a></td>
            <td><?php echo $group['total_service_providers']; ?></td>
            <td><?php echo '&pound;' . $group['claim_options']['initial']; ?></td>
            <td><?php echo '&pound;' . $group['claim_options']['claim_4_week']; ?></td>
            <td><?php echo '&pound;' . $group['claim_options']['claim_12_week']; ?></td>
            <td><?php echo @$group['pct_name'] ?></td>
            <td><img src="/img/icons/<?php echo (@$group['claim_options']['do_not_claim'] ? 'tick' : 'cross'); ?>.png" alt="" /></td>
        </tr>

        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">There are no listed groups.</p>

    <?php endif; ?>

</div>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
