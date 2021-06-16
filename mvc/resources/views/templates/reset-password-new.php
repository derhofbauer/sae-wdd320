<div class="row justify-content-center mt-3">
    <form action="<?php echo BASE_URL . "/reset-password/new/{$token->token}/do"; ?>" method="post" class="col-4">
        <h2>Set new password</h2>

        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" placeholder="Password" class="form-control" id="password" name="password">
        </div>

        <div class="form-group mb-3">
            <label for="password_repeat">Password wiederholen</label>
            <input type="password" placeholder="Password" class="form-control" id="password_repeat" name="password_repeat">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>

        <a href="<?php echo BASE_URL; ?>/login">Back to login</a>
    </form>
</div>
