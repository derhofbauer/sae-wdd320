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
    <div class="collapse navbar-collapse navbar-flex" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo BASE_URL; ?>">Blog</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/categories">Categories</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/favourites">
                    Favourites
                    <?php if (\App\Models\User::isLoggedIn()): ?>
                        (<span class="favourites-counter"><?php echo count(\App\Models\User::getLoggedIn()->favourites()); ?></span>)
                    <?php endif; ?>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav navbar-right">
            <?php if (\App\Models\User::isLoggedIn()): ?>
                <?php if (\App\Models\User::getLoggedIn()->is_admin === true): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin">Admin</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/profile">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/logout/do">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/sign-up">Sign-up</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
