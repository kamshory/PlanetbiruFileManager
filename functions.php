<?php
if(!defined('FILTER_SANITIZE_NO_DOUBLE_SPACE'))
{
	define('FILTER_SANITIZE_NO_DOUBLE_SPACE', 512);
}
if(!defined('FILTER_SANITIZE_PASSWORD'))
{
	define('FILTER_SANITIZE_PASSWORD', 511);
}
if(!defined('FILTER_SANITIZE_ALPHA'))
{
	define('FILTER_SANITIZE_ALPHA', 510);
}
if(!defined('FILTER_SANITIZE_ALPHANUMERIC'))
{
	define('FILTER_SANITIZE_ALPHANUMERIC', 509);
}
if(!defined('FILTER_SANITIZE_ALPHANUMERICPUNC'))
{
	define('FILTER_SANITIZE_ALPHANUMERICPUNC', 506);
}
if(!defined('FILTER_SANITIZE_NUMBER_UINT'))
{
	define('FILTER_SANITIZE_NUMBER_UINT', 508);
}
if(!defined('FILTER_SANITIZE_STRING_INLINE'))
{
	define('FILTER_SANITIZE_STRING_INLINE', 507);
}
if(!defined('FILTER_SANITIZE_STRING_BASE64'))
{
	define('FILTER_SANITIZE_STRING_BASE64', 505);
}
if(!defined('FILTER_SANITIZE_IP'))
{
	define('FILTER_SANITIZE_IP', 504);
}
if(!defined('FILTER_SANITIZE_NUMBER_OCTAL'))
{
	define('FILTER_SANITIZE_NUMBER_OCTAL', 503);
}
if(!defined('FILTER_SANITIZE_NUMBER_HEXADECIMAL'))
{
	define('FILTER_SANITIZE_NUMBER_HEXADECIMAL', 502);
}
if(!defined('FILTER_SANITIZE_COLOR'))
{
	define('FILTER_SANITIZE_COLOR', 501);
}
if(!defined('FILTER_SANITIZE_POINT'))
{
	define('FILTER_SANITIZE_POINT', 500);
}


