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
				<th><a href="?order=r.title<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'r.title') echo ' class="' . $_GET['sort'] . '"'; ?>>Title</a></th>
				<th><a href="?order=rc.title<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'rc.title') echo ' class="' . $_GET['sort'] . '"'; ?>>Category</a></th>
				<th>Description</th>
				<th>Type</th>
				<th>Size</th>
				<th>Download</th>
			</tr>

			<?php foreach($resources as $resource) : ?>
				<tr class="row">
					<td style="width:150px;"><?php echo $resource['title']; ?></td>
					<td><?php echo $resource['cat_title']; ?></td>
					<td style="width:350px;"><?php echo $resource['description']; ?></td>
					<td><img src="/img/icons/<?php echo $resource['file_ext']; ?>.png" alt="<?php echo $resource['file_ext']; ?>" /></td>
					<td><?php echo $resource['file_size']; ?></td>
					<td style="text-align:center;">
						<a href="<?php echo ($resource['file_ext'] == 'link' ? $resource['link'] : '/resources/' . $resource['file_name']); ?>" target="_blank" data-link='1'>
							<img src="/img/icons/download.png" alt="Download" />
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="no_results">No resources returned.</p>
	<?php endif; ?>
</div>

<?php echo $this->pagination->create_links(); ?>
