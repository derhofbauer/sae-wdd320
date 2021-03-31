<h1>Links</h1>

<?php
/**
 * Wir definieren uns eine Liste an Urls, die ausgegeben werden sollen. Dadurch können wir den HTML Code dynamisch
 * generieren und ganz einfach Elemente hinzufügen oder entfernen.
 */
$urls = [
    'https://sae.edu' => 'SAE',
    'https://nasa.gov' => 'NASA',
    'https://google.com' => 'Google',
    'https://bing.com' => 'Bing :('
];

?>

<ul>
    <?php
    /**
     * Dadurch, dass wir in $urls sowohl die URL selbst als auch den Text, der angezeigt werden soll, haben, können wir
     * hier beides durchgehen und einen <a>-Tag generieren, der die $url aus dem Array an das redirector-File übergibt.
     * Dabei verwenden wir die urlencode()-Funktion, die alle Zeichen eines Strings so maskiert, dass sie der String in
     * URLs problemlos übergeben werden kann.
     */
    foreach ($urls as $url => $label): ?>
    <li>
        <a href="functions/redirector.php?url=<?php echo urlencode($url);?>">
            <?php echo $label; ?>
        </a>
        <?php
        /**
         * Wenn die URL in der Session gefunden wird, wurde sie bereits besucht.
         */
        if (in_array($url, $_SESSION['visited_urls'])): ?>
            <span> - bereits besucht</span>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
