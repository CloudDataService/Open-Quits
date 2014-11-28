<div class="header">
	<h2>Import</h2>
</div>

<div class="item">

	<p>Import a correctly formatted CSV file containing the required missing information.</p>

	<form action="" method="post" id="csv_import" enctype="multipart/form-data">
        <table class="form">
            <tr>
                <th><label for="userfile">CSV file</label></th>
                <td><input type="file" name="userfile" id="userfile" /></td>
                <td><input type="image" src="/img/btn/import.png" alt="Import" /></td>
                <td class="e"><?php if(@$upload_errors) echo $upload_errors; ?></td>
            </tr>
        </table>
    </form>

</div>

<div class="header">
	<h2>Export</h2>
</div>

<div class="item">

	<p>Click to export a formatted CSV file containing <?php echo ($total == 1 ? '1 client' : $total . ' clients'); ?> missing either their NHS number, GP code or both. <a href="?export=1" class="action" title="Click OK to export <?php echo ($total == 1 ? '1 row' : $total . ' rows'); ?> to CSV."><img src="/img/btn/export.png" alt="Export" style="vertical-align:middle;" /></a></p>

</div>
