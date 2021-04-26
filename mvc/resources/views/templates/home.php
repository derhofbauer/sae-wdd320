<?php
/**
 * Hier gehen wir alle Posts durch, die vom Controller über das Post Model abgefragt wurden und generieren eine sehr
 * einfache Ansicht daraus.
 */
foreach ($posts as $post): ?>
<article>
    <h2><?php echo $post->title; ?></h2>
    <div class="content"><?php echo $post->content; ?></div>
</article>
<?php endforeach; ?>
