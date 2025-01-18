<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php"; //NOSONAR
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
$dir2 = PlanetbiruFileManager::path_decode(@$_GET['dir'], $cfg->rootdir);
if(!is_dir($dir2)){
$dir2 = PlanetbiruFileManager::path_decode('base', $cfg->rootdir);	
}
$arrfile2 = array();
$arrfile = array();
$arrdir = array();
if(file_exists($dir2) && $handle = opendir($dir2))
{
	
	$i=0;
	while (false !== ($ufile = readdir($handle))) 
	{ 
		$fn = "$dir2/$ufile";
		if($ufile == "." || $ufile == ".." ) 
		{
		continue;
		}
		$filetype = filetype($fn);
		unset($obj);
		$obj = array();
		if($filetype=="file")
		{
			$ft = PlanetbiruFileManager::getMIMEType($fn);
			$obj['url'] = $cfg->rooturl.'/'.substr(PlanetbiruFileManager::path_encode($fn, $cfg->rootdir),5);
			//$obj['path'] = PlanetbiruFileManager::path_encode($fn, $cfg->rootdir);
			//$obj['location'] = PlanetbiruFileManager::path_encode(dirname($fn), $cfg->rootdir);
			$obj['name'] = basename($fn);
			$fs = filesize($fn);
			$obj['filesize'] = $fs;
			/*
			if($fs>=1048576)
			{
				$obj['size'] = number_format($fs/1048576, 2, '.','').'M';
			}
			else if($fs>=1024)
			{
				$obj['size'] = number_format($fs/1024, 2, '.','').'K';
			}
			else
			{
				$obj['size'] = $fs;
			}
			*/
			$obj['type'] = $ft->mime;
			//$obj['extension'] = $ft->extension;
			//$obj['permission'] = substr(sprintf('%o', fileperms($fn)), -4);
			$fti = filemtime($fn);
			$obj['filemtime'] = date('Y-m-d H:i:s', $fti);
			if(stripos($obj['type'], 'image') !== false && $obj['filesize'] <= $cfg->thumbnail_max_size)
			{
				try
				{
					$is = getimagesize($fn);
					if($is)
					{
						$obj['image_width'] = $is[0];
						$obj['image_height'] = $is[1];
						if(stripos($is['mime'], 'image')===0) 
						{
							$obj['type'] = $is['mime'];
						}
					}
					else
					{
					$obj['image_width'] = 0;
					$obj['image_height'] = 0;
					}
				}
				catch(Exception $e)
				{
					$obj['image_width'] = 0;
					$obj['image_height'] = 0;
				}
			}
			else
			{
				$obj['image_width'] = 0;
				$obj['image_height'] = 0;
			}
			$arrfile[] = $obj;
		}
	}
}

$sortby = @$_GET['sortby'];
if(!in_array($sortby, array('name', 'filesize', 'type', 'permission', 'filemtime')))
$sortby = '';							
if($sortby == '')
{
	$sortby = 'type';
}
$sortorder = @$_GET['sortorder'];
if(!in_array($sortorder, array('asc', 'desc')))
{
	$sortorder = '';
}							
if($sortorder == '')
{
	$sortorder = 'asc';
}

// Pertama, mengurutkan direktori berdasarkan nama
$_order = array();
foreach ($arrdir as $row) {
    $_order[] = $row['name']; // Tidak perlu referensi, cukup gunakan nilai
}
array_multisort($_order, SORT_ASC, SORT_STRING, $arrdir);

// Kedua, mengurutkan file berdasarkan kolom tertentu (misalnya $sortby) dan nama
$_order = array();
$_order2 = array();
foreach ($arrfile as $row) {
    $_order[] = $row[$sortby];   // Ambil nilai dari kolom yang ditentukan oleh $sortby
    $_order2[] = $row['name'];   // Ambil nilai dari kolom 'name'
}

// Tentukan urutan (ascending atau descending) untuk sortby
$sortDirection = ($sortorder == 'desc') ? SORT_DESC : SORT_ASC;

// Lakukan pengurutan dengan dua kolom (sortby dan name)
array_multisort($_order, $sortDirection, $_order2, SORT_ASC, SORT_STRING, $arrfile);


if(count($arrfile))
{
	foreach($arrfile as $key=>$val)
	{
		if(stripos($val['type'], 'image/') === 0)
		{
			$arrfile2[] = $val;
		}
	}
}
if(basename($_SERVER['PHP_SELF']) == basename(__FILE__))
{
header("Content-Type:text/plain; charset=utf-8");
}
echo json_encode($arrfile2);
