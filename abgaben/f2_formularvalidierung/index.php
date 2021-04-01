<?php
/**
 * @todo: comment everything!
 */

$validSalutations = ['male', 'female', 'lgbtq2s'];
$countries = [
    'at' => 'Österreich',
    'de' => 'Deutschland',
    'ch' => 'Schweiz',
    'fl' => 'Liechtenstein'
];

if (isset($_POST['name'])) {
    require_once 'validate.php';
}

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formularvalidierung</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col">
            <h1>Formularvalidierung</h1>
        </div>
    </div>

    <form method="POST" novalidate autocomplete="off">

        <?php foreach ($errors as $field => $error): ?>
            <div class="alert alert-danger"><?php echo "$field: $error";?></div>
        <?php endforeach; ?>
        <div class="row">
            <div class="salutation col-3">
                <span class="label">Anrede</span>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="salutation" id="salutation-female" value="female" required>
                    <label for="salutation-female" class="form-check-label">Frau</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="salutation" id="salutation-male" value="male" required>
                    <label for="salutation-male" class="form-check-label">Herr</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="salutation" id="salutation-lgbtq2s" value="lgbtq2s" required>
                    <label for="salutation-lgbtq2s" class="form-check-label">LGBTQ2S+</label>
                </div>
            </div>

            <div class="form-group name col-4">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>

            <div class="form-group age col-2">
                <label for="age">Alter in Jahren</label>
                <input type="number" class="form-control" name="age" id="age" min="0" max="130" step="1" required>
            </div>

            <div class="form-group phone col-3">
                <label for="phone">Telefonnummer</label>
                <input type="tel" class="form-control" name="phone" id="phone" required>
            </div>
        </div>

        <div class="row">
            <div class="form-group email col-6">
                <label for="email">E-Mail</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>

            <div class="form-group password col-6">
                <label for="password">Passwort</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
        </div>

        <div class="row">
            <div class="form-group street col-6">
                <label for="street">Straße</label>
                <input type="text" class="form-control" name="street" id="street" required>
            </div>

            <div class="form-group zip col-2">
                <label for="zip">Postleitzahl</label>
                <input type="text" class="form-control" name="zip" id="zip" required>
            </div>

            <div class="form-group country col-4">
                <label for="country">Land</label>
                <select name="country" id="country" class="form-control" required>
                    <option value="_default" selected hidden>Bitte auswählen ...</option>
                    <?php foreach ($countries as $key => $country): ?>
                        <option value="<?php echo $key; ?>"><?php echo $country; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-check agb">
            <input type="checkbox" class="form-check-input" name="agb" id="agb" required>
            <label for="agb">Ich habe die AGB gelesen und akzeptiere diese.</label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Senden</button>
            <button class="btn btn-danger" type="reset">Abbrechen</button>
        </div>

    </form>

</div>

</body>
</html>
