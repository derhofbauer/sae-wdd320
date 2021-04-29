<div class="blog">
    <?php
    /**
     * Hier gehen wir alle Posts durch, die vom Controller über das Post Model abgefragt wurden und generieren eine sehr
     * einfache Ansicht daraus.
     * @todo: comment (partial)
     */
    foreach ($posts as $post) {
        require __DIR__ . '/../../partials/post/teaser.php';
    }
    ?>

</div>
