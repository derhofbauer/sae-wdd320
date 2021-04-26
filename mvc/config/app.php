<?php

return [
    /**
     * Hier definieren wir den Namen der Anwendung. Das bietet uns die Möglichkeit einen zentralen String zu definieren,
     * den wir dann beispielsweise für den <title>-Tag verwenden können.
     */
    'appname' => 'SAE WDD320',

    /**
     * Die baseurl wird benötigt um den <base>-Tag zu setzen, damit CSS, JS und IMG Imports immer von der selben URL
     * ausgehen und nicht von der aktuell im Browser aufgerufenen. Das ermöglicht es uns die src-Attribute relativ zu
     * setzen und die Files werden trotzdem absolut geladen.
     */
    'baseurl' => 'http://localhost:8080/mvc/public/', // bei euch: http://localhost/mvc

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
    'default-layout' => 'default'
];
