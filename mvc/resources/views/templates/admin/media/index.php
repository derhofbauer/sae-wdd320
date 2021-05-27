<h2>Media <a href="<?php echo BASE_URL; ?>/admin/media/new" class="btn btn-primary btn-sm">New</a></h2>

<form action="<?php echo BASE_URL; ?>/admin/media/delete-multiple" method="post">
    <div class="media">
        <?php foreach ($files as $file): ?>
            <div class="image">
                <a href="<?php echo BASE_URL; ?>/admin/media/<?php echo $file->id; ?>/edit">
                    <img src="<?php echo $file->getFilePath(true, true); ?>" alt="<?php echo $file->alttext; ?>" title="<?php echo $file->title; ?>">
                </a>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="delete-file[<?php echo $file->id; ?>]" id="delete-file[<?php echo $file->id; ?>]">
                    <label for="delete-file[<?php echo $file->id; ?>]">Löschen?</label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-danger">Löschen?!</button>
</form>
