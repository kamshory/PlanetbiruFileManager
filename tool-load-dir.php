<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	exit();
}
$rooturl = $cfg->rootdir;
$seldir = kh_filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING);
$dir2 = path_decode(kh_filter_input(INPUT_GET, 'seldir'), $cfg->rootdir);
if(!is_dir($dir2)){
$dir2 = path_decode('', $cfg->rootdir);	
}
$arrdir = array();
if(file_exists($dir2))
{
	if ($handle = opendir($dir2))
	{
		$i=0;
		while (false !== ($ufile = readdir($handle))) 
		{ 
			$fn = "$dir2/$ufile";
			if($ufile == "." || $ufile == ".." ) 
			{
				continue;
			}
			try
			{
				$filetype = filetype($fn);
				unset($obj);
				if($filetype == "dir")
				{
					$obj['path'] = path_encode($fn, $cfg->rootdir);
					$obj['location'] = path_encode(dirname($fn), $cfg->rootdir);
					$obj['name'] = basename($fn);
					$arrdir[] = $obj;
				}
			}
			catch(Exception $e)
			{
				try
				{
					unset($obj);
					if(is_dir($fn))
					{
						$obj['path'] = path_encode($fn, $cfg->rootdir);
						$obj['location'] = path_encode(dirname($fn), $cfg->rootdir);
						$obj['name'] = basename($fn);
						$arrdir[] = $obj;
					}
					
				}
				catch(Exception $e)
				{
				}
			}
		}
	
	}
}

$_order = array();
foreach ($arrdir as &$row){
$_order[] = &$row['name'];
}
array_multisort($_order, SORT_ASC, SORT_STRING, $arrdir);
if(count($arrdir))
{
?>
<ul>
<?php
foreach($arrdir as $k=>$val)
{
?>
<li class="row-data-dir dir-control" data-file-name="<?php echo $val['name'];?>" data-file-location="<?php echo $val['location'];?>" data-file-path="<?php echo str_replace("'", "\'", $val['path']);?>"><a href="javascript:;" onClick="return openDir('<?php echo str_replace("'", "\'", $val['path']);?>')"><?php echo $val['name'];?></a>
<?php
if($val['location']){
if(stripos($seldir, $val['path']) !== false)
{
// recursive
// list dir tree
echo builddirtree($seldir);
}
}
?>
</li>
<?php
}
?>
</ul>
<?php
}
?>