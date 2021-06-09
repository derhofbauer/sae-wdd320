<div class="blog">
    <?php
    /**
     * Hier gehen wir alle Posts durch, die vom Controller über das Post Model abgefragt wurden und generieren eine sehr
     * einfache Ansicht daraus.
     */
    foreach ($posts as $post) {
        /**
         * Nachdem wir den Post-Teaser auch in einem anderen Template brauchen, haben wir ihn in ein Partial
         * ausgelagert. Dadurch müssen Änderungen am Post-Teaser nur an einer Stelle durchgeführt werden und überall
         * wird der Post-Teaser dann trotzdem einheitlich dargestellt.
         */
        require __DIR__ . '/../../partials/post/teaser.php';
    }
    ?>
</div>

<?php
/**
 * Pagination generieren.
 *
 * Wir wissen, wie viele Seiten es geben muss, und können daher gut mit einer for-Schleife arbeiten.
 */
?>
<ul class="pagination">
    <?php for ($i = 1; $i <= $numberOfPages; $i++): ?>
    <li class="page-item">
        <a href="<?php echo BASE_URL; ?>/blog/<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
    </li>
    <?php endfor; ?>
</ul>
