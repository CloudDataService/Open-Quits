<div class="header">
    <h2>Add/Update</h2>
</div>

<div class="item">

    <form action="" method="post" id="pct_form">

        <table class="form">

            <tr>
                <th><label for="pct_name">Local Authority name</label></th>
                <td><input type="text" name="pct_name" id="pct_name" class="text" value="<?php echo @$selected_pct['pct_name']; ?>" maxlength="16" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><a href="/admin/options/pcts"><img src="/img/btn/cancel.png" alt="Cancel" /></a> <input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>

        </table>

    </form>

</div>

<div class="header">
    <h2>Local Authorities</h2>
</div>



<div class="item results">

	<?php if($pcts) : ?>

    <table class="results">

    	<tr class="order">
        	<th style="width:500px;">Local Authority name</th>
            <th>Total service providers</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>

    	<?php foreach($pcts as $pct) : ?>

        <tr class="row">
            <td><?php echo $pct['pct_name']; ?></td>
            <td><?php echo $pct['total_service_providers']; ?></td>
            <td><a href="?pct_id=<?php echo $pct['id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></td>
            <td><a href="?pct_id=<?php echo $pct['id']; ?>&amp;delete=1" class="delete-pct"><img src="/img/icons/cross.png" alt="Delete" /></td>
        </tr>

        <?php endforeach ;?>

    </table>

    <?php else : ?>

    <p class="no_results">There are no listed Local Authorities</p>

    <?php endif; ?>

</div>

<?php if(@$selected_pct) : ?>

<div class="header">
	<h2>Service providers</h2>
</div>

<div class="item">

	<p>Assign service providers to <?php echo $selected_pct['pct_name']; ?> Local Authority.</p>

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

<?php endif; ?>



