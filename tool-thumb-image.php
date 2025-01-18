<?php
include_once dirname(__FILE__) . "/functions.php";
include_once dirname(__FILE__) . "/auth.php";
include dirname(__FILE__) . "/conf.php"; //NOSONAR
if ($cfg->authentification_needed && !$userlogin) {
	exit();
}
if (!$cfg->thumbnail_quality) {
	$cfg->thumbnail_quality = 75;
}

$filepath = PlanetbiruFileManager::path_decode(@$_GET['filepath'], $cfg->rootdir);



if (file_exists($filepath)) {
	$filetype = filetype($filepath);
	if ($filetype == "file") {
		$expires = $cfg->cache_max_age_file;
		header("Pragma: public");
		header("Cache-Control: maxage=" . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
		if (!function_exists('imagecreatefrompng')) {
			readfile(dirname(__FILE__) . "/style/images/common/image.png");
			header('Content-Type: image/png');
		} else {
			$image = PlanetbiruFileManager::gettumbpict($filepath, 96, 96);
			if ($image) {
				header('Content-Type: image/jpeg');
				@imagejpeg($image, null, $cfg->thumbnail_quality);
			} else {
				$ft = pathinfo($filepath, PATHINFO_EXTENSION);
				if (file_exists(dirname(__FILE__) . "/style/images/common/$ft.png")) {
					$image = imagecreatefrompng(dirname(__FILE__) . "/style/images/common/$ft.png");
				} else {
					$image = imagecreatefrompng(dirname(__FILE__) . "/style/images/binfile.png");
				}
				header('Content-Type: image/png');
				@imagepng($image);
			}
		}
	}
	if ($filetype == "dir") {
		$expires = $cfg->cache_max_age_dir;
		header("Pragma: public");
		header("Cache-Control: maxage=" . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
		if (!function_exists('imagecreatefrompng')) {
			readfile(dirname(__FILE__) . "/style/images/folder.png");
			header('Content-Type: image/png');
		} else {
			$image = imagecreatefrompng(dirname(__FILE__) . "/style/images/folder.png");
			if ($handle = opendir($filepath)) {
				$i = 0;
				while (false !== ($ufile = readdir($handle))) {
					$fn = "$filepath/$ufile";
					if ($ufile == "." || $ufile == "..") {
						continue;
					}
					$filetype = filetype($fn);
					if ($filetype == "file") {
						$img2[$i] = PlanetbiruFileManager::gettumbpict($fn, 40, 40);
						if ($img2[$i]) {
							$width = imagesx($img2[$i]);
							$height = imagesy($img2[$i]);
							$x1 = floor((40 - $width) / 2);

							$y1 = floor((40 - $height) / 2);
							if ($i < 2) {
								$y = 8;
							} else {
								$y = 52;
							}
							if (!($i % 2)) {
								$x = 6;
							} else {
								$x = 50;
							}
							@imagecopy($image, $img2[$i], $x + $x1, $y + $y1, 0, 0, $width, $height);
							$i++;
						}
					}
					if ($i > 3) {
						break;
					}
				}
			}

			header('Content-Type: image/jpeg');
			@imagejpeg($image, null, $cfg->thumbnail_quality);
		}
	}
}
