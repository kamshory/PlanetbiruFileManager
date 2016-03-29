<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
$filename = path_decode(kh_filter_input(INPUT_GET, 'filepath'), $cfg->rootdir);
$json_exif = "";
if(@$_GET['type']=='directory')
{
if(file_exists($filename))
{
	$filectime = date('Y-m-d H:i:s',  filectime($filename));
	$fileatime = date('Y-m-d H:i:s',  fileatime($filename));
	$filemtime = date('Y-m-d H:i:s',  filemtime($filename));
	$fileperms = substr(sprintf('%o', fileperms($filename)), -4);
}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
  <tr>
    <td width="30%">Directory Name</td>
    <td><div class="file-name-cropped"><?php echo basename($filename);?></div></td>
  </tr>
  <tr>
    <td>Created</td>
    <td><?php echo $filectime;?></td>
  </tr>
  <tr>
    <td>Last Modified</td>
    <td><?php echo $filemtime;?></td>
  </tr>
  <tr>
    <td>Last Accessed</td>
    <td><?php echo $fileatime;?></td>
  </tr>
  <tr>
    <td>Permission</td>
    <td><span class="permission-info"><?php echo $fileperms;?></span></td>
  </tr>
</table>
<?php
}
else if(@$_GET['type']=='image')
{
$ft = getMIMEType($filename);
if(file_exists($filename))
{
	$is = @getimagesize($filename);
	if($is)
	{
		if($is['mime'])
		{
			$ft->mime = $is['mime'];
		}
		$width = $is[0];
		$height = $is[1];
		if(function_exists("exif_read_data"))
		{
			$exif = @exif_read_data($filename, 0, true);
			$json_exif = rawurlencode(json_encode(array(get_capture_info($exif))));
			if(isset($exif['IFD0']['Make']))
			{
				if(isset($exif['IFD0']['Model']))
				{
					if(strpos($exif['IFD0']['Model'], $exif['IFD0']['Make']) === 0)
					{
						$exif['IFD0']['Make'] = '';
					}
				}
			}
			
			$camera = (isset($exif['IFD0']['Make']))?(($exif['IFD0']['Make'].' '.$exif['IFD0']['Model'])):'-';
			$time_capture = (@$exif['IFD0']['Datetime'])?(@$exif['IFD0']['Datetime']):(@$exif['EXIF']['DateTimeOriginal'])?(@$exif['EXIF']['DateTimeOriginal']):'-';
			if(isset($exif['GPS']))
			{
				$gpsinfo = $exif['GPS'];
				
				$latar = explode("/",@$gpsinfo['GPSLatitude'][0]);
				if(count($latar)>1 && $latar[1])
				$latd = $latar[0]/$latar[1];
				$latar = explode("/",@$gpsinfo['GPSLatitude'][1]);
				if(count($latar)>1 && $latar[1])
				$latm = $latar[0]/$latar[1];
				$latar = explode("/",@$gpsinfo['GPSLatitude'][2]);
				if(count($latar)>1 && $latar[1])
				$lats = $latar[0]/$latar[1];
				$reallat = dmstoreal($latd, $latm, $lats);
				if(stripos(@$gpsinfo['GPSLatitudeRef'],"S")!==false)
				$reallat = $reallat*-1;
				$latitude = "$latd; $latm; $lats ".@$gpsinfo['GPSLatitudeRef'];
				$latitude = trim($latitude, " ; ");
				
				$longar = explode("/",@$gpsinfo['GPSLongitude'][0]);
				if(count($longar)>1 && $longar[1])
				$longd = $longar[0]/$longar[1];
				$longar = explode("/",@$gpsinfo['GPSLongitude'][1]);
				if(count($longar)>1 && $longar[1])
				$longm = $longar[0]/$longar[1];
				$longar = explode("/",@$gpsinfo['GPSLongitude'][2]);
				if(count($longar)>1 && $longar[1])
				$longs = $longar[0]/$longar[1];
				
				$reallong = dmstoreal($longd, $longm, $longs);
				if(stripos(@$gpsinfo['GPSLongitudeRef'],"W")!==false)
				$reallong = $reallong*-1;
				$longitude = "$longd; $longm; $longs ".@$gpsinfo['GPSLongitudeRef'];
				
				$longitude = trim($longitude, " ; ");
				
				$alar = explode("/",@$gpsinfo['GPSAltitude']);
				if(count($alar)>1 && $alar[1])
				$altitude = $alar[0]/$alar[1];
				$altref = @$gpsinfo['GPSAltitudeRef'];
			}
			else
			{
				$latitude = "-";
				$longitude = "-";
				$altitude = "-";
				$altref = "";
			}
			
		}
	}
	$filectime = date('Y-m-d H:i:s',  filectime($filename));
	$fileatime = date('Y-m-d H:i:s',  fileatime($filename));
	$filemtime = date('Y-m-d H:i:s',  filemtime($filename));
	$fileperms = substr(sprintf('%o', fileperms($filename)), -4);
	$md5 = md5_file($filename);
	$filesize = filesize($filename);
}
$url = $cfg->rooturl.'/'.substr(path_encode($filename, $cfg->rootdir),5);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
  <tr>
    <td width="30%">File Name</td>
    <td><div class="file-name-cropped"><a href="<?php echo $url;?>" target="_blank" title="Click to download"><?php echo basename($filename);?></a></div></td>
  </tr>
  <tr>
    <td>MIME Type</td>
    <td><?php echo $ft->mime;?></td>
  </tr>
  <tr>
    <td>Image Width</td>
    <td><?php echo $width;?></td>
  </tr>
  <tr>
    <td>Image Height</td>
    <td><?php echo $height;?></td>
  </tr>
  <tr>
    <td>Time Taken</td>
    <td><?php echo $time_capture;?></td>
  </tr>
  <tr>
    <td>Camera</td>
    <td><span class="capture-info" data-exif="<?php echo $json_exif;?>"><?php echo $camera;?></span></td>
  </tr>
  <tr>
    <td>Latitude</td>
    <td><?php echo $latitude;?></td>
  </tr>
  <tr>
    <td>Longitude</td>
    <td><?php echo $longitude;?></td>
  </tr>
  <tr>
    <td>Altitude</td>
    <td><?php echo $altitude." ".$altref;?></td>
  </tr>
  <tr>
    <td>File Size</td>
    <td><?php echo ($filesize>0)?($filesize.' bytes'):($filesize.' byte');?></td>
  </tr>
  <tr>
    <td>MD5</td>
    <td><?php echo $md5;?></td>
  </tr>
  <tr>
    <td>Created</td>
    <td><?php echo $filectime;?></td>
  </tr>
  <tr>
    <td>Last Modified</td>
    <td><?php echo $filemtime;?></td>
  </tr>
  <tr>
    <td>Last Accessed</td>
    <td><?php echo $fileatime;?></td>
  </tr>
  <tr>
    <td>Permission</td>
    <td><span class="permission-info"><?php echo $fileperms;?></span></td>
  </tr>
</table>
<?php
}
else if(@$_GET['type']=='video')
{
$ft = getMIMEType($filename);
if(file_exists($filename))
{
	$filectime = date('Y-m-d H:i:s',  filectime($filename));
	$fileatime = date('Y-m-d H:i:s',  fileatime($filename));
	$filemtime = date('Y-m-d H:i:s',  filemtime($filename));
	$fileperms = substr(sprintf('%o', fileperms($filename)), -4);
	$md5 = md5_file($filename);
	$filesize = filesize($filename);
}
$url = $cfg->rooturl.'/'.substr(path_encode($filename, $cfg->rootdir),5);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
  <tr>
    <td width="30%">File Name</td>
    <td><div class="file-name-cropped"><a href="<?php echo $url;?>" target="_blank" title="Click to download"><?php echo basename($filename);?></a></div></td>
  </tr>
  <tr>
    <td>MIME Type</td>
    <td><span class="mime-type" data-content="<?php echo trim($ft->mime);?>"><?php echo $ft->mime;?></span></td>
  </tr>
  <tr>
    <td>File Size</td>
    <td><?php echo ($filesize>0)?($filesize.' bytes'):($filesize.' byte');?></td>
  </tr>
  <tr>
    <td>MD5</td>
    <td><?php echo $md5;?></td>
  </tr>
  <tr>
    <td>Created</td>
    <td><?php echo $filectime;?></td>
  </tr>
  <tr>
    <td>Last Modified</td>
    <td><?php echo $filemtime;?></td>
  </tr>
  <tr>
    <td>Last Accessed</td>
    <td><?php echo $fileatime;?></td>
  </tr>
  <tr>
    <td>Permission</td>
    <td><span class="permission-info"><?php echo $fileperms;?></span></td>
  </tr>
</table>
<?php
}
else
{
$ft = getMIMEType($filename);
if(file_exists($filename))
{
	$filectime = date('Y-m-d H:i:s',  filectime($filename));
	$fileatime = date('Y-m-d H:i:s',  fileatime($filename));
	$filemtime = date('Y-m-d H:i:s',  filemtime($filename));
	$fileperms = substr(sprintf('%o', fileperms($filename)), -4);
	$md5 = md5_file($filename);
	$filesize = filesize($filename);
}
$url = $cfg->rooturl.'/'.substr(path_encode($filename, $cfg->rootdir),5);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
  <tr>
    <td width="30%">File Name</td>
    <td><div class="file-name-cropped"><a href="<?php echo $url;?>" target="_blank" title="Click to download"><?php echo basename($filename);?></a></div></td>
  </tr>
  <tr>
    <td>MIME Type</td>
    <td><span class="mime-type" data-content="<?php echo trim($ft->mime);?>"><?php echo $ft->mime;?></span></td>
  </tr>
  <tr>
    <td>File Size</td>
    <td><?php echo ($filesize>0)?($filesize.' bytes'):($filesize.' byte');?></td>
  </tr>
  <tr>
    <td>MD5</td>
    <td><?php echo $md5;?></td>
  </tr>
  <tr>
    <td>Created</td>
    <td><?php echo $filectime;?></td>
  </tr>
  <tr>
    <td>Last Modified</td>
    <td><?php echo $filemtime;?></td>
  </tr>
  <tr>
    <td>Last Accessed</td>
    <td><?php echo $fileatime;?></td>
  </tr>
  <tr>
    <td>Permission</td>
    <td><span class="permission-info"><?php echo $fileperms;?></span></td>
  </tr>
</table>
<?php
}
?>