<style type="text/css">
	table.form tr th {
		width:155px;
	}
</style>
<?php if(@$service_provider_staff) : ?>
<div class="functions">
	<a href="?delete=1" class="action" title="Click OK to permanently delete this staff member."><img src="/img/btn/delete.png" alt="Delete" /></a>

	<div class="clear"></div>
</div>
<?php endif; ?>

<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

	<form action="" method="post" id="service_provider_staff_form">

		<input type="hidden" name="sps_id" id="sps_id" value="<?php echo @$service_provider_staff['id']; ?>" />

		<table class="form">

			<tr>
				<th>Service provider</th>
				<td><?php echo $service_provider['name']; ?></td>
			</tr>

			<tr>
				<th><label for="fname">First name</label></th>
				<td><input type="text" name="fname" id="fname" value="<?php echo htmlentities(@$service_provider_staff['fname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="sname">Surname</label></th>
				<td><input type="text" name="sname" id="sname" value="<?php echo htmlentities(@$service_provider_staff['sname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email">Email address</label></th>
				<td><input type="text" name="email" id="email" value="<?php echo htmlentities(@$service_provider_staff['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email_confirmed">Confirm email</label></th>
				<td><input type="text" name="email_confirmed" id="email_confirmed" value="<?php echo htmlentities(@$service_provider_staff['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:250px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="advisor_code">Advisor Code</label></th>
				<td><input type="text" name="advisor_code" id="advisor_code" value="<?php echo htmlentities(@$service_provider_staff['advisor_code'], ENT_QUOTES); ?>" class="text" style="width:auto; text-transform:uppercase;" maxlength="7" /></td>
				<td class="e"></td>
			</tr>

			<?php if( ! @$service_provider_staff) : ?>
				<tr>
					<th>Password</th>
					<td><span id="password" style="font-weight:bold;"><?php echo $default_password; ?></span></td>
				</tr>
			<?php endif; ?>

			<tr>
				<th><label for="master">Make admin</label></th>
				<td><input type="checkbox" name="master" id="master" value="1" <?php if(@$service_provider_staff['master']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr class="vat">
				<th>
					<label for="send_email">Send notifcation email</label>
					<?php if(@$service_provider_staff) : ?>
					<small>Notifies staff member that their details have been updated</small>
					<?php else : ?>
					<small>Notifies staff member that they have been added to Call it Quits</small>
					<?php endif; ?>
				</th>
				<td><input type="checkbox" name="send_email" id="send_email" value="1" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<?php if(@$service_provider_staff) : ?>
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

			<tr class="vat">
				<th>
					<label for="send_email">Send notifcation email</label>
					<small>Notifies staff member that their password has been changed</small>
				</th>
				<td><input type="checkbox" name="send_email" id="send_email" value="1" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<?php endif; ?>

<a href="/admin/service-providers/info/<?php echo $service_provider['id']; ?>" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
