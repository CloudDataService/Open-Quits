<noscript>
<meta http-equiv="refresh" content="0; url=/home/javascript">
</noscript>
<style type="text/css">
	div.body_content {
        width:350px;
    }
	div.body_content {
		position:relative;
	}
    img.sotw {
        display:block;
        position:absolute;
		right:0;
		margin-top:30px;
    }
</style>
<?php if(@$banned) : ?>

<div class="message error">
    <p style="background-position:top left;">You have failed to login 3 times. You will not be able to attempt another login for 10 minutes (<?php echo $banned['datetime_set_format']; ?>).
    If you need to login before this time please call <?php echo $support_options['tel_support']; ?> and quote "<?php echo $banned['ip']; ?>".</p>
</div>

<?php elseif(@$failed_login) : ?>

<div class="message error">
	<p>Sorry, but your login attempt failed.</p>
</div>

<?php elseif(@$_GET['terms_and_conditions']) : ?>

<div class="message error">
	<p style="background-position:top left;">You must agree to the Terms and Conditions in order to use Call it Quits.</p>
</div>

<?php elseif(@$_GET['timeout']) : ?>

<div class="message action">
	<p>For security reaons you were timed out of your session.</p>
</div>

<?php elseif(@$_GET['logged_out']) : ?>

<div class="message action">
	<p>You have been successfully logged out.</p>
</div>

<?php elseif(@$_GET['auth_failed']) : ?>

<div class="message action">
	<p>Please login to access this page.</p>
</div>

<?php endif; ?>

<form action="/" method="post" id="login_form">

	<table class="login">

		<tr>
			<td><label for="email">Email:</label> <input type="text" name="email" id="email" class="text" autocomplete="off"/></td>
		</tr>

		<tr>
			<td><label for="password">Password:</label> <input type="password" name="password" id="password" class="text" autocomplete="off"/></td>
		</tr>

		<tr>
			<td style="text-align:right;"><input type="image" src="/img/btn/login.png" alt="Login" /></td>
		</tr>

	</table>

</form>

<div class="problem">

    <p><a href="" id="problems_logging_in">Problem logging in? Click here</a></p>

	<div class="notice">
    	<?php echo nl2br($support_options['problems_logging_in']); ?>
    </div>

</div>

<img src="/img/style/NHS-logo-sotw.png" alt="NHS South of Tyne and Wear logo" class="sotw" />
