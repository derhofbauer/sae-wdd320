<div class="row justify-content-center">
    <form action="login/do" method="post" class="col-4">

        <div class="form-group mb-3">
            <label for="email-or-username">Username Or Email</label>
            <input type="text" placeholder="Username Or Email" class="form-control" id="email-or-username" name="email-or-username">
        </div>

        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" placeholder="Password" class="form-control" id="password" name="password">
        </div>

        <div class="form-group mb-3">
            <input type="checkbox" class="form-check-control" id="remember-me" name="remember-me">
            <label for="remember-me">Remember Me?</label>
        </div>

        <button type="submit" class="btn btn-primary">Login!</button>

    </form>
</div>
