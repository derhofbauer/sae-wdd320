<h2>Category <small class="text-muted">Create</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/categories/create" method="post">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="e.g. Category #1" required>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" class="form-control" name="slug" id="slug" placeholder="e.g. category-1">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="10" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/categories" class="btn btn-danger">Cancel</a>

</form>
