<div class="categories">
    <div class="category">
        <h2><?php echo $category->title; ?></h2>
        <div class="description"><?php echo $category->description; ?></div>
    </div>

    <div class="posts">
        <?php
        foreach ($posts as $post) {
            require __DIR__ . '/../../partials/post/teaser.php';
        }
        ?>
    </div>
</div>
