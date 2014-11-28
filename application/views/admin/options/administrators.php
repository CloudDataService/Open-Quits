<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

	<form action="" method="post" id="admin_form">

		<input type="hidden" name="admin_id" id="admin_id" value="<?php echo @$admin['id']; ?>" />

		<table class="form">

			<tr>
				<th><label for="fname">First name</label></th>
				<td><input type="text" name="fname" id="fname" value="<?php echo htmlentities(@$admin['fname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="sname">Surname</label></th>
				<td><input type="text" name="sname" id="sname" value="<?php echo htmlentities(@$admin['sname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email">Email</label></th>
				<td><input type="text" name="email" id="email" value="<?php echo htmlentities(@$admin['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email_confirmed">Confirm email</label></th>
				<td><input type="text" name="email_confirmed" id="email_confirmed" value="<?php echo htmlentities(@$admin['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="password">Password</label></th>
				<td><input type="password" name="password" id="password" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="password_confirmed">Confirm password</label></th>
				<td><input type="password" name="password_confirmed" id="password_confirmed" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pct_id">Local Authority</label></th>
				<td>
					<select name="pct_id" id="pct_id">

						<option value="0">-- Unassigned --</option>

						<?php foreach($pcts as $pct) : ?>
						<option value="<?php echo $pct['id']; ?>" <?php if(@$admin['pct_id'] == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
						<?php endforeach; ?>

					</select>
				</td>
			</tr>

			<tr style="display: none" class="master-admin-row">
				<th><label for="master">Master admin</label></th>
				<td><input type="checkbox" name="master" id="master" value="1" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td></td>
				<td><?php if(@$admin) echo '<a href="?delete=1" class="action" title="Click OK to permanently delete this administrator." style="float:left;"><img src="/img/btn/delete.png" alt="Delete" /></a>'; ?><input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" /></td>
			</tr>

		</table>

	</form>
</div>

<div class="header">
	<h2>Administrators</h2>
</div>

<div class="item results">
	<?php if($admins) : ?>

		<table class="results">

			<tr class="order">

				<th>Name</th>
				<th>Email</th>
				<th>Last login</th>
				<th>Local Authority</th>
				<th>Edit</th>

			</tr>

			<?php foreach($admins as $admin) : ?>

			<tr class="row">

				<td><?php echo $admin['fname'] . ' ' . $admin['sname']; ?></td>
				<td><?php echo $admin['email']; ?></td>
				<td><?php echo $admin['datetime_last_login_format']; ?></td>
				<td><?php echo $admin['pct_name']; ?></td>
				<td><a href="/admin/options/administrators/<?php echo $admin['id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>

			</tr>

			<?php endforeach; ?>

		</table>

	<?php else : ?>

	<p class="no_results">There are no other administrators.</p>

	<?php endif; ?>
</div>


<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
