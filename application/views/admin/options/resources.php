<style type="text/css">
	table.form tr th {
		width:100px;
	}
	div#resource {
		display:inline;
		position:absolute;
	}
</style>

<div class="header">
	<h2>Categories</h2>
</div>

<div class="item">
	<form action="" method="post" id="cat_form">
		<table class="form">
			<tr>
				<th><label for="cat_title">Category title</label></th>
				<td><input type="text" name="cat_title" id="cat_tile" value="<?php echo htmlentities(@$resource_category['title'], ENT_QUOTES, 'UTF-8'); ?>" class="text" maxlength="32" /></td>
				<td><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

			<?php foreach($resource_categories as $cat) : ?>
				<tr>
					<td></td>
					<td><a href="?cat_id=<?php echo $cat['id']; ?>"><?php echo $cat['title']; ?></a> <a href="?delete_cat=<?php echo $cat['id']; ?>" class="action" title="Click OK to delete this resource category."><img src="/img/icons/cross.png" alt="Delete" class="va_middle" /></a></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</form>
</div>

<div class="clear"></div>

<div class="header">
	<h2>Add resource</h2>
</div>

<div class="item">
	<form action="" method="post" id="resource_form" enctype="multipart/form-data">
		<table class="form">
			<tr>
				<th><label for="title">Title</label></th>
				<td><input type="text" name="title" id="title" value="<?php echo htmlentities(@$resource['title'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="cat_id">Category</label></th>
				<td>
					<select name="cat_id" id="cat_id">

						<option value="">-- Please select --</option>

						<?php foreach($resource_categories as $cat) : ?>

						<option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == @$resource['cat_id']) echo 'selected="selected"'; ?>><?php echo htmlentities($cat['title'], ENT_QUOTES, 'UTF-8'); ?></option>

						<?php endforeach; ?>

					</select>
				</td>
				<td class="e"></td>
			</tr>

			<tr class="vat">
				<th><label for="description">Description</label></th>
				<td><textarea name="description" id="description" style="width:300px; height:75px;" class="text"><?php echo htmlentities(@$resource['description'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="link">Link</label></th>
				<td><input type="text" name="link" id="link" value="<?php echo htmlentities(@$resource['link'], ENT_QUOTES, 'UTF-8'); ?>" class="text" size="40"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="userfile">File</label></th>
				<td style="position:relative;">
					<input type="file" name="userfile" id="usefile">
					<?php if(isset($resource) && !empty($resource) && $resource['file_ext'] != 'link') : ?>
						<div id="resource">
							<img src="/img/icons/<?php echo $resource['file_ext']; ?>.png" alt="" class="va_middle" /> <a href="/resources/<?php echo $resource['file_name']; ?>" class="window"><?php echo $resource['file_name']; ?></a> (<?php echo $resource['file_size']; ?>)
						</div>
					<?php endif; ?>
				</td>
				<td class="e"><?php if(@$upload_errors) echo $upload_errors; ?></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><a href="/admin/options/resources/"><img src="/img/btn/cancel.png" alt="Cancel" /></a> <input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>
		</table>
	</form>
</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Resources</h2>
</div>

<div class="item results">
	<?php if($resources) : ?>
		<table class="results">
			<tr class="order">
				<th>Title</th>
				<th>Category</th>
				<th>Description</th>
				<th>Type</th>
				<th>Size</th>
				<th></th>
			</tr>

			<?php foreach($resources as $upload) : ?>
				<?php if ($upload['deleted_at'] != '') continue; ?>
				<tr class="row">
					<td style="width:150px;"><a href="?resource_id=<?php echo $upload['id']; ?>"><?php echo $upload['title']; ?></a></td>
					<td><?php echo $upload['cat_title']; ?></td>
					<td style="width:350px;"><?php echo $upload['description']; if ($upload['file_ext'] == 'link') { echo ' (' . $upload['link'] . ')'; } ?></td>
					<td><img src="/img/icons/<?php echo $upload['file_ext']; ?>.png" alt="<?php echo $upload['file_ext']; ?>" /></td>
					<td><?php echo $upload['file_size']; ?></td>
					<td><a href="?delete=<?php echo $upload['id']; ?>" class="action" title="Click OK to delete <?php echo $upload['title']; ?>."><img src="/img/btn/delete.png" alt="Delete"></a></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="no_results">No resources returned.</p>
	<?php endif; ?>
</div>

<div class="header">
	<h2>Deleted Resources</h2>
</div>

<div class="item results">
	<?php if($deleted_resources) : ?>
		<table class="results">
			<tr class="order">
				<th>Title</th>
				<th>Category</th>
				<th>Description</th>
				<th>Type</th>
				<th>Size</th>
				<th></th>
			</tr>

			<?php foreach($deleted_resources as $deleted_upload) : ?>
				<tr class="row">
					<td style="width:150px;"><a href="?resource_id=<?php echo $deleted_upload['id']; ?>"><?php echo $deleted_upload['title']; ?></a></td>
					<td><?php echo $deleted_upload['cat_title']; ?></td>
					<td style="width:350px;"><?php echo $deleted_upload['description']; ?></td>
					<td><img src="/img/icons/<?php echo $deleted_upload['file_ext']; ?>.png" alt="<?php echo $deleted_upload['file_ext']; ?>" /></td>
					<td><?php echo $deleted_upload['file_size']; ?></td>
					<td><a href="?restore=<?php echo $deleted_upload['id']; ?>" class="action" title="Click OK to restore <?php echo $deleted_upload['title']; ?>."><img src="/img/btn/restore.png" alt="Restore"></a></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="no_results">No resources returned.</p>
	<?php endif; ?>
</div>

<?php echo $this->pagination->create_links(); ?>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>

<script type="text/javascript">
	$(function() {
		<?php if(isset($resource) && !empty($resource)) : ?>
			<?php if($resource['file_ext'] == 'link') : ?>
				$('input[name="userfile"]').attr('disabled', 'disabled')
			<?php else : ?>
				$('input[name="link"]').attr('disabled', 'disabled')
			<?php endif ?>
		<?php endif ?>

		$('input[name="link"]').bind('keyup change', function(event) {
			if ($(this).val() != '') {
				$('input[name="userfile"]').attr('disabled', 'disabled');
			} else {
				$('input[name="userfile"]').removeAttr('disabled');
			}
		});
	});
</script>
