<style type="text/css">
	table.quick_links {
        width:50%;
        float:left;
    }
    	table.quick_links tr td {
            padding:15px 5px;
        }
</style>
<div class="header">
	<h2>Quick links</h2>
</div>
<div class="item">

	<table class="form quick_links">

    	<tr class="row">
            <td><img src="/img/icons/account.png" alt="My account" /></td>
            <td>
                <a href="/admin/options/my-account">My account</a>
                <p>Change your email address or password login details.</p>
            </td>
        </tr>

    	<tr class="row">
            <td><img src="/img/icons/claims.png" alt="Claim options" /></td>
            <td>
                <a href="/admin/options/claim-options">Claim options</a>
                <p>Set the cost of claim types and choose claim processing settings.</p>
            </td>
        </tr>

        <tr class="row">
            <td><img src="/img/icons/mobile.png" alt="SMS templates" /></td>
            <td>
                <a href="/admin/options/sms-options">SMS templates</a>
                <p>Edit the available SMS templates for bulk and individual sending.</p>
            </td>
        </tr>

        <tr class="row">
            <td><img src="/img/icons/resources.png" alt="Resources" /></td>
            <td>
                <a href="/admin/options/resources">Resources</a>
                <p>Add service provider resources.</p>
            </td>
        </tr>

        <?php if($this->session->userdata('master')) : ?>

        <tr class="row">
            <td><img src="/img/icons/admins.png" alt="Administrators" /></td>
            <td>
                <a href="/admin/options/administrators">Administrators</a>
                <p>Add extra administrators to the Call it Quits system.</p>
            </td>
        </tr>

        <tr class="row">
            <td><img src="/img/icons/pms_staff.png" alt="PMS Staff" /></td>
            <td>
                <a href="/admin/options/pms-staff">Programme Management System</a>
                <p>Manage accounts for the Programme Management System.</p>
            </td>
        </tr>

        <?php endif; ?>

    </table>


	<table class="form quick_links">

    	<tr class="row">
            <td><img src="/img/icons/terms-and-conditions.png" alt="Terms and conditions" /></td>
            <td>
                <a href="/admin/options/terms-and-conditions">Terms and conditions</a>
                <p>Set terms and conditions that service providers must agree in order to login.</p>
            </td>
        </tr>

        <tr class="row">
            <td><img src="/img/icons/security.png" alt="Security" /></td>
            <td>
                <a href="/admin/options/security-options">Security options</a>
                <p>Keep Call it Quits secure.</p>
            </td>
        </tr>

        <tr class="row">
            <td><img src="/img/icons/groups.png" alt="Groups" /></td>
            <td>
                <a href="/admin/options/groups">Groups</a>
                <p>Group service providers into specific groups.</p>
            </td>
        </tr>

       	<tr class="row">
            <td><img src="/img/icons/export-schemas.png" alt="Export schemas" /></td>
            <td>
                <a href="/admin/options/export-schemas">Export schemas</a>
                <p>Create export schemas for monitoring forms and claims CSV outputs.</p>
            </td>
        </tr>

       	<tr class="row">
            <td><img src="/img/icons/advizorz.png" alt="Advisors" /></td>
            <td>
                <a href="/admin/options/advisors">Advisors</a>
                <p>Add, update and remove advisors.</p>
            </td>
        </tr>


    </table>

    <div class="clear"></div>

</div>