function kh_filter_input($type, $variable_name, $filter=FILTER_DEFAULT, $options=null)
{
	switch($type)
	{
		case INPUT_GET:
		$var = $_GET;
		break;
		case INPUT_POST:
		$var = $_POST;
		break;
		case INPUT_COOKIE:
		$var = $_COOKIE;
		break;
		case INPUT_SERVER:
		$var = $_SERVER;
		break;
		case INPUT_ENV:
		$var = $_ENV;
	}
	$val = (isset($var[$variable_name]))?$var[$variable_name]:"";
	if(!is_scalar($val))
	{
		unset($val);
		$val = "";
		// ignore
	}

	if(@get_magic_quotes_gpc() || @get_magic_quotes_runtime())
	{
		$val = my_stripslashes($val);
	}
	
	// add filter
	if($filter == FILTER_SANITIZE_EMAIL)
	{
		$val = trim(strtolower($val));
		$val = filter_var($val, FILTER_VALIDATE_EMAIL);
		if($val === false)
		$val = "";
	}
	if($filter == FILTER_SANITIZE_URL)
	{
		// filter url
		$val = trim($val);
		if(stripos($val, "://") === false && strlen($val)>2)
		$val = "http://".$val;
		$val = filter_var($val, FILTER_VALIDATE_URL);
		if($val === false)
		$val = "";
	}
	if($filter == FILTER_SANITIZE_ALPHA)
	{
		$val = preg_replace("/[^A-Za-z]/i","",$val);
	}
	if($filter == FILTER_SANITIZE_ALPHANUMERIC)
	{
		$val = preg_replace("/[^A-Za-z\d]/i","",$val);
	}
	if($filter == FILTER_SANITIZE_ALPHANUMERICPUNC)
	{
		$val = preg_replace("/[^A-Za-z\.\-\d_]/i","",$val);
	}
	if($filter == FILTER_SANITIZE_NUMBER_FLOAT)
	{
		$val = preg_replace("/[^Ee\+\-\.\d]/i","",$val);
	}
	if($filter == FILTER_SANITIZE_NUMBER_INT)
	{
		$val = preg_replace("/[^\+\-\d]/i","",$val);
		$val = (int) $val;
	}
	if($filter == FILTER_SANITIZE_NUMBER_UINT)
	{
		$val = preg_replace("/[^\+\-\d]/i","",$val);
		$val = (int) $val;
		$val = abs($val);
	}
	if($filter == FILTER_SANITIZE_NUMBER_OCTAL)
	{
		$val = preg_replace("/[^0-7]/i","",$val);
	}
	if($filter == FILTER_SANITIZE_NUMBER_HEXADECIMAL)
	{
		$val = preg_replace("/[^A-Fa-f\d]/i","",$val);
	}
	if($filter == FILTER_SANITIZE_COLOR)
	{
		$val = preg_replace("/[^A-Fa-f\d]/i","",$val);
		if(strlen($val) < 3)
		{
			$val = "";			
		}
		else if(strlen($val) > 3 && strlen($val) != 3 && strlen($val) < 6)
		{
			$val = substr($val, 0, 3);
		}
		else if(strlen($val) > 6)
		{
			$val = substr($val, 0, 6);			
		}
		if(strlen($val) >= 3)
		{
			$val = strtoupper("#".$val);
		}
	}
	if($filter == FILTER_SANITIZE_NO_DOUBLE_SPACE)
	{
		$val = trim(preg_replace("/\s+/"," ",$val));
	}
	if($filter == FILTER_SANITIZE_PASSWORD)
	{
		$val = trim(preg_replace("/\s+/"," ",$val));
		$val = str_ireplace(array('"',"'","`","\\","\0","\r","\n","\t"),"",$val);
	}
	if($filter == FILTER_SANITIZE_SPECIAL_CHARS)
	{
		$val = htmlspecialchars($val);
	}
	if($filter == FILTER_SANITIZE_ENCODED)
	{
		$val = rawurlencode($val);
	}
	if($filter == FILTER_SANITIZE_STRIPPED || $filter == FILTER_SANITIZE_STRING)
	{
		$val = trim(strip_tags($val),"\r\n\t ");
		$val = str_replace(array('<','>','"'), array('&lt;','&gt;','&quot;'), $val);
	}
	if($filter == FILTER_SANITIZE_STRING_INLINE)
	{
		$val = trim(strip_tags($val),"\r\n\t ");
		$val = str_replace(array('<','>','"'), array('&lt;','&gt;','&quot;'), $val);
	}
	if($filter == FILTER_SANITIZE_STRING_BASE64)
	{
		$val = preg_replace("/[^A-Za-z0-9\+\/\=]/","",$val);
	}
	if($filter == FILTER_SANITIZE_POINT)
	{
		$val = preg_replace("/[^0-9\-\+\/\.,]/","",$val);
	}
	if($filter == FILTER_SANITIZE_IP)
	{
		$val = filter_var($val, FILTER_VALIDATE_IP);
		if($val === false)
		$val = "";
	}
	if(	$filter == FILTER_SANITIZE_EMAIL || 
		$filter == FILTER_SANITIZE_ENCODED || 
		$filter == FILTER_SANITIZE_IP || 
		$filter == FILTER_SANITIZE_MAGIC_QUOTES || 
		$filter == FILTER_SANITIZE_NO_DOUBLE_SPACE || 
		$filter == FILTER_SANITIZE_SPECIAL_CHARS || 
		$filter == FILTER_SANITIZE_STRING || 
		$filter == FILTER_SANITIZE_STRING_INLINE || 
		$filter == FILTER_SANITIZE_STRIPPED ||
		$filter == FILTER_SANITIZE_URL
		)
	{
		$val = my_addslashes($val);
	}
	return $val;
}

function kh_filter_input_search_get($var='q')
{
	$val = (isset($_GET[$var]))?$_GET[$var]:"";
	if($val != "")
	{
		if(is_array($val))
		{
			unset($val);
			$val = "";
			// ignore
		}
	}
	$val = trim(strip_tags($val),"\r\n\t ");
	$val = str_replace(array('<','>','"'), array('&lt;','&gt;','&quot;'), $val);
	if(@get_magic_quotes_gpc() || @get_magic_quotes_runtime())
	{
		$val = my_stripslashes($val);
	}
	return $val;
}

function kh_filter_file_name_safe($input)
{
	$output = preg_replace( 
				array("/\s+/", "/[^-\.\w\s+]+/"), 
				array("_", "-"),
				$input);
	$output = str_replace(
				array("-_", "_-", "__", "___", "--", "---"),
				array("_", "_", "_", "_", "-", "-"),
				$output);
	return $output;
}

function kh_filter_file_name($input)
{
	$output = preg_replace( 
				array("/\s+/", "/[^-\.\w\s+]+/"), 
				array(" ", "-"),
				$input);
	$output = str_replace(
				array("-_", "_-", "__", "___", "--", "---"),
				array("_", "_", "_", "_", "-", "-"),
				$output);
	return $output;
}
function my_addslashes($inp)
{ 
	return addslashes($inp);
} 
function my_stripslashes($inp){
	$inp = str_replace(array("\\'"), array("'"), $inp);
	$inp = str_replace(array("\\\""), array("\""), $inp);
	$inp = str_replace(array("\\\\","\\0", "\\n", "\\r", "\\Z"), array("\\","\0", "\n", "\r", "\x1a"), $inp);
	return $inp;
}
function array_addslashes(&$item, $key)
{
	$item = my_addslashes($item);
}
function array_stripslashes(&$item, $key)
{
	$item = my_stripslashes($item);
}


class listFile{
	var $location;
	var $result_file = array();
	var $result_dir = array();
	
