<style type="text/css">
	table.form tr th {
		width:150px;
	}
</style>
<div class="header">
	<h2><?php echo (empty($ms) ? 'Add new' : 'Update') ?> source</h2>
</div>

<div class="item">

	<form action="" method="post" id="marketing_source_form">

		<?php
		if ( ! empty($ms))
		{
			echo form_hidden('ms_id', $ms['ms_id']);
		}
		?>

		<table class="form">

			<tr>
				<th><label for="ms_title">Title</label></th>
				<td><input type="text" name="ms_title" id="ms_title" class="text" maxlength="128" size="40" value="<?php echo set_value('ms_title', element('ms_title', $ms)) ?>" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th>
					<label for="ms_active">Enabled</label>
					<small>Allow this item to be selected.</small>
				</th>
				<td>
					<input type="hidden" name="ms_active" value="0">
					<input type="checkbox" name="ms_active" id="ms_active" value="1" <?php echo set_checkbox('ms_active', '1', (element('ms_active', $ms, 1) == 1)) ?>/>
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
	<h2>Marketing Sources</h2>
</div>

<div class="item results">

	<?php if($marketing_sources) : ?>

	<table class="results">

		<tr class="order">
			<th>Title</th>
			<th style="width: 32px">Enabled</th>
			<th style="width: 32px">Edit</th>
			<th style="width: 32px">Delete</th>
		</tr>

		<?php foreach($marketing_sources as $item) : ?>

		<tr class="row">
			<td><a href="<?php echo site_url('admin/options/marketing_sources/' . $item['ms_id']) ?>"><?php echo $item['ms_title']; ?></a></td>
			<td><img src="/img/icons/<?php echo ($item['ms_active'] == 1 ? 'tick' : 'cross'); ?>.png" alt="" /></td>
			<td><a href="<?php echo site_url('admin/options/marketing_sources/' . $item['ms_id']) ?>"><img src="/img/icons/edit.png" alt="Edit" /></td>
            <td>
            	<?php if ($item['ms_id'] >= 100): ?>
            	<a href="<?php echo site_url('admin/options/marketing_sources/' . $item['ms_id']) ?>?delete=1" class="delete-source"><img src="/img/icons/cross.png" alt="Delete" />
            	<?php endif; ?>
            </td>
		</tr>

		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">No marketing sources have been created.</p>

	<?php endif; ?>

</div>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
