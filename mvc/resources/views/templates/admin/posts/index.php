<h2>Posts</h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Title</th>
        <th>Slug</th>
        <th>Author</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($posts as $post): ?>
        <tr>
            <th><?php echo $post->id; ?></th>
            <td><?php echo $post->title; ?></td>
            <td><code><?php echo $post->slug; ?></code></td>
            <td><?php echo $post->author()?->username; ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/admin/posts/<?php echo $post->id; ?>/edit" class="btn btn-primary btn-sm">Edit</a>
                <a href="<?php echo BASE_URL; ?>/admin/posts/<?php echo $post->id; ?>/delete" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
