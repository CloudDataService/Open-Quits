<style type="text/css">
	table.form tr th {
        width:200px;
    }
    textarea.text {
        width:400px;
        height:100px;
    }
    input.enabled {
        margin-right:8px;
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


<?php if ($options_pct_id): ?>

<form action="" method="post" id="sms_options_form">

    <?php echo form_hidden('pct_id', $options_pct_id); ?>

    <div class="header">
        <h2>Enable SMS</h2>
    </div>

    <div class="item">

    	<p>A total of <?php echo $total_sms_sent; ?> text messages have been sent to clients. You have a remaining <?php echo $total_sms_remaining; ?> text messages in your account.</p>

        <table class="form">

            <tr>
                <th>
                    <label for="enabled">Enable SMS</label>
                    <small>Enable SMS to send text messages to those clients who have chosen to receive them.</small>
                </th>
                <td><input type="checkbox" name="enabled" id="enabled" value="1" <?php if($sms_options['enabled']) echo 'checked="checked"'; ?> /></td>
            </tr>

        </table>
    </div>

    <div class="header">
        <h2>Text messages</h2>
    </div>

    <div class="item">

        <table class="form vat">

            <tr>
                <th>
                    <label for="texts_welcome_value">Welcome</label>
                    <small>The first text message the client will receive. This text is sent on the day of their agreed quit date.</small>
                </th>
                <td><textarea name="texts[welcome][value]" id="texts_welcome_value" class="text"><?php echo htmlentities($sms_options['texts']['welcome']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[welcome][enabled]" id="texts_welcome_enabled" value="1" class="enabled" <?php if($sms_options['texts']['welcome']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_welcome_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_week_1_value">Week 1</label>
                    <small>Text message client receives 1 week after agreed quit date.</small>
                </th>
                <td><textarea name="texts[week_1][value]" id="texts_week_1_value" class="text"><?php echo htmlentities($sms_options['texts']['week_1']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[week_1][enabled]" id="texts_week_1_enabled" value="1" class="enabled" <?php if($sms_options['texts']['week_1']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_week_1_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_week_2_value">Week 2</label>
                    <small>Text message client receives 2 weeks after agreed quit date.</small>
                </th>
                <td><textarea name="texts[week_2][value]" id="texts_week_2_value" class="text"><?php echo htmlentities($sms_options['texts']['week_2']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[week_2][enabled]" id="texts_week_2_enabled" value="1" class="enabled" <?php if($sms_options['texts']['week_2']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_week_2_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_week_3_value">Week 3</label>
                    <small>Text message client receives 3 weeks after agreed quit date.</small>
                </th>
                <td><textarea name="texts[week_3][value]" id="texts_week_3_value" class="text"><?php echo htmlentities($sms_options['texts']['week_3']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[week_3][enabled]" id="texts_week_3_enabled" value="1" class="enabled" <?php if($sms_options['texts']['week_3']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_week_3_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_follow_up_reminder_value">Follow up reminder (4)</label>
                    <small>Text mesage client receives 3 days before their 4 week follow up date to remind them they must attend.</small>
                </th>
                <td><textarea name="texts[follow_up_reminder][value]" id="texts_follow_up_reminder_value" class="text"><?php echo htmlentities($sms_options['texts']['follow_up_reminder']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[follow_up_reminder][enabled]" id="texts_follow_up_reminder_enabled" value="1" class="enabled" <?php if($sms_options['texts']['follow_up_reminder']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_follow_up_reminder_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_follow_up_12_reminder_value">Follow up reminder (12)</label>
                    <small>Text mesage client receives 3 days before their <strong>12 week</strong> follow up date to remind them they must attend.</small>
                </th>
                <td><textarea name="texts[follow_up_12_reminder][value]" id="texts_follow_up_12_reminder_value" class="text"><?php echo htmlentities(@$sms_options['texts']['follow_up_12_reminder']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[follow_up_12_reminder][enabled]" id="texts_follow_up_12_reminder_enabled" value="1" class="enabled" <?php if(@$sms_options['texts']['follow_up_12_reminder']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_follow_up_12_reminder_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_quit_value">Confirmed quit</label>
                    <small>Text mesage client receives upon a confirmed successful quit (Quit CO verified).</small>
                </th>
                <td><textarea name="texts[quit][value]" id="texts_quit_value" class="text"><?php echo htmlentities($sms_options['texts']['quit']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[quit][enabled]" id="texts_quit_enabled" value="1" class="enabled" <?php if($sms_options['texts']['quit']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_quit_enabled">Enable</label></td>
            </tr>

            <tr>
                <th>
                    <label for="texts_lost_to_follow_up_value">Lost to follow up</label>
                    <small>Text mesage client receives upon treatment outcome being marked as "Lost to follow up".</small>
                </th>
                <td><textarea name="texts[lost_to_follow_up][value]" id="texts_lost_to_follow_up_value" class="text"><?php echo htmlentities($sms_options['texts']['lost_to_follow_up']['value'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td><input type="checkbox" name="texts[lost_to_follow_up][enabled]" id="texts_lost_to_follow_up_enabled" value="1" class="enabled" <?php if($sms_options['texts']['lost_to_follow_up']['enabled']) echo 'checked="checked"'; ?> /> <label for="texts_lost_to_follow_up_enabled">Enable</label></td>
            </tr>

        </table>

    </div>

    <input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" />

</form>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>

<?php endif; ?>
