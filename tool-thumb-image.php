<?php
include_once(dirname(__FILE__)."/functions.php");
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php"; //NOSONAR
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
if(!$cfg->thumbnail_quality)
$cfg->thumbnail_quality=75;

$filepath = path_decode(kh_filter_input(INPUT_GET, 'filepath'), $cfg->rootdir);

function gettumbpict($originalfile, $maxw, $maxh)
{
	global $cfg;
	$image = new StdClass();
	$filesize = filesize($originalfile);
	if($filesize > $cfg->thumbnail_max_size)
	{
		return false;
	}
	$imageinfo=@getimagesize($originalfile);
	if(empty($imageinfo))
	{
		return 0;
	}
	$image->width=$imageinfo[0];
	$image->height=$imageinfo[1];
	$image->type=$imageinfo[2];
	$newwidth=$image->width;
	$newheight=$image->height;
	if(!$newwidth || !$newheight)
	{
		return false;
	}
	if($maxw)
	{
		if($image->width>$maxw)
		{
			$newwidth=$maxw;
			$newheight=$image->height*$maxw/$image->width;
		}
	}
	if($maxh)
	{
		if($newheight>$maxh)
		{
			$tmp=$newheight;$newheight=$maxh;
			$newwidth=$newwidth*$maxh/$tmp;
		}
	}
	switch($image->type)
	{
		case IMAGETYPE_GIF:
		if(function_exists('ImageCreateFromGIF'))
		{
			$im=@ImageCreateFromGIF($originalfile);
		} 
		else
		{
			return false;
		}
		break;
		case IMAGETYPE_JPEG:
		if(function_exists('ImageCreateFromJPEG'))
		{
		$im=@ImageCreateFromJPEG($originalfile);
		}
		else
		{
			return false;
		}
		break;
		case IMAGETYPE_PNG:
		if(function_exists('ImageCreateFromPNG'))
		{
			$im=@ImageCreateFromPNG($originalfile);
		}
		else
		{
			return false;
		}
		break;
		default:
		return false;
	}
	$im1=imagecreatetruecolor($newwidth ,$newheight);
	$cx=$image->width / 2;
	$cy=$image->height / 2;
	if($image->width < $image->height)
	{
		$half=floor($image->width / 2.0);
	}
	else
	{
		$half=floor($image->height / 2.0);
	}
	$white = imagecolorallocate($im1, 255, 255, 255);
	$black = imagecolorallocate($im1, 0, 0, 0);
	if(!$im)
	return false;
	imagefilledrectangle($im1, 0, 0, $newwidth ,$newheight, $white);
	imagecopyresized($im1,$im,0,0,0,0,$newwidth ,$newheight,$image->width,$image->height);
	return $im1;
}

if(file_exists($filepath))
{
	$filetype=filetype($filepath);
	if($filetype=="file")
	{
		$expires = $cfg->cache_max_age_file;
		header("Pragma: public");
		header("Cache-Control: maxage=".$expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
		if(!function_exists('imagecreatefrompng'))
		{
			readfile(dirname(__FILE__)."/style/images/common/image.png");
			header('Content-Type: image/png');
		}
		else
		{
			$image=gettumbpict($filepath,96,96);
			if($image)
			{
				header('Content-Type: image/jpeg');
				@imagejpeg($image,NULL,$cfg->thumbnail_quality);
			}
			else
			{	
				$ft=getfiletype(basename($filepath));
				if(file_exists(dirname(__FILE__)."/style/images/common/$ft.png"))
				{
					$image=imagecreatefrompng(dirname(__FILE__)."/style/images/common/$ft.png");
				}
				else
				{		
					$image=imagecreatefrompng(dirname(__FILE__)."/style/images/binfile.png");
				}
				header('Content-Type: image/png');
				@imagepng($image);
			}
		}
	}
	if($filetype=="dir")
	{
		$expires = $cfg->cache_max_age_dir;
		header("Pragma: public");
		header("Cache-Control: maxage=".$expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
		if(!function_exists('imagecreatefrompng'))
		{
			readfile(dirname(__FILE__)."/style/images/folder.png");
			header('Content-Type: image/png');
		}
		else
		{
			$image=imagecreatefrompng(dirname(__FILE__)."/style/images/folder.png");
			if($handle=opendir($filepath))
			{
				$i=0;
				while(false !==($ufile=readdir($handle)))
				{
					$fn="$filepath/$ufile";
					if($ufile == "." || $ufile == "..")
					{
						continue;
					}
					$filetype=filetype($fn);
					if($filetype=="file")
					{
						$img2[$i]=gettumbpict($fn,40,40);
						if($img2[$i])
						{
							$width=imagesx($img2[$i]);
							$height=imagesy($img2[$i]);
							$x1=floor((40-$width)/2);

							$y1=floor((40-$height)/2);
							if($i<2)
								$y=8;
							else
								$y=52;
							if(!($i%2))
								$x=6;
							else
								$x=50;
							@imagecopy($image,$img2[$i],$x+$x1,$y+$y1,0,0,$width,$height);
							$i++;
						}
					}
					if($i>3)
					{
						break;
					}
				}
			}

			header('Content-Type: image/jpeg');
			@imagejpeg($image, NULL, $cfg->thumbnail_quality);
		}
	}
}
?>