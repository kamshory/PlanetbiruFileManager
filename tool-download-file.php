<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php"; //NOSONAR
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
if(isset($_GET['relative']))
{
	$filepath = $cfg->rootdir.'/'.substr(str_replace(array("./", "../"), "", @$_GET['filepath']), strlen(basename($cfg->rooturl)));
}
else
{
	$filepath = rawurldecode(PlanetbiruFileManager::path_decode(@$_GET['filepath'], $cfg->rootdir));
}
if(!file_exists($filepath)) 
{
	exit();
}
$ft = PlanetbiruFileManager::getMIMEType($filepath);
$mime = $ft->mime;
header('Content-type: '.$mime);
if(!isset($_GET['relative']))
{
	header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
}
readfile($filepath);
