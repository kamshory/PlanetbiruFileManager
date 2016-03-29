<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
$authblogid = 1;
if(isset($_POST['save']))
{
	if(isset($_POST['data']))
	{
		parse_str($_POST['data'], $_POST);
		$imagequality = kh_filter_input(INPUT_POST, 'imagequality', FILTER_SANITIZE_NUMBER_UINT);
		if($imagequality > 100) $imagequality = 100;
		writeprofile("imagequality", $imagequality, $authblogid);
		writeprofile("imageinterlace", kh_filter_input(INPUT_POST, 'imageinterlace', FILTER_SANITIZE_NUMBER_UINT), $authblogid);
		writeprofile("compressimageonupload", kh_filter_input(INPUT_POST, 'compressimageonupload', FILTER_SANITIZE_NUMBER_UINT), $authblogid);
		writeprofile("maximagewidth", kh_filter_input(INPUT_POST, 'maximagewidth', FILTER_SANITIZE_NUMBER_UINT), $authblogid);
		writeprofile("maximageheight", kh_filter_input(INPUT_POST, 'maximageheight', FILTER_SANITIZE_NUMBER_UINT) ,$authblogid);
		writeprofile("imageformat", kh_filter_input(INPUT_POST, 'imageformat', FILTER_SANITIZE_STRING), $authblogid);
		echo 'SAVED';
	}
}
if(isset($_POST['change-state']))
{
	$state = kh_filter_input(INPUT_POST, 'state', FILTER_SANITIZE_NUMBER_UINT);
	$_SESSION['compress-image-cb'] = $state;
}
if(@$_GET['show-form'])
{
?>
<form name="uploadsetting" id="uploadsetting" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method="post" enctype="multipart/form-data">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dialog-table">
<tr>
<td class="tdr" width="50%">Compress Image Automatically</td>
<td><?php
$compressimageonupload=getfmprofile('compressimageonupload',$authblogid,0);?>
<label><input type="radio" name="compressimageonupload" class="compressimageonupload" value="1"<?php if($compressimageonupload==1) echo ' checked="checked"';?> /> Yes</label>
<label><input type="radio" name="compressimageonupload" class="compressimageonupload" value="0"<?php if($compressimageonupload==0) echo ' checked="checked"';?> /> No</label>
</td>
</tr>
<tr>
<td class="tdr">Progressive Display</td>
<td><?php
$imageinterlace=getfmprofile('imageinterlace',$authblogid,0);?>
<label><input type="radio" name="imageinterlace" class="imageinterlace" value="1"<?php if($imageinterlace==1) echo ' checked="checked"';?> /> Yes</label>
<label><input type="radio" name="imageinterlace" class="imageinterlace" value="0"<?php if($imageinterlace==0) echo ' checked="checked"';?> /> No</label>
</td>
</tr>
<tr>
<td class="tdr">Image Format to be Compressed</td>
<td><select name="imageformat" id="imageformat">
	<?php
    $imageformat = getfmprofile('imageformat',$authblogid,0);
    ?>
    <option value="0"<?php if($imageformat==0) echo ' selected';?>>JPEG only</option>
	<option value="1"<?php if($imageformat==1) echo ' selected';?>>All formats</option>
  </select></td>
</tr>
<tr>
<td class="tdr">Maximum Image Width</td>
<td><input type="text" name="maximagewidth" id="maximagewidth" value="<?php echo getfmprofile('maximagewidth',$authblogid,600);?>" class="input-text input-text-short" autocomplete="off" /></td>
</tr>
<tr>
<td class="tdr">Maximum Image Height</td>
<td><input type="text" name="maximageheight" id="maximageheight" value="<?php echo getfmprofile('maximageheight',$authblogid,800);?>" class="input-text input-text-short" autocomplete="off" /></td>
</tr>

<tr>
<td class="tdr">Image Quality</td>
<td><input type="text" name="imagequality" id="imagequality" value="<?php echo getfmprofile('imagequality',$authblogid,80);?>" class="input-text input-text-short" autocomplete="off" /></td>
</tr>
</table>
</form>
<?php
}
if(@$_GET['show-control'])
{
$compressimageonupload=getfmprofile('compressimageonupload',$authblogid,0);
$imageformat = getfmprofile('imageformat',$authblogid,0);
?>
<label><input type="checkbox" id="compress-image-cb" name="compress-image-cb" value="1"<?php if($compressimageonupload==1) echo ' checked="checked"';?> onChange="setActiveCompress(this.checked)" /> Compress <?php if($imageformat==0) echo 'JPEG ';?>Image Maximum <?php echo getfmprofile('maximagewidth',$authblogid,600);?>x<?php echo getfmprofile('maximageheight',$authblogid,800);?></label>
<?php
}
?>