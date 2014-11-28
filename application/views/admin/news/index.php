<div class="functions">

	<a href="/admin/news/set"><img src="/img/btn/add-new.png" alt="Add new" /></a>

	<div class="clear"></div>

</div>


<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/news" method="get">

		<table class="filter">

			 <tr>
					<th><label for="date_from">Date from</label></th>
					<th><label for="date_to">Date to</label></th>
					<th><label for="pp">Category</label></th>
					<th><label for="pp">Results per page</label></th>
				</tr>

				<tr>
					<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
					<td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
					<td><?php echo form_dropdown('nc_id', $news_categories, $this->input->get('nc_id'), 'id="nc_id"') ?></td>
					<td>
						<select name="pp" id="pp">
							<?php foreach($pp as $pp) : ?>
							<option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td><input type="image" src="/img/btn/filter.png" alt="Filter" /></td>
					<td><a href="/admin/news"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
				</tr>

		</table>

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>News</h2>
</div>

<div class="item results">

	<?php if($news) : ?>

	<table class="results">

		<tr class="order">
			<th>Date created</th>
			<th>Category</th>
			<th>Title</th>
			<th>Body</th>
			<th>More</th>
		</tr>

		<?php foreach($news as $news_item) : ?>
		<tr class="row vat">
			<td style="width:90px;"><?php echo $news_item['datetime_created_format']; ?></td>
			<td><?php echo $news_item['nc_title'] ?></td>
			<td><?php echo $news_item['title']; ?></td>
			<td><?php echo $news_item['body_excerpt']; ?></td>
			<td><a href="/admin/news/set/<?php echo $news_item['id']; ?>"><img src="/img/icons/magnifier.png" alt="More" /></a></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">Your search returned no news items.</p>

	<?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>