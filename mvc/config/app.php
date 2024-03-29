<?php

return [
    /**
     * Hier definieren wir den Namen der Anwendung. Das bietet uns die Möglichkeit einen zentralen String zu definieren,
     * den wir dann beispielsweise für den <title>-Tag verwenden können.
     */
    'appname' => 'SAE WDD320',

    /**
     * Hier definieren wir eine Slug Form des Anwendungsnamens. Dieser wird beispielsweise in der Session Klasse
     * verwendet, um den Namen des Session Cookies zu setze.
     */
    'app-slug' => 'sae-wdd320',

    /**
     * Die baseurl wird benötigt um den <base>-Tag zu setzen, damit CSS, JS und IMG Imports immer von der selben URL
     * ausgehen und nicht von der aktuell im Browser aufgerufenen. Das ermöglicht es uns die src-Attribute relativ zu
     * setzen und die Files werden trotzdem absolut geladen.
     */
    'baseurl' => 'http://localhost:8080/mvc/public', // bei euch: http://localhost/mvc/ od. sowas wie http://localhost/sae-wdd320/mvc/

    /**
     * Um einzelne Funktionalitäten je nach Umgebung leicht umschalten zu können, führen wir eine Einstellung ein, die
     * zwischen dev und prod unterscheiden kann. Dadurch können wir Beispielsweise das Error Reporting ein- bzw.
     * ausschalten.
     */
    'environment' => 'dev',

    /**
     * Hier definieren wir welches Layout standardmäßig verwendet wird. Hier könnte beispielsweise bei Werbeaktionen,
     * bei denen die gesamte Seite von einem Werbekunden gebrandet wird, hilfreich sein.
     */
    'default-layout' => 'default',

    /**
     * Upload Limit (Dateigröße) definieren. Dieses kann größer oder kleiner sein als das in PHP definierte Uploadlimit.
     * Ist es größer als das PHP Limit, so greift das PHP Limit.
     */
    'upload-limit' => 1024 * 1024 * 10, // 5MB

    /**
     * Maximale Dimensionen für das Avatar Bild. Diese brauchen wir in der Update Funktionalität der User*innen.
     */
    'avatar-max-dimensions' => [1920, 1080],

    /**
     * Wo sollen hochgeladene Dateien hingespeichert werden?
     */
    'uploads-folder' => '/storage/uploads',

    /**
     * Wie viele Einträge sollen im Rahmen der Pagination pro Seite angezeigt werden?
     */
    'items-per-page' => 3,
];
