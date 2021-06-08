<h2>Shares</h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>User</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($shares as $share): ?>
        <tr>
            <th><?php echo $share->id; ?></th>
            <td><?php echo $share->user(); ?></td>
            <td><?php echo $share->status; ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/admin/shares/<?php echo $share->id; ?>/edit" class="btn btn-primary btn-sm">Edit</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
