<style type="text/css">
	table.form tr th {
		width:150px;
	}
</style>
<div class="header">
	<h2><?php echo (empty($template) ? 'Add new' : 'Update') ?> SMS template</h2>
</div>

<div class="item">

	<form action="" method="post" id="sms_template_form">

		<?php
		if ( ! empty($template))
		{
			echo form_hidden('sms_t_id', $template['sms_t_id']);
		}
		?>

		<table class="form">

			<tr>
				<th><label for="sms_t_title">Title</label></th>
				<td><input type="text" name="sms_t_title" id="sms_t_title" class="text" maxlength="64" size="40" value="<?php echo set_value('sms_t_title', element('sms_t_title', $template)) ?>" /></td>
				<td class="e"></td>
			</tr>

			<tr class="vat">
				<th><label for="sms_t_text">Message</label></th>
				<td>
					<textarea name="sms_t_text" id="sms_t_text" class="text" maxlength="1024" cols="60" rows="4"><?php echo set_value('sms_t_text', element('sms_t_text', $template)) ?></textarea>
					<br>
					<span class="sms_count" id="sms_t_text_sms_count"></span>. <span class="chars_remaining" id="sms_t_text_chars_remaining"></span>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th>
					<label for="sms_t_enabled">Enabled</label>
					<small>Allow this template to be used.</small>
				</th>
				<td>
					<input type="hidden" name="sms_t_enabled" value="0">
					<input type="checkbox" name="sms_t_enabled" id="sms_t_enabled" value="1" <?php echo set_checkbox('sms_t_enabled', '1', (element('sms_t_enabled', $template, 1) == 1)) ?>/>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<div class="header">
	<h2>SMS Templates</h2>
</div>

<div class="item results">

	<?php if($templates) : ?>

	<table class="results">

		<tr class="order">
			<th>Title</th>
			<th>Text</th>
			<th>Enabled</th>
			<th>Edit</th>
			<th>Delete</th>
		</tr>

		<?php foreach($templates as $t) : ?>

		<tr class="row">
			<td><a href="<?php echo site_url('admin/options/sms_templates/' . $t['sms_t_id']) ?>"><?php echo $t['sms_t_title']; ?></a></td>
			<td><?php echo word_limiter($t['sms_t_text'], 15) ?></td>
			<td><img src="/img/icons/<?php echo ($t['sms_t_enabled'] == 1 ? 'tick' : 'cross'); ?>.png" alt="" /></td>
			<td><a href="<?php echo site_url('admin/options/sms_templates/' . $t['sms_t_id']) ?>"><img src="/img/icons/edit.png" alt="Edit" /></td>
            <td><a href="<?php echo site_url('admin/options/sms_templates/' . $t['sms_t_id']) ?>?delete=1" class="delete-template"><img src="/img/icons/cross.png" alt="Delete" /></td>
		</tr>

		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">No SMS templates have been created.</p>

	<?php endif; ?>

</div>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
