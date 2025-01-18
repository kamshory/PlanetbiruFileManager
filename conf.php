<?php
if(!isset($cfg)) $cfg = new StdClass();
$cfg->authentification_needed = true;		
/* When Kams File Manager is used on online system, it must be set true.*/
$cfg->rootdir = dirname((__FILE__))."/content/upload";	 
/* Root directory for uploaded file. Use .htaccess file to protect this directory from executing PHP files.*/
$cfg->hiddendir = array();	 
/* File or directory under root directory to be hidden and forbidden to access it.*/
$cfg->rooturl = "content/upload";						
/* Root url for uploaded file. It can be relative or absoulute.*/
$cfg->thumbnail = true;						
/* Thumbnail for image files.*/
$cfg->thumbnail_quality = 75;				
/* Quality for thumbnail image.*/
$cfg->thumbnail_max_size = 5000000; 
/* Maximum file size to show with thumbnail */
$cfg->readonly = false;						
/* Is user allowed to modify the file or the directory including upload, delete, or extract files.*/
$cfg->allow_upload_all_file = true;			
/* Is user allowed to upload file beside image.*/
$cfg->allow_upload_image = true;			
/* Is user allowed to upload images.*/


$cfg->cache_max_age_file = 3600; 			/* Maximum age for file thumbnail cache (in second) */
$cfg->cache_max_age_dir = 120; 				/* Maximum age for directory thumbnail cache (in second) */


$cfg->delete_forbidden_extension = true;	
/* Delete forbidden files on upload, rename, copy, or extract operation */
$cfg->forbidden_extension = array();

/* Note
   You can permit user to upload images but not other type for security reason.
   You can add .htaccess file to prevent user executing PHP script but its location is not on {$cfg->rootdir}
   
   For example:
   Your root document of your system is
   /home/youname/public_html
   
   You set upload directory to
   /home/yourname/public_html/upload
   
   You can place an .htaccess file in
   /home/youname/public_html
   to redirect client access   
   
   
*/
if(strlen(@$cfg->rootdir))
{
	if(strlen(@$cfg->rootdir))
	{
		if(!file_exists($cfg->rootdir))
		{
			mkdir($cfg->rootdir);
		}
	}
}


$cfg->users = '';

if(file_exists(dirname(__FILE__)."/.htpasswd"))
{
	$cfg->users = array();
	$row = file(dirname(__FILE__)."/.htpasswd");
	foreach($row as $idx=>$line)
	{
		$row[$idx] = trim($line, " \r\n\t ");
	}
	$cfg->users = implode("\r\n", $row);
}
else
{
	$cfg->users = 'administrator:$apr1$ZlYfGv7V$0cAZNh8Si4WKBgY5H1mS1/';
	file_put_contents(dirname(__FILE__)."/.htpasswd", $cfg->users);
}
/*
0 = username
1 = password
2 = type of password (plain, md5, sha1)
*/

?>
