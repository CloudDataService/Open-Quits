<style type="text/css">
	table.form tr th {
		width:100px;
	}
</style>

<div class="functions">
	<a href="?delete=1" class="action" title="Click OK to permanently delete this staff member."><img src="/img/btn/delete.png" alt="Delete" /></a>

	<div class="clear"></div>
</div>

<div class="header">
	<h2>Update staff member</h2>
</div>

<div class="item">

	<form action="" method="post" id="service_provider_staff_form">

		<input type="hidden" name="sps_id" id="sps_id" value="<?php echo $service_provider_staff['id']; ?>" />

		<table class="form">

			<tr>
				<th><label for="fname">First name</label></th>
				<td><input type="text" name="fname" id="fname" value="<?php echo htmlentities($service_provider_staff['fname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="sname">Surname</label></th>
				<td><input type="text" name="sname" id="sname" value="<?php echo htmlentities($service_provider_staff['sname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email">Email address</label></th>
				<td><input type="text" name="email" id="email" value="<?php echo htmlentities($service_provider_staff['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email_confirmed">Confirm email</label></th>
				<td><input type="text" name="email_confirmed" id="email_confirmed" value="<?php echo htmlentities($service_provider_staff['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="advisor_code">Advisor Code</label></th>
				<td><input type="text" name="advisor_code" id="advisor_code" value="<?php echo htmlentities($service_provider_staff['advisor_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:auto; text-transform:uppercase;" maxlength="7" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="master">Make admin</label></th>
				<td><input type="checkbox" name="master" id="master" value="1" <?php if($service_provider_staff['master']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<div class="header">
	<h2>Training</h2>
</div>
<div class="item">
	<?php if(isset($service_provider_staff['spst_date'])): ?>
		Latest training: <?= $service_provider_staff['spst_date_format'] ?> <?= ($service_provider_staff['spst_title'] != '' ? '(' . $service_provider_staff['spst_title'] . ')' : '') ?>
		<br><br>
	<?php endif; ?>

	Record latest training taken by this staff member
	<form action="" method="post" id="training_form">

		<table class="form">
			<tr>
				<th><label for="spst_date">Date</label></th>
				<td><input type="text" name="spst_date" id="spst_date" value="" class="datepicker text" /></td>
				<td class="e"></td>
			</tr>
			<tr>
				<th><label for="spst_title">Title/ref (optional)</label></th>
				<td><input type="text" name="spst_title" id="spst_title" value="" class="text" /></td>
				<td class="e"></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>
		</table>

	</form>

</div>

<div class="header">
	<h2>Password</h2>
</div>

<div class="item">

	<form action="" method="post" id="password_form">

		<table class="form">

			<tr>
				<th><label for="new_password">New password</label></th>
				<td><input type="password" name="new_password" id="new_password" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="new_password_confirmed">Confirm new password</label></th>
				<td><input type="password" name="new_password_confirmed" id="new_password_confirmed" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<a href="/service-providers/staff" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
