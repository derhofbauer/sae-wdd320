<article class="post">
    <h2><?php echo $post->title; ?></h2>
    <?php require __DIR__ . '/../../partials/post/meta.php'; ?>

    <div class="content"><?php echo $post->teaserSentence(); ?></div>
    <a href="blog/<?php echo $post->slug; ?>" class="more">read more</a>
</article>