	function listFile($location = null)
	{
		if($location !== null)
		{
			$this->location = $location;
			$this->findAll($location);
		}
	}
	function findAll($location)
	{
		global $cfg;
		if(file_exists($location))
		{
			if($handle = opendir($location))
			{
				$i=0;
				while (false !== ($ufile = readdir($handle))) 
				{ 
					$fn = "$location/$ufile";
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
						$this->result_file[] = $obj;
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
						$this->result_dir[] = $obj;
						
						
						// recursive
						$lv = new listFile($fn);
						foreach($lv->result_file as $key=>$val)
						{
							$this->result_file[] = $val;
						}
						foreach($lv->result_dir as $key=>$val)
						{
							$this->result_dir[] = $val;
						}
					}
				}
			}
		}
	}
}

function cleanforbiddenall($dir){
@chmod(dirname($dir), 0777);
@chmod($dir, 0777);
cleanforbidden($dir);
@chmod(dirname($dir), 0755);
}
function cleanforbidden($dir){
global $cfg;
$dir = rtrim($dir,"/");
$mydir = opendir($dir);
while(false !== ($file = readdir($mydir))){
	if($file != "." && $file != ".."){
	@chmod($dir."/".$file, 0777);
	if(@is_dir($dir."/".$file)){
		chdir('.');
		cleanforbidden($dir."/".$file);
	}
	else{
		$fn = $dir."/".$file;
		$tt = getMIMEType($fn);
		if(in_array($tt->extension, $cfg->forbidden_extension)){
			@unlink($fn);
		}
	}
}
}
closedir($mydir);
}

function destroyall($dir)
{
@chmod(dirname($dir), 0777);
@chmod($dir, 0777);
destroy($dir);
@rmdir($dir);
@chmod(dirname($dir), 0755);
}
function destroy($dir){
	$dir = rtrim($dir,"/");
	$mydir = opendir($dir);
	while(false !== ($file = readdir($mydir))){
		if($file != "." && $file != ".."){
		@chmod($dir."/".$file, 0777);
		if(is_dir($dir."/".$file)) 
		{
			chdir('.');
			destroy($dir."/".$file);
			rmdir($dir."/".$file) or DIE("couldn't delete $dir$/file<br />");
		}
		else
		@unlink($dir."/".$file) or DIE("couldn't delete $dir$/file<br />");
		}
	}
	closedir($mydir);
}

// copy all files and folders in directory to specified directory
function cp($wf, $wto)
{ 
	if (!file_exists($wto)){
		@mkdir($wto,0755);
	}
	$arr=ls_a($wf);
	foreach ($arr as $fn)
	{
	if($fn)
	{
	$fl="$wf/$fn";
	$flto="$wto/$fn";
	if(is_dir($fl)) 
		cp($fl,$flto);
	else 
		@copy($fl,$flto);
	}
	}
}

function ls_a($wh)
{
	$files = "";
	if($handle=opendir($wh)){
	while(false!==($file = readdir($handle))) 
	{
	if($file!="." && $file!= "..")
	{
	if(empty($files)) 
		$files="$file";
	else 
		$files="$file\n$files";
	}
	}
	closedir($handle);
	}
	$arr=explode("\n",$files);
	return $arr;
}
function chmoddir($dir, $perms)
{
	chmod($dir, $perms);
	$arr = ls_a($dir);
	foreach ($arr as $fn)
	{
		if($fn)
		{
			$fn = $dir."/".$fn;
			$ft = filetype($fn);
			if($ft == "file")
			{
				chmod($fn, $perms);
			}
			else
			{
				chmoddir($fn, $perms);
			}
		}
	}
}

