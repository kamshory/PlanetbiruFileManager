<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
if($cfg->readonly){
	die('READONLY');
}


$rooturl = $cfg->rootdir;
if(@$_GET['option']=='createdir')
{
$dir2 = path_decode(kh_filter_input(INPUT_POST, 'location'), $cfg->rootdir);
$name = kh_filter_input(INPUT_POST, 'name');
$name = trim(str_replace(array("../","./","..\\",".\\","\\"),"/",$name),"/\\");
if(file_exists($dir2))
{
	if(!file_exists($dir2."/".$name)){
		if(mkdir($dir2."/".$name))
		{
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILED';
		}
	}
	else
	{
		echo 'EXIST';
	}
}
}
if(@$_GET['option']=='createfile')
{
$dir2 = path_decode(kh_filter_input(INPUT_POST, 'location'), $cfg->rootdir);
$name = kh_filter_input(INPUT_POST, 'name');
$name = trim(str_replace(array("../","./","..\\",".\\","\\"),"/",$name),"/\\");
if(file_exists($dir2))
{
	$tt = getMIMEType($dir2."/".$name);
	if(in_array($tt->extension, $cfg->forbidden_extension)){
		die('FORBIDDENEXT');
	}
	if(!file_exists($dir2."/".$name)){
		$fp = fopen($dir2."/".$name, "w");
		if($fp)
		{
			fclose($fp);
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILED';
		}
	}
	else
	{
		echo 'EXIST';
	}
}
deleteforbidden($dir2);
}

if(@$_GET['option']=='copyfile')
{
	
parse_str(@$_POST['postdata'], $_POST);
if(isset($_POST) && (@get_magic_quotes_gpc() || @get_magic_quotes_runtime()))
{
	array_walk_recursive($_POST, 'array_stripslashes');
}

$targetdir = path_decode(kh_filter_input(INPUT_POST, 'targetdir'), $cfg->rootdir);

// prepare dir
$dir = str_replace("\\","/",$targetdir);
$arr = explode("/", $dir);
if(is_array($arr))
{
	$d2c = "";
	foreach($arr as $k=>$v)
	{
		$d2c .= $v;
		if(strlen($d2c)>=strlen($cfg->rootdir))
		{
			if(!file_exists($d2c))
			{
				mkdir($d2c);
			}
		}
		$d2c .= "/";
	}
}


$files = @$_POST['file'];
$filemoved = array();
$dirmoved = array();
if(is_array($files))
{
	foreach($files as $k=>$file)
	{
		$source = path_decode($file, $cfg->rootdir);
		if(file_exists($source))
		{
			if(is_dir($source))
			{
				if($source != $targetdir."/".basename($source))
				{
					cp($source, $targetdir."/".basename($source));
					if($source != $targetdir."/".basename($source))
					{
						$dirmoved[] = $source;
					}
				}
			}
			else
			{
				if($source != $targetdir."/".basename($source))
				{
					copy($source, $targetdir."/".basename($source));
					if($source != $targetdir."/".basename($source))
					{
						$filemoved[] = $source;
					}
				}
			}
		}
	}
	echo 'SUCCESS';
}
else{
echo 'FAILED';
}
if(isset($_GET['deletesource']))
{
	foreach($dirmoved as $k=>$path)
	{
		destroyall($path);
	}
	foreach($filemoved as $k=>$path)
	{
		@unlink($path);
	}
}
deleteforbidden($targetdir, true);
}

if(@$_GET['option']=='deletefile')
{
parse_str(@$_POST['postdata'], $_POST);
if(isset($_POST) && (@get_magic_quotes_gpc() || @get_magic_quotes_runtime()))
{
	array_walk_recursive($_POST, 'array_stripslashes');
}
$files = @$_POST['file'];
if(is_array($files))
{
	foreach($files as $k=>$file)
	{
		$source = path_decode($file, $cfg->rootdir);
		if(is_dir($source))
		{
			destroyall($source);
		}
		else
		{
			@unlink($source);
		}
	}
	echo 'SUCCESS';
}
else{
echo 'FAILED';
}
}

if(@$_GET['option']=='renamefile')
{
$location = path_decode(kh_filter_input(INPUT_POST, 'location'), $cfg->rootdir);
$oldname = $location."/".trim(str_replace(array("../","./","..\\",".\\","\\"),"/",kh_filter_input(INPUT_POST, 'oldname')),"/\\");
$newname = $location."/".trim(str_replace(array("../","./","..\\",".\\","\\"),"/",kh_filter_input(INPUT_POST, 'newname')),"/\\");
if(file_exists($newname))
{
	die('EXIST');
}
else
{
	$tt = getMIMEType($newname);
	if(in_array($tt->extension, $cfg->forbidden_extension)){
		die('FORBIDDENEXT');
	}
	if(rename($oldname, $newname))
	{
		echo 'SUCCESS';
	}
	else
	{
		echo 'FAILED';
	}
}
deleteforbidden(dirname($newname));
}

if(@$_GET['option']=='extractfile')
{
if(!class_exists('ZipArchive'))
{
	die('NOTSUPPORTED');
}
$targetdir = path_decode(kh_filter_input(INPUT_POST, 'targetdir'), $cfg->rootdir);
$filepath = path_decode(kh_filter_input(INPUT_POST, 'filepath'), $cfg->rootdir);

if(file_exists($filepath))
{
	if(filesize($filepath)<10)
	{
		echo 'FAILED';
	}
	else
	{
		$zip = new ZipArchive;
		if ($zip->open($filepath) === TRUE) 
		{
			$zip->extractTo($targetdir.'/');
			$zip->close();
			deleteforbidden($targetdir, true);
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILED';
		}
	}
}
}


