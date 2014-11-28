<style type="text/css">
	div.body_content {
        width:350px;
    }
</style>
<?php if($admin = $this->session->flashdata('admin')) : ?>

	<table class="login">

    	<tr>
        	<td><label for="email">Email:</label> <?php echo $admin['email']; ?></td>
        </tr>

        <tr>
        	<td><label for="password">Password:</label> <?php echo $admin['password']; ?></td>
        </tr>

        <tr>
        	<td><a href="/home/conference"><img src="/img/btn/back.png" alt="Back" /></a></td>
        </tr>

    </table>

<?php else : ?>
<form action="" method="post">

	<table class="login">

    	<tr>
        	<td><label for="fname">First name:</label> <input type="text" name="fname" id="fname" class="text" /></td>
        </tr>

    	<tr>
        	<td><label for="sname">Surname:</label> <input type="text" name="sname" id="sname" class="text" /></td>
        </tr>

    	<tr>
        	<td><label for="email">Email:</label> <input type="text" name="email" id="email" class="text" /></td>
        </tr>

        <tr>
        	<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
        </tr>

    </table>

</form>
<?php endif; ?>
