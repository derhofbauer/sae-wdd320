<?php

return [
    'baseurl' => 'http://localhost:8080/mvc', // bei euch: http://localhost/mvc

    /**
     * Um einzelne Funktionalitäten je nach Umgebung leicht umschalten zu können, führen wir eine Einstellung ein, die
     * zwischen dev und prod unterscheiden kann. Dadurch können wir Beispielsweise das Error Reporting ein- bzw.
     * ausschalten.
     */
    'environment' => 'dev',

    /**
     * @todo: comment
     */
    'default-layout' => 'default'
];
