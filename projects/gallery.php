<?php
$service = trim($_GET['service'] ?? '');
$target = 'index.php';

if ($service !== '') {
    $target .= '?service=' . urlencode($service);
}

header('Location: ' . $target, true, 302);
exit;
