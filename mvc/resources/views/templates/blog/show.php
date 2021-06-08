<div class="blog">
    <article>
        <h2>
            <?php echo $post->title; ?>
            <small>
                <button class="btn btn-primary btn-sm favourite-add" data-href="<?php echo BASE_URL; ?>/api/favourites/add/<?php echo $post->id;?>">Favorit (DE!!)</button>
            </small>
        </h2>
        <?php require __DIR__ . '/../../partials/post/meta.php'; ?>

        <div class="content"><?php echo $post->content; ?></div>

        <div class="images slider">
            <?php foreach ($post->files() as $file): ?>
                <figure>
                    <?php echo $file->getImgTag(); ?>
                    <figcaption>
                        <?php echo $file->caption; ?>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </article>
</div>
