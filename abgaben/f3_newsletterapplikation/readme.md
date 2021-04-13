# Newsletter-Applikation

Ziel dieser Abgabe ist es Benutzern zu ermöglichen, sich für einen Newsletter in einem bestimmten Themenbereich einzutragen.
Auf einer weiteren Seite sollen außerdem alle eigentragenen Benutzer mit ihren gewählten Themengebieten aufgelistet werden.

## Frontend

In der linken Spalte sind die unterschiedlichen Themengebiete mit ihren Beschreibungen zu finden. Diese Daten kommen aus der Datenbank.

In der rechten Spalte ist das Formular (Datei **/content/newsletter_form.php**) um sich für ein bestimmtes Thema einzutragen. Das Select wird mit den Daten (Kategorien) aus der Datenbank befüllt.
Nachdem sich ein Benutzer eingetragen hat, soll anstelle des Formulars der Bestätigungstext stehen (Datei **/content/thank_you.php**).

## Backend

Im Menüpunkt "Administration" (Datei **/backend/index.php**) werden alle angemeldeten Benutzer mit dem von ihnen ausgewähltem Thema in einer Tabelle aufgelistet (Selektieren aus 2 Tabellen, **newsletter_categories** und **recipients**). Hierzu wird kein Login benötigt (jeder User hat Zugriff auf diese Seite).


## TODO

Alle Stellen an denen ihr arbeiten müsst sind im Code mit dem Kommentar **TODO** sowie einer detaillierten Beschreibung versehen.

Ihr müsst in folgenden Dateien arbeiten:

* /dbconnect.php
* /index.php
* /logic.php
* /backend/index.php
* /content/newsletter_form.php

### Viel Erfolg!


