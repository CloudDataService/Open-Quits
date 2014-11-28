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

    <form action="" method="post" id="terms_and_conditions_form">

    <?php echo form_hidden('pct_id', $options_pct_id); ?>

        <div class="header">
            <h2>Enable</h2>
        </div>

        <div class="item">

            <table class="form">
                <th><label>Enable terms and conditions</label></th>
                <td><input type="checkbox" name="on" id="on" value="1" <?php if($terms_and_conditions['on']) echo 'checked="checked"'; ?></td>
            </table>

        </div>

        <div class="header">
            <h2>Terms and conditions</h2>
        </div>

        <div class="item">

            <textarea name="value" id="value" style="width:100%; height:400px;"><?php echo $terms_and_conditions['value']; ?></textarea>

        </div>

        <div class="header">
            <h2>Changes</h2>
        </div>

        <div class="item">

            <p>Please detail a brief summary of any changes made to the terms and conditions, this will help existing service providers review the new terms and conditions more easily.</p>

            <textarea name="last_changes" id="last_changes" style="width:100%;"><?php echo $terms_and_conditions['last_changes']; ?></textarea>

        </div>

        <input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" />

    </form>

    <a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>

<?php endif; ?>
