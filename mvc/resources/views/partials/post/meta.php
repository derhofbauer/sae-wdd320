<div class="meta">
    <?php foreach ($post->categories() as $category): ?>
        <span>
            <a href="categories/<?php echo $category->slug; ?>"><?php echo $category->title; ?></a>
        </span>
    <?php endforeach; ?>
</div>
