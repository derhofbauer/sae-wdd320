<h2><?php echo ($file->title ? $file->title : $file->name); ?> <small class="text-muted">Edit</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/media/<?php echo $file->id; ?>/update" method="post">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="e.g. View of Vienna" value="<?php echo $file->title; ?>" required>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="alttext">Alternative Text</label>
                <input type="text" class="form-control" name="alttext" id="alttext" placeholder="e.g. View of Vienna" value="<?php echo $file->alttext; ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="caption">Caption</label>
        <textarea name="caption" id="caption" rows="10" class="form-control editor"><?php echo $file->caption; ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/media" class="btn btn-danger">Cancel</a>

</form>
