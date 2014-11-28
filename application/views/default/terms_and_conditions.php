<style type="text/css">
	div.body_content {
        width:900px;
    }
</style>
<label for="terms_and_conditions">Terms and conditions</label>
<?php if(@$_GET['new']) : ?>
<p>In order to use Call it Quits, you must agree to the folowing Terms and Conditions set.</p>
<?php else : ?>
<p>The Terms and Conditions have changed since the last time you logged in. Please review any changes and agree to them in order to use Call it Quits.</p>
<?php endif; ?>

<div class="item"><?php echo $terms_and_conditions['value']; ?></div>

<?php if( ! @$_GET['new']) : ?>
<label for="last_changes">Changes</label>
<p>Below is a summary of the changes made to the Terms and Conditions since the last time you agreed to them.</p>
<div class="item"><?php echo $terms_and_conditions['last_changes']; ?></div>
<?php endif; ?>

<div>
	<a href="?agree=0&amp;token=<?php echo $token; ?>" style="float:left;"><img src="/img/btn/disagree.png" alt="Disagree" /></a>

    <a href="?agree=1&amp;token=<?php echo $token; ?>" style="float:right;"><img src="/img/btn/agree.png" alt="Agree" /></a>
</div>
