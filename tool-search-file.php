<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
$dir = path_decode(kh_filter_input(INPUT_GET, 'dir'), $cfg->rootdir);
$lv2 = new listFile($dir);
$arrfile = $lv2->result_file;
$arrdir = $lv2->result_dir;

if((count($arrfile)+count($arrdir))>0)
{
?>     
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="file-table file-result-table">
<thead>
<tr>
  <td width="16">Icon</td>
  <td width="60%">File Name</td>
  <td>Location</td>
  <td>Size</td>
  <td>MIME Type</td>
  <td>Modified</td>
</tr>
</thead>
<tbody>
<?php
$i = 0;
foreach($arrdir as $k=>$val)
{
$i++;
?>
<tr class="row-data-dir row-<?php echo ($i%2)?'odd':'even';?>" data-file-name="<?php echo $val['name'];?>" data-file-location="<?php echo $val['location'];?>" data-file-type="dir">
  <td><img src="style/images/trans16.gif" class="fileicon fileicon-dir" /></td>
  <td><a href="javascript:;" onClick="return openDirSearch('<?php echo str_replace("'", "\'", $val['path']);?>')"><?php echo $val['name'];?></a></td>
  <td><a href="javascript:;" onClick="return openDirSearch('<?php echo str_replace("'", "\'", dirname($val['path']));?>')" title="<?php echo $val['location'];?>"><?php echo basename($val['location']);?></a></td>
  <td align="right"></td>
  <td>dir</td>
  <td><?php echo $val['filemtime'];?></td>
</tr>
<?php
}
?>
<?php
foreach($arrfile as $k=>$val)
{
	$i++;
?>
<tr class="row-data-file row-<?php echo ($i%2)?'odd':'even';?>" data-file-url="<?php echo $val['url'];?>" data-file-name="<?php echo $val['name'];?>" data-file-location="<?php echo $val['location'];?>" data-file-type="<?php echo $val['type'];?>" data-file-size="<?php echo $val['size'];?>" data-image-width="<?php echo $val['image_width'];?>" data-image-height="<?php echo $val['image_height'];?>">
  <td><img src="style/images/trans16.gif" class="fileicon fileicon-<?php echo $val['extension'];?>" /></td>
  <td><a href="javascript:;" onClick="return selectFileSeach('<?php echo $val['url'];?>')"><?php echo $val['name'];?></a></td>
  <td><a href="javascript:;" onClick="return openDirSearch('<?php echo $val['location'];?>')" title="<?php echo $val['location'];?>"><?php echo basename($val['location']);?></a></td>
  <td align="right"><?php echo $val['size'];?></td>
  <td><?php if(strlen($val['type'])>18){$val['type'] = '<span title="'.$val['type'].'">'.substr($val['type'],0,18).'&hellip;</span>';} echo ($val['type'])?$val['type']:$val['extension'];?></td>
  <td><?php echo $val['filemtime'];?></td>
</tr>
<?php
}
?>
</tbody>
</table>
<?php
}
?>