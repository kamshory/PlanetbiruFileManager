<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
if(isset($_POST['postdata']))
{
	parse_str(@$_POST['postdata'], $_GET);
	if(isset($_GET) && (@get_magic_quotes_gpc() || @get_magic_quotes_runtime()))
	{
		array_walk_recursive($_GET, 'array_stripslashes');
	}
}
$filepath = path_decode(kh_filter_input(INPUT_GET, 'filepath'), $cfg->rootdir);
$angle = kh_filter_input(INPUT_GET, 'angle', FILTER_SANITIZE_NUMBER_UINT);
$angle = $angle % 360;
$fliph = kh_filter_input(INPUT_GET, 'fliph', FILTER_SANITIZE_NUMBER_UINT);
$flipv = kh_filter_input(INPUT_GET, 'flipv', FILTER_SANITIZE_NUMBER_UINT);
$width = kh_filter_input(INPUT_GET, 'width', FILTER_SANITIZE_NUMBER_UINT);
$height = kh_filter_input(INPUT_GET, 'height', FILTER_SANITIZE_NUMBER_UINT);
$crop = kh_filter_input(INPUT_GET, 'crop', FILTER_SANITIZE_NUMBER_UINT);

if(!function_exists('imagecreatetruecolor'))
{
	exit();
}

function image_flip($imgsrc, $flipv = false, $fliph = false)
{
$flipv = ($flipv)?1:0;
$fliph = ($fliph)?1:0;
$mode = ($flipv*1)+($fliph*2);
$width = imagesx($imgsrc);
$height	= imagesy($imgsrc);
$src_x = 0;
$src_y = 0;
$src_width = $width;
$src_height = $height;

switch ($mode)
{
case '1': //vertical
$src_y		= $height -1;
$src_height = -$height;
break;

case '2': //horizontal
$src_x      = $width -1;
$src_width  = -$width;
break;
case '3': //both
$src_x      = $width -1;
$src_y      = $height -1;
$src_width  = -$width;
$src_height = -$height;
break;
default:
return $imgsrc;
}
$imgdest = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($imgdest, 255, 255, 255);
imagefilledrectangle($imgdest, 0, 0, $width, $height, $white);
if(imagecopyresampled($imgdest, $imgsrc, 0, 0, $src_x, $src_y , $width, $height, $src_width, $src_height))
{
return $imgdest;
}
return $imgsrc;
}
 
if(file_exists($filepath))
{
$info = getimagesize($filepath, $arr);
$strdata = file_get_contents($filepath);
$img = imagecreatefromstring($strdata);
unset($strdata);
if($flipv || $fliph)
{
$tmp = image_flip($img, $flipv, $fliph);
$img = $tmp;
}

if($angle == 0 || $angle == 90 || $angle == 180 || $angle == 270)
{
	$tmp = imagerotate($img, $angle, 0);
	$img = $tmp;
}
if($width && $height)
{
	if(!$crop){
	$tmp = imagecreatetruecolor($width, $height);
	$white = imagecolorallocate($tmp, 255, 255, 255);
	imagefilledrectangle($tmp, 0, 0, $width, $height, $white);
	imagecopyresampled($tmp, $img, 0, 0, 0, 0, $width, $height, imagesx($img), imagesy($img));
	$img = $tmp;
	}
	else
	{
		$tmp = imageresizecrop($img, $width, $height);
		$img = $tmp;
	}
}
if(@$_GET['option']=='save2file')
{
if($cfg->readonly){
	die('READONLY');
}
if($info['mime']=='image/jpeg')
imagejpeg($img, $filepath, 85);
else if($info['mime']=='image/png')
imagepng($img, $filepath);
else if($info['mime']=='image/gif')
imagegif($img, $filepath);
else
imagejpeg($img, $filepath, 85);
echo 'SUCCESS';
}
else
{
header("Content-type:".$info['mime']);
if($info['mime']=='image/jpeg')
imagejpeg($img, null, 65);
else if($info['mime']=='image/png')
imagepng($img, null);
else if($info['mime']=='image/gif')
imagegif($img, null);
else
imagejpeg($img, null, 65);
}
}

function imageresizecrop($imgsrc, $dwidth, $dheight){
$ww = imagesx($imgsrc);
$hh = imagesy($imgsrc);
$ratio1 = $dheight/$dwidth;
$ratio2 = $dwidth/$dheight;
if($hh>= ($ratio1*$ww)){
	$hh2 = (int)($ratio1*$ww);
	$ww2 = $ww;
	$y2 = (int)(($hh-$hh2)/2);
	$x2 = 0;
}
else{
	$ww2 = (int)($ratio2*$hh);
	$hh2 = $hh;
	$x2 = (int)(($ww-$ww2)/2);
	$y2 = 0;
}
$im1 = imagecreatetruecolor($ww2,$hh2); 
$white = imagecolorallocate($im1, 255, 255, 255);
imagefilledrectangle($im1, 0, 0, $ww2, $hh2, $white);
imagecopyresampled($im1, $imgsrc, 0, 0, $x2, $y2, $ww2, $hh2, $ww2, $hh2);
$im2 = imagecreatetruecolor($dwidth, $dheight); 	
$white = imagecolorallocate($im2, 255, 255, 255);
imagefilledrectangle($im2, 0, 0, $dwidth, $dheight, $white);
imagecopyresampled($im2, $im1, 0, 0, 0, 0, $dwidth, $dheight, $ww2, $hh2);
return $im2;
}
?>