if(@$_GET['option']=='compressfile')
{

if(!class_exists('ZipArchive'))
{
	die('NOTSUPPORTED');
}

if(isset($_POST['postdata']))
{
	parse_str(@$_POST['postdata'], $_POST);
	if(isset($_POST) && (@get_magic_quotes_gpc() || @get_magic_quotes_runtime()))
	{
		array_walk_recursive($_POST, 'array_stripslashes');
	}
}
$target = path_decode(kh_filter_input(INPUT_POST, 'targetpath'), $cfg->rootdir);
if(file_exists($target))
{
	die('CONFLICT');
}

// prepare dir
$dir = dirname($target);
$dir = str_replace("\\","/",$dir);
$arr = explode("/", $dir);
if(is_array($arr))
{
	$d2c = "";
	foreach($arr as $k=>$v)
	{
		$d2c .= $v;
		if(strlen($d2c)>=strlen($cfg->rootdir))
		{
			if(!file_exists($d2c))
			{
				mkdir($d2c);
			}
		}
		$d2c .= "/";
	}
}



$file2compress = @$_POST['sourcepath'];
if(is_array($file2compress))
{
for($i=0;$i<count($file2compress);$i++){
$file2compress[$i] = path_decode($file2compress[$i], $cfg->rootdir);

if($file2compress[$i] == $target)
{
die('CONFLICT');
}

$arr2 = explode("/",$file2compress[$i]);
$nslashes=count($arr2);
if(count($arr2)<$nslashes||$nslashes==0){
$dir2remove=dirname($file2compress[$i]);
}
else
{
$dir2remove = dirname($file2compress[0]);
}


if(file_exists($file2compress[$i]))
{
	$file_list .= $file2compress[$i]."\r\n";
		if(filetype($file2compress[$i])=='dir')
		{
			dir_list($file2compress[$i]);
		}
	}
}

$file_list = trim($file_list,"\r\n");
$arrfile = explode("\r\n",$file_list);

if(count($arrfile) && !empty($target))
{
$zip = new ZipArchive;
$res = $zip->open($target, ZipArchive::CREATE);
if($res === TRUE) 
{
	foreach($arrfile as $entry)
	{
		$localname = trim(substr($entry, strlen($dir2remove)),"/");
		if(is_dir($entry))
		{
			$zip->addEmptyDir($localname);
		}
		else
		{
			$zip->addFile($entry, $localname);
		}
	} 
	$zip->close();
	echo 'SUCCESS';
	exit();
}
else 
{
	echo 'FAILED';
	exit();
}
}
}
}

if(@$_GET['option'] == 'transferfile')
{
$source = kh_filter_input(INPUT_POST, 'source');
$location = trim(str_replace(array("../","./","..\\",".\\","\\"),"/",kh_filter_input(INPUT_POST, 'location')),"/\\");
$name = basename(trim(str_replace(array("../","./","..\\",".\\","\\"),"/",kh_filter_input(INPUT_POST, 'name')),"/\\"));

$target = path_decode(rtrim($location, "\\/")."/".trim($name, "\\/"), $cfg->rootdir);

$data = @file_get_contents($source);
if($data === false)
{
	$arr = @file($source);
	if($arr !== false)
	{
		$data = implode("", $arr);
	}
	else
	{
		die('FAILED');
	}
}

// prepare dir
$dir = dirname($target);
$dir = str_replace("\\","/",$dir);
$arr = explode("/", $dir);
if(is_array($arr))
{
	$d2c = "";
	foreach($arr as $k=>$v)
	{
		$d2c .= $v;
		if(strlen($d2c)>=strlen($cfg->rootdir))
		{
			if(!file_exists($d2c))
			{
				mkdir($d2c);
			}
		}
		$d2c .= "/";
	}
}
if(!file_put_contents($target, $data))
{
	echo "FAILED";
}
else
{
	echo 'SUCCESS';
}

}

if(@$_GET['option'] == 'get-perms')
{
	$filename = path_decode(kh_filter_input(INPUT_GET, 'filepath'), $cfg->rootdir);
	if(file_exists($filename))
	{
		$fileperms = substr(sprintf('%o', fileperms($filename)), -4);
		$filetype = filetype($filename);
		// explode
		
		$data = substr($fileperms, 1);
		$u = substr($data, 0, 1);
		$g = substr($data, 1, 1);
		$w = substr($data, 2, 1);
		$perms['user_read'] = ($u>>2)%2;
		$perms['user_write'] = ($u>>1)%2;
		$perms['user_execute'] = ($u)%2;
		
		$perms['group_read'] = ($g>>2)%2;
		$perms['group_write'] = ($g>>1)%2;
		$perms['group_execute'] = ($g)%2;
		
		$perms['world_read'] = ($w>>2)%2;
		$perms['world_write'] = ($w>>1)%2;
		$perms['world_execute'] = ($w)%2;
		
		$perms['file-permission'] = $fileperms;
		$perms['filetype'] = $filetype;
		echo json_encode(array($perms));
	}
}
if(@$_GET['option'] == 'change-perms')
{
	$perms = @$_POST['perms'];
	$recursive = @$_POST['recursive'];
	list($permission) = sscanf($perms, "%o");
	if(isset($_POST['data']))
	{
		$fpa = $_POST['data'];
		foreach($fpa as $fp)
		{
			$filename = path_decode($fp, $cfg->rootdir);
			if(file_exists($filename))
			{
				if($recursive == '1')
				{
					$type = filetype($filename);
					if($type == 'file')
					{
						chmod($filename, $permission);
					}
					else
					{
						chmoddir($filename, $permission);
					}
				}
				else
				{
					chmod($filename, $permission);
				}
			}
		}
	}
	echo 'SUCCESS';
}
?>