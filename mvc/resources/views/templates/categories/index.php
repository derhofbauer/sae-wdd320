<?php foreach($categories as $category): ?>
    <article class="category">
        <h2><?php echo $category->title; ?></h2>
        <div class="content"><?php echo $category->description; ?></div>
        <a href="categories/<?php echo $category->slug; ?>" class="more">Show Posts</a>
    </article>
<?php endforeach; ?>