function getMIMEType($filename){
$obj = new StdClass();
$arr = array(
'323'=>'text/h323',
'3gp'=>'video/3gp',
'ogg'=>'video/ogg',
'mp4'=>'video/mp4',
'ram'=>'audio/ram',
'wma'=>'audio/wma',
'*'=>'application/octet-stream',
'acx'=>'application/internet-property-stream',
'ai'=>'application/postscript',
'aif'=>'audio/x-aiff',
'aifc'=>'audio/x-aiff',
'aiff'=>'audio/x-aiff',
'asf'=>'video/x-ms-asf',
'asr'=>'video/x-ms-asf',
'asx'=>'video/x-ms-asf',
'au'=>'audio/basic',
'avi'=>'video/x-msvideo',
'axs'=>'application/olescript',
'bas'=>'text/plain',
'bcpio'=>'application/x-bcpio',
'bin'=>'application/octet-stream',
'bmp'=>'image/bmp',
'c'=>'text/plain',
'cat'=>'application/vnd.ms-pkiseccat',
'cdf'=>'application/x-cdf',
'cdf'=>'application/x-netcdf',
'cer'=>'application/x-x509-ca-cert',
'class'=>'application/octet-stream',
'clp'=>'application/x-msclip',
'cmx'=>'image/x-cmx',
'cod'=>'image/cis-cod',
'conf'=>'text/conf',
'ini'=>'text/ini',
'cpio'=>'application/x-cpio',
'cpp'=>'text/cpp',
'crd'=>'application/x-mscardfile',
'crl'=>'application/pkix-crl',
'crt'=>'application/x-x509-ca-cert',
'csh'=>'application/x-csh',
'css'=>'text/css',
'dcr'=>'application/x-director',
'der'=>'application/x-x509-ca-cert',
'dir'=>'application/x-director',
'dll'=>'application/x-msdownload',
'dms'=>'application/octet-stream',
'doc'=>'application/msword',
'docx'=>'application/msword',
'dot'=>'application/msword',
'dvi'=>'application/x-dvi',
'dxr'=>'application/x-director',
'eps'=>'application/postscript',
'etx'=>'text/x-setext',
'evy'=>'application/envoy',
'exe'=>'application/octet-stream',
'fif'=>'application/fractals',
'flr'=>'x-world/x-vrml',
'flv'=>'video/flv',
'gif'=>'image/gif',
'gtar'=>'application/x-gtar',
'gz'=>'application/x-gzip',
'h'=>'text/plain',
'hdf'=>'application/x-hdf',
'hlp'=>'application/winhlp',
'hqx'=>'application/mac-binhex40',
'hta'=>'text/hta',
'htc'=>'text/x-component',
'htm'=>'text/html',
'htaccess'=>'text/htaccess',
'html'=>'text/html',
'htt'=>'text/webviewhtml',
'ico'=>'image/x-icon',
'ief'=>'image/ief',
'iii'=>'application/x-iphone',
'ins'=>'application/x-internet-signup',
'isp'=>'application/x-internet-signup',
'jfif'=>'image/pipeg',
'jpe'=>'image/jpeg',
'jpeg'=>'image/jpeg',
'jpg'=>'image/jpeg',
'js'=>'text/x-javascript',
'latex'=>'application/x-latex',
'lha'=>'application/octet-stream',
'lsf'=>'video/x-la-asf',
'lsx'=>'video/x-la-asf',
'lzh'=>'application/octet-stream',
'm13'=>'application/x-msmediaview',
'm14'=>'application/x-msmediaview',
'm3u'=>'audio/x-mpegurl',
'man'=>'application/x-troff-man',
'md'=>'text/markdown',
'mdb'=>'application/x-msaccess',
'me'=>'application/x-troff-me',
'mht'=>'message/rfc822',
'mhtml'=>'message/rfc822',
'mid'=>'audio/mid',
'mny'=>'application/x-msmoney',
'mov'=>'video/quicktime',
'movie'=>'video/x-sgi-movie',
'mp2'=>'video/mpeg',
'mp3'=>'audio/mpeg',
'mpa'=>'video/mpeg',
'mpe'=>'video/mpeg',
'mpeg'=>'video/mpeg',
'mpg'=>'video/mpeg',
'wmv'=>'video/wmv',
'mpp'=>'application/vnd.ms-project',
'mpv2'=>'video/mpeg',
'mkv'=>'video/mkv',
'ms'=>'application/x-troff-ms',
'msg'=>'application/vnd.ms-outlook',
'mvb'=>'application/x-msmediaview',
'nc'=>'application/x-netcdf',
'nws'=>'message/rfc822',
'oda'=>'application/oda',
'p10'=>'application/pkcs10',
'p12'=>'application/x-pkcs12',
'p7b'=>'application/x-pkcs7-certificates',
'p7c'=>'application/x-pkcs7-mime',
'p7m'=>'application/x-pkcs7-mime',
'p7r'=>'application/x-pkcs7-certreqresp',
'p7s'=>'application/x-pkcs7-signature',
'pbm'=>'image/x-portable-bitmap',
'pdf'=>'application/pdf',
'pfx'=>'application/x-pkcs12',
'pgm'=>'image/x-portable-graymap',
'php'=>'application/x-httpd-php',
'pko'=>'application/ynd.ms-pkipko',
'pma'=>'application/x-perfmon',
'pmc'=>'application/x-perfmon',
'pml'=>'application/x-perfmon',
'pmr'=>'application/x-perfmon',
'pmw'=>'application/x-perfmon',
'png'=>'image/png',
'pnm'=>'image/x-portable-anymap',
'pot'=>'application/vnd.ms-powerpoint',
'ppm'=>'image/x-portable-pixmap',
'pps'=>'application/vnd.ms-powerpoint',
'ppt'=>'application/vnd.ms-powerpoint',
'pptx'=>'application/vnd.ms-powerpoint',
'prf'=>'application/pics-rules',
'ps'=>'application/postscript',
'pub'=>'application/x-mspublisher',
'qt'=>'video/quicktime',
'ra'=>'audio/x-pn-realaudio',
'ram'=>'audio/x-pn-realaudio',
'ras'=>'image/x-cmu-raster',
'rgb'=>'image/x-rgb',
'rmi'=>'audio/mid',
'roff'=>'application/x-troff',
'rtf'=>'application/rtf',
'rtx'=>'text/richtext',
'scd'=>'application/x-msschedule',
'sct'=>'text/scriptlet',
'setpay'=>'application/set-payment-initiation',
'setreg'=>'application/set-registration-initiation',
'sh'=>'application/x-sh',
'shar'=>'application/x-shar',
'sit'=>'application/x-stuffit',
'snd'=>'audio/basic',
'spc'=>'application/x-pkcs7-certificates',
'spl'=>'application/futuresplash',
'sql'=>'text/sql',
'src'=>'application/x-wais-source',
'sst'=>'application/vnd.ms-pkicertstore',
'stl'=>'application/vnd.ms-pkistl',
'stm'=>'text/html',
'sv4cpio'=>'application/x-sv4cpio',
'sv4crc'=>'application/x-sv4crc',
'svg'=>'text/svg+xml',
'swf'=>'application/x-shockwave-flash',
't'=>'application/x-troff',
'tar'=>'application/x-tar',
'tcl'=>'application/x-tcl',
'tex'=>'application/x-tex',
'texi'=>'application/x-texinfo',
'texinfo'=>'application/x-texinfo',
'tgz'=>'application/x-compressed',
'tif'=>'image/tiff',
'tiff'=>'image/tiff',
'tr'=>'application/x-troff',
'trm'=>'application/x-msterminal',
'tsv'=>'text/tab-separated-values',
'txt'=>'text/plain',
'uls'=>'text/iuls',
'ustar'=>'application/x-ustar',
'vcf'=>'text/x-vcard',
'vrml'=>'x-world/x-vrml',
'wav'=>'audio/x-wav',
'wcm'=>'application/vnd.ms-works',
'wdb'=>'application/vnd.ms-works',
'wks'=>'application/vnd.ms-works',
'wmf'=>'application/x-msmetafile',
'wps'=>'application/vnd.ms-works',
'wri'=>'application/x-mswrite',
'wrl'=>'x-world/x-vrml',
'wrz'=>'x-world/x-vrml',
'xaf'=>'x-world/x-vrml',
'xbm'=>'image/x-xbitmap',
'xla'=>'application/vnd.ms-excel',
'xlc'=>'application/vnd.ms-excel',
'xlm'=>'application/vnd.ms-excel',
'xls'=>'application/vnd.ms-excel',
'xlsx'=>'application/vnd.ms-excel',
'xlt'=>'application/vnd.ms-excel',
'xlw'=>'application/vnd.ms-excel',
'xml'=>'text/xml',
'xof'=>'x-world/x-vrml',
'xpm'=>'image/x-xpixmap',
'xwd'=>'image/x-xwindowdump',
'z'=>'application/x-compress',
'zip'=>'application/zip');

$ext = '';
$mime = '';

$filename2 = strrev(strtolower($filename));

foreach($arr as $key=>$val)
{
	$ext2 = strrev($key).'.';
	$pos = stripos($filename2, $ext2);
	if($pos === 0)
	{
		$ext = $key;
		$mime = $val;
		break;
	}
}
if(!$ext)
{
	if(stripos($filename, ".")!==false)
	{
		$arr2 = explode(".", $filename);
		$ext = $arr2[count($arr2)-1];
	}
	else
	{
		$ext = "";
		$mime = "";
	}
}
$obj->extension = $ext;
$obj->mime = $mime;
return $obj;
}

