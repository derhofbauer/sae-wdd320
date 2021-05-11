<?php require_once __DIR__ . '/../partials/html/head.php'; ?>

<body>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container content mt-5">

    <?php require_once __DIR__ . '/../partials/errorsAndSuccess.php'; ?>

    <div class="row">
        <div class="col-2">
            <?php require_once __DIR__ . '/../partials/admin/sidebar.php'; ?>
        </div>
        <div class="col">
            <!-- In der View-Klasse definieren wir, welches Template geladen werden soll. Der eigentliche Vorgang des Ladens passiert
             hier. -->
            <?php require_once $renderTemplate; ?>
        </div>
    </div>
</div>

</body>

</html>
