<article>
    <h2><?php echo $post->title; ?></h2>
    <div class="meta">
        <?php foreach ($post->categories() as $category): ?>
            <span><?php echo $category->title; ?></span>
        <?php endforeach; ?>
    </div>
    <div class="content"><?php echo $post->content; ?></div>
</article>
