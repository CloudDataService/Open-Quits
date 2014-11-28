<h2><?php echo $news_item['title']; ?></h2><br>
<h3><?php echo $news_item['datetime_created_format']; ?></h3><br>

<div class="tiny_mce">

	<?php echo $news_item['body']; ?>

	<div class="clear"></div>

</div>

<a href="/service-providers/news" class="back"><img src="/img/btn/back.png" alt="Back" /></a>