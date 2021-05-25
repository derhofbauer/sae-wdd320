<h2>User <small class="text-muted">New</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/users/create" method="post" enctype="multipart/form-data">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="e.g. arthur.dent@galaxy.com" required value="<?php echo \Core\Session::old('email'); ?>">
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="e.g. zbeeblebrox" value="<?php echo \Core\Session::old('username'); ?>">
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
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" class="form-control-file" name="avatar" id="avatar">
            </div>
        </div>

        <div class="col">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="is_admin" id="is_admin">
                <label for="is_admin" class="form-check-label">Is Admin?</label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/users" class="btn btn-danger">Cancel</a>

</form>
