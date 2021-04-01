<?php
/**
 * @todo: comment everything
 */

$errors = [];

if (!isset($_POST['salutation']) || !in_array($_POST['salutation'], $validSalutations)) {
    $errors['salutation'] = 'Bitte wählen Sie eine Anrede aus.';
}

if (!isset($_POST['name']) || empty($_POST['name']) || strlen($_POST['name']) < 2) {
    $errors['name'] = 'Bitte geben Sie einen Namen an.';
}

if (!isset($_POST['age']) || !is_numeric($_POST['age'])) {
    $errors['age'] = 'Bitte geben Sie ein Alter an.';
} else {
    $age = (int)$_POST['age'];
    if ($age < 0) {
        $errors['age'] = 'Negatives Alter?! Come on!';
    }
    if ($age > 130) {
        $errors['age'] = 'Ganz schön alt!';
    }
}

/**
 * Regular Expression definieren:
 * + \+? meint 0 oder 1 +
 * + [0-9 ]+ meint mindestens eine Ziffer von 0-9 und/oder ein Leerzeichen
 * + @todo: {5,} kommentieren
 * + ^ und $ geben Start und Ende des Strings an, der geprüft wird.
 *
 * Diese Expression ist alles andere als vollständig und soll nur verdeutlichen, wie Regular Expressions in PHP
 * funktionieren.
 */
$regex = '/^\+?[0-9 ]{5,}$/';

/**
 * Wenn ein Wert übergeben wurde, dann verwenden wir die preg_match()-Funktion, um den Wert gegen eine Regular Expression
 * zu prüfen. preg_match() gibt 1 zurück, wenn die Prüfung erfolgreich ist, in allen anderen Fällen war die Prüfung also
 * nicht erfolgreich oder es ist ein Fehler aufgetreten.
 */
if (!isset($_POST['phone']) || preg_match($regex, $_POST['phone']) !== 1) {
    $errors['phone'] = 'Telefonnummer darf nur aus Ziffern bestehen.';
}

$regex = '/^[a-z0-9._]+\@[a-z]+(\.[a-z]{2,})+$/';
if (!isset($_POST['email']) || preg_match($regex, $_POST['email']) !== 1) {
    $errors['email'] = 'Bitte geben Sie eine valide E-Mail Adresse ein.';
}

$regex = '/^(?=[a-z]+)(?=[A-Z]+)(?=[0-9]+)(?=[\W_]+]).{8,}$/';
if (!isset($_POST['password']) || preg_match($regex, $_POST['password']) !== 1) {
    $errors['password'] = 'Bitte geben Sie ein Passwort ein, dass mindestens 8 Zeichen hat und aus Groß- und Kleinbuchstaben, Ziffern und Sonderzeichen besteht.';
}

if (!isset($_POST['street']) || strlen($_POST['street']) < 4) {
    $errors['street'] = 'Bitte geben Sie eine existierende Straße an.';
}

if (!isset($_POST['zip']) || !is_numeric($_POST['zip']) || strlen($_POST['zip']) < 2) {
    $errors['zip'] = 'Bitte geben Sie einen gültigen ZIP Code an.';
}

if (!isset($_POST['country']) || !array_key_exists($_POST['country'], $countries)) {
    $errors['country'] = 'Bitte wählen Sie ein Land aus.';
}

if (!isset($_POST['agb']) || $_POST['agb'] !== 'on') {
    $errors['agb'] = 'Bitte akzeptieren Sie die AGB.';
}
