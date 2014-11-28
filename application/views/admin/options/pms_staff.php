<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

	<form action="" method="post" id="pmss_form">

		<input type="hidden" name="pmss_id" id="pmss_id" value="<?php echo @$pmss['pmss_id']; ?>" />

		<table class="form">

			<tr>
				<th><label for="pmss_fname">First name</label></th>
				<td><input type="text" name="pmss_fname" id="pmss_fname" value="<?php echo htmlentities(@$pmss['pmss_fname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pmss_sname">Surname</label></th>
				<td><input type="text" name="pmss_sname" id="pmss_sname" value="<?php echo htmlentities(@$pmss['pmss_sname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pmss_email">Email</label></th>
				<td><input type="text" name="pmss_email" id="pmss_email" value="<?php echo htmlentities(@$pmss['pmss_email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pmss_email_confirmed">Confirm email</label></th>
				<td><input type="text" name="pmss_email_confirmed" id="pmss_email_confirmed" value="<?php echo htmlentities(@$pmss['pmss_email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pmss_password">Password</label></th>
				<td><input type="password" name="pmss_password" id="pmss_password" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pmss_password_confirmed">Confirm password</label></th>
				<td><input type="password" name="pmss_password_confirmed" id="pmss_password_confirmed" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td></td>
				<td><?php if(@$admin) echo '<a href="?delete=1" class="action" title="Click OK to permanently delete this PMS staff." style="float:left;"><img src="/img/btn/delete.png" alt="Delete" /></a>'; ?><input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" /></td>
			</tr>

		</table>

	</form>
</div>

<div class="header">
	<h2>PMS Staff</h2>
</div>

<div class="item results">
	<?php if ($all_staff) : ?>

		<table class="results">

			<tr class="order">

				<th>Name</th>
				<th>Email</th>
				<th>Last login</th>
				<th>Edit</th>

			</tr>

			<?php foreach ($all_staff as $staff) : ?>

			<tr class="row">

				<td><?php echo $staff['pmss_fname'] . ' ' . $staff['pmss_sname']; ?></td>
				<td><?php echo $staff['pmss_email']; ?></td>
				<td><?php echo $staff['pmss_datetime_last_login_format']; ?></td>
				<td><a href="/admin/options/pms-staff/<?php echo $staff['pmss_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>

			</tr>

			<?php endforeach; ?>

		</table>

	<?php else : ?>

	<p class="no_results">There are no PMS staff.</p>

	<?php endif; ?>
</div>


<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
