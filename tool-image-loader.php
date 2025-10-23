<?php
include_once dirname(__FILE__) . "/functions.php";
include_once dirname(__FILE__) . "/auth.php";
include dirname(__FILE__) . "/conf.php"; //NOSONAR

// Authentification
if ($cfg->authentification_needed && !$userlogin) {
	exit();
}

$filepath = PlanetbiruFileManager::path_decode(@$_GET['filepath'], $cfg->rootdir);

if (file_exists($filepath) && is_file($filepath)) {
    $size = @getimagesize($filepath);
    if ($size && stripos($size['mime'], 'image/') === 0) {
        header('Content-Type: ' . $size['mime']);
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}

// Return a 404 if the image is not found or not an image
header("HTTP/1.0 404 Not Found");
exit;
