<style type="text/css">
	table.form tr th {
        width:200px;
    }
    tr.contact {
        display:none;
    }
</style>

<div class="header">
    <h2>New support ticket</h2>
</div>

<div class="item">

	<p>We are sorry that you are experiencing a problem with Call it Quits, however, by letting us know about it quickly we can make sure it is fixed as soon as possible.</p>

    <form action="" method="post" id="support_form">

        <table class="form">

            <tr>
                <th><label for="problem">What specifically are you having a problem with?</label></th>
                <td>
                    <select name="problem" id="problem">

                        <option value="">-- Please select --</option>
                        <option value="Monitoring forms">Monitoring forms</option>
                        <option value="Claims">Claims</option>
                        <option value="News">News</option>
                        <option value="My account">My account</option>
                        <option value="Staff">Staff</option>
                        <option value="Other">Other</option>

                    </select>
                </td>
                <td class="e"></td>
            </tr>

            <tr class="vat">
                <th>
                    <label for="subject">Subject</label>
                    <small>(Brief summary of issue)</small>
                </th>
                <td><input name="subject" id="subject" class="text" /></td>
                <td class="e"></td>
            </tr>

            <tr class="vat">
                <th>
                    <label for="description">Please can you provide us with as much information as possible?</label>
                    <small>(Where there any error messages?, what page were you on?, what were you trying to do?)</small>
                </th>
                <td><textarea name="description" id="description" class="text" style="width:525px; height:275px;"></textarea></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="contact_telephone">Telephone number</label></th>
                <td><input type="text" name="contact_telephone" id="contact_telephone" class="text" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th><label for="contact_time">When is the best time to contact you?</label></th>
                <td><input type="text" name="contact_time" id="contact_time" class="text" style="width:300px;" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/submit.png" alt="Send" /></td>
            </tr>

        </table>

    </form>
</div>
