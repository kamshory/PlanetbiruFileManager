<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}

if(@$cfg->thumbnail_on_load)
{
  if(@$_COOKIE['togglethumb']==1)
  {
	  $_GET['thumbnail']=1;
  }
}


$dir2 = path_decode(kh_filter_input(INPUT_GET, 'dir'), $cfg->rootdir);
if(!is_dir($dir2)){
$dir2 = path_decode('base', $cfg->rootdir);	
}
$arrfile = array();
$arrdir = array();
if(file_exists($dir2))
{
	if($handle = opendir($dir2))
	{
		$i=0;
		while (false !== ($ufile = readdir($handle))) 
		{ 
			$fn = "$dir2/$ufile";
			if($ufile == "." || $ufile == ".." ) 
			{
			continue;
			}
			$filetype = filetype($fn);
			unset($obj);
			if($filetype=="file")
			{
				$ft = getMIMEType($fn);
				$obj['url'] = $cfg->rooturl.'/'.substr(path_encode($fn, $cfg->rootdir),5);
				$obj['path'] = path_encode($fn, $cfg->rootdir);
				$obj['location'] = path_encode(dirname($fn), $cfg->rootdir);
				$obj['name'] = basename($fn);
				$fs = filesize($fn);
				$obj['filesize'] = $fs;
				if($fs>=1048576)
				{
					$obj['size'] = number_format($fs/1048576, 2, '.','').'M';
				}
				else if($fs>=1024)
				{
					$obj['size'] = number_format($fs/1024, 2, '.','').'K';
				}
				else
				{
					$obj['size'] = $fs;
				}
				$obj['type'] = $ft->mime;
				$obj['extension'] = $ft->extension;
				$obj['permission'] = substr(sprintf('%o', fileperms($fn)), -4);
				$fti = filemtime($fn);
				$obj['filemtime'] = '<span title="'.date('Y-m-d H:i:s', $fti).'">'.date('y-m-d', $fti).'</span>';
				$obj['mtime'] = $fti;
				
				if((stripos($obj['type'], 'image') !== false || stripos($obj['type'], 'application/x-shockwave-flash') !== false) && $obj['filesize'] <= $cfg->thumbnail_max_size)
				{
					try
					{
						$is = @getimagesize($fn);
						if($is)
						{
							$obj['image_width'] = $is[0];
							$obj['image_height'] = $is[1];
							if(stripos($is['mime'], 'image')===0) 
							{
								$obj['type'] = $is['mime'];
							}
						}
						else
						{
						$obj['image_width'] = 0;
						$obj['image_height'] = 0;
						}
					}
					catch(Exception $e)
					{
						$obj['image_width'] = 0;
						$obj['image_height'] = 0;
					}
				}
				else
				{
					$obj['image_width'] = 0;
					$obj['image_height'] = 0;
				}
				$arrfile[] = $obj;
			}
			else if($filetype=="dir")
			{
				$obj['path'] = path_encode($fn, $cfg->rootdir);
				$obj['location'] = path_encode(dirname($fn), $cfg->rootdir);
				$obj['name'] = basename($fn);
				$obj['type'] = 'dir';
				$obj['permission'] = substr(sprintf('%o', fileperms($fn)), -4);
				$fti = filemtime($fn);
				$obj['filemtime'] = '<span title="'.date('Y-m-d H:i:s', $fti).'">'.date('y-m-d', $fti).'</span>';
				$obj['mtime'] = $fti;
				$arrdir[] = $obj;
			}
		}
	}
}
$sortby = kh_filter_input(INPUT_GET, 'sortby', FILTER_SANITIZE_STRING);
if(!in_array($sortby, array('name', 'filesize', 'type', 'permission', 'filemtime')))
$sortby = '';							
if($sortby == '')
$sortby = 'type';
$sortorder = kh_filter_input(INPUT_GET, 'sortorder', FILTER_SANITIZE_STRING);
if(!in_array($sortorder, array('asc', 'desc')))
$sortorder = '';							
if($sortorder == '')
$sortorder = 'asc';


$_order = array();
foreach ($arrdir as &$row){
$_order[] = &$row['name'];
}
array_multisort($_order, SORT_ASC, SORT_STRING, $arrdir);

