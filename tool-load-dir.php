<?php
include_once dirname(__FILE__) . "/functions.php";
include_once dirname(__FILE__) . "/auth.php";
include dirname(__FILE__) . "/conf.php"; //NOSONAR
if ($cfg->authentification_needed && !$userlogin) {
	exit();
}
$rooturl = $cfg->rootdir;
$seldir = @$_GET['dir'];
$dir2 = PlanetbiruFileManager::path_decode(@$_GET['seldir'], $cfg->rootdir);
if (!is_dir($dir2)) {
	$dir2 = PlanetbiruFileManager::path_decode('', $cfg->rootdir);
}
$arrdir = array();
if (file_exists($dir2) && $handle = opendir($dir2)) {

	$i = 0;
	while (false !== ($ufile = readdir($handle))) {
		$fn = "$dir2/$ufile";
		if ($ufile == "." || $ufile == "..") {
			continue;
		}
		try {
			$filetype = filetype($fn);
			unset($obj);
			if ($filetype == "dir") {
				$obj['path'] = PlanetbiruFileManager::path_encode($fn, $cfg->rootdir);
				$obj['location'] = PlanetbiruFileManager::path_encode(dirname($fn), $cfg->rootdir);
				$obj['name'] = basename($fn);
				$arrdir[] = $obj;
			}
		} catch (Exception $e) {
			try {
				unset($obj);
				if (is_dir($fn)) {
					$obj['path'] = PlanetbiruFileManager::path_encode($fn, $cfg->rootdir);
					$obj['location'] = PlanetbiruFileManager::path_encode(dirname($fn), $cfg->rootdir);
					$obj['name'] = basename($fn);
					$arrdir[] = $obj;
				}
			} catch (Exception $e) {
				// do nothing
			}
		}
	}
}

// Membuat array $_order yang berisi nama dari setiap elemen di $arrdir
$_order = array();
foreach ($arrdir as $row) {
	$_order[] = $row['name']; // Ambil nilai dari kolom 'name'
}

// Melakukan pengurutan array $arrdir berdasarkan $_order
array_multisort($_order, SORT_ASC, SORT_STRING, $arrdir);

if (count($arrdir)) {
?>
	<ul>
		<?php
		foreach ($arrdir as $k => $val) {
		?>
			<li class="row-data-dir dir-control" data-file-name="<?php echo $val['name']; ?>" data-file-location="<?php echo $val['location']; ?>" data-file-path="<?php echo str_replace("'", "\'", $val['path']); ?>"><a href="javascript:;" onclick="return openDir('<?php echo str_replace("'", "\'", $val['path']); ?>')"><?php echo $val['name']; ?></a>
				<?php
				if ($val['location'] && stripos($seldir, $val['path']) !== false) {
					// recursive
					// list dir tree
					echo PlanetbiruFileManager::builddirtree($seldir);
				}
				?>
			</li>
		<?php
		}
		?>
	</ul>
<?php
}
?>