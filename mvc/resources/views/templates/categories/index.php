<div class="categories">
    <article>
        <h2><?php echo $post->title; ?></h2>
        <?php require __DIR__ . '/../../partials/post/meta.php'; ?>

        <div class="content"><?php echo $post->content; ?></div>
    </article>
</div>
