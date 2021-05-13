<?php
/**
 * Dieser View wird immer dann verwendet, wenn ein Objekt gelöscht und davor eine Bestätigung dieser
 * Löschung erfragt werden soll. Daher ist der View sehr allgemein gehalten und benötigt daher aus dem Controller neben
 * $type und $title auch zwei URLs für die beiden Buttons.
 */
?>
<div class="card">
    <div class="card-header">
        <?php echo $type; ?> wirklich löschen?
    </div>
    <div class="card-body">
        <h5 class="card-title">Wollen Sie dieses Element wirklich löschen?</h5>
        <p class="card-text">
            <?php echo "{$type}: {$title}"; ?>
        </p>
        <a href="<?php echo $confirmUrl; ?>" class="btn btn-danger">Löschen!</a>
        <a href="<?php echo $abortUrl; ?>" class="btn btn-warning">Nein, lieber doch nicht.</a>
    </div>
</div>
