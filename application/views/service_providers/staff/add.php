<style type="text/css">
	table.form tr th {
		width:100px;
	}
</style>
<div class="header">
	<h2>Add staff member</h2>
</div>

<div class="item">

	<form action="" method="post" id="service_provider_staff_form">

		<table class="form">

			<tr>
				<th><label for="fname">First name</label></th>
				<td><input type="text" name="fname" id="fname" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="sname">Surname</label></th>
				<td><input type="text" name="sname" id="sname" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email">Email address</label></th>
				<td><input type="text" name="email" id="email" class="text" style="width:250px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email_confirmed">Confirm email</label></th>
				<td><input type="text" name="email_confirmed" id="email_confirmed" class="text" style="width:250px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="password">New password</label></th>
				<td><input type="password" name="password" id="password" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="password_confirmed">Confirm new password</label></th>
				<td><input type="password" name="password_confirmed" id="password_confirmed" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="advisor_code">Advisor Code</label></th>
				<td><input type="text" name="advisor_code" id="advisor_code" class="text" style="width:auto; text-transform:uppercase;" maxlength="7" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="master">Make admin</label></th>
				<td><input type="checkbox" name="master" id="master" value="1" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<a href="/service-providers/staff" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
