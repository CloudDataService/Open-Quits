<style type="text/css">
	table.group tr th {
        width:150px;
    }
</style>
<div class="functions">

    <a href="?delete=1" class="action" title="Click OK to permanently delete this group. Any service providers currently assigned to this group will be unassigned."><img src="/img/btn/delete.png" alt="Delete" /></a>

    <div class="clear"></div>

</div>

<div class="header">
	<h2>Update group</h2>
</div>

<div class="item">

	<form action="" method="post" id="group_form">

        <table class="form group">

            <tr>
                <th><label for="name">Group name</label></th>
                <td><input type="text" name="name" id="name" class="text" value="<?php echo $group['name']; ?>" maxlength="32" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="initial">Initial claim cost</label></th>
                <td>&pound; <input type="text" name="initial" id="initial" class="text" style="width:50px;" value="<?php echo $group['claim_options']['initial']; ?>" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="claim_4_week">4 week claim cost</label></th>
                <td>&pound; <input type="text" name="claim_4_week" id="claim_4_week" class="text" style="width:50px;" value="<?php echo $group['claim_options']['claim_4_week']; ?>" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="claim_12_week">12 week claim cost</label></th>
                <td>&pound; <input type="text" name="claim_12_week" id="claim_12_week" class="text" style="width:50px;" value="<?php echo $group['claim_options']['claim_12_week']; ?>" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

			<tr>
				<th><label for="pct_id">Local Authority</label></th>
				<td>
					<?php if ($pct_id) echo form_hidden('pct_id', $group['pct_id']); ?>
					<select name="pct_id" id="pct_id" <?php if ($pct_id): ?> disabled="disabled" <?php endif; ?>>

						<option value="">-- All --</option>

						<?php foreach($pcts as $pct) : ?>
						<option value="<?php echo $pct['id']; ?>" <?php if(@$group['pct_id'] == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
						<?php endforeach; ?>

					</select>
				</td>
			</tr>

            <tr>
                <th>
                	<label for="do_not_claim">Do not claim</label>
                    <small>If this option is selected, any service providers in this group will not submit claims.</small>
                </th>
                <td><input type="checkbox" name="do_not_claim" id="do_not_claim" value="1" <?php if($group['claim_options']['do_not_claim']) echo 'checked="checked"'; ?> /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>

        </table>

    </form>

</div>

<div class="header">
	<h2>Service providers</h2>
</div>

<div class="item">

    <table class="form">
        <tr>
            <th style="text-align:center;">Unassigned service providers</th>
            <th style="text-align:center;">Assigned service providers</th>
        </tr>
        <tr>
            <td>
            	<form action="" method="post">

                    <select name="unassigned_service_providers[]" multiple="multiple" style="width:385px; height:250px;">

                        <?php if(@$service_providers_select['unassigned']) : ?>

						<?php foreach($service_providers_select['unassigned'] as $char => $sp_array) : ?>

                        <optgroup label="<?php echo $char; ?>">

                            <?php foreach($sp_array as $sp) : ?>

                            <option value="<?php echo $sp['id']; ?>"><?php echo $sp['name']; ?></option>

                            <?php endforeach; ?>

                        </optgroup>

                        <?php endforeach; ?>

                        <?php endif; ?>

                    </select>

                   <input type="image" src="/img/btn/right.png" alt=">" />
                </form>
            </td>

            <td>
            	<form action="" method="post">

                    <input type="image" src="/img/btn/left.png" alt="<" />

                    <select name="assigned_service_providers[]" multiple="multiple" style="width:385px; height:250px;">

                    	<?php if(@$service_providers_select['assigned']) : ?>

                    	<?php foreach($service_providers_select['assigned'] as $char => $sp_array) : ?>

                        <optgroup label="<?php echo $char; ?>">

                            <?php foreach($sp_array as $sp) : ?>

                            <option value="<?php echo $sp['id']; ?>"><?php echo $sp['name']; ?></option>

                            <?php endforeach; ?>

                        </optgroup>

                        <?php endforeach; ?>

						<?php endif; ?>

                    </select>

                </form>
            </td>
        </tr>
    </table>

</div>

<a href="/admin/options/groups" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
