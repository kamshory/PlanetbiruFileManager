<?php
include_once dirname(__FILE__)."/functions.php";
include dirname(__FILE__)."/conf.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
$filepath = path_decode(kh_filter_input(INPUT_GET, 'filepath'), $cfg->rootdir);
$fileurl = htmlspecialchars(kh_filter_input(INPUT_GET, 'filepath', FILTER_SANITIZE_STRING));

$error_code = "";
if(file_exists($filepath))
{
if(!is_dir($filepath))
{
$size = getimagesize($filepath);
$url = path_decode_to_url(@$_GET['filepath'], $cfg->rooturl);
if(stripos($size['mime'], 'image')===0)
{
?>
<div id="image-editor-all">
<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <div class="image-editor-filename-name-area"><input type="text" name="curfilepath" id="curfilepath" value="<?php echo $fileurl;?>" /><input type="hidden" name="curfileurl" id="curfileurl" value="<?php echo htmlspecialchars(stripslashes($url));?>" />
    <input type="button" name="openimage" id="openimage" value="Open" onClick="editImage($('#curfilepath').val())" class="com-button" />
    <input type="button" name="closeeditor" id="closeeditor" value="Close" onClick="destroyImageEditor()" class="com-button delete-button" />
  </div>
  <div class="image-editor-middle">
	<div class="image-editor-sidebar">
    <?php
	if(function_exists('imagecreatefromjpeg'))
	{
	?>
    <div class="image-editor-sidebar-inner">
      <div class="original-image"><img id="imageori" src="<?php echo htmlspecialchars(stripslashes($url));?>?rand=<?php echo mt_rand(111111,999999);?>" /></div>
    	<div class="current-dimension">
    	<table width="165" border="0" cellspacing="0" cellpadding="0">
    	  <tr>
    	    <td width="90">Current Width</td>
    	    <td width="75"><?php echo $size[0];?> px <input type="hidden" name="curwidth" id="curwidth" value="<?php echo $size[0];?>" /></td>
  	    </tr>
    	  <tr>
    	    <td>Current Height</td>
    	    <td><?php echo $size[1];?> px <input type="hidden" name="curheight" id="curheight" value="<?php echo $size[1];?>" /></td>
  	    </tr>
  	  </table>
      </div>
      <div class="image-editor-tool">
      	<div class="image-editor-tool-item image-tool-resize"><a href="javascript:resizeImage('<?php echo $fileurl;?>')">Resize Image</a></div>
        <div class="image-tool-resize-dimension">
            <div class="new-dimension">
            <table width="150" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="75">New Width</td>
                <td><input type="text" name="newwidth" id="newwidth" class="image-dim" value="<?php echo $size[0];?>" /> 
                  px</td>
            </tr>
              <tr>
                <td>New Height</td>
                <td><input type="text" name="newheight" id="newheight" class="image-dim" value="<?php echo $size[1];?>" /> px</td>
            </tr>
              <tr>
                <td colspan="2"><label><input type="checkbox" name="aspectratio" id="aspectratio" value="1" class="input-checkbox" checked="checked" /> Keep aspect ratio</label></td>
                </tr>
              <tr>
                <td colspan="2"><label><input type="checkbox" name="cropimage" id="cropimage" value="1" class="input-checkbox" /> Crop image on center</label></td>
                </tr>
            </table>
          </div>
        
        </div>
      	<div class="image-editor-tool-item image-tool-rotate-cw"><a href="javascript:rotateCW('<?php echo $fileurl;?>')">Rotate Clockwise</a></div>
      	<div class="image-editor-tool-item image-tool-rotate-ccw"><a href="javascript:rotateCCW('<?php echo $fileurl;?>')">Rotate Counterclockwise</a></div>
      	<div class="image-editor-tool-item image-tool-flip-h"><a href="javascript:flipH('<?php echo $fileurl;?>')">Flip Horizontal</a></div>
      	<div class="image-editor-tool-item image-tool-flip-v"><a href="javascript:flipV('<?php echo $fileurl;?>')">Flip Vertical</a></div>
      </div>
      <div class="button-area">
      <input type="button" name="save" id="save" value="Save" class="com-button" onclick="saveImage()" />
      <input type="button" name="discharge" id="discharge" value="Close" class="com-button delete-button" onClick="destroyImageEditor()" />
      </div>
      </div>
      <?php
	}
	else
	{
	?>
    <div class="image-editor-sidebar-inner">
    <div class="warning">GD module is not installed on this server.</div>
    </div>
    <?php
	}
	?>
    </div>  
	<div class="image-editor-mainbar">
    	<div class="image-editor-mainbar-inner">
        	<div id="image-content">
            	<img id="image2edit" src="<?php echo htmlspecialchars(stripslashes($url));?>?rand=<?php echo mt_rand(111111,999999);?>" />
            </div>
        </div>
    </div>  
  </div>
</form>
</div>
<?php
}
else
{
$error_code = "NOT_IMAGE";
}
}
else
{
$error_code = "NOT_FILE";
}
}
else
{
$error_code = "NOT_FOUND";
}

if(strlen($error_code))
{
?>
<div id="image-editor-all">
<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
<div class="image-editor-filename-name-area"><input type="text" name="curfilepath" id="curfilepath" value="<?php echo $fileurl;?>" /><input type="hidden" name="curfileurl" id="curfileurl" value="<?php echo htmlspecialchars(stripslashes($url));?>" />
<input type="button" name="openimage" id="openimage" value="Open" onClick="editImage($('#curfilepath').val())" class="com-button" />
<input type="button" name="closeeditor" id="closeeditor" value="Close" onClick="destroyImageEditor()" class="com-button delete-button" />
</div>
</form>
  <div class="image-editor-middle">
  <?php
  switch($error_code)
  {
	  case "NOT_IMAGE":
	  echo "This file has not valid image format.";
	  break;
	  case "NOT_FILE":
	  echo "The path you type is not a file.";
	  break;
	  case "NOT_FOUND":
	  echo "File not found. Be sure that you type correct path of file.";
	  break;
  }
  ?>
  
</div>
</div>
<?php
}
?>