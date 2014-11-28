<div class="functions">

	<a href="/service-providers/mail-merge/set-document"><img src="/img/btn/new-document.png" alt="New document" /></a>
	<a href="/service-providers/mail-merge/set-field"><img src="/img/btn/new-field.png" alt="New field" /></a>

	<div class="clear"></div>

</div>



<div class="header">
	<h2>Documents</h2>
</div>

<div class="item results">

	<?php if ($mmds): ?>

	<table class="results" id="mail_merge_documents">

		<thead>

			<tr class="order">
				<th><span>Title</span></th>
				<th style="width: 30px"><span>Edit</span></th>
			</tr>

		</thead>

		<tbody>

			<?php foreach ($mmds as $mmd): ?>

			<tr class="row">
				<td><?php echo $mmd['mmd_title'] ?></td>
				<td><a href="/service-providers/mail-merge/set-document/<?php echo $mmd['mmd_id']; ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
			</tr>

			<?php endforeach; ?>

		</table>

	<?php else : ?>

	<p class="no_results">There are no mail merge documents.</p>

	<?php endif; ?>

</div>




<div class="header">
	<h2>Custom fields</h2>
</div>

<div class="item results">

	<?php if ($mmfs): ?>

	<table class="results" id="mail_merge_fields">

		<thead>

			<tr class="order">
				<th><span>Field name</span></th>
				<th><span>Description</span></th>
				<th><span>Value</span></th>
				<th style="width: 30px"><span>View</span></th>
			</tr>

		</thead>

		<tbody>

			<?php foreach ($mmfs as $mmf): ?>

			<tr class="row">
				<td><?php echo element('mmf_name', $mmf) ?></td>
				<td><?php echo ellipsize(element('mmf_description', $mmf), 40, .5) ?></td>
				<td><?php echo ellipsize(element('mmf_value', $mmf), 40, .5) ?></td>
				<td><a href="/service-providers/mail-merge/set-field/<?php echo $mmf['mmf_id']; ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
			</tr>

			<?php endforeach; ?>

		</table>

	<?php else : ?>

	<p class="no_results">There are no custom mail merge fields.</p>

	<?php endif; ?>

</div>
