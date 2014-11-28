<?php if ($mmf): ?>
<div class="functions">
	<a href="?delete=1" class="action" title="Are you sure you want to delete this mail merge field?"><img src="/img/btn/delete.png" alt="Delete" /></a>
	<div class="clear"></div>
</div>
<?php endif; ?>

<div class="header">
	<h2>Mail merge field details</h2>
</div>

<div class="item">

	<?php echo form_open('', array('id' => 'mail_merge_field_form')) ?>

		<table class="form">

			<tr class="vat">
				<th style="width:200px">
					<label for="mmf_name">Name</label>
					<small>Lowercase, no spaces.</small>
				</th>
				<td><?php
				$name = 'mmf_name';
				echo form_input(array(
					'type' => 'text',
					'name' => $name,
					'id' => $name,
					'maxlength' => 32,
					'size' => 20,
					'class' => 'text',
					'style' => 'text-transform: lowercase',
					'value' => element($name, $mmf),
				)) ?></td>
				<td class="e"><?php echo form_error($name) ?></td>
			</tr>

			<tr class="vat">
				<th>
					<label for="mmf_description">Description</label>
					<small>Something to remind you about what this field is for</small>
				</th>
				<td><?php
				$name = 'mmf_description';
				echo form_input(array(
					'type' => 'text',
					'name' => $name,
					'id' => $name,
					'maxlength' => 255,
					'size' => 50,
					'class' => 'text',
					'value' => element($name, $mmf),
				)) ?></td>
				<td class="e"><?php echo form_error($name) ?></td>
			</tr>

			<tr>
				<th><label for="mmf_format">Format</label></th>
				<td><?php
				$formats = array(
					'' => '-- Please select --',
					'single' => 'Single-line',
					'multi' => 'Multi-line',
				);
				echo form_dropdown('mmf_format', $formats, element('mmf_Format', $mmf), 'id="mmf_format"');
				?></td>
				<td class="e"><?php echo form_error('mmf_format') ?></td>
			</tr>

			<tr class="vat">
				<th>
					<label for="mmf_value">Value</label>
					<small>The actual value that will be displayed in mail merge documents.</small>
				</th>
				<td><?php
				$name = 'mmf_value';
				echo form_textarea(array(
					'name' => $name,
					'id' => $name,
					'rows' => 8,
					'cols' => 50,
					'value' => element($name, $mmf),
				)) ?></td>
				<td class="e"><?php echo form_error($name) ?></td>
			</tr>

			<tr>
				<td></td>
				<td>
					<input type="image" src="/img/btn/save.png" alt="Save" />
				</td>
			</tr>

		</table>

	</form>
</div>
