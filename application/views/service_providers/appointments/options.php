<p>Use this section to configure the appointment scheduling times for your service.</p><br><br>

<?php echo form_open('', array('id' => 'sp_')) ?>

	<div class="item">

		<table class="form">

			<tr class="vat">
				<th>
					<label for="ao_length">Appointment length</label>
					<small>in minutes</small>
				</th>
				<td><?php
				$name = 'ao_length';
				echo form_input(array(
					'type' => 'text',
					'name' => $name,
					'id' => $name,
					'maxlength' => 2,
					'size' => 10,
					'class' => 'text',
					'value' => element('ao_length', @$options[1]),
				)) ?></td>
				<td class="e"></td>
			</tr>

		</table>

	</div>

	<table class="sp-appointment-options">

		<tr>
			<th>&nbsp;</th>
			<th>Capacity <span class="hint">(number of parallel appointments)</span></th>
			<th>First appointment <span class="hint">(24hr time, 08:30)</span></th>
			<th>Last appointment <span class="hint">(24hr time, 17:30)</span></th>
		</tr>

		<?php foreach (config_item('days_long') as $num => $day_name): ?>

		<?php $options_day = element($num, $options, array()); ?>

		<tr>

			<th><?php echo $day_name ?></th>

			<td>
				<?php
				$capacity_options[0] = '(Closed)';
				for ($i = 1; $i <= 10; $i++) { $capacity_options[$i] = $i; }
				echo form_dropdown('day[' . $num . '][ao_capacity]', $capacity_options, element('ao_capacity', $options_day), 'class="capacity"');
				?>
			</td>

			<td>
				<?php echo form_input(array(
					'name' => 'day[' . $num . '][ao_first_appt_time]',
					'id' => 'ao_first_appt_time_' . $num,
					'value' => element('ao_first_appt_time_format', $options_day),
					'class' => '',
					'size' => 10,
				)) ?>
			</td>

			<td>
				<?php echo form_input(array(
					'name' => 'day[' . $num . '][ao_last_appt_time]',
					'id' => 'ao_first_appt_time_' . $num,
					'value' => element('ao_last_appt_time_format', $options_day),
					'class' => '',
					'size' => 10,
				)) ?>
			</td>

		</tr>

		<?php endforeach; ?>

	</table>

	<br><br>

	<div class="functions">
		<input type="image" src="/img/btn/save.png" style="float:right" alt="Save" />
	</div>

</form>


<script type="text/javascript">
$("select.capacity").on("change", function() {
	var $row = $(this).parents("tr");
	var $inputs = $row.find("input[type='text']");
	($(this).val() == 0) ? $inputs.hide() : $inputs.show();
}).trigger("change");
</script>
