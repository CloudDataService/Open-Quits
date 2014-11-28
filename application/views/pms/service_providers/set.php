<style type="text/css">
	table.form tr th {
        width:150px;
    }
</style>
<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

    <form action="" method="post" id="service_provider_form">

        <table class="form">

            <tr>
                <th><label for="name">Name</label></th>
                <td><input type="text" name="name" id="name" value="<?php echo htmlentities(@$service_provider['name'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
                <td class="e"></td>
            </tr>

            <tr>
            	<th><label for="post_code">Post code</label></th>
                <td><input type="text" name="post_code" id="post_code" value="<?php echo htmlentities(@$service_provider['post_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text" maxlength="8" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="location">Location/setting</label></th>
                <td>
                	<select name="location" id="location">
                    	<option value="">-- Please select --</option>

                        <?php foreach($locations as $location) : ?>

                        <option value="<?php echo $location; ?>" <?php if(@$service_provider['location'] == $location) echo 'selected="selected"'; ?>><?php echo $location; ?></option>

                        <?php endforeach; ?>

                        </option>
                    </select>
                </td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="department">Department/ward</label></th>
                <td><input type="text" name="department" id="department" value="<?php echo htmlentities(@$service_provider['department'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="venue">Venue</label></th>
                <td><input type="text" name="venue" id="venue" value="<?php echo htmlentities(@$service_provider['venue'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="telephone">Telephone</label></th>
                <td><input type="text" name="telephone" id="telephone" value="<?php echo htmlentities(@$service_provider['telephone'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
                <td class="e"></td>
            </tr>

            <tr>
            	<th><label for="pct">PCT</label></th>
                <td>
                	<select name="pct" id="pct">

                    	<option value="">-- Unassigned --</option>

                        <?php foreach($pcts as $pct) : ?>
                        <option value="<?php echo $pct['pct_name']; ?>" <?php if(@$service_provider['pct'] == $pct['pct_name']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>

        </table>

    </form>

</div>

<a href="/pms/service-providers" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
