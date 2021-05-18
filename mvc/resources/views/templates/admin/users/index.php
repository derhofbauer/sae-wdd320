<h2>Users</h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Username</th>
        <th>Admin?</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <th><?php echo $user->id; ?></th>
            <td><?php echo $user->username; ?></td>
            <td><?php echo($user->is_admin === true ? 'Ja' : 'Nein'); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/admin/users/<?php echo $user->id; ?>/edit" class="btn btn-primary btn-sm">Edit</a>

                <?php if (\App\Models\User::getLoggedIn()->id !== $user->id): ?>
                    <a href="<?php echo BASE_URL; ?>/admin/users/<?php echo $user->id; ?>/delete" class="btn btn-danger btn-sm">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
