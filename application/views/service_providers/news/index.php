<?php if($news) : ?>

<div class="news">

    <?php foreach($news as $news) : ?>

    <h2><?php echo $news['title']; ?></h2>

    <div>
        <p><?php echo $news['body_excerpt']; ?> <a href="/service-providers/news/item/<?php echo $news['id']; ?>">[read more]</a></p>

        <span><?php echo $news['datetime_created_format']; ?></span>
    </div>

    <?php endforeach; ?>

</div>

<?php echo $this->pagination->create_links(); ?>

<?php else : ?>

<p class="no_results">There is currently no news.</p>

<?php endif; ?>
