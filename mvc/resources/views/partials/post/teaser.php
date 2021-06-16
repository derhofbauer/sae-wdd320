<article class="post">
    <h2><?php echo $post->title; ?>
        <small><?php echo $post->getAverageAndNumberRatings()['average']; ?>/5 Sternen (<?php echo $post->getAverageAndNumberRatings()['numberOfRatings']; ?> Ratings)</small>
    </h2>
    <?php require __DIR__ . '/../../partials/post/meta.php'; ?>

    <div class="content"><?php echo $post->teaserSentence(); ?></div>
    <a href="blog/<?php echo $post->slug; ?>" class="more">read more</a>
</article>
