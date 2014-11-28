<?php if ($mmd): ?>
<div class="functions">
	<a href="?delete=1" class="action" title="Are you sure you want to delete this mail merge document?"><img src="/img/btn/delete.png" alt="Delete" /></a>
	<div class="clear"></div>
</div>
<?php endif; ?>

<div class="header">
	<h2>Mail merge document</h2>
</div>


<div class="item">


	<dl class="htabs">
		<dd><a href="#set" class="selected">Compose</a></dd>
		<dd><a href="#preview" class="">Preview</a></dd>
		<dd style="float: right" class="hidden" id="page_count_dd">Page count: <span id="page_count"></span></dd>
	</dl>




	<ul class="htabs-content">

		<li id="setTab" class="selected">

			<?php echo form_open('', array('id' => 'mail_merge_document_form')) ?>

				<table class="form">

					<tr class="vat">
						<th>
							<label for="mmd_title">Title</label>
						</th>
						<td><?php
						$name = 'mmd_title';
						echo form_input(array(
							'type' => 'text',
							'name' => $name,
							'id' => $name,
							'maxlength' => 128,
							'size' => 45,
							'class' => 'text',
							'value' => element('mmd_title', $mmd),
						)) ?></td>
						<td class="e"></td>
					</tr>

					<tr class="vat">
						<th>
							<label for="mmd_content">Content</label>
						</th>
						<td colspan="2"><?php
						$name = 'mmd_content';
						echo form_textarea(array(
							'name' => $name,
							'id' => $name,
							'rows' => 20,
							'cols' => 50,
							'class' => 'text',
							'value' => element('mmd_content', $mmd),
						)) ?></td>
					</tr>

					<tr class="vat">
						<th>Tags</th>
						<td colspan="2">
							<?php
							$types = array(
								'custom' => 'Custom',
								'monitoring_form' => 'Monitoring form',
								'client' => 'Client',
							);
							?>

							<?php foreach ($fields as $type => $tags): ?>

								<h3 style="font-weight: normal; font-size: 16px; color: #444; margin-bottom: 10px;"><?php echo element($type, $types, "Other") ?></h3>

								<p class="mail_merge_tags">
								<?php foreach ($tags as $tag): ?>
								<span><?php echo $tag->insert_link() ?></span>
								<?php endforeach; ?>
								</p>

							<?php endforeach; ?>
						</td>
					</tr>

					<tr>
						<td></td>
						<td>
							<div class="functions">
								<input type="image" src="/img/btn/save.png" alt="Save" />
							</div>
						</td>
					</tr>

				</table>

			</form>

		</li>



		<li id="previewTab">
			Loading preview...
		</li>


	</ul>

</div>
