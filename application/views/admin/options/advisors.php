<div class="header">
	<h2><?php echo $set_title ?></h2>
</div>

<div class="item">

	<form action="" method="post" id="group_form">

		<?php if ($advisor): ?>
		<input type="hidden" name="a_id" value="<?php echo $advisor['a_id'] ?>" />
		<a href="?a_id=<?php echo $advisor['a_id'] ?>&amp;delete=1" class="action" title="Click OK to permanently delete this advisor."><img src="/img/btn/delete.png" alt="Delete" /></a>
		<?php endif; ?>

		<table class="form">

			<tr>
				<th><label for="a_number">Advisor number</label></th>
				<td><input type="text" name="a_number" id="a_number" class="text" maxlength="7" style="text-transform: uppercase" value="<?php echo element('a_number', $advisor) ?>" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="a_fname">First name</label></th>
				<td><input type="text" name="a_fname" id="a_fname" class="text" size="20" maxlength="32" value="<?php echo element('a_fname', $advisor) ?>" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="a_sname">Last name</label></th>
				<td><input type="text" name="a_sname" id="a_sname" class="text" size="20" maxlength="32" value="<?php echo element('a_sname', $advisor) ?>" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/options/advisors" method="get" id="filter_form">

		<table class="filter" style="width: 100%">

			<tr>
				<th><label for="a_number">Number</label></th>
				<th><label for="a_fname">First name</label></th>
				<th><label for="a_sname">Last name</label></th>
				<th><label for="pp">Results per page</label></th>
				<th></th>
			</tr>

			<tr>
				<td>
					<input type="text" name="a_number" id="a_number" value="<?php echo $this->input->get('a_number') ?>" class="text" size="10" style="text-transform: uppercase" />
				</td>
				<td>
					<input type="text" name="a_fname" id="a_fname" value="<?php echo $this->input->get('a_fname') ?>" class="text" size="20" />
				</td>
				<td>
					<input type="text" name="a_sname" id="a_sname" value="<?php echo $this->input->get('a_sname') ?>" class="text" size="20" />
				</td>
				<td>
					<select name="pp" id="pp">
						<?php foreach($pp as $pp) : ?>
						<option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td style="text-align: right">
					<input type="image" src="/img/btn/filter.png" alt="Filter" />
					<a href="/admin/options/advisors"><img src="/img/btn/clear.png" alt="Clear" /></a>
				</td>

			</tr>

		</table>

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if ($advisors): ?>

	<table class="results">

		<tr class="order">
			<th><a href="?order=a_number<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'a_number') echo ' class="' . $_GET['sort'] . '"'; ?>>Number</a></th>
			<th><a href="?order=a_fname<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'a_fname') echo ' class="' . $_GET['sort'] . '"'; ?>>First name</a></th>
			<th><a href="?order=a_sname<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'a_sname') echo ' class="' . $_GET['sort'] . '"'; ?>>Last name</a></th>
		</tr>

		<?php foreach ($advisors as $a) : ?>
		<tr class="row">
			<td><a href="?a_id=<?php echo $a['a_id'] ?>"><?php echo $a['a_number'] ?></a></td>
			<td><?php echo $a['a_fname'] ?></td>
			<td><?php echo $a['a_sname'] ?></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">Your search returned no advisors.</p>

	<?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
