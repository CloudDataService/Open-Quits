<style type="text/css">
	table.form tr th {
        width:75px;
    }
    input.text {
        width:250px;
    }
</style>
<div class="header">
	<h2>My details</h2>
</div>

<div class="item">

    <form action="" method="post" id="my_account_form">

        <table class="form">

            <tr>
                <th><label for="email">Email</label></th>
                <td><input type="text" name="email" id="email" value="<?php echo htmlentities($this->session->userdata('email'), ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="email_confirmed">Confirm email</label></th>
                <td><input type="text" name="email_confirmed" id="email_confirmed" value="<?php echo htmlentities($this->session->userdata('email'), ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="fname">First name</label></th>
                <td><input type="text" name="fname" id="fname" value="<?php echo htmlentities($this->session->userdata('fname'), ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:auto;" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="sname">Surname</label></th>
                <td><input type="text" name="sname" id="sname" value="<?php echo htmlentities($this->session->userdata('sname'), ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:auto;" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/update.png" alt="Update" /></td>
            </tr>

        </table>

    </form>

</div>

<div class="header">
	<h2>My password</h2>
</div>

<div class="item">

    <form action="" method="post" id="my_account_password_form">

        <table class="form">

            <tr>
                <th><label for="current_password">Current password</label></th>
                <td><input type="password" name="current_password" id="current_password" class="text" /></td>
                <td class="e"></td>
            </tr>

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
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/update.png" alt="Update" /></td>
            </tr>

        </table>

    </form>

</div>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
