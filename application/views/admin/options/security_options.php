<style type="text/css">
	table.form tr th {
        width:150px;
    }
</style>

<?php if ( ! $pct_id): ?>

<div class="header">
    <h2>Local Authority</h2>
</div>

<form method="get" action="<?php echo current_url() ?>">

    <div class="item">

        <table class="form">

            <tr>
                <th>
                    <label for="initial">Local Authority</label>
                </th>
                <td>
                    <select name="pct_id" id="pct_id" <?php if ($pct_id): ?> disabled="disabled" <?php endif; ?>>
                        <option value="">-- Please Select --</option>
                        <?php foreach($pcts as $pct) : ?>
                        <option value="<?php echo $pct['id']; ?>" <?php if($this->input->get('pct_id') == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="e"></td>
            </tr>

        </table>

    </div>

</form>

<?php endif; ?>


<form action="" method="post" id="security_options_form">


    <?php if ($options_pct_id): ?>

    <?php echo form_hidden('pct_id', $options_pct_id); ?>

    <div class="header">
        <h2>Contact information</h2>
    </div>

    <div class="item">

        <table class="form">

            <tr>
                <th>
                    <label for="organisation_name">Organisation name</label>
                    <small>The name of your organisation or PCT.</small>
                </th>
                <td><input type="text" name="organisation_name" id="organisation_name" value="<?php echo htmlentities($contact_details['organisation_name'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th>
                    <label for="point_of_contact">Point of contact</label>
                    <small>The name of the person or department responsible for Call it Quits in your organisation.</small>
                </th>
                <td><input type="text" name="point_of_contact" id="point_of_contact" value="<?php echo htmlentities($contact_details['point_of_contact'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
                <td class="e"></td>
            </tr>

            <tr class="vat">
                <th><label for="address">Address</label></th>
                <td><textarea name="address" id="address" style="width:250px; height:100px;"><?php echo htmlentities($contact_details['address'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="email">Email address</label></th>
                <td><input type="text" name="email" id="email" value="<?php echo htmlentities($contact_details['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="telephone">Telephone number</label></th>
                <td><input type="text" name="telephone" id="telephone" value="<?php echo htmlentities($contact_details['telephone'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
                <td class="e"></td>
            </tr>

        </table>

    </div>

    <?php else: ?>


    <div class="header">
    	<h2>Support</h2>
    </div>

    <div class="item">

        <table class="form">

        	<tr>
            	<th>
                	<label for="tel_support_enabled">Enable telephone support</label>
                    <small>When enabled, support instructions appear in header.</small>
                </th>
                <td><input type="checkbox" name="tel_support_enabled" id="tel_support_enabled" value="1" <?php if(@$support_options['tel_support_enabled']) echo 'checked="checked"'; ?> /></td>
                <td class="e"></td>
            </tr>

        	<tr>
            	<th>
                	<label for="tel_support">Telephone number</label>
                    <small>The number service providers can call for telephone support.</small>
                </th>
                <td><input type="text" name="tel_support" id="tel_support" value="<?php echo htmlentities(@$support_options['tel_support'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
                <td class="e"></td>
            </tr>


        </table>

    </div>

    <div class="header">
        <h2>Log in</h2>
    </div>

    <div class="item">

        <table class="form">

            <tr class="vat">
                <th>
                    <label for="problems_logging_in">Problems logging in</label>
                    <small>Problem logging in message.</small>
                </th>
                <td><textarea name="problems_logging_in" style="width:500px; height:100px;"><?php echo htmlentities($support_options['problems_logging_in'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td class="e"></td>
            </tr>

        </table>

    </div>

    <div class="header" id="blocked_ips">
    	<h2>Blocked IPs</h2>
    </div>

    <div class="item results">

		<?php if($blocked_ips) : ?>

        <table class="results">

            <tr class="order">
                <th>IP</th>
                <th>Time blocked</th>
                <th>Unblock</th>
            </tr>

            <?php foreach($blocked_ips as $ip) : ?>
            <tr class="row">
     			<td><?php echo $ip['ip']; ?></td>
                <td><?php echo $ip['datetime_set_format']; ?></td>
                <td><a href="?unblock=<?php echo $ip['ip']; ?>"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
            </tr>
            <?php endforeach; ?>

        </table>

        <?php else : ?>

        <p class="no_results">There are currently no blocked users.</p>

        <?php endif; ?>

    </div>

    <?php endif; ?>

    <input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" />

</form>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
