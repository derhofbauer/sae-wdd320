<h2>Categories <a href="<?php echo BASE_URL; ?>/admin/categories/new" class="btn btn-primary btn-sm">New</a></h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Title</th>
        <th>Slug</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $category): ?>
        <tr>
            <th><?php echo $category->id; ?></th>
            <td><?php echo $category->title; ?></td>
            <td><code><?php echo $category->slug; ?></code></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/admin/categories/<?php echo $category->id; ?>/edit" class="btn btn-primary btn-sm">Edit</a>
                <a href="<?php echo BASE_URL; ?>/admin/categories/<?php echo $category->id; ?>/delete" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
