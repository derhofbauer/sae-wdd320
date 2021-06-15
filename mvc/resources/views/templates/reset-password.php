<div class="row justify-content-center mt-3">
    <form action="reset-password/do" method="post" class="col-4">
        <h2>Reset Password</h2>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="text" placeholder="Email" class="form-control" id="email" name="email">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>

        <a href="<?php echo BASE_URL; ?>/login">Back to login</a>
    </form>
</div>
