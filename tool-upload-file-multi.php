<?php
include_once(dirname(__FILE__)."/functions.php");
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
if(!$cfg->allow_upload_all_file && !$cfg->allow_upload_image)
{
	die('DENIED');
}
if($cfg->readonly)
{
	die('READONLY');
}
$targetdir = path_decode(kh_filter_input(INPUT_GET, 'targetdir'), $cfg->rootdir);


if(isset($_FILES["images"]))
{
if(is_array($_FILES["images"]["error"]))
{
foreach($_FILES["images"]["error"] as $key => $error){
    if($error == 0) {
        $name = $_FILES["images"]["name"][$key];
		$name = kh_filter_file_name_safe($name);
		$compressimage = @$_SESSION['compress-image-cb'];
		$settings['compressimageonupload'] = $compressimage;
		// if exist before, file will not be deleted
		$allowdelete = true;
		if(file_exists($targetdir."/".$name))
		{
			$allowdelete = false;
		}
		if(isset($_FILES['images']['tmp_name']))
		{
			if(is_uploaded_file($_FILES['images']['tmp_name'][$key])){
			copy($_FILES['images']['tmp_name'][$key], $targetdir."/".$name);
			} 
			move_uploaded_file($_FILES["images"]["tmp_name"][$key], $targetdir."/".$name);
			$info = getimagesize($targetdir."/".$name);
			compressImageFile($targetdir."/".$name, $authblogid);
			deleteforbidden($targetdir);
			if(stripos($info['mime'],'image')!==false)
			{
				if(!$cfg->allow_upload_image)
				{
					if($allowdelete)
					{
						@unlink($targetdir."/".$name);
					}
					die('FORBIDDEN');
				}
			}
			else if(!$cfg->allow_upload_all_file)
			{
				if($allowdelete)
				{
					@unlink($targetdir."/".$name);
				}
				die('FORBIDDEN');
			}
		}
	}
}
}
}
echo 'SUCCESS';
?>