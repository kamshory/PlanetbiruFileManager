<?php
include_once dirname(__FILE__)."/functions.php";
include dirname(__FILE__)."/auth.php";
if($cfg->authentification_needed && !$userlogin)
{
	include_once dirname(__FILE__)."/tool-login-form.php";
	exit();
}

$dir = trim(stripslashes(@$_GET['dir']),"/");
if(!is_dir(path_decode($dir, $cfg->rootdir))){
	$dir = '';	
}

if(!$dir) $dir =  'base';
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Planetbiru File Manager</title>
<link rel="shortcut icon" href="style/images/icon.png" type="image/jpeg" />
<link rel="stylesheet" type="text/css" href="js/jquery/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="style/file-type.css" />
<link rel="stylesheet" type="text/css" href="style/style.css" />
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui.datetimepicker.addon.min.js"></script>
<script type="text/javascript" src="js/overlay-dialog.js"></script>
<script type="text/javascript">
var vrel = '<?php echo $cfg->rooturl;?>/';
var vabs = 'base/';
window.onload = function(){
updateToolbarStatus();
$(document).on('change', 'input[type=checkbox]', function(){
updateToolbarStatus()
});
$(document).on('change', '.checkbox-selector', function(){
	selectAll($(this)[0].checked);
});
if(document.images){var preload = new Image(16,16);preload.src = 'style/images/loading.gif';}
setTimeout(function(){
initContextMenuFile();
initContextMenuDir();
initContextMenuFileArea();
setCheckRelation();
initDropable();
initSortable();
$('#opendir').val('Open');
preloadImage();
},1000);
removeCheckboxBorder();
loadAnimationStop();
setSize();
initPermission();
initEXIF();
initPreviewImageUpload();
initDragDropUpload();
$(window).resize(function(){setSize();});

var tgl = cookieRead('togglethumb');
togglethumb = (tgl==1)?true:false;
if(togglethumb)
{
	$('#tb-thumbnail').addClass('tb-selected');
	<?php
	if(!@$cfg->thumbnail_on_load)
	{
	?>
	$('.file-table').css('display', 'none');
	openDir();
	<?php
	}
	?>
	
}
}
</script>
<?php
if(@$_GET['editor']=='tiny_mce')
{
?>
<script type="text/javascript" src="js/for_tinymce.js"></script>
<script type="text/javascript">
function selectFileIndex(url){
selectFileForTinyMCE(url);
}
</script>
<?php
}
else
{
?>
<script type="text/javascript">
function selectFileIndex(url){
}
</script>
<?php
}
?>
<body class="kamsfilemanager">
<div id="all">
<div id="wrapper">
<div class="toolbar">
<div id="toolbar-inner" class="toolbar-inner">
<div id="anim-loader" class="anim-active"></div>
  <ul>
    <li><a href="javascript:createFile()" title="Create New File"><img src="style/images/trans16.gif" class="createfile" alt="New" /></a></li>
    <li><a href="javascript:createDirectory()" title="Create New Directory"><img src="style/images/trans16.gif" class="createdir" alt="New" /></a></li>
    <li><a href="javascript:uploadFile()" title="Upload File"><img src="style/images/trans16.gif" class="upload" alt="Upload" /></a></li>
    <li><a href="javascript:transferFile()" title="Transfer File"><img src="style/images/trans16.gif" class="transfer" alt="Transfer" /></a></li>
    <li><a href="javascript:goToUpDir()" title="Go to One Up Level Directory"><img src="style/images/trans16.gif" class="up" alt="Up" /></a></li>
    <li><a href="javascript:refreshList()" title="Reload"><img src="style/images/trans16.gif" class="refresh" alt="Reload" /></a></li>
    <li><a href="javascript:searchFile()" title="Search"><img src="style/images/trans16.gif" class="search" alt="Search" /></a></li>
    <li><a href="javascript:selectAll(1)" title="Check All"><img src="style/images/trans16.gif" class="check" alt="Check" /></a></li>
    <li><a href="javascript:selectAll(0)" title="Uncheck All"><img src="style/images/trans16.gif" class="uncheck" alt="Uncheck" /></a></li>
    <li><a href="javascript:copySelectedFile()" title="Copy Selected File"><img src="style/images/trans16.gif" class="copy" alt="Copy" /></a></li>
    <li><a href="javascript:cutSelectedFile()" title="Cut Selected File"><img src="style/images/trans16.gif" class="cut" alt="Cut" /></a></li>
    <li><a href="javascript:moveSelectedFile()" title="Move Selected File"><img src="style/images/trans16.gif" class="move" alt="Move" /></a></li>
    <li><a href="javascript:pasteFile()" title="Paste File"><img src="style/images/trans16.gif" class="paste" alt="Paste" /></a></li>
    <li><a href="javascript:renameFile()" title="Rename First Selected File"><img src="style/images/trans16.gif" class="rename" alt="Rename" /></a></li>
    <li><a href="javascript:deleteSelectedFile()" title="Delete Selected File"><img src="style/images/trans16.gif" class="delete" alt="Delete" /></a></li>
    <li><a href="javascript:compressSelectedFile()" title="Compress Selected File"><img src="style/images/trans16.gif" class="compress" alt="Compress" /></a></li>
    <li><a href="javascript:extractFile()" title="Extract First Selected File"><img src="style/images/trans16.gif" class="extract" alt="Extract" /></a></li>
    <li><a href="javascript:changePermission()" title="Change File Permission"><img src="style/images/trans16.gif" class="permission" alt="Permission" /></a></li>
    <li id="tb-setting"><a href="javascript:uploadFileSettings()" title="Upload File Setting"><img src="style/images/trans16.gif" class="setting" alt="Settings" /></a></li>
    <li id="tb-thumbnail"><a href="javascript:thumbnail()"><img src="style/images/trans16.gif" class="view" alt="View" title="Change View Type" /></a></li>
    <li><a href="javascript:about()" title="About"><img src="style/images/trans16.gif" class="help" alt="Help" /></a></li>
    <li id="tb-clipboard" class="tb-hide"><a href="javascript:showClipboard()"><img src="style/images/trans16.gif" class="clipboard" alt="Clipboard" title="Show Clipboard" /></a></li>
    <li id="tb-clipboard-empty" class="tb-hide"><a href="javascript:emptyClipboard()"><img src="style/images/trans16.gif" class="cleanup" alt="Empty Clipboard" title="Empty Clipboard" /></a></li>
    <li id="tb-logout"><a href="logout.php"><img src="style/images/trans16.gif" class="logout" alt="Logout" title="Logout" /></a></li>
  </ul>
</div>
</div>

<div class="addressbar">
<form name="dirform" method="get" enctype="multipart/form-data" action="" onSubmit="return openDir()">
<table class="address-bar" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<input type="text" class="input-text address" name="address" id="address" value="<?php echo $dir;?>" autocomplete="off" />
</td>
<td>
<input type="submit" name="opendir" id="opendir" class="com-button" value="Open" />
</td>
</tr>
</table>
</form>
</div>

<div class="middle">
	<div class="directory-area">
    	<div id="directory-container">
            <ul>
            <li class="basedir dir-control" data-file-name="base" data-file-location="">
            <a href="javascript:;" onClick="return openDir('base')">base</a>
			  <?php 
              include_once dirname(__FILE__)."/tool-load-dir.php";
              ?>
            </li>
            </ul>
    	</div>
    </div>
    
    <div class="file-area">
    	<div id="file-container">
    	  <?php 
		  include_once dirname(__FILE__)."/tool-load-file.php";
		  ?>
    	</div>
    </div>
    
</div>
</div>
</div>

<div style="display:none">
<div id="common-dialog" title="">
<div id="common-dialog-inner">
</div>
</div>
</div>
<div id="overlay-container" style="display:none"></div>
</body>
</html>