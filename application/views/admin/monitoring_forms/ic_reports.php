<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="" method="get">

		<table class="filter">

			<tr>
				<th><label for="date_from">Date from</label></th>
				<th><label for="date_to">Date to</label></th>
				<th><label for="date_type">Date type</label></th>
				<th><label for="sp_id">Service provider</label></th>
				<th><label for="PCT">PCT</label></th>
			</tr>

			<tr>
				<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
				<td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
				<td><select name="date_type">
					  <option value="qds" <?php if(@$_GET['date_type'] == 'qds') { echo 'selected="selected"'; } ?>>Agreed Quit Date</option>
					  <option value="dc" <?php if(@$_GET['date_type'] == 'dc') { echo('selected="selected"'); } ?>>Date Created</option>
				  </select></td>
				<td>
					<select name="sp_id" id="sp_id">

						<option value="">-- All --</option>

						<?php foreach($service_providers_select as $char => $sp_array) : ?>

						<optgroup label="<?php echo $char; ?>">

							<?php foreach($sp_array as $sp) : ?>

							<option value="<?php echo $sp['id']; ?>" <?php if(@$_GET['sp_id'] == $sp['id']) echo 'selected="selected"'; ?>><?php echo $sp['name']; ?></option>

							<?php endforeach; ?>

						</optgroup>

						<?php endforeach; ?>

					</select>
				</td>
				<td>
					<select name="pct_id" id="pct_id" <?php if ($pct_id) echo 'disabled="disabled"' ?>>

						<option value="">-- All --</option>

						<?php foreach($pcts_select as $pct) : ?>
						<option value="<?php echo $pct['id']; ?>" <?php if(@$_GET['pct_id'] == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name_truncated']; ?></option>
						<?php endforeach; ?>

					</select>
				</td>
			</tr>
		</table>

		<table class="filter" style="margin-top: 10px; width: 100%">
			<tr>
				<th><label for="quarter">Quarter</label></th>
				<th><label for="follow_up">Follow up</label></th>
				<th></th>
			</tr>
			<tr>
				<td>
					<?php echo quarters_dropdown('quarter', 'quarter', '2010', 'd/m/Y', $this->input->get('quarter'), 'class="js-quarters"') ?>
				</td>
				<td>
					<select name="follow_up">
						<option value="4" <?php echo (@$_GET['follow_up'] == 4) ? 'selected="selected"' : ''; ?>>4 week</option>
						<option value="12" <?php echo (@$_GET['follow_up'] == 12) ? 'selected="selected"' : ''; ?>>12 week</option>
					</select>
				</td>
				<td style="text-align: right;">
					<input type="image" src="/img/btn/filter.png" alt="Filter" /> <a href="/admin/monitoring-forms/ic-reports"><img src="/img/btn/clear.png" alt="Clear" /></a>
				</td>
			</tr>
		</table>

	</form>

</div>

<h2>Overall</h2>
<br>

<table class="stats">
	<tr>
		<th title="Total number of records where a quit date has been set">Total setting a quit date</th>
		<th title="Total number of records where a successful quit outcome has been recorded at 4 or 12 weeks">Total successful quits</th>
		<th title="Total number of records for male clients where a successful quit was recorded at <?php echo $follow_up ?> weeks">Total male quits</th>
		<th title="Total number of records for female clients where a successful quit was recorded at <?php echo $follow_up ?> weeks">Total female quits</th>
		<th title="Combined (male + female) total successful quits recorded at <?php echo $follow_up ?> weeks">Total quit at <?php echo $follow_up ?> weeks</th>
		<th title="Number of successful quits recorded at <?php echo $follow_up ?> that are CO verified">CO verified</th>
	</tr>
	<tr>
		<td><?php echo $ic['total']; ?></td>
		<td><?php echo $ic['total_quit_overall']; ?></td>
		<td><?php echo $ic['total_male_quit_overall']; ?></td>
		<td><?php echo $ic['total_female_quit_overall']; ?></td>
		<td><?php echo $ic['total_quit_' . $follow_up]; ?> <span class="percentage">(<?php echo percentage($ic['total_quit_' . $follow_up], $ic['total'], 1) ?>)</span></td>
		<td><?php echo $ic['total_quit_co_' . $follow_up]; ?> <span class="percentage">(<?php echo percentage($ic['total_quit_co_' . $follow_up], $ic['total_quit_' . $follow_up], 1) ?>)</span></td>
	</tr>
</table>


<h2>1A: Number of people setting a quit date and successful quitters by ethnic category and gender</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(1)</th>
		<th>(2)</th>
		<th>(3)</th>
		<th>(4)</th>
		<th>(5)</th>
		<th>(6)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th>Ethnic category and gender</th>

		<th>Males setting a quit date</th>
		<th>Females setting a quit date</th>
		<th>Total persons setting a quit date</th>

		<th>Males successfully quit</th>
		<th>Females successfully quit</th>
		<th>Total persons successfully quit</th>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="4">
		a White
		</td>
	</tr>
	<tr>
		<td class="no">01</td>
		<td class="header">British</td>
		<td><?php echo $ic['british_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['british_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['british_males_setting_a_quit_date'] + $ic['british_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['british_males_successfully_quit']; ?></td>
		<td><?php echo $ic['british_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['british_males_successfully_quit'] + $ic['british_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">02</td>
		<td class="header">Irish</td>
		<td><?php echo $ic['irish_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['irish_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['irish_males_setting_a_quit_date'] + $ic['irish_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['irish_males_successfully_quit']; ?></td>
		<td><?php echo $ic['irish_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['irish_males_successfully_quit'] + $ic['irish_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">03</td>
		<td class="header">Any other white background</td>
		<td><?php echo $ic['other_white_background_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_white_background_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['other_white_background_males_setting_a_quit_date'] + $ic['other_white_background_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['other_white_background_males_successfully_quit']; ?></td>
		<td><?php echo $ic['other_white_background_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['other_white_background_males_successfully_quit'] + $ic['other_white_background_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">04</td>
		<td class="header"><strong>Sub-total</strong></td>
		<td><?php echo $total_white_males_setting_a_quit_date = ($ic['british_males_setting_a_quit_date'] + $ic['irish_males_setting_a_quit_date'] + $ic['other_white_background_males_setting_a_quit_date']); ?></td>
		<td><?php echo $total_white_females_setting_a_quit_date = ($ic['british_females_setting_a_quit_date'] + $ic['irish_females_setting_a_quit_date'] + $ic['other_white_background_females_setting_a_quit_date']); ?></td>
		<td><?php echo $total_white_males_setting_a_quit_date + $total_white_females_setting_a_quit_date; ?></td>


		<td><?php echo $total_white_males_successfully_quit = ($ic['british_males_successfully_quit'] + $ic['irish_males_successfully_quit'] + $ic['other_white_background_males_successfully_quit']); ?></td>
		<td><?php echo $total_white_females_successfully_quit = ($ic['british_females_successfully_quit'] + $ic['irish_females_successfully_quit'] + $ic['other_white_background_females_successfully_quit']); ?></td>
		<td><?php echo $total_white_males_successfully_quit + $total_white_females_successfully_quit; ?></td>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="4">
		b Mixed
		</td>
	</tr>
	<tr>
		<td class="no">05</td>
		<td class="header">White and Black Caribbean</td>
		<td><?php echo $ic['white_and_black_caribbean_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['white_and_black_caribbean_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['white_and_black_caribbean_males_setting_a_quit_date'] + $ic['white_and_black_caribbean_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['white_and_black_caribbean_males_successfully_quit']; ?></td>
		<td><?php echo $ic['white_and_black_caribbean_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['white_and_black_caribbean_males_successfully_quit'] + $ic['white_and_black_caribbean_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">06</td>
		<td class="header">White and Black African</td>
		<td><?php echo $ic['white_and_black_african_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['white_and_black_african_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['white_and_black_african_males_setting_a_quit_date'] + $ic['white_and_black_african_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['white_and_black_african_males_successfully_quit']; ?></td>
		<td><?php echo $ic['white_and_black_african_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['white_and_black_african_males_successfully_quit'] + $ic['white_and_black_african_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">07</td>
		<td class="header">White and Asian</td>
		<td><?php echo $ic['white_and_asian_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['white_and_asian_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['white_and_asian_males_setting_a_quit_date'] + $ic['white_and_asian_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['white_and_asian_males_successfully_quit']; ?></td>
		<td><?php echo $ic['white_and_asian_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['white_and_asian_males_successfully_quit'] + $ic['white_and_asian_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">08</td>
		<td class="header">Any other mixed background</td>
		<td><?php echo $ic['other_mixed_groups_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_mixed_groups_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['other_mixed_groups_males_setting_a_quit_date'] + $ic['other_mixed_groups_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['other_mixed_groups_males_successfully_quit']; ?></td>
		<td><?php echo $ic['other_mixed_groups_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['other_mixed_groups_males_successfully_quit'] + $ic['other_mixed_groups_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">09</td>
		<td class="header"><strong>Sub-total</strong></td>
		<td><?php echo $total_mixed_males_setting_a_quit_date = ($ic['white_and_black_caribbean_males_setting_a_quit_date'] + $ic['white_and_black_african_males_setting_a_quit_date'] + $ic['white_and_asian_males_setting_a_quit_date'] + $ic['other_mixed_groups_males_setting_a_quit_date']); ?></td>
		<td><?php echo $total_mixed_females_setting_a_quit_date = ($ic['white_and_black_caribbean_females_setting_a_quit_date'] + $ic['white_and_black_african_females_setting_a_quit_date'] + $ic['white_and_asian_females_setting_a_quit_date'] + $ic['other_mixed_groups_females_setting_a_quit_date']); ?></td>
		<td><?php echo $total_mixed_males_setting_a_quit_date + $total_mixed_females_setting_a_quit_date; ?></td>

		<td><?php echo $total_mixed_males_successfully_quit = ($ic['white_and_black_caribbean_males_successfully_quit'] + $ic['white_and_black_african_males_successfully_quit'] + $ic['white_and_asian_males_successfully_quit'] + $ic['other_mixed_groups_males_successfully_quit']); ?></td>
		<td><?php echo $total_mixed_females_successfully_quit = ($ic['white_and_black_caribbean_females_successfully_quit'] + $ic['white_and_black_african_females_successfully_quit'] + $ic['white_and_asian_females_successfully_quit'] + $ic['other_mixed_groups_females_successfully_quit']); ?></td>
		<td><?php echo $total_mixed_males_successfully_quit + $total_mixed_females_successfully_quit; ?></td>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="4">
		c Asian or Asian British
		</td>
	</tr>
	<tr>
		<td class="no">10</td>
		<td class="header">Indian</td>
		<td><?php echo $ic['indian_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['indian_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['indian_males_setting_a_quit_date'] + $ic['indian_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['indian_males_successfully_quit']; ?></td>
		<td><?php echo $ic['indian_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['indian_males_successfully_quit'] + $ic['indian_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">11</td>
		<td class="header">Pakistani</td>
		<td><?php echo $ic['pakistani_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['pakistani_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['pakistani_males_setting_a_quit_date'] + $ic['pakistani_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['pakistani_males_successfully_quit']; ?></td>
		<td><?php echo $ic['pakistani_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['pakistani_males_successfully_quit'] + $ic['pakistani_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">12</td>
		<td class="header">Bangladeshi</td>
		<td><?php echo $ic['bangladeshi_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['bangladeshi_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['bangladeshi_males_setting_a_quit_date'] + $ic['bangladeshi_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['bangladeshi_males_successfully_quit']; ?></td>
		<td><?php echo $ic['bangladeshi_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['bangladeshi_males_successfully_quit'] + $ic['bangladeshi_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">13</td>
		<td class="header">Any other Asian background</td>
		<td><?php echo $ic['other_asian_background_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_asian_background_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['other_asian_background_males_setting_a_quit_date'] + $ic['other_asian_background_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['other_asian_background_males_successfully_quit']; ?></td>
		<td><?php echo $ic['other_asian_background_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['other_asian_background_males_successfully_quit'] + $ic['other_asian_background_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">14</td>
		<td class="header"><strong>Sub-total</strong></td>
		<td><?php echo $total_asian_males_setting_a_quit_date = ($ic['indian_males_setting_a_quit_date'] + $ic['pakistani_males_setting_a_quit_date'] + $ic['bangladeshi_males_setting_a_quit_date'] + $ic['other_asian_background_males_setting_a_quit_date']); ?></td>
		<td><?php echo $total_asian_females_setting_a_quit_date = ($ic['indian_females_setting_a_quit_date'] + $ic['pakistani_females_setting_a_quit_date'] + $ic['bangladeshi_females_setting_a_quit_date'] + $ic['other_asian_background_females_setting_a_quit_date']); ?></td>
		<td><?php echo $total_asian_males_setting_a_quit_date + $total_asian_females_setting_a_quit_date; ?></td>

		<td><?php echo $total_asian_males_successfully_quit = ($ic['indian_males_successfully_quit'] + $ic['pakistani_males_successfully_quit'] + $ic['bangladeshi_males_successfully_quit'] + $ic['other_asian_background_males_successfully_quit']); ?></td>
		<td><?php echo $total_asian_females_successfully_quit = ($ic['indian_females_successfully_quit'] + $ic['pakistani_females_successfully_quit'] + $ic['bangladeshi_females_successfully_quit'] + $ic['other_asian_background_females_successfully_quit']); ?></td>
		<td><?php echo $total_asian_males_successfully_quit + $total_asian_females_successfully_quit; ?></td>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="7">
		d Black or Black British
		</td>
	</tr>
	<tr>
		<td class="no">15</td>
		<td class="header">Caribbean</td>
		<td><?php echo $ic['caribbean_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['caribbean_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['caribbean_males_setting_a_quit_date'] + $ic['caribbean_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['caribbean_males_successfully_quit']; ?></td>
		<td><?php echo $ic['caribbean_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['caribbean_males_successfully_quit'] + $ic['caribbean_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">16</td>
		<td class="header">African</td>
		<td><?php echo $ic['african_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['african_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['african_males_setting_a_quit_date'] + $ic['african_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['african_males_successfully_quit']; ?></td>
		<td><?php echo $ic['african_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['african_males_successfully_quit'] + $ic['african_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">17</td>
		<td class="header">Any other Black background</td>
		<td><?php echo $ic['other_black_background_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_black_background_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['other_black_background_males_setting_a_quit_date'] + $ic['other_black_background_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['other_black_background_males_successfully_quit']; ?></td>
		<td><?php echo $ic['other_black_background_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['other_black_background_males_successfully_quit'] + $ic['other_black_background_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">18</td>
		<td class="header"><strong>Sub-total</strong></td>
		<td><?php echo $total_black_males_setting_a_quit_date = ($ic['caribbean_males_setting_a_quit_date'] + $ic['african_males_setting_a_quit_date'] + $ic['other_black_background_males_setting_a_quit_date']); ?></td>
		<td><?php echo $total_black_females_setting_a_quit_date = ($ic['caribbean_females_setting_a_quit_date'] + $ic['african_females_setting_a_quit_date'] + $ic['other_black_background_females_setting_a_quit_date']); ?></td>
		<td><?php echo ($total_black_males_setting_a_quit_date + $total_black_females_setting_a_quit_date); ?></td>

		<td><?php echo $total_black_males_successfully_quit = ($ic['caribbean_males_successfully_quit'] + $ic['african_males_successfully_quit'] + $ic['other_black_background_males_successfully_quit']); ?></td>
		<td><?php echo $total_black_females_successfully_quit = ($ic['caribbean_females_successfully_quit'] + $ic['african_females_successfully_quit'] + $ic['other_black_background_females_successfully_quit']); ?></td>
		<td><?php echo ($total_black_males_successfully_quit + $total_black_females_successfully_quit); ?></td>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="4">
		e Other ethnic groups
		</td>
	</tr>
	<tr>
		<td class="no">19</td>
		<td class="header">Chinese</td>
		<td><?php echo $ic['chinese_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['chinese_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['chinese_males_setting_a_quit_date'] + $ic['chinese_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['chinese_males_successfully_quit']; ?></td>
		<td><?php echo $ic['chinese_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['chinese_males_successfully_quit'] + $ic['chinese_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">20</td>
		<td class="header">Any other ethnic group</td>
		<td><?php echo $ic['other_ethnic_group_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_ethnic_group_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['other_ethnic_group_males_setting_a_quit_date'] + $ic['other_ethnic_group_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['other_ethnic_group_males_successfully_quit']; ?></td>
		<td><?php echo $ic['other_ethnic_group_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['other_ethnic_group_males_successfully_quit'] + $ic['other_ethnic_group_females_successfully_quit']); ?></td>
	</tr>
	<tr>
		<td class="no">21</td>
		<td class="header"><strong>Sub-total</strong></td>
		<td><?php echo $total_ethnic_males_setting_a_quit_date = ($ic['chinese_males_setting_a_quit_date'] + $ic['other_ethnic_group_males_setting_a_quit_date'])?></td>
		<td><?php echo $total_ethnic_females_setting_a_quit_date = ($ic['chinese_females_setting_a_quit_date'] + $ic['other_ethnic_group_females_setting_a_quit_date'])?></td>
		<td><?php echo ($total_ethnic_males_setting_a_quit_date + $total_ethnic_females_setting_a_quit_date); ?></td>

		<td><?php echo $total_ethnic_males_successfully_quit = ($ic['chinese_males_successfully_quit'] + $ic['other_ethnic_group_males_successfully_quit'])?></td>
		<td><?php echo $total_ethnic_females_successfully_quit = ($ic['chinese_females_successfully_quit'] + $ic['other_ethnic_group_females_successfully_quit'])?></td>
		<td><?php echo ($total_ethnic_males_successfully_quit + $total_ethnic_females_successfully_quit); ?></td>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="4">
		f Not stated
		</td>
	</tr>
	<tr>
		<td class="no">22</td>
		<td class="header">Not stated</td>
		<td><?php echo $ic['not_stated_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['not_stated_females_setting_a_quit_date']; ?></td>
		<td><?php echo ($ic['not_stated_males_setting_a_quit_date'] + $ic['not_stated_females_setting_a_quit_date']); ?></td>
		<td><?php echo $ic['not_stated_males_successfully_quit']; ?></td>
		<td><?php echo $ic['not_stated_females_successfully_quit']; ?></td>
		<td><?php echo ($ic['not_stated_males_successfully_quit'] + $ic['not_stated_females_successfully_quit']); ?></td>
	</tr>

	<tr class="subtitle">
		<td class="no">
		<td colspan="4">
		</td>
	</tr>
	<tr>
		<td class="no">23</td>
		<td class="header">Total</td>
		<td><?php echo $ic['total_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['total_females_setting_a_quit_date']; ?></td>
		<td><?php echo $total_setting_a_quit_date = $ic['total_males_setting_a_quit_date'] + $ic['total_females_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['total_males_successfully_quit']; ?></td>
		<td><?php echo $ic['total_females_successfully_quit']; ?></td>
		<td><?php echo $total_successfully_quit = $ic['total_males_successfully_quit'] + $ic['total_females_successfully_quit']; ?></td>
	</tr>

</table>

<h2>1B: Number of people setting a quit date by age, gender and outcome at 4 week follow up</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(7)</th>
		<th>(8)</th>
		<th>(9)</th>
		<th>(10)</th>
		<th>(11)</th>
		<th>(9 .. 11)</th>
		<th>(12)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>

		<th>All ages</th>
		<th>Under 18</th>
		<th>18 - 34</th>

		<th>35 - 44</th>
		<th>45 - 59</th>
		<th>18 - 59</th>
		<th>60 and over</th>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="8">
			Males
		</td>
	</tr>
	<tr>
		<td class="no">24</td>
		<td class="header">Total number setting a quit date</td>
		<td><?php echo $ic['total_males_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['males_under_18_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['males_18_34_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['males_35_44_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['males_45_59_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['males_18_59_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['males_60_and_over_setting_a_quit_date']; ?></td>
	</tr>
		<tr>
		<td class="no">25</td>
		<td class="header">Number who had successfully quit (self reported)</td>
		<td><?php echo $ic['total_males_quit_self_reported']; ?></td>
		<td><?php echo $ic['males_under_18_quit_self_reported']; ?></td>
		<td><?php echo $ic['males_18_34_quit_self_reported']; ?></td>
		<td><?php echo $ic['males_35_44_quit_self_reported']; ?></td>
		<td><?php echo $ic['males_45_59_quit_self_reported']; ?></td>
		<td><?php echo $ic['males_18_59_quit_self_reported']; ?></td>
		<td><?php echo $ic['males_60_and_over_quit_self_reported']; ?></td>
	</tr>
	<tr>
		<td class="no">26</td>
		<td class="header">Number who had not quit (self reported)</td>
		<td><?php echo $ic['total_males_not_quit']; ?></td>
		<td><?php echo $ic['males_under_18_not_quit']; ?></td>
		<td><?php echo $ic['males_18_34_not_quit']; ?></td>
		<td><?php echo $ic['males_35_44_not_quit']; ?></td>
		<td><?php echo $ic['males_45_59_not_quit']; ?></td>
		<td><?php echo $ic['males_18_59_not_quit']; ?></td>
		<td><?php echo $ic['males_60_and_over_not_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">27</td>
		<td class="header">Number not known/lost to follow-up</td>
		<td><?php echo $ic['total_males_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['males_under_18_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['males_18_34_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['males_35_44_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['males_45_59_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['males_18_59_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['males_60_and_over_lost_to_follow_up']; ?></td>
	</tr>
	<tr>
		<td class="no">28</td>
		<td class="header">Number who had successfully quit (self reported) where non-smoking status confirmed by CO validation</td>
		<td><?php echo $ic['total_males_co_verified']; ?></td>
		<td><?php echo $ic['males_under_18_co_verified']; ?></td>
		<td><?php echo $ic['males_18_34_co_verified']; ?></td>
		<td><?php echo $ic['males_35_44_co_verified']; ?></td>
		<td><?php echo $ic['males_45_59_co_verified']; ?></td>
		<td><?php echo $ic['males_18_59_co_verified']; ?></td>
		<td><?php echo $ic['males_60_and_over_co_verified']; ?></td>
	</tr>
	<tr>
		<td class="no"></td>
		<td class="header">Number referred to GP</td>
		<td><?php echo $ic['total_males_referred_to_gp']; ?></td>
		<td><?php echo $ic['males_under_18_referred_to_gp']; ?></td>
		<td><?php echo $ic['males_18_34_referred_to_gp']; ?></td>
		<td><?php echo $ic['males_35_44_referred_to_gp']; ?></td>
		<td><?php echo $ic['males_45_59_referred_to_gp']; ?></td>
		<td><?php echo $ic['males_18_59_referred_to_gp']; ?></td>
		<td><?php echo $ic['males_60_and_over_referred_to_gp']; ?></td>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="7">
		</td>
	</tr>
		<tr class="no">
		<th></th>
		<th></th>
		<th>(13)</th>
		<th>(14)</th>
		<th>(15)</th>
		<th>(16)</th>
		<th>(17)</th>
		<th>(15 .. 17)</th>
		<th>(18)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>

		<th>All ages</th>
		<th>Under 18</th>
		<th>18 - 34</th>

		<th>35 - 44</th>
		<th>45 - 59</th>
		<th>18 - 59</th>
		<th>60 and over</th>
	</tr>
	<tr class="subtitle">
		<td class="no">
		<td colspan="8">
			Females
		</td>
	</tr>
	<tr>
		<td class="no">29</td>
		<td class="header">Total number setting a quit date in the quarter</td>
		<td><?php echo $ic['total_females_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['females_under_18_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['females_18_34_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['females_35_44_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['females_45_59_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['females_18_59_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['females_60_and_over_setting_a_quit_date']; ?></td>
	</tr>
	<tr>
		<td class="no">30</td>
		<td class="header">Number who had successfully quit (self reported)</td>
		<td><?php echo $ic['total_females_quit_self_reported']; ?></td>
		<td><?php echo $ic['females_under_18_quit_self_reported']; ?></td>
		<td><?php echo $ic['females_18_34_quit_self_reported']; ?></td>
		<td><?php echo $ic['females_35_44_quit_self_reported']; ?></td>
		<td><?php echo $ic['females_45_59_quit_self_reported']; ?></td>
		<td><?php echo $ic['females_18_59_quit_self_reported']; ?></td>
		<td><?php echo $ic['females_60_and_over_quit_self_reported']; ?></td>
	</tr>
	<tr>
		<td class="no">31</td>
		<td class="header">Number who had not quit (self reported)</td>
		<td><?php echo $ic['total_females_not_quit']; ?></td>
		<td><?php echo $ic['females_under_18_not_quit']; ?></td>
		<td><?php echo $ic['females_18_34_not_quit']; ?></td>
		<td><?php echo $ic['females_35_44_not_quit']; ?></td>
		<td><?php echo $ic['females_45_59_not_quit']; ?></td>
		<td><?php echo $ic['females_18_59_not_quit']; ?></td>
		<td><?php echo $ic['females_60_and_over_not_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">32</td>
		<td class="header">Number not known/lost to follow-up</td>
		<td><?php echo $ic['total_females_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['females_under_18_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['females_18_34_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['females_35_44_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['females_45_59_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['females_18_59_lost_to_follow_up']; ?></td>
		<td><?php echo $ic['females_60_and_over_lost_to_follow_up']; ?></td>
	</tr>
	<tr>
		<td class="no">33</td>
		<td class="header">Number who had successfully quit (self reported) where non-smoking status confirmed by CO validation</td>
		<td><?php echo $ic['total_females_co_verified']; ?></td>
		<td><?php echo $ic['females_under_18_co_verified']; ?></td>
		<td><?php echo $ic['females_18_34_co_verified']; ?></td>
		<td><?php echo $ic['females_35_44_co_verified']; ?></td>
		<td><?php echo $ic['females_45_59_co_verified']; ?></td>
		<td><?php echo $ic['females_18_59_co_verified']; ?></td>
		<td><?php echo $ic['females_60_and_over_co_verified']; ?></td>
	</tr>
	<tr>
		<td class="no"></td>
		<td class="header">Number referred to GP</td>
		<td><?php echo $ic['total_females_referred_to_gp']; ?></td>
		<td><?php echo $ic['females_under_18_referred_to_gp']; ?></td>
		<td><?php echo $ic['females_18_34_referred_to_gp']; ?></td>
		<td><?php echo $ic['females_35_44_referred_to_gp']; ?></td>
		<td><?php echo $ic['females_45_59_referred_to_gp']; ?></td>
		<td><?php echo $ic['females_18_59_referred_to_gp']; ?></td>
		<td><?php echo $ic['females_60_and_over_referred_to_gp']; ?></td>
	</tr>
</table>

<h2>1C: Number of pregnant woman setting a quit date and outcome at 4 week follow up</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(19)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>
		<th>Number</th>
	</tr>
	<tr>
		<td class="no">34</td>
		<td class="header">Total number setting a quit date</td>
		<td><?php echo $ic['pregnant_women_setting_a_quit_date']; ?></td>

	</tr>
	<tr>
		<td class="no">35</td>
		<td class="header">Number who had successfully quit (self reported)</td>
		<td><?php echo $ic['pregnant_women_quit_self_reported']; ?></td>
	</tr>
	<tr>
		<td class="no">36</td>
		<td class="header">Number who had not quit (self reported)</td>
		<td><?php echo $ic['pregnant_women_not_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">37</td>
		<td class="header">Number not known/lost to follow-up</td>
		<td><?php echo $ic['pregnant_women_lost_to_follow_up']; ?></td>
	</tr>
	<tr>
		<td class="no">38</td>
		<td class="header">Number who had successfully quit (self reported) where non-smoking status confirmed by CO validation</td>
		<td><?php echo $ic['pregnant_women_co_verified']; ?></td>
	</tr>
</table>

<h2>1D: Number of people setting a quit date and successful quitters receiving free prescriptions.</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(20)</th>
		<th>(21)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>
		<th>Number setting a quit date</th>
		<th>Number successfully quit</th>
	</tr>
	<tr>
		<td class="no">39</td>
		<td class="header">Number eligible who received free prescriptions</td>
		<td><?php echo $ic['free_prescriptions_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['free_prescriptions_successfully_quit']; ?></td>
	</tr>
</table>

<h2>1E: Number of people setting a quit date and successful quitters by socio-economic classification.</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(22)</th>
		<th>(23)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>
		<th>Number setting a quit date</th>
		<th>Number successfully quit</th>
	</tr>
	<tr>
		<td class="no">40</td>
		<td class="header">Number of Full-time students</td>
		<td><?php echo $ic['students_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['students_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">41</td>
		<td class="header">Number who have never worked or unemployed for more than 1 year</td>
		<td><?php echo $ic['unemployed_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['unemployed_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">42</td>
		<td class="header">Number who have retired</td>
		<td><?php echo $ic['retired_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['retired_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">43</td>
		<td class="header">Number sick/disabled and unable to return to work</td>
		<td><?php echo $ic['disabled_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['disabled_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">44</td>
		<td class="header">Number of home carers (unpaid)</td>
		<td><?php echo $ic['home_carers_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['home_carers_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">45</td>
		<td class="header">Number in managerial and professional occupations</td>
		<td><?php echo $ic['managerial_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['managerial_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">46</td>
		<td class="header">Number in intermediate occupations</td>
		<td><?php echo $ic['intermediate_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['intermediate_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">47</td>
		<td class="header">Number in Routine and manual occupations</td>
		<td><?php echo $ic['manual_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['manual_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">48</td>
		<td class="header">Number in prison</td>
		<td><?php echo $ic['prisoner_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['prisoner_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">49</td>
		<td class="header">Unable to code</td>
		<td><?php echo $ic['unable_to_code_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['unable_to_code_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">50</td>
		<td class="header">Total number setting a quit date and successful quitters</td>
		<td><?php echo $total_setting_a_quit_date; ?></td>
		<td><?php echo $total_successfully_quit; ?></td>
	</tr>
</table>

<h2>1F: Number of people setting a quit date and successful quitters by pharmacotherapy treatment received.</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(24)</th>
		<th>(25)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>
		<th>Number setting a quit date</th>
		<th>Number successfully quit</th>
	</tr>

	<tr>
		<td class="no">51</td>
		<td class="header">Number who received NRT only</td>
		<td><?php echo $ic['nrt_only_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['nrt_only_successfully_quit']; ?></td>
	</tr>

	<tr>
		<td class="no">52</td>
		<td class="header">Number who received brupropion (Zyban) only</td>
		<td><?php echo $ic['zyban_only_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['zyban_only_successfully_quit']; ?></td>
	</tr>

	<tr>
		<td class="no">53</td>
		<td class="header">Number who received Champix (Varenicline) only</td>
		<td><?php echo $ic['champix_only_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['champix_only_successfully_quit']; ?></td>
	</tr>

	<tr>
		<td class="no">54</td>
		<td class="header">Number who received both NRT and brupropion (Zyban) either concurrently or consecutively</td>
		<td><?php echo $ic['nrt_zyban_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['nrt_zyban_successfully_quit']; ?></td>
	</tr>

	<tr>
		<td class="no">55</td>
		<td class="header">Number who received both NRT and Champix (Varenicline) consecutively</td>
		<td><?php echo $ic['nrt_champix_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['nrt_champix_successfully_quit']; ?></td>
	</tr>

	<tr>
		<td class="no">56</td>
		<td class="header">Number who did not receive NRT, brupropion (Zyban) or Champix (Varenicline)</td>
		<td><?php echo $ic['no_pharmacotherapy_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['no_pharmacotherapy_successfully_quit']; ?></td>
	</tr>

	<tr>
		<td class="no">57</td>
		<td class="header">Number where treatment option not known</td>
		<td>0</td>
		<td>0</td>
	</tr>

	<tr>
		<td class="no">58</td>
		<td class="header">Total number setting a quit date and successful quitters</td>
		<td><?php echo $total_setting_a_quit_date; ?></td>
		<td><?php echo $total_successfully_quit; ?></td>
	</tr>
</table>

<h2>1G: Number of people setting a quit date and successful quitters by intervention type.</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(26)</th>
		<th>(27)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>
		<th>Number setting a quit date</th>
		<th>Number successfully quit</th>
	</tr>

	<tr>
		<td class="no">59</td>
		<td class="header">Number who attended closed groups</td>
		<td><?php echo $ic['closed_group_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['closed_group_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">60</td>
		<td class="header">Number who attended open groups</td>
		<td><?php echo $ic['open_group_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['open_group_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">61</td>
		<td class="header">Number who attended drop-in clinics</td>
		<td><?php echo $ic['drop_in_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['drop_in_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">62</td>
		<td class="header">Number who attended one to ones</td>
		<td><?php echo $ic['one_to_one_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['one_to_one_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">63</td>
		<td class="header">Number who attended family/couples groups</td>
		<td><?php echo $ic['family_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['family_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">64</td>
		<td class="header">Number dealt with through telephone support sessions</td>
		<td><?php echo $ic['telephone_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['telephone_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">65</td>
		<td class="header">Number who attended other support</td>
		<td><?php echo $ic['other_support_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_support_successfully_quit']; ?></td>
	</tr>
</table>

<h2>1H: Number of people setting a quit date and successful quitters by intervention setting.</h2>

<table class="stats">
	<tr class="no">
		<th></th>
		<th></th>
		<th>(29)</th>
		<th>(30)</th>
	</tr>
	<tr>
		<td class="no"></td>
		<th></th>
		<th>Number setting a quit date</th>
		<th>Number successfully quit</th>
	</tr>
	<tr>
		<td class="no">69</td>
		<td class="header">Number using Stop Smoking Services setting</td>
		<td><?php echo $ic['stop_smoking_services_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['stop_smoking_services_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">70</td>
		<td class="header">Number using pharmacy setting</td>
		<td><?php echo $ic['pharmacy_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['pharmacy_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">71</td>
		<td class="header">Number using prison setting</td>
		<td><?php echo $ic['prison_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['prison_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">72</td>
		<td class="header">Number using primary care setting</td>
		<td><?php echo $ic['primary_care_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['primary_care_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">73</td>
		<td class="header">Number using hospital ward setting</td>
		<td><?php echo $ic['hospital_ward_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['hospital_ward_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">74</td>
		<td class="header">Number using dental practice setting</td>
		<td><?php echo $ic['dental_practice_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['dental_practice_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">75</td>
		<td class="header">Number using military base setting</td>
		<td><?php echo $ic['military_base_setting_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['military_base_setting_successfully_quit']; ?></td>
	</tr>
	<tr>
		<td class="no">76</td>
		<td class="header">Number using other setting</td>
		<td><?php echo $ic['other_setting_setting_a_quit_date']; ?></td>
		<td><?php echo $ic['other_setting_successfully_quit']; ?></td>
	</tr>
</table>