function path_encode($dir, $root=NULL){
if($root===NULL){
global $cfg;
$rootdir = $cfg->rootdir;
}
else{
$rootdir = $root;
}
$dir = rtrim(str_replace(array("/..","../","./","..\\",".\\","\\","//"),"/",$dir),"/\\");
$rootdir = trim(str_replace(array("/..","../","./","..\\",".\\","\\","//"),"/",$rootdir),"/\\");
$dir2 = trim(str_replace($rootdir, 'base', $dir),"/");
$dir2 = str_replace("//","/",$dir2);
return $dir2;
}
function path_decode($dir, $root=NULL){
if(is_array($dir))
$dir = "";
if($root===NULL){
global $cfg;
$rootdir = $cfg->rootdir;
}
else{
$rootdir = $root;
}
$dir2 = $dir;
if(substr($dir2,0,4)=="base")
{
$dir2 = substr($dir2,4);
}
$dir2 = rtrim($dir2,"/\\");
$rootdir = rtrim($rootdir,"/\\");
$dir2 = str_replace(array("\\..","/.."),"/",$dir2);
$dir2 = str_replace("\\", "/", $dir2);
$dir2 = str_replace("//", "/", $dir2);
$dir2 = str_replace("//", "/", $dir2);
$dir2 = str_replace("../", "/", $dir2);
$dir2 = str_replace("//", "/", $dir2);
$dir2 = rtrim($rootdir,"/\\")."/".ltrim($dir2, "/\\");
$dir2 = rtrim($dir2,"/\\");
return $dir2;
}

