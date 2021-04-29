<?php

/**
 * Der Webroot der Anwendung sollte auf /mvc/public/ zeigen, aber nachdem das im MAMP nicht so einfach zu konfigurieren
 * ist, definieren wir hier eine Hilfsdatei, die einfach nur das eigentlich index.php File aus dem /public Ordner lädt.
 */
require_once 'public/index.php';

/**
 * @todo: continue here!
 * Login
 *
 * 1) Username & Passwort ins Login Formular eingeben
 * 2) Remember Me Checkbox anhakerln (optional)
 * 3) Formular absenden
 * 4) Gibts den User schon? ja: weiter, nein: Fehlermeldung
 * 5) Passwort aus DB abrufen (Salted Hashes)
 * 6) Passwort aus Eingabe und DB ident? ja: weiter, nein: Fehlermeldung
 * 7) "Remember Me" angehakerlt? ja: $exp=7, nein: $exp=0 (für die aktuelle Browser Session, bis der Tab geschlossen wird)
 * 8) Session schreiben: logged_in=>true, expiration_date=$exp
 * 9) Redirect zu bspw. Dashboard/Home Seite/whatever
 */
