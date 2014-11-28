<form method="post" action="<?php echo current_url() ?>">

	<?php echo form_hidden('sms_t_id', $template['sms_t_id']) ?>

	<div class="header">
		<h2>Message</h2>
	</div>

	<div class="item">
		<p><?php echo $template['sms_t_text'] ?></p>
	</div>


	<div class="header">
		<h2>Administration</h2>
	</div>

	<div class="item">

		<table class="form">

			<tr class="vat">
				<th>
					<label for="c_notes">Notes</label>
					<small>Enter some notes <br>about this communication (optional).</small>
				</th>
				<td>
					<textarea name="c_notes" id="c_notes" class="text" maxlength="1024" cols="60" rows="4"></textarea>
				</td>
				<td class="e"></td>
			</tr>

		</table>
	</div>


	<div class="header">
		<h2>Valid clients (<?php echo count($monitoring_forms) ?>)</h2>
	</div>

	<div class="item results" style="height: 300px; overflow-y: scroll">

		<?php if($monitoring_forms) : ?>

		<table class="results">

			<tr class="order">
				<th>ID</th>
				<th>Name</th>
			</tr>

			<?php foreach($monitoring_forms as $mf) : ?>

			<?php echo form_hidden('mf_id[]', $mf['id']) ?>

			<tr class="row no_click">
				<td>#<?php echo $mf['id']; ?></td>
				<td><?php echo $mf['client_name']; ?></td>
			</tr>

			<?php endforeach; ?>

		</table>

		<?php else : ?>

		<p class="no_results">No clients found.</p>

		<?php endif; ?>

	</div>

	<input type="image" src="/img/btn/ok.png" alt="OK" />

</form>