function path_decode_to_url($dir,$rooturl=""){
if(is_array($dir))
$dir = "";
$dir2 = $dir;
if(substr($dir2,0,4)=="base")
{
$dir2 = substr($dir2,4);
}
$dir2 = rtrim($dir2,"/\\");
$dir2 = $rooturl."/".$dir2;
$dir2 = rtrim($dir2,"/\\");
return $dir2;
}

function path_encode_trash($dir, $trash=NULL){
if($trash===NULL){
global $cfg;
$trashdir = $cfg->trashdir;
}
else{
$trashdir = $trash;
}
$dir = rtrim(str_replace(array("/..","../","./","..\\",".\\","\\","//"),"/",$dir),"/\\");
$trashdir = rtrim(str_replace(array("/..","../","./","..\\",".\\","\\","//"),"/",$trashdir),"/\\");
$dir2 = trim(str_replace($trashdir, 'base', $dir),"/");
return $dir2;
}
function path_decode_trash($dir, $trash=NULL){
if(is_array($dir))
$dir = "";
if($trash===NULL){
global $cfg;
$trashdir = $cfg->trashdir;
}
else{
$trashdir = $trash;
}
$dir2 = $dir;
if(substr($dir2,0,4)=="base")
{
$dir2 = substr($dir2,4);
}
$dir2 = rtrim($dir2,"/\\");
$trashdir = rtrim($trashdir,"/\\");
$dir2 = str_replace(array("/..","../","./","..\\",".\\","\\","//"),"/",$dir2);
$dir2 = $trashdir."/".$dir2;
$dir2 = rtrim($dir2,"/\\");
return $dir2;
}

$file_list = '';
function dir_list($dir){
global $file_list;
$dh=opendir($dir);
if($dh){
while($subitem=readdir($dh)){
if(preg_match('/^\.\.?$/',$subitem)) 
continue;
if(is_file($dir."/".$subitem))
$file_list .= "$dir/$subitem\r\n";
if( is_dir("$dir/$subitem") )
dir_list("$dir/$subitem");
}
closedir($dh);
}
}

function deleteforbidden($dir, $containsubdir=false){
global $cfg;
if($cfg->delete_forbidden_extension && file_exists($dir) && is_array($cfg->forbidden_extension))
{
	if($containsubdir){
		cleanforbiddenall($dir);
	}
	else
	{
	$dh=opendir($dir);
	if($dh){
	while($subitem=readdir($dh)){
	$fn = "$dir/$subitem";
	if($subitem == "." || $subitem == ".." ){
	continue;
	}
	$filetype = filetype($fn);
	if($filetype=="file"){
	$tt = getMIMEType($fn);
	if(in_array($tt->extension, $cfg->forbidden_extension)){
		@unlink($fn);
	}
	}
	}
	closedir($dh);
	}
}
}
}
function dmstoreal($deg, $min, $sec){
return $deg + ((($min*60)+($sec))/3600);
}

function real2dms($val){
$tm = $val * 3600;
$tm = round($tm);
$h = sprintf("%02d",date("H",$tm)-7);
if($h<0) $h+=24;
$m = date("i",$tm);
$s = date("s",$tm);
return array($h,$m,$s);
}

function builddirtree($dir){
$dir = str_replace("\\", "/", $dir);
$arr = explode("/", $dir);
$ret = "%s";
$dt = array();
$dt['path'] = "";
$dt['name'] = "";
$dt['location'] = "";
foreach($arr as $k=>$val){
	$dt['path'] = $dt['path'].$val;
	$dt['name'] = basename($val);
	$dt['location'] = $dt['location'].($val);
	if($k>1){
	$html = "<ul>\r\n";
	$html .= "<li class=\"row-data-dir dir-control\" data-file-name=\"".$dt['name']."\" data-file-location=\"".$dt['location']."\"><a href=\"javascript:;\" onClick=\"return openDir('".$dt['path']."')\">".$dt['name']."</a>";
	$html .= "%s</li>\r\n";
	$html .= "</ul>";
	$ret2 = sprintf($ret, $html);
	$ret = $ret2;
	}
	$dt['path'] = $dt['path']."/";
	$dt['name'] = $dt['name']."/";
	$dt['location'] = $dt['location']."/";
}
$ret = str_replace("%s","",$ret);
return $ret;
}

function getfmprofile($name, $authblogid, $default=NULL)
{
	global $settings;
	if(isset($settings[$name]))
	{
		return $settings[$name];
	}
	else
	{
		return $default;
	}
}

