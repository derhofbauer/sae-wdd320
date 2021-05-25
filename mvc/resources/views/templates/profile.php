<h2>Profile bearbeiten</h2>

<form action="<?php echo BASE_URL; ?>/profile/update" method="post" enctype="multipart/form-data">

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
    </div>

    <button type="submit" class="btn btn-primary">Save</button>

</form>
