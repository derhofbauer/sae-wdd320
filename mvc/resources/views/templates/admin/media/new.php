<h2>Media <small class="text-muted">Upload</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/media/create" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label for="files">Media Upload</label>
        <input type="file" name="files[]" id="files" class="form-control-file" multiple />
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/media" class="btn btn-danger">Cancel</a>

</form>
