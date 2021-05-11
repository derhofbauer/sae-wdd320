<div class="row justify-content-center mt-3">
    <form action="sign-up/do" method="post" class="col-4">
        <h2>Sign-up</h2>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" placeholder="Email" class="form-control" id="email" name="email" value="<?php echo \Core\Session::old('email'); ?>">
        </div>

        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" placeholder="Username" class="form-control" id="username" name="username" value="<?php echo \Core\Session::old('username'); ?>">
        </div>

        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" placeholder="Password" class="form-control" id="password" name="password">
        </div>

        <div class="form-group mb-3">
            <label for="password_repeat">Password wiederholen</label>
            <input type="password" placeholder="Password" class="form-control" id="password_repeat" name="password_repeat">
        </div>

        <button type="submit" class="btn btn-primary">Sign-up!</button>

    </form>
</div>
