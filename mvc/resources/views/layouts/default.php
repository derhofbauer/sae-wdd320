<?php require_once __DIR__ . '/../partials/html/head.php'; ?>

<body>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container content mt-5">

    <?php require_once __DIR__ . '/../partials/errorsAndSuccess.php'; ?>

    <!--
    In der View-Klasse definieren wir, welches Template geladen werden soll. Der eigentliche Vorgang des Ladens passiert
     hier.
     -->
    <?php require_once $renderTemplate; ?>
</div>

<?php require_once __DIR__ . '/../partials/html/footer.php'; ?>
