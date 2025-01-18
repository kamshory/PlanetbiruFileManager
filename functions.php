<?php

class FileManager
{
	// Properti untuk menyimpan daftar file
	private $fileList = '';

	// Metode untuk mendapatkan daftar file
	public function getFileList($dir)
	{
		// Reset daftar file sebelum mengambil yang baru
		$this->fileList = '';

		// Memulai pencarian file di direktori
		$this->dirList($dir);

		// Mengembalikan hasil daftar file
		return $this->fileList;
	}

	// Metode rekursif untuk membaca isi direktori
	private function dirList($dir)
	{
		// Pastikan direktori ada dan dapat dibuka
		if ($dh = opendir($dir)) {
			// Menelusuri setiap item dalam direktori
			while (($subitem = readdir($dh)) !== false) {
				// Mengabaikan "." dan ".."
				if (preg_match('/^\.\.?$/', $subitem)) {
					continue;
				}

				// Jika item adalah file, tambahkan ke fileList
				if (is_file($dir . "/" . $subitem)) {
					$this->fileList .= "$dir/$subitem\r\n";
				}

				// Jika item adalah direktori, lakukan pencarian rekursif
				if (is_dir("$dir/$subitem")) {
					$this->dirList("$dir/$subitem");
				}
			}

			// Menutup direktori setelah selesai
			closedir($dh);
		}
	}
}

