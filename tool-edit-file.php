<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
if(@$_GET['option']=='openfile')
{
$filepath = path_decode(kh_filter_input(INPUT_GET, 'filepath'), $cfg->rootdir);
if(file_exists($filepath))
{
	$cnt = file_get_contents($filepath);
}
else
{
	$cnt = "";
}
?>
<form id="filetexteditor" name="filetexteditor" method="post" action="">
<div class="filename-area">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<input type="text" class="input-text" name="filepath" id="filepath" value="<?php echo htmlspecialchars(path_encode($filepath, $cfg->rootdir));?>" autocomplete="off" />
</td>
<td width="64" align="right">
  <input type="button" name="open" id="open" value="Open" class="com-button" onclick="openFile($('#filepath').val())" />
  </td>
  </tr>
  </table>
</div>
<div class="fileeditor">
<textarea name="filecontent" id="filecontent" spellcheck="false"><?php echo htmlspecialchars($cnt);?></textarea>
</div>
</form>
<?php
}
if(@$_GET['option']=='savefile' && isset($_POST['filepath']))
{
	if($cfg->readonly){
		die('READONLY');
	}
	
	$filepath = path_decode(kh_filter_input(INPUT_POST, 'filepath'));
	// prepare dir
	$dir = dirname($filepath);
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
	
	$content = kh_filter_input(INPUT_POST, 'filecontent');
	$content = str_replace(array("\n"), array("\r\n"), $content);
	$content = str_replace(array("\r\r\n"), array("\r\n"), $content);
	
	$tt = getMIMEType($filepath);
	if(in_array($tt->extension, $cfg->forbidden_extension)){
		die('FORBIDDENEXT');
	}
	
	if(!is_writable($filepath) && file_exists($filepath))
	{
		die('READONLYFILE');
	}
	if(filetype($filepath) == 'dir' && file_exists($filepath))
	{
		die('ISDIR');
	}
	$md51 = md5_file($filepath);
	$fp = fopen($filepath, "w");
	fwrite($fp, $content);
	fclose($fp);
	$md52 = md5_file($filepath);
	if($md51 != $md52)
	{
	echo 'SAVED';
	}
	else
	{
	echo 'NOTMODIFIED';
	}
}
?>