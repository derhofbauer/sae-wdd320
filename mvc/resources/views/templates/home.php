<!--@todo: comment-->
<?php foreach ($posts as $post): ?>
<article>
    <h2><?php echo $post->title; ?></h2>
    <div class="content"><?php echo $post->content; ?></div>
</article>
<?php endforeach; ?>