$_order = array();
$_order2 = array();
// remove extension before sort
$arrfilesort =  $arrfile;
foreach($arrfilesort as $key=>$val)
{
	$pos = strripos($val['name'], ".");
	if($pos !== false)
	{
		$arrfilesort[$key]['name'] = substr($val['name'], 0, $pos);
	}
}
foreach ($arrfilesort as &$row){
$_order[] = &$row[$sortby];
$_order2[] = &$row['name'];
}
array_multisort($_order, ($sortorder=='desc')?SORT_DESC:SORT_ASC, $_order2, SORT_ASC, SORT_STRING, $arrfile);

if(count($arrdir)>0 || count($arrfile)>0)
{
	if(@$_GET['thumbnail'])
	{
		?>
        <div class="file-list">
        <ul>
        <?php
		$i = 0;
		foreach($arrdir as $k=>$val)
		{
			$i++;
		?>
        <li class="row-data-dir" data-file-name="<?php echo $val['name'];?>" data-file-location="<?php echo $val['location'];?>" data-file-type="dir">
        <div class="thumbitem thumbdir">
        <div class="thumbimage" <?php if(@$cfg->thumbnail && @$val['filesize'] <= $cfg->thumbnail_max_size){ ?>style="background-image:url('tool-thumb-image.php?filepath=<?php echo rawurlencode($val['path']);?>&mtime=<?php echo $val['mtime'];?>')" <?php } ?>><div class="thumbimage-inner"><a href="javascript:;" title="<?php echo $val['name'];?>" onClick="return openDir('<?php echo str_replace("'", "\'", $val['path']);?>')"><img id="imageid-<?php echo $i;?>" src="style/images/trans96.gif" /></a></div></div>
        <div class="thumbcheck"><input type="checkbox" class="input-checkbox fileid" data-isdir="true" name="fileid[]" id="fileid-<?php echo $i;?>" value="<?php echo $val['path'];?>" /></div>
        <div class="thumbname"><a href="javascript:;" title="<?php echo $val['name'];?>" onClick="return openDir('<?php echo $val['path'];?>')"><?php echo $val['name'];?></a></div>
        </div>
        </li>
        <?php
		}
		foreach($arrfile as $k=>$val)
		{
			$i++;
		?>
        <li class="row-data-file row-<?php echo ($i%2)?'odd':'even';?>" data-file-url="<?php echo $val['url'];?>" data-file-name="<?php echo $val['name'];?>" data-file-location="<?php echo $val['location'];?>" data-file-type="<?php echo $val['type'];?>" data-file-size="<?php echo $val['size'];?>" data-image-width="<?php echo $val['image_width'];?>" data-image-height="<?php echo $val['image_height'];?>">
        <div class="thumbitem thumbfile thumbfile-<?php echo $val['extension'];?>">
        <div class="thumbimage" <?php if($val['image_width'] > 0 && $val['image_height'] > 0 && stripos($val['type'], 'image')===0 && $cfg->thumbnail){ ?> style="background-image:url('tool-thumb-image.php?filepath=<?php echo rawurlencode($val['path']);?>&mtime=<?php echo $val['mtime'];?>')"<?php } ?>><div class="thumbimage-inner"><a href="javascript:;" title="<?php echo $val['name'];?>" onClick="return selectFile('<?php echo $val['url'];?>')"><img id="imageid-<?php echo $i;?>" src="style/images/trans96.gif" /></a></div></div>
        <div class="thumbcheck"><input type="checkbox" class="input-checkbox fileid" data-isdir="false" data-iszip="<?php echo ($val['type']=='application/zip')?'true':'false';?>" name="fileid[]" id="fileid-<?php echo $i;?>" value="<?php echo $val['path'];?>" /></div>
        <div class="thumbname"><a href="javascript:;" title="<?php echo $val['name'];?>" onClick="return selectFile('<?php echo $val['url'];?>')"><?php echo $val['name'];?></a></div>
        </div>
        </li>
        <?php
		}
		?>
        </ul>
        </div>
        <?php
	}
	else
	{
		$sort_order = array();
		$sort_order['type'] = 'asc';
		$sort_order['name'] = 'asc';
		$sort_order['filesize'] = 'asc';
		$sort_order['permission'] = 'asc';
		$sort_order['filemtime'] = 'asc';
		
		if(isset($_GET['sortby']) && isset($_GET['sortorder']))
		{
			if($sortorder == 'asc')
			{
				$sort_order[$sortby] = 'desc';
			}
			else if($sortorder == 'desc')
			{
				$sort_order[$sortby] = 'asc';
			}
		}
		else if(isset($_GET['sortby']) && !isset($_GET['sortorder']))
		{
			$sort_order[$sortby] = 'asc';
		}
												 
		
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="file-table">
<thead>
<tr>
  <td width="8"><input type="checkbox" name="control-fileid" id="control-fileid" class="input-checkbox checkbox-selector" value="1"></td>
  <td class="sort-holder" data-sortby="type" data-sortorder="<?php echo $sort_order['type'];?>" width="16" title="Sort by MIME Type<?php echo ($sort_order['type']=='desc')?' Descending':' Ascending';?>">Icon</td>
  <td class="sort-holder" data-sortby="name" data-sortorder="<?php echo $sort_order['name'];?>" title="Sort by File Name<?php echo ($sort_order['name']=='desc')?' Descending':' Ascending';?>">File Name</td>
  <td class="sort-holder" data-sortby="filesize" data-sortorder="<?php echo $sort_order['filesize'];?>" width="60" align="right" title="Sort by File Size<?php echo ($sort_order['filesize']=='desc')?' Descending':' Ascending';?>">Size</td>
  <td class="sort-holder" data-sortby="type" data-sortorder="<?php echo $sort_order['type'];?>" width="70" title="Sort by MIME Type<?php echo ($sort_order['type']=='desc')?' Descending':' Ascending';?>">MIME Type</td>
  <td class="sort-holder" data-sortby="permission" data-sortorder="<?php echo $sort_order['permission'];?>" width="40" title="Sort by Permission<?php echo ($sort_order['permission']=='desc')?' Descending':' Ascending';?>">Perms</td>
  <td class="sort-holder" data-sortby="filemtime" data-sortorder="<?php echo $sort_order['filemtime'];?>" width="60" title="Sort by Time<?php echo ($sort_order['filemtime']=='desc')?' Descending':' Ascending';?>">Modified</td>
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
  <td><input type="checkbox" class="input-checkbox fileid" data-isdir="true" name="fileid[]" id="fileid-<?php echo $i;?>" value="<?php echo $val['path'];?>" /></td>
  <td><img src="style/images/trans16.gif" class="fileicon fileicon-dir" /></td>
  <td><a href="javascript:;" onClick="return openDir('<?php echo str_replace("'", "\'", $val['path']);?>')"><?php echo $val['name'];?></a></td>
  <td align="right"></td>
  <td>dir</td>
  <td><span class="permission-info"><?php echo $val['permission'];?></span></td>
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
  <td><input type="checkbox" class="input-checkbox fileid" data-isdir="false" data-iszip="<?php echo ($val['type']=='application/zip')?'true':'false';?>" name="fileid[]" id="fileid-<?php echo $i;?>" value="<?php echo $val['path'];?>" /></td>
  <td><img src="style/images/trans16.gif" class="fileicon fileicon-<?php echo $val['extension'];?>" /></td>
  <td><a href="javascript:;" onClick="return selectFile('<?php echo $val['url'];?>')"><?php echo $val['name'];?></a></td>
  <td align="right"><?php echo $val['size'];?></td>
  <td><?php if(strlen($val['type'])>18){$val['type'] = '<span title="'.$val['type'].'">'.substr($val['type'],0,18).'&hellip;</span>';} echo ($val['type'])?$val['type']:$val['extension'];?></td>
  <td><span class="permission-info"><?php echo $val['permission'];?></span></td>
  <td><?php echo $val['filemtime'];?></td>
</tr>
<?php
}
?>
</tbody>
</table>
<?php
}
}
else
{
?>
<div class="message-info">No file or directory found.</div>
<?php
}
?>