class PlanetbiruFileManager
{
	public static function kh_filter_input_search_get($var = 'q')
	{
		$val = (isset($_GET[$var])) ? $_GET[$var] : "";
		if ($val != "" && is_array($val)) {
			unset($val);
			$val = "";
			// ignore
		}
		$val = trim(strip_tags($val), "\r\n\t ");
		$val = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $val);
		return $val;
	}

	public static function kh_filter_file_name_safe($input)
	{
		$output = preg_replace(
			array("/\s+/", "/[^-\.\w\s+]+/"),
			array("_", "-"),
			$input
		);
		$output = str_replace(
			array("-_", "_-", "__", "___", "--", "---"),
			array("_", "_", "_", "_", "-", "-"),
			$output
		);
		return $output;
	}

	public static function kh_filter_file_name($input)
	{
		$output = preg_replace(
			array("/\s+/", "/[^-\.\w\s+]+/"),
			array(" ", "-"),
			$input
		);
		$output = str_replace(
			array("-_", "_-", "__", "___", "--", "---"),
			array("_", "_", "_", "_", "-", "-"),
			$output
		);
		return $output;
	}

	public static function cleanforbiddenall($dir)
	{
		@chmod(dirname($dir), 0777);
		@chmod($dir, 0777);
		self::cleanforbidden($dir);
		@chmod(dirname($dir), 0755);
	}
	public static function cleanforbidden($dir)
	{
		global $cfg;
		$dir = rtrim($dir, "/");
		$mydir = opendir($dir);
		while (false !== ($file = readdir($mydir))) {
			if ($file != "." && $file != "..") {
				@chmod($dir . "/" . $file, 0777);
				if (@is_dir($dir . "/" . $file)) {
					chdir('.');
					self::cleanforbidden($dir . "/" . $file);
				} else {
					$fn = $dir . "/" . $file;
					$tt = self::getMIMEType($fn);
					if (in_array($tt->extension, $cfg->forbidden_extension)) {
						@unlink($fn);
					}
				}
			}
		}
		closedir($mydir);
	}

	public static function destroyall($dir)
	{
		@chmod(dirname($dir), 0777);
		@chmod($dir, 0777);
		self::destroy($dir);
		@rmdir($dir);
		@chmod(dirname($dir), 0755);
	}
	public static function destroy($dir)
	{
		$dir = rtrim($dir, "/");
		$mydir = opendir($dir);
		while (false !== ($file = readdir($mydir))) {
			if ($file != "." && $file != "..") {
				@chmod($dir . "/" . $file, 0777);
				if (is_dir($dir . "/" . $file)) {
					chdir('.');
					self::destroy($dir . "/" . $file);
					rmdir($dir . "/" . $file) || die("couldn't delete $dir$/file<br />");
				} else {
					@unlink($dir . "/" . $file) || die("couldn't delete $dir$/file<br />");
				}
			}
		}
		closedir($mydir);
	}

	// copy all files and folders in directory to specified directory
	public static function cp($wf, $wto)
	{
		if (!file_exists($wto)) {
			@mkdir($wto, 0755);
		}
		$arr = self::ls_a($wf);
		foreach ($arr as $fn) {
			if ($fn) {
				$fl = "$wf/$fn";
				$flto = "$wto/$fn";
				if (is_dir($fl)) {
					self::cp($fl, $flto);
				} else {
					@copy($fl, $flto);
				}
			}
		}
	}

	public static function ls_a($wh)
	{
		$files = "";
		if ($handle = opendir($wh)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (empty($files)) {
						$files = "$file";
					} else {
						$files = "$file\n$files";
					}
				}
			}
			closedir($handle);
		}
		return explode("\n", $files);
	}
	public static function chmoddir($dir, $perms)
	{
		chmod($dir, $perms);
		$arr = self::ls_a($dir);
		foreach ($arr as $fn) {
			if ($fn) {
				$fn = $dir . "/" . $fn;
				$ft = filetype($fn);
				if ($ft == "file") {
					chmod($fn, $perms);
				} else {
					self::chmoddir($fn, $perms);
				}
			}
		}
	}

	public static function getMIMEType($filename)
	{
		$obj = new StdClass();
		$arr = array(
			'323' => 'text/h323',
			'3gp' => 'video/3gp',
			'ogg' => 'video/ogg',
			'mp4' => 'video/mp4',
			'ram' => 'audio/ram',
			'wma' => 'audio/wma',
			'*' => 'application/octet-stream',
			'acx' => 'application/internet-property-stream',
			'ai' => 'application/postscript',
			'aif' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'asf' => 'video/x-ms-asf',
			'asr' => 'video/x-ms-asf',
			'asx' => 'video/x-ms-asf',
			'au' => 'audio/basic',
			'avi' => 'video/x-msvideo',
			'axs' => 'application/olescript',
			'bas' => 'text/plain',
			'bcpio' => 'application/x-bcpio',
			'bin' => 'application/octet-stream',
			'bmp' => 'image/bmp',
			'c' => 'text/plain',
			'cat' => 'application/vnd.ms-pkiseccat',
			'cdf' => 'application/x-cdf',
			'cdf' => 'application/x-netcdf',
			'cer' => 'application/x-x509-ca-cert',
			'class' => 'application/octet-stream',
			'clp' => 'application/x-msclip',
			'cmx' => 'image/x-cmx',
			'cod' => 'image/cis-cod',
			'conf' => 'text/conf',
			'ini' => 'text/ini',
			'cpio' => 'application/x-cpio',
			'cpp' => 'text/cpp',
			'crd' => 'application/x-mscardfile',
			'crl' => 'application/pkix-crl',
			'crt' => 'application/x-x509-ca-cert',
			'csh' => 'application/x-csh',
			'css' => 'text/css',
			'dcr' => 'application/x-director',
			'der' => 'application/x-x509-ca-cert',
			'dir' => 'application/x-director',
			'dll' => 'application/x-msdownload',
			'dms' => 'application/octet-stream',
			'doc' => 'application/msword',
			'docx' => 'application/msword',
			'dot' => 'application/msword',
			'dvi' => 'application/x-dvi',
			'dxr' => 'application/x-director',
			'eps' => 'application/postscript',
			'etx' => 'text/x-setext',
			'evy' => 'application/envoy',
			'exe' => 'application/octet-stream',
			'fif' => 'application/fractals',
			'flr' => 'x-world/x-vrml',
			'flv' => 'video/flv',
			'gif' => 'image/gif',
			'gtar' => 'application/x-gtar',
			'gz' => 'application/x-gzip',
			'h' => 'text/plain',
			'hdf' => 'application/x-hdf',
			'hlp' => 'application/winhlp',
			'hqx' => 'application/mac-binhex40',
			'hta' => 'text/hta',
			'htc' => 'text/x-component',
			'htm' => 'text/html',
			'htaccess' => 'text/htaccess',
			'html' => 'text/html',
			'htt' => 'text/webviewhtml',
			'ico' => 'image/x-icon',
			'ief' => 'image/ief',
			'iii' => 'application/x-iphone',
			'ins' => 'application/x-internet-signup',
			'isp' => 'application/x-internet-signup',
			'jfif' => 'image/pipeg',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'js' => 'text/x-javascript',
			'latex' => 'application/x-latex',
			'lha' => 'application/octet-stream',
			'lsf' => 'video/x-la-asf',
			'lsx' => 'video/x-la-asf',
			'lzh' => 'application/octet-stream',
			'm13' => 'application/x-msmediaview',
			'm14' => 'application/x-msmediaview',
			'm3u' => 'audio/x-mpegurl',
			'man' => 'application/x-troff-man',
			'md' => 'text/markdown',
			'mdb' => 'application/x-msaccess',
			'me' => 'application/x-troff-me',
			'mht' => 'message/rfc822',
			'mhtml' => 'message/rfc822',
			'mid' => 'audio/mid',
			'mny' => 'application/x-msmoney',
			'mov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' => 'video/mpeg',
			'mp3' => 'audio/mpeg',
			'mpa' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'wmv' => 'video/wmv',
			'mpp' => 'application/vnd.ms-project',
			'mpv2' => 'video/mpeg',
			'mkv' => 'video/mkv',
			'ms' => 'application/x-troff-ms',
			'msg' => 'application/vnd.ms-outlook',
			'mvb' => 'application/x-msmediaview',
			'nc' => 'application/x-netcdf',
			'nws' => 'message/rfc822',
			'oda' => 'application/oda',
			'p10' => 'application/pkcs10',
			'p12' => 'application/x-pkcs12',
			'p7b' => 'application/x-pkcs7-certificates',
			'p7c' => 'application/x-pkcs7-mime',
			'p7m' => 'application/x-pkcs7-mime',
			'p7r' => 'application/x-pkcs7-certreqresp',
			'p7s' => 'application/x-pkcs7-signature',
			'pbm' => 'image/x-portable-bitmap',
			'pdf' => 'application/pdf',
			'pfx' => 'application/x-pkcs12',
			'pgm' => 'image/x-portable-graymap',
			'php' => 'application/x-httpd-php',
			'pko' => 'application/ynd.ms-pkipko',
			'pma' => 'application/x-perfmon',
			'pmc' => 'application/x-perfmon',
			'pml' => 'application/x-perfmon',
			'pmr' => 'application/x-perfmon',
			'pmw' => 'application/x-perfmon',
			'png' => 'image/png',
			'pnm' => 'image/x-portable-anymap',
			'pot' => 'application/vnd.ms-powerpoint',
			'ppm' => 'image/x-portable-pixmap',
			'pps' => 'application/vnd.ms-powerpoint',
			'ppt' => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.ms-powerpoint',
			'prf' => 'application/pics-rules',
			'ps' => 'application/postscript',
			'pub' => 'application/x-mspublisher',
			'qt' => 'video/quicktime',
			'ra' => 'audio/x-pn-realaudio',
			'ram' => 'audio/x-pn-realaudio',
			'ras' => 'image/x-cmu-raster',
			'rgb' => 'image/x-rgb',
			'rmi' => 'audio/mid',
			'roff' => 'application/x-troff',
			'rtf' => 'application/rtf',
			'rtx' => 'text/richtext',
			'scd' => 'application/x-msschedule',
			'sct' => 'text/scriptlet',
			'setpay' => 'application/set-payment-initiation',
			'setreg' => 'application/set-registration-initiation',
			'sh' => 'application/x-sh',
			'shar' => 'application/x-shar',
			'sit' => 'application/x-stuffit',
			'snd' => 'audio/basic',
			'spc' => 'application/x-pkcs7-certificates',
			'spl' => 'application/futuresplash',
			'sql' => 'text/sql',
			'src' => 'application/x-wais-source',
			'sst' => 'application/vnd.ms-pkicertstore',
			'stl' => 'application/vnd.ms-pkistl',
			'stm' => 'text/html',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc' => 'application/x-sv4crc',
			'svg' => 'text/svg+xml',
			'swf' => 'application/x-shockwave-flash',
			't' => 'application/x-troff',
			'tar' => 'application/x-tar',
			'tcl' => 'application/x-tcl',
			'tex' => 'application/x-tex',
			'texi' => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz' => 'application/x-compressed',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'tr' => 'application/x-troff',
			'trm' => 'application/x-msterminal',
			'tsv' => 'text/tab-separated-values',
			'txt' => 'text/plain',
			'uls' => 'text/iuls',
			'ustar' => 'application/x-ustar',
			'vcf' => 'text/x-vcard',
			'vrml' => 'x-world/x-vrml',
			'wav' => 'audio/x-wav',
			'wcm' => 'application/vnd.ms-works',
			'wdb' => 'application/vnd.ms-works',
			'wks' => 'application/vnd.ms-works',
			'wmf' => 'application/x-msmetafile',
			'wps' => 'application/vnd.ms-works',
			'wri' => 'application/x-mswrite',
			'wrl' => 'x-world/x-vrml',
			'wrz' => 'x-world/x-vrml',
			'xaf' => 'x-world/x-vrml',
			'xbm' => 'image/x-xbitmap',
			'xla' => 'application/vnd.ms-excel',
			'xlc' => 'application/vnd.ms-excel',
			'xlm' => 'application/vnd.ms-excel',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.ms-excel',
			'xlt' => 'application/vnd.ms-excel',
			'xlw' => 'application/vnd.ms-excel',
			'xml' => 'text/xml',
			'xof' => 'x-world/x-vrml',
			'xpm' => 'image/x-xpixmap',
			'xwd' => 'image/x-xwindowdump',
			'z' => 'application/x-compress',
			'zip' => 'application/zip'
		);

		$ext = '';
		$mime = '';

		$filename2 = strrev(strtolower($filename));

		foreach ($arr as $key => $val) {
			$ext2 = strrev($key) . '.';
			$pos = stripos($filename2, $ext2);
			if ($pos === 0) {
				$ext = $key;
				$mime = $val;
				break;
			}
		}
		if (!$ext) {
			if (stripos($filename, ".") !== false) {
				$arr2 = explode(".", $filename);
				$ext = $arr2[count($arr2) - 1];
			} else {
				$ext = "";
				$mime = "";
			}
		}
		$obj->extension = $ext;
		$obj->mime = $mime;
		return $obj;
	}

	public static function path_encode($dir, $root = null)
	{
		if ($root === null) {
			global $cfg;
			$rootdir = $cfg->rootdir;
		} else {
			$rootdir = $root;
		}
		$dir = rtrim(str_replace(array("/..", "../", "./", "..\\", ".\\", "\\", "//"), "/", $dir), "/\\");
		$rootdir = trim(str_replace(array("/..", "../", "./", "..\\", ".\\", "\\", "//"), "/", $rootdir), "/\\");
		$dir2 = trim(str_replace($rootdir, 'base', $dir), "/");
		$dir2 = str_replace("//", "/", $dir2);
		return $dir2;
	}
	public static function path_decode($dir, $root = null)
	{
		if (is_array($dir)) {
			$dir = "";
		}
		if ($root === null) {
			global $cfg;
			$rootdir = $cfg->rootdir;
		} else {
			$rootdir = $root;
		}
		$dir2 = $dir;
		if (substr($dir2, 0, 4) == "base") {
			$dir2 = substr($dir2, 4);
		}
		$dir2 = rtrim($dir2, "/\\");
		$rootdir = rtrim($rootdir, "/\\");
		$dir2 = str_replace(array("\\..", "/.."), "/", $dir2);
		$dir2 = str_replace("\\", "/", $dir2);
		$dir2 = str_replace("//", "/", $dir2);
		$dir2 = str_replace("//", "/", $dir2);
		$dir2 = str_replace("../", "/", $dir2);
		$dir2 = str_replace("//", "/", $dir2);
		$dir2 = rtrim($rootdir, "/\\") . "/" . ltrim($dir2, "/\\");
		$dir2 = rtrim($dir2, "/\\");
		return $dir2;
	}

	public static function path_decode_to_url($dir, $rooturl = "")
	{
		if (is_array($dir)) {
			$dir = "";
		}
		$dir2 = $dir;
		if (substr($dir2, 0, 4) == "base") {
			$dir2 = substr($dir2, 4);
		}
		$dir2 = rtrim($dir2, "/\\");
		$dir2 = $rooturl . "/" . $dir2;
		$dir2 = rtrim($dir2, "/\\");
		return $dir2;
	}

	public static function path_encode_trash($dir, $trash = null)
	{
		if ($trash === null) {
			global $cfg;
			$trashdir = $cfg->trashdir;
		} else {
			$trashdir = $trash;
		}
		$dir = rtrim(str_replace(array("/..", "../", "./", "..\\", ".\\", "\\", "//"), "/", $dir), "/\\");
		$trashdir = rtrim(str_replace(array("/..", "../", "./", "..\\", ".\\", "\\", "//"), "/", $trashdir), "/\\");
		return trim(str_replace($trashdir, 'base', $dir), "/");
	}
	public static function path_decode_trash($dir, $trash = null)
	{
		if (is_array($dir)) {
			$dir = "";
		}
		if ($trash === null) {
			global $cfg;
			$trashdir = $cfg->trashdir;
		} else {
			$trashdir = $trash;
		}
		$dir2 = $dir;
		if (substr($dir2, 0, 4) == "base") {
			$dir2 = substr($dir2, 4);
		}
		$dir2 = rtrim($dir2, "/\\");
		$trashdir = rtrim($trashdir, "/\\");
		$dir2 = str_replace(array("/..", "../", "./", "..\\", ".\\", "\\", "//"), "/", $dir2);
		$dir2 = $trashdir . "/" . $dir2;
		$dir2 = rtrim($dir2, "/\\");
		return $dir2;
	}

	// Properti untuk menyimpan daftar file

	// Metode untuk mendapatkan daftar file
	public static function dir_list($dir)
	{
		$fileManager = new FileManager();

		// Mendapatkan daftar file dari direktori yang diinginkan
		return $fileManager->getFileList($dir);
	}

	public static function deleteforbidden($dir, $containsubdir = false)
	{
		global $cfg;
		if ($cfg->delete_forbidden_extension && file_exists($dir) && is_array($cfg->forbidden_extension)) {
			if ($containsubdir) {
				self::cleanforbiddenall($dir);
			} else {
				$dh = opendir($dir);
				if ($dh) {
					while ($subitem = readdir($dh)) {
						$fn = "$dir/$subitem";
						if ($subitem == "." || $subitem == "..") {
							continue;
						}
						$filetype = filetype($fn);
						if ($filetype == "file") {
							$tt = self::getMIMEType($fn);
							if (in_array($tt->extension, $cfg->forbidden_extension)) {
								@unlink($fn);
							}
						}
					}
					closedir($dh);
				}
			}
		}
	}
	public static function dmstoreal($deg, $min, $sec)
	{
		return $deg + ((($min * 60) + ($sec)) / 3600);
	}

	public static function real2dms($val)
	{
		$tm = $val * 3600;
		$tm = round($tm);
		$h = sprintf("%02d", date("H", $tm) - 7);
		if ($h < 0) {
			$h += 24;
		}
		$m = date("i", $tm);
		$s = date("s", $tm);
		return array($h, $m, $s);
	}

	public static function builddirtree($dir)
	{
		$dir = str_replace("\\", "/", $dir);
		$arr = explode("/", $dir);
		$ret = "%s";
		$dt = array();
		$dt['path'] = "";
		$dt['name'] = "";
		$dt['location'] = "";
		foreach ($arr as $k => $val) {
			$dt['path'] = $dt['path'] . $val;
			$dt['name'] = basename($val);
			$dt['location'] = $dt['location'] . ($val);
			if ($k > 1) {
				$html = "<ul>\r\n";
				$html .= "<li class=\"row-data-dir dir-control\" data-file-name=\"" . $dt['name'] . "\" data-file-location=\"" . $dt['location'] . "\"><a href=\"javascript:;\" onClick=\"return openDir('" . $dt['path'] . "')\">" . $dt['name'] . "</a>";
				$html .= "%s</li>\r\n";
				$html .= "</ul>";
				$ret2 = sprintf($ret, $html);
				$ret = $ret2;
			}
			$dt['path'] = $dt['path'] . "/";
			$dt['name'] = $dt['name'] . "/";
			$dt['location'] = $dt['location'] . "/";
		}
		$ret = str_replace("%s", "", $ret);
		return $ret;
	}

	public static function getfmprofile($name, $authblogid, $default = null)
	{
		global $settings;
		if (isset($settings[$name])) {
			return $settings[$name];
		} else {
			return $default;
		}
	}

	public static function compressImageFile($path, $authblogid)
	{
		if (self::getfmprofile('compressimageonupload', $authblogid, 0)) {
			global $cfg;
			$maxsize = $cfg->thumbnail_max_size;
			if (filesize($path) <= $maxsize) {
				// get mime type
				$info = @getimagesize($path);
				if (@stripos($info['mime'], 'image') !== false) {
					if (@stripos($info['mime'], 'jpeg') !== false || self::getfmprofile('imageformat', $authblogid, 0) == 1) {
						// copress here
						$quality = self::getfmprofile('imagequality', $authblogid, 80);
						$interlace = self::getfmprofile('imageinterlace', $authblogid, 0);
						$maxwidth = self::getfmprofile('maximagewidth', $authblogid, 600);
						$maxheight = self::getfmprofile('maximageheight', $authblogid, 800);
						if (@stripos($info['mime'], 'jpeg') !== false) {
							// jpeg
							$imagelocation = self::imageresizemax($path, $path, $maxwidth, $maxheight, $interlace, $quality);
						} else if (@stripos($info['mime'], 'png') !== false) {
							// png
							$imagelocation = self::imageresizemax($path, $path, $maxwidth, $maxheight, $interlace);
						} else if (@stripos($info['mime'], 'gif') !== false) {
							// gif
							$imagelocation = self::imageresizemax($path, $path, $maxwidth, $maxheight, $interlace);
						}
					}
				}
			}
		}
	}


	public static function imageresizemax($source, $destination, $maxwidth, $maxheight, $interlace = false, $quality = 80)
	{
		$image = new StdClass();
		$imageinfo = getimagesize($source);
		if (empty($imageinfo)) {
			if (file_exists($source)) {
				unlink($source);
			}
			return false;
		}
		$image->width  = $imageinfo[0];
		$image->height = $imageinfo[1];
		$image->type   = $imageinfo[2];
		switch ($image->type) {
			case IMAGETYPE_GIF:
				if (function_exists('ImageCreateFromGIF')) {
					$im = @ImageCreateFromGIF($source);
				} else {

					unlink($source);
					return false;
				}
				break;
			case IMAGETYPE_JPEG:
				if (function_exists('ImageCreateFromJPEG')) {
					$im = @ImageCreateFromJPEG($source);
				} else {

					unlink($source);
					return false;
				}
				break;
			case IMAGETYPE_PNG:
				if (function_exists('ImageCreateFromPNG')) {
					$im = @ImageCreateFromPNG($source);
				} else {

					unlink($source);
					return false;
				}
				break;
			default:
				unlink($source);
				return false;
		}
		if (!$im) {
			return false;
		}

		$currentwidth = $image->width;
		$currentheight = $image->height;
		// adapting image width
		if ($currentwidth > $maxwidth) {
			$tmpwidth = round($maxwidth);
			$tmpheight = round($currentheight * ($tmpwidth / $currentwidth));

			$currentwidth = $tmpwidth;
			$currentheight = $tmpheight;
		}
		// adapting image height
		if ($currentheight > $maxheight) {
			$tmpheight = round($maxheight);
			$tmpwidth = round($currentwidth * ($tmpheight / $currentheight));
			$currentwidth = $tmpwidth;
			$currentheight = $tmpheight;
		}
		$im2 = imagecreatetruecolor($currentwidth, $currentheight);
		$white = imagecolorallocate($im2, 255, 255, 255);
		imagefilledrectangle($im2, 0, 0, $currentwidth, $currentheight, $white);
		imagecopyresampled($im2, $im, 0, 0, 0, 0, $currentwidth, $currentheight, $image->width, $image->height);
		if (file_exists($source)) {
			unlink($source);
		}
		if ($interlace) {
			imageinterlace($im2, true);
		}
		imagejpeg($im2, $destination, $quality);
		return $destination;
	}

	public static function get_capture_info($exif)
	{
		// Check if EXIF data is provided and it's an array
		if (!is_array($exif)) {
			return null;
		}

		// Initialize an empty array to store the capture information
		$exifdata = [];

		// Define a list of keys to extract from the EXIF data
		$keys = [
			'Camera Maker'               => 'IFD0.Make',
			'Camera Model'               => 'IFD0.Model',
			'Capture Time'               => ['IFD0.Datetime', 'EXIF.DateTimeOriginal'],
			'Aperture F Number'         => 'COMPUTED.ApertureFNumber',
			'Orientation'               => 'IFD0.Orientation',
			'X Resolution'              => 'IFD0.XResolution',
			'Y Resolution'              => 'IFD0.YResolution',
			'YCbCr Positioning'         => 'IFD0.YCbCrPositioning',
			'Exposure Time'             => 'EXIF.ExposureTime',
			'F Number'                  => 'EXIF.FNumber',
			'ISO Speed Ratings'         => 'EXIF.ISOSpeedRatings',
			'Shutter Speed Value'      => 'EXIF.ShutterSpeedValue',
			'Aperture Value'           => 'EXIF.ApertureValue',
			'Light Source'             => 'EXIF.LightSource',
			'Flash'                    => 'EXIF.Flash',
			'Focal Length'             => 'EXIF.FocalLength',
			'SubSec Time Original'     => 'EXIF.SubSecTimeOriginal',
			'SubSec Time Digitized'    => 'EXIF.SubSecTimeDigitized',
			'Flash Pix Version'        => 'EXIF.FlashPixVersion',
			'Color Space'              => 'EXIF.ColorSpace',
			'Custom Rendered'          => 'EXIF.CustomRendered',
			'Exposure Mode'            => 'EXIF.ExposureMode',
			'White Balance'            => 'EXIF.WhiteBalance',
			'Digital Zoom Ratio'       => 'EXIF.DigitalZoomRatio',
			'Scene Capture Type'       => 'EXIF.SceneCaptureType',
			'Gain Control'             => 'EXIF.GainControl',
		];

		// Loop through the keys and extract values from EXIF data
		foreach ($keys as $label => $key) {
			// Check if the key is a nested array (i.e., multiple possible locations in the EXIF data)
			if (is_array($key)) {
				// Try to find the first valid value
				foreach ($key as $subkey) {
					if (isset($exif[$subkey]) && !empty($exif[$subkey])) {
						$exifdata[$label] = $exif[$subkey];
						break;
					}
				}
			} else {
				// Extract the value if the key exists and is not empty
				$parts = explode('.', $key);
				$value = $exif;
				foreach ($parts as $part) {
					if (isset($value[$part])) {
						$value = $value[$part];
					} else {
						$value = null;
						break;
					}
				}
				if (!empty($value)) {
					$exifdata[$label] = $value;
				}
			}
		}

		// Return the extracted EXIF data (only non-empty values)
		return $exifdata;
	}

	public static function gettumbpict($originalfile, $maxw, $maxh)
	{
		global $cfg;
		$image = new StdClass();
		$filesize = filesize($originalfile);
		if ($filesize > $cfg->thumbnail_max_size) {
			return false;
		}
		$imageinfo = @getimagesize($originalfile);
		if (empty($imageinfo)) {
			return 0;
		}
		$image->width = $imageinfo[0];
		$image->height = $imageinfo[1];
		$image->type = $imageinfo[2];
		$newwidth = $image->width;
		$newheight = $image->height;
		if (!$newwidth || !$newheight) {
			return false;
		}
		if ($maxw != 0 && $image->width > $maxw) {

			$newwidth = $maxw;
			$newheight = $image->height * $maxw / $image->width;
		}
		if ($maxh != 0 && $newheight > $maxh) {

			$tmp = $newheight;
			$newheight = $maxh;
			$newwidth = $newwidth * $maxh / $tmp;
		}
		switch ($image->type) {
			case IMAGETYPE_GIF:
				if (function_exists('ImageCreateFromGIF')) {
					$im = @ImageCreateFromGIF($originalfile);
				} else {
					return false;
				}
				break;
			case IMAGETYPE_JPEG:
				if (function_exists('ImageCreateFromJPEG')) {
					$im = @ImageCreateFromJPEG($originalfile);
				} else {
					return false;
				}
				break;
			case IMAGETYPE_PNG:
				if (function_exists('ImageCreateFromPNG')) {
					$im = @ImageCreateFromPNG($originalfile);
				} else {
					return false;
				}
				break;
			default:
				return false;
		}
		$im1 = imagecreatetruecolor($newwidth, $newheight);
		$cx = $image->width / 2;
		$cy = $image->height / 2;
		if ($image->width < $image->height) {
			$half = floor($image->width / 2.0);
		} else {
			$half = floor($image->height / 2.0);
		}
		$white = imagecolorallocate($im1, 255, 255, 255);
		$black = imagecolorallocate($im1, 0, 0, 0);
		if (!$im) {
			return false;
		}
		imagefilledrectangle($im1, 0, 0, $newwidth, $newheight, $white);
		imagecopyresized($im1, $im, 0, 0, 0, 0, $newwidth, $newheight, $image->width, $image->height);
		return $im1;
	}
}