function compressImageFile($path, $authblogid)
{
	if(getfmprofile('compressimageonupload',$authblogid,0))
	{
		global $cfg;
		$maxsize = $cfg->thumbnail_max_size;
		if(filesize($path) <= $maxsize)
		{
			// get mime type
			$info = @getimagesize($path);
			if(@stripos($info['mime'],'image')!==false)
			{
				if(@stripos($info['mime'],'jpeg')!== false || getfmprofile('imageformat',$authblogid,0) == 1)
				{
					// copress here
					$quality = getfmprofile('imagequality',$authblogid,80);
					$interlace = getfmprofile('imageinterlace',$authblogid,0);
					$maxwidth = getfmprofile('maximagewidth',$authblogid,600);
					$maxheight = getfmprofile('maximageheight',$authblogid,800);
					if(@stripos($info['mime'],'jpeg') !== false)
					{
						// jpeg
						$imagelocation = imageresizemax($path, $path, $maxwidth, $maxheight, $interlace, $quality);
						
					}
					else if(@stripos($info['mime'],'png') !== false)
					{
						// png
						$imagelocation = imageresizemax($path, $path, $maxwidth, $maxheight, $interlace);
					} 
					else if(@stripos($info['mime'],'gif') !== false)
					{
						// gif
						$imagelocation = imageresizemax($path, $path, $maxwidth, $maxheight, $interlace);
					}
										 
				}
			}
		}
	}
}


function imageresizemax($source, $destination, $maxwidth, $maxheight, $interlace=false, $quality=80)
{
	$image = new StdClass();
	$imageinfo = getimagesize($source);
    if (empty($imageinfo)) 
	{
        if (file_exists($originalfile)) 
		{
            unlink($source);
        }
        return false;
    }
    $image->width  = $imageinfo[0];
    $image->height = $imageinfo[1];
    $image->type   = $imageinfo[2];
    switch ($image->type) 
	{
        case IMAGETYPE_GIF:
            if (function_exists('ImageCreateFromGIF')) 
			{
                $im = @ImageCreateFromGIF($source);
            } 
			else 
			{
                //notice('GIF not supported on this server');
                unlink($source);
                return false;
            }
            break;
        case IMAGETYPE_JPEG:
            if (function_exists('ImageCreateFromJPEG')) 
			{
                $im = @ImageCreateFromJPEG($source);
            } 
			else 
			{
                //notice('JPEG not supported on this server');
                unlink($source);
                return false;
            }
            break;
        case IMAGETYPE_PNG:
            if (function_exists('ImageCreateFromPNG')) 
			{
                $im = @ImageCreateFromPNG($source);
            } 
			else 
			{
                //notice('PNG not supported on this server');
                unlink($originalfile);
                return false;
            }
            break;
        default:
            unlink($originalfile);
            return false;
    }
	if(!$im)
	{
		return false;
	}
	
	$currentwidth = $image->width;
	$currentheight = $image->height;
	// adapting image width
	if($currentwidth > $maxwidth)
	{
		$tmpwidth = round($maxwidth);
		$tmpheight = round($currentheight*($tmpwidth/$currentwidth));
		
		$currentwidth = $tmpwidth;
		$currentheight = $tmpheight;
	}
	// adapting image height
	if($currentheight > $maxheight)
	{
		$tmpheight = round($maxheight);
		$tmpwidth = round($currentwidth*($tmpheight/$currentheight));
		$currentwidth = $tmpwidth;
		$currentheight = $tmpheight;
	}
	$im2 = imagecreatetruecolor($currentwidth, $currentheight);
	$white = imagecolorallocate($im2, 255, 255, 255);
	imagefilledrectangle($im2,0,0,$currentwidth, $currentheight,$white);
	imagecopyresampled($im2,$im,0,0,0,0,$currentwidth,$currentheight,$image->width,$image->height);
	if (file_exists($source)) 
	{
		unlink($source);
	}
	if($interlace)
	imageinterlace($im2, true);
	imagejpeg($im2,$destination,$quality);
	return $destination;
	
}

