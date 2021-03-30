<?php
/**
 * Variablen vorbereiten, damit wir sie später verwenden können.
 */
$errors = [];
$newsletter = false;

/**
 * Wenn das Name-Feld aus dem Formular übergeben wurde und der Wert weniger als 2 Zeichen hat, fügen wir einen Fehler
 * in unser $errors Array ein.
 *
 * Die isset()-Funktion prüft, ob eine Variable oder ein Array-Index existieren.
 */
if (isset($_POST['name']) && strlen($_POST['name']) < 2) {
    $errors[] = "Der Name muss mindestens 2 Zeichen haben.";
}

/**
 * Hier prüfen wir zunächst, ob die email überhaupt aus dem Formular übergeben wurde und ob sie ein @ und einen .
 * enthält. Wenn das zutrifft, handelt es sich um eine potenziell korrekte E-Mail Adresse und wir prüfen genauer.
 */
if (isset($_POST['email']) && str_contains($_POST['email'], '@') && str_contains($_POST['email'], '.')) {
    /**
     * Nun speichern wir uns den Index vom @, damit wir prüfen können, ob nach dem @ noch ein Punkt kommt oder nicht.
     * a@b ist zwar eine valide E-Mail Adresse, aber nur innerhalb einer einzigen Maschine, im Web ist diese Adresse
     * nicht gültig, weil keine Toplevel-Domain vorhanden ist.
     * Die strpos()-Funktion gibt einen Integer zurück oder false, wenn die $needle nicht gefunden wurde. Nachdem wir
     * aber durch die Zeile 20 schon wissen, dass ein @ vorhanden ist, brauchen wir diesen Fall (false) nicht abfangen,
     * weil er nicht eintreten kann.
     */
    $indexOfAt = strpos($_POST['email'], '@');

    /**
     * Das @ darf auch nicht an erster Stelle stehen. Es muss also ein Zeichen vor dem @ vorhanden sein. Wenn nicht,
     * schreiben wir einen Fehler.
     */
    if ($indexOfAt < 1) {
        $errors[] = "Bitte geben Sie eine valide E-Mail Adresse ein.";
    } else {
        /**
         * Gibt es etwas vor dem @, dann prüfen wir, ob nach dem @ ein Punkt vorkommt.
         *
         * Wir holen uns also den Index vom ersten Punkt nach dem @+1. Die strpos()-Funktion hat 3 Parameter, wobei der
         * letzte optional ist. Ist er jedoch gesetzt so wird die Suche nach der Needle erst ab der im 3. Parameter
         * übergebenen Stelle begonnen. @+1 deshalb, weil mindestens ein Zeichen zwischen @ und . vorhanden sein muss.
         */
        $indexOfDot = strpos($_POST['email'], '.', $indexOfAt + 1);

        /**
         * Existiert kein Punkt mehr nach dem @+1, so handelt es sich nicht um eine valide, weltweit gültige E-Mail
         * Adresse und wir schreiben einen Fehler.
         */
        if ($indexOfDot === false) {
            $errors[] = "Bitte geben Sie eine valide E-Mail Adresse ein.";
        }
    }
}

/**
 * @todo: comment
 */
/*if (isset($_POST['phone']) && !is_numeric($_POST['phone'])) {
    $errors[] = "Telefonnummer darf nur aus Ziffern bestehen.";
}*/
$regex = "/^\+?[0-9 ]+$/";
if (isset($_POST['phone']) && preg_match($regex, $_POST['phone']) !== 1) {
    $errors[] = "Telefonnummer darf nur aus Ziffern bestehen.";
}

/**
 * Wurde ein Name übergeben und wir wissen somit, dass das Formular abgeschickt wurde, so muss auch ein gender übergeben
 * werden. Wurde kein gender übergeben, so wissen wir, dass keiner der Radio-Buttons ausgewählt wurde.
 */
if (isset($_POST['name']) && !isset($_POST['gender'])) {
    $errors[] = "Bitte wählen Sie eine Anrede aus.";
}

/**
 * Wurde die Newsletter Checkbox aktiviert, so setzten wir die oben definierte Steuerungsvariable auf true.
 * Eine Checkbox, die nicht angehakerlt wurde, wird nicht im Post Request übergeben. Eine Checkbox ohne value-Attribut
 * erhält den Value "on".
 */
if (isset($_POST['newsletter']) && $_POST['newsletter'] === 'on') {
    $newsletter = true;
}

?>

<h1>Contact</h1>

<?php
/**
 * Gibt es keine $errors und wurde die Newsletter Steuerungsvariable auf true gesetzt, geben wir eine Erfolgsmeldung
 * aus.
 *
 * Die empty()-Funktion prüft ob eine Variable leer ist. Leer sind beispielsweise [] oder "" (s.
 * https://www.php.net/manual/de/function.empty.php).
 */
if (empty($errors) && $newsletter === true): ?>
    <p class="alert alert-success">Sie haben den Newsletter erfolgreich abonniert! :D</p>
<?php endif; ?>

<?php
/**
 * Für jeden Fehler im $errors-Array geben wir nun eine Fehlermeldung aus. Ist das $errors-Array leer, wird hier auch
 * keine Fehlermeldung ausgegeben. Wir brauchen also nicht mit empty() zu prüfen.
 */
foreach ($errors as $error): ?>
    <p class="alert alert-danger"><?php echo $error; ?></p>
<?php endforeach; ?>

<!-- Wird kein action Attribut angegeben, so wird das Formular an die aktuelle URL geschickt. -->
<form method="post">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" name="name" placeholder="Name" id="name">
    </div>

    <div class="form-group">
        <label for="email">E-Mail</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="E-Mail">
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" class="form-control" name="phone" id="phone" placeholder="+43 1234 5678">
    </div>

    <div class="form-group">
        <label>
            <input type="radio" class="form-control" name="gender" value="male"> Male
        </label>
        <label>
            <input type="radio" class="form-control" name="gender" value="female"> Female
        </label>
        <label>
            <input type="radio" class="form-control" name="gender" value="rainbow"> Rainbow
        </label>
    </div>

    <div class="form-group">
        <label for="message">Message</label>
        <textarea name="message" id="message" cols="30" rows="10" placeholder="Message"></textarea>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="newsletter" id="newsletter"> Newsletter?
        </label>
    </div>

    <button type="submit">Submit</button>
</form>
