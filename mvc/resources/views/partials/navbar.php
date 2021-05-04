<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <!--
    Hier verwenden wir den die baseurl und den appname um den Logo-Link in der Navbar zu erstellen.
    -->
    <a class="navbar-brand" href="<?php echo BASE_URL ?>">
        <?php echo \Core\Config::get('app.appname'); ?>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo BASE_URL; ?>">Blog</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/categories">Categories</a>
            </li>
            <li class="nav-item">
                <?php if (\app\Models\User::isLoggedIn()): ?>
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/logout/do">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/login">Login</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>