function get_capture_info($exif)
{
	/* 
	Copyright 2013 Kamshory Developer
	*/
	$exifdata = array();
	$tmpdt = array();
	if(is_array($exif))
	{
		$tmpdt['Camera Maker'] = @$exif['IFD0']['Make'];
		$tmpdt['Camera Model'] = @$exif['IFD0']['Model'];
		$tmpdt['Capture Time'] = (@$exif['IFD0']['Datetime'])?(@$exif['IFD0']['Datetime']):(@$exif['EXIF']['DateTimeOriginal'])?(@$exif['EXIF']['DateTimeOriginal']):'';
		$tmpdt['Aperture F Number'] = @$exif['COMPUTED']['ApertureFNumber'];
		$tmpdt['Orientation'] = @$exif['IFD0']['Orientation'];
		$tmpdt['X Resolution'] = @$exif['IFD0']['XResolution'];
		$tmpdt['Y Resolution'] = @$exif['IFD0']['YResolution'];
		$tmpdt['YCbCr Positioning'] = @$exif['IFD0']['YCbCrPositioning'];
		$tmpdt['Exposure Time'] = @$exif['EXIF']['ExposureTime'];
		$tmpdt['F Number'] = @$exif['EXIF']['FNumber'];
		$tmpdt['ISO Speed Ratings'] = @$exif['EXIF']['ISOSpeedRatings'];
		$tmpdt['Shutter Speed Value'] = @$exif['EXIF']['ShutterSpeedValue'];
		$tmpdt['Aperture Value'] = @$exif['EXIF']['ApertureValue'];
		$tmpdt['Light Source'] = @$exif['EXIF']['LightSource'];
		$tmpdt['Flash'] = @$exif['EXIF']['Flash'];
		$tmpdt['Focal Length'] = @$exif['EXIF']['FocalLength'];
		$tmpdt['SubSec Time Original'] = @$exif['EXIF']['SubSecTimeOriginal'];
		$tmpdt['SubSec Time Digitized'] = @$exif['EXIF']['SubSecTimeDigitized'];
		$tmpdt['Flash Pix Version'] = @$exif['EXIF']['FlashPixVersion'];
		$tmpdt['Color Space'] = @$exif['EXIF']['ColorSpace'];
		$tmpdt['Custom Rendered'] = @$exif['EXIF']['CustomRendered'];
		$tmpdt['Exposure Mode'] = @$exif['EXIF']['ExposureMode'];
		$tmpdt['White Balance'] = @$exif['EXIF']['WhiteBalance'];
		$tmpdt['Digital Zoom Ratio'] = @$exif['EXIF']['DigitalZoomRatio'];
		$tmpdt['Scene Capture Type'] = @$exif['EXIF']['SceneCaptureType'];
		$tmpdt['Gain Control'] = @$exif['EXIF']['GainControl'];
		foreach($tmpdt as $key=>$val)
		{
			if(@$val != "")
			{
				$exifdata[$key] = $val;
			}
		}
		return $exifdata;
	}
	return null;
}
class HTPasswd 
{
    const APRMD5_ALPHABET = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const BASE64_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    public static function hash($mdp, $salt = null) 
	{
        if (is_null($salt))
        {
			$salt = self::salt();
		}
        $salt = substr($salt, 0, 8);
        $max = strlen($mdp);
        $context = $mdp.'$apr1$'.$salt;
        $binary = pack('H32', md5($mdp.$salt.$mdp));
        for($i=$max; $i>0; $i-=16)
        {
			$context .= substr($binary, 0, min(16, $i));
		}
        for($i=$max; $i>0; $i>>=1)
		{
            $context .= ($i & 1) ? chr(0) : $mdp[0];
		}
        $binary = pack('H32', md5($context));
        for($i=0; $i<1000; $i++) 
		{
            $new = ($i & 1) ? $mdp : $binary;
            if($i % 3) $new .= $salt;
            if($i % 7) $new .= $mdp;
            $new .= ($i & 1) ? $binary : $mdp;
            $binary = pack('H32', md5($new));
        }
        $hash = '';
        for ($i = 0; $i < 5; $i++) 
		{
            $k = $i+6;
            $j = $i+12;
            if($j == 16) $j = 5;
            $hash = $binary[$i].$binary[$k].$binary[$j].$hash;
        }
        $hash = chr(0).chr(0).$binary[11].$hash;
        $hash = strtr(
            strrev(substr(base64_encode($hash), 2)),
            self::BASE64_ALPHABET,
            self::APRMD5_ALPHABET
        );
        return '$apr1$'.$salt.'$'.$hash;
    }
    // 8 character salts are the best. Don't encourage anything but the best.
    public static function salt() 
	{
        $alphabet = self::APRMD5_ALPHABET;
        $salt = '';
        for($i=0; $i<8; $i++) {
            $offset = hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % 64;
            $salt .= $alphabet[$offset];
        }
        return $salt;
    }
    public static function check($plain, $hash) 
	{
        $parts = explode('$', $hash);
        return self::hash($plain, $parts[2]) === $hash;
    }
	public static function crypt_sha1($password)
	{
		return "{SHA}".base64_encode(hex2bin(sha1($password)));
	}

	public static function auth($username, $password, $stored)
	{
		$buff = $stored;
		$buff = str_replace("\n", "\r\n", $buff);
		$buff = str_replace("\r\n\n", "\r\n", $buff);
		$buff = str_replace("\r", "\r\n", $buff);
		$buff = str_replace("\r\r\n", "\r\n", $buff);
		$buffs = explode("\r\n", $buff);
		foreach($buffs as $line)
		{
			$line = trim($line, "\r\n");
			$arr = explode(":", $line, 2);
			if($arr[0] == $username)
			{
				if(stripos($arr[1], '{SHA}') === 0)
				{
					if(self::crypt_sha1($password) === $arr[1])
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else if(stripos($arr[1], '$apr1$') === 0)
				{
					if(self::check($password, $arr[1]))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
				break;
			}
		}
	}
}


?>
