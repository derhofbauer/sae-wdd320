<?php
/**
 * Fehler-Array anlegen, damit wir Fehler hinein speichern und später wieder auslesen können.
 */
$errors = [];

/**
 * Wenn die Anrede nicht gesetzt ist UND/ODER nicht in den erlaubten Werten vorkommt, so schreiben wir einen Fehler.
 */
if (!isset($_POST['salutation']) || !in_array($_POST['salutation'], $validSalutations)) {
    $errors['salutation'] = 'Bitte wählen Sie eine Anrede aus.';
}

/**
 * Wenn der Name nicht gesetzt ist UND/ODER leer ist UND/ODER kürzer als 2 Zeichen, schreiben wir einen Fehler.
 */
if (!isset($_POST['name']) || empty($_POST['name']) || strlen($_POST['name']) < 2) {
    $errors['name'] = 'Bitte geben Sie einen Namen an.';
}

/**
 * Wenn das Alter nicht gesetzt ist UND/ODER nicht numerisch ist, schreiben wir einen Fehler.
 */
if (!isset($_POST['age']) || !is_numeric($_POST['age'])) {
    $errors['age'] = 'Bitte geben Sie ein Alter an.';
} else {
    /**
     * Ist das Alter gesetzt und numerisch, legen wir ein Alias davon an und konvertieren den Wert in einen Integer.
     */
    $age = (int)$_POST['age'];

    /**
     * Ist das Alter negativ, schreiben wir einen Fehler.
     */
    if ($age < 0) {
        $errors['age'] = 'Negatives Alter?! Come on!';
    }
    /**
     * Ist das Alter unrealistisch hoch, schreiben wir einen Fehler.
     */
    if ($age > 130) {
        $errors['age'] = 'Ganz schön alt!';
    }
}

/**
 * Regular Expression definieren:
 * + \+? meint 0 oder 1 +
 * + [0-9 ]+ meint mindestens eine Ziffer von 0-9 und/oder ein Leerzeichen
 * + {5,} gibt die Mindest- und Maximallänge an. In diesem Fall handelt es sich um mindestens 5, weil keine obere Grenze
 *     angegeben ist.
 * + ^ und $ geben Start und Ende des Strings an, der geprüft wird.
 *
 * Diese Expression ist alles andere als vollständig und soll nur verdeutlichen, wie Regular Expressions in PHP
 * funktionieren.
 */
$regex = '/^\+?[0-9 ]{5,}$/';

/**
 * Wenn ein Wert übergeben wurde, dann verwenden wir die preg_match()-Funktion, um den Wert gegen eine Regular
 * Expression zu prüfen. preg_match() gibt 1 zurück, wenn die Prüfung erfolgreich ist, in allen anderen Fällen war die
 * Prüfung also nicht erfolgreich oder es ist ein Fehler aufgetreten.
 */
if (!isset($_POST['phone']) || preg_match($regex, $_POST['phone']) !== 1) {
    $errors['phone'] = 'Telefonnummer darf nur aus Ziffern bestehen.';
}

/**
 * Hier definieren wir eine Regular Expression für eine Email-Adresse. Um eine Erklärung zu den einzelnen Teilen der
 * Expression zu haben, gebt sie bitte auf regex101.com ein.
 */
$regex = '/^[a-z0-9._]+\@[a-z]+(\.[a-z]{2,})+$/';
/**
 * Ist die E-Mail Adresse nicht gesetzt UND/ODER trifft sie nicht auf die Regular Expression zu, schreiben wir einen
 * Fehler.
 */
if (!isset($_POST['email']) || preg_match($regex, $_POST['email']) !== 1) {
    $errors['email'] = 'Bitte geben Sie eine valide E-Mail Adresse ein.';
}

/**
 * Nun definieren wir eine Regular Expression für ein Passwort. Um eine Erklärung zu den einzelnen Teilen der
 * Expression zu haben, gebt sie bitte auf regex101.com ein.
 */
$regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
/**
 * Ist kein Passwort gesetzt UND/ODER trifft es nicht auf die Expression zu, schreiben wir einen Fehler.
 */
if (!isset($_POST['password']) || preg_match($regex, $_POST['password']) !== 1) {
    $errors['password'] = 'Bitte geben Sie ein Passwort ein, dass mindestens 8 Zeichen hat und aus Groß- und Kleinbuchstaben, Ziffern und Sonderzeichen besteht.';
}

/**
 * Ist keine Straße gesetzt UND/ODER ist der Wert leer UND/ODER ist der Wert kürzer als 4 Zeichen, schreib wir einen
 * Fehler.
 */
if (!isset($_POST['street']) || empty($_POST['street']) || strlen($_POST['street']) < 4) {
    $errors['street'] = 'Bitte geben Sie eine existierende Straße an.';
}

/**
 * Ist kein ZIP Code gesetzt UND/ODER ist der Wert nicht numerisch UND/ODER kürzer als 2 Zeichen, schreiben wir einen
 * Fehler.
 */
if (!isset($_POST['zip']) || !is_numeric($_POST['zip']) || strlen($_POST['zip']) < 2) {
    $errors['zip'] = 'Bitte geben Sie einen gültigen ZIP Code an.';
}

/**
 * Ist kein Land gesetzt UND/ODER kommt es nicht in der Liste der Länger aus der index.php Datei vor, schreiben wir
 * einen Fehler.
 */
if (!isset($_POST['country']) || !array_key_exists($_POST['country'], $countries)) {
    $errors['country'] = 'Bitte wählen Sie ein Land aus.';
}

/**
 * Ist der AGB Wert nicht gesetzt UND/ODER ungleich "on", schreiben wir einen Fehler.
 */
if (!isset($_POST['agb']) || $_POST['agb'] !== 'on') {
    $errors['agb'] = 'Bitte akzeptieren Sie die AGB.';
}
