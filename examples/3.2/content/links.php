<h1>Links</h1>

<?php

$urls = [
    'https://sae.edu' => 'SAE',
    'https://nasa.gov' => 'NASA',
    'https://google.com' => 'Google',
    'https://bing.com' => 'Bing :('
];

?>

<ul>
    <?php foreach ($urls as $url => $label): ?>
    <li>
        <a href="functions/redirector.php?url=<?php echo urlencode($url);?>">
            <?php echo $label; ?>
        </a>
        <?php if (array_key_exists($url, $_SESSION['counted_urls'])): ?>
            <span> - bereits <?php echo $_SESSION['counted_urls'][$url]; ?>x besucht</span>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
