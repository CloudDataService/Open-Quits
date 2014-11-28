<style type="text/css">
	div#graph_div {
		width:870px;
		height:300px;
	}
    div.header {
        width:auto;
        line-height:20px;
    }
    	div.header select {
            float:right;
        }
    select, select option {
        font-size:13px;
        padding-left:0px;
    }
</style>
<?php if($this->session->userdata('change_password')) : ?>
<div class="error">
	<p style="background-position:top left">You are currently using the default password for your service provider. This password is long and hard to remember, for security reasons please change it now by <a href="/service-providers/my-account#my_account_password_form">clicking here</a>.</p>
</div>
<?php endif; ?>

<?php if($this->session->userdata('pct_id') == 3): ?>
<div class="error general-success">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	<p><strong>This opportunity is now closed and we will not be taking any further expressions of interest. Thank you for your interest.</strong></p>
	<br><br>
	<strong>Gateshead:</strong> We are looking for a dedicated and motivated Active Intervention provider within the
	Gateshead area who would be interested in offering stop smoking support within the Queen Elizabeth Hospital.
	A consultation room will be provided free of charge, times and days to be confirmed.
</p>
</div>

<div class="error general-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	<strong>Gateshead:</strong> In light of the new contract, the ruling around prescribing <strong>Quickmist</strong> and the <strong>nasal Spray on a NRT voucher</strong> still applies.<br>
	This is currently under review from the Medicines Management Team. You will be notified in due course, should they become available again on the voucher system.
</p>
</div>
<?php endif; ?>

<div class="error general-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	Please note that Champix and Zyban can <strong>NOT</strong> be given to <strong>pregnant or breastfeeding women</strong><br>Please check your prescribing guidelines for other contra indications.
</p>
</div>

<div class="error general-success">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	<strong>New Client Form</strong> – Must be used from 1st April 2014 to collect additional data. Download it from the <a href="<?php echo site_url('service-providers/help/resources') ?>">Resources</a> page.
</p>
</div>

<div class="error critical-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	With immediate affect, all advisors must ask every new client whether they are receiving treatment from any other Stop Smoking Service.<br>
	The Client Consent Form <strong>MUST</strong> be signed in order for the Client to register with your service.
</p>
</div>

<div class="error general-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	<strong>NEW CLIENT CONSENT FORM &amp; NRT RECEIPT FORM</strong>
	Please could all advisors destroy copies of the old Client Consent Form. There is a new version on page 2 of the resources page. In addition to this form a second form must also be signed and held on file. This is on page 1 and is named NRT Receipt Form. Hopefully these forms will help prevent clients from signing up with multiple providers.
</p>
</div>

<div class="error general-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	<strong>To ALL Providers.</strong>
	Please write 'NHS' in permanent marker over the barcode of all products dispensed as part of the NHS Stop Smoking Service.
</p>
</div>

<div class="error general-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	<strong>All Advisors Warning.</strong>
	Please be aware there are a number of individuals signing up with multiple providers to obtain NRT under false pretences. Please call the Hub Team on <strong>0800 531 6317</strong> to report any concerns.
</p>
</div>

<div class="error general-error">
<p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
	Please be aware that <strong>Cools Lozenge</strong> and <strong>Icy White Gum</strong> are not on the formulary and are not to be prescribed on a NRT voucher.
	A stop smoking medicines review is currently underway and all advisors will be informed in due course of changes from staff within the Stop Smoking Service.
</p>
</div>

<?php if($this->session->userdata('pct_id') == 3): ?>
<div class="error general-error">
    <p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">
    <strong>URGENT!! GATESHEAD ADVISORS PLEASE READ</strong><br><br>Please note that <strong>NRT QuickMist</strong> and <strong>Nasal Spray</strong> have been suspended for use with NHS Stop Smoking Service clients with Immediate Effect.  Advisors can use their discretion with existing clients on whether to continue supplying these products for the remainder of the clients’ quit attempts. Please contact the hub on <strong>0800 531 6317</strong> with any questions or concerns.</p>
</div>
<?php endif; ?>

<?php if (empty($appointment_options)): ?>
<div class="error general-error">
    <p style="background-position:top left; background-image: url(/img/icons/exclamation.png);">We have a new feature for smoking cessation appointments. Before you can use this feature you must set your opening times and capacity.
    <strong>Please <a href="/service-providers/appointments/options">click here</a> to do this now.</strong> You can then manage all appointments by clicking the Appointments tab in the menu above.</p>
</div>
<?php endif; ?>

<!--
<div class="error general-error">
    Call it Quits has been updated for the new 12-week quit. <a href="/">Click here to read more.</a>
</div> -->


<div class="panel_left">
	<div class="header">
    	<h2>Clients approaching follow up (4 Week)</h2>
    </div>
    <div class="item results">
    	<?php $this->load->view('service_providers/home/approaching_follow_up', array('monitoring_forms' => $approaching_follow_up[4])) ?>
    </div>
</div>


<div class="panel_right">
	<div class="header">
		<h2>Clients approaching follow up (12 Week)</h2>
	</div>

	<div class="item results">
		<?php $this->load->view('service_providers/home/approaching_follow_up', array('monitoring_forms' => $approaching_follow_up[12])) ?>
	</div>
</div>


<div class="clear"></div>


<div class="header">
	<h2>Quick links</h2>
</div>

<div class="item results">

	<table class="form quick_links">

		<tr class="row">

			<td><img src="/img/icons/add-new.png" alt="Add new" /></td>
			<td>
				<a href="/service-providers/monitoring-forms/set">Add new monitoring form</a>
				<p>Add a new stop smoking monitoring form and client information.</p>
			</td>

			<td><img src="/img/icons/stats.png" alt="View statistics" /></td>
			<td>
				<a href="/service-providers/monitoring-forms/statistics">View statistics</a>
				<p>View NHS Connecting for Health Information Governance compliant statistics.</p>
			</td>

			<td><img src="/img/icons/help.png" alt="Help" /></td>
			<td>
				<a href="/service-providers/help">Help</a>
				<p>If you are new to Call it Quits or need help with something, read the help documentation.</p>
			</td>

		</tr>

	</table>

</div>


<div class="header">

    <select name="range" id="range">
        <option value="month">This month</option>
        <option value="week">This week</option>
        <option value="year">This year</option>
    </select>

    <h2>Total claims</h2>

</div>


<div class="item">

    <div id="graph_div">

        <img src="" alt="" id="graph" />

    </div>

</div>
