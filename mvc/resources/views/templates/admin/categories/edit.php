<h2><?php echo $category->title; ?> <small class="text-muted">Edit</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/categories/<?php echo $category->id; ?>/update" method="post">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="e.g. Category #1" value="<?php echo $category->title; ?>" required>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" class="form-control" name="slug" id="slug" placeholder="e.g. category-1" value="<?php echo $category->slug; ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="10" class="form-control editor"><?php echo $category->description; ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/categories" class="btn btn-danger">Cancel</a>

</form>
