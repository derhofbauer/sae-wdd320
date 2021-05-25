<h2>Media</h2>

<div class="media">
    <?php foreach ($files as $file): ?>
        <div class="image">
            <a href="<?php echo BASE_URL; ?>/admin/media/<?php echo $file->id; ?>/edit">
                <img src="<?php echo $file->getFilePath(true, true); ?>" alt="<?php echo $file->alttext; ?>" title="<?php echo $file->title; ?>">
            </a>
        </div>
    <?php endforeach; ?>
</div>
