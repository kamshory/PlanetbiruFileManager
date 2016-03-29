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
else
{
	// if exist before, file will not be deleted
	$allowdelete = true;
	if(isset($_FILES['file']['tmp_name']))
	{
		$name = $_FILES["file"]["name"];
		$name = kh_filter_file_name_safe($name);
		if(file_exists($targetdir."/".$name))
		{
			$allowdelete = false;
		}
		if(is_uploaded_file(@$_FILES['file']['tmp_name'])){
		copy($_FILES['file']['tmp_name'], $targetdir."/".$name);
		} 
		move_uploaded_file( $_FILES["file"]["tmp_name"], $targetdir."/".$name);
		deleteforbidden($targetdir);
		$info = getimagesize($targetdir."/".$name);
		compressImageFile($targetdir."/".$name, $authblogid);
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
		?>
        <script type="text/javascript">
			var html = ''+
			'<div style="padding-bottom:4px">'+
			'<form method="post" enctype="multipart/form-data" action="tool-upload-file.php?iframe=1" target="formdumper">'+
			'<input type="hidden" name="targetdir" id="targetdir" value="">'+
			'File <input type="file" name="file" />'+
			'<input type="submit" class="upload-button" value="Upload File" />'+
			'</form></div>'+
			'<div id="response"></div><ul id="image-list"></ul></div>'+
			'<iframe style="display:none; width:0px; height:0px;" id="formdumper" name="formdumper"></iframe>';
			parent.refreshList();
			parent.document.getElementById('imageuploader').innerHTML = html;
		</script>
        <?php
		exit();
	}
}
echo 'SUCCESS';
?>