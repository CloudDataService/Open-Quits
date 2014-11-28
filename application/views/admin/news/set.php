<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

	<form action="" method="post" id="news_form">

		<table class="form">

			<tr>
				<th><label for="nc_id">Category</label></th>
				<td><?php echo form_dropdown('nc_id', $news_categories, set_value('nc_id', element('nc_id', $news_item, 1))) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="title">Title</label></th>
				<td><input type="text" name="title" id="title" value="<?php echo htmlentities(@$news_item['title'], ENT_QUOTES, 'UTF-8'); ?>" class="text" maxlength="128" style="width:700px;" /></td>
				<td class="e"></td>
			</tr>

			<tr class="vat">
				<th><label for="body">Body</label></th>
				<td><textarea name="body" id="body" style="width:714px; height:400px;"><?php echo @$news_item['body']; ?></textarea></td>
				<td class="e"></td>
			</tr>


			<?php $news_pcts = explode(',', @$news_item['pct_ids']); ?>

			<tr class="vat">
				<th><label for="title">Local Authorities</label></th>
				<td class="checks">
					<label for="all_areas" style="clear: both">
						<?php
						echo form_hidden('all_areas', '0');
						echo form_checkbox(array(
							'name' => 'all_areas',
							'id' => 'all_areas',
							'value' => '1',
							'checked' => (int) $news_item['all_areas'] === 1,
						));
						?>All
					</label>

					<?php foreach ($pcts as $pct): ?>

					<label for="pct_<?php echo $pct['id'] ?>" style="clear: both">
						<?php
						echo form_checkbox(array(
							'name' => 'news_pcts[]',
							'id' => 'pct_' . $pct['id'],
							'value' => $pct['id'],
							'checked' => in_array($pct['id'], $news_pcts),
							'class' => 'pct',
						));
						echo $pct['pct_name'];
						?>
					</label>

					<?php endforeach; ?>

				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<td></td>
				<td><?php if(@$news_item) echo '<a href="?delete=1" class="action" title="Click OK to delete this news item." style="float:left;"><img src="/img/btn/delete.png" alt="Delete" /></a>'; ?><input type="image" src="/img/btn/save.png" alt="Save" style="float:right;" /></td>
			</tr>

		</table>

	</form>

</div>

<a href="/admin/news" class="back"><img src="/img/btn/back.png" alt="Back" /></a>