<h2><?php echo $user->username; ?> <small class="text-muted">Edit</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/users/<?php echo $user->id; ?>/update" method="post" enctype="multipart/form-data">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="e.g. arthur.dent@galaxy.com" value="<?php echo $user->email; ?>" required>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="e.g. zbeeblebrox" value="<?php echo $user->username; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" class="form-control" name="password" id="password">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="password_repeat">Passwort wiederholen</label>
                <input type="password" class="form-control" name="password_repeat" id="password_repeat">
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <div class="avatar">
                <?php echo $user->avatar()?->getImgTag(); ?>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" class="form-control-file" name="avatar" id="avatar">
            </div>
        </div>

        <div class="col">
            <div class="form-check">
                <?php
                /**
                 * Damit ein Admin sich nicht selbst die Admin Reche weg nehmen kann und dadurch die Situation vermieden
                 * wird, dass der letzte Admin sich die Admin Rechte weg nimmt und kein Admin mehr existiert, der wieder
                 * Admin Rechte vergeben könnte, disablen wir die is_admin Checkbox, wenn ein Admin sich selbst
                 * bearbeitet.
                 */
                $disabledParticle = '';
                if (\App\Models\User::getLoggedIn()->id === $user->id) {
                    $disabledParticle = ' disabled';
                }
                ?>
                <input type="checkbox" class="form-check-input" name="is_admin" id="is_admin"<?php echo ($user->is_admin === true ? ' checked' : ''); ?><?php echo $disabledParticle; ?>>
                <label for="is_admin" class="form-check-label">Is Admin?</label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/users" class="btn btn-danger">Cancel</a>

</form>
