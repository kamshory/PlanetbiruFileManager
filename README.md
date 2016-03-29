# PlanetbiruFileManager

Planetbiru File Manager is a web-based file manager using the PHP language that can be integrated with a variety of programs for managing files on the server. Planetbiru File Manager has a lot of very useful functions to create a new file, upload, edit, delete, move, rename, compress, extract, and so on. You can also drag and drop files from local storage to file area to upload them.

## Functions
* Create New File
* Create New Directory
* Upload File
* Transfe File
* Go to One Up Level Directory
* Refresh
* Find
* Check All
* Uncheck All
* Copy
* Cut
* Move
* Paste
* Rename
* Delete
* Compress
* Extract
* Change File Permission
* Change View Type
* Help
* Logout

## Context Menu
Planetbiru File Manager provide contect menu for directory and file.
### Directory
Context Menu for directory when no file selected
* Create New File
* Create New Directory
* Up Directory
* Refresh File List
* Change View Type
* Upload File
* Check All

Context Menu for directory when any file selected
* Copy Selected File
* Cut Selected File
* Move Selected File
* Delete Selected File
* Compress Selected File
* Set Permission
* Create New File
* Create New Directory
* Up Directory
* Refresh File List
* Change View Type
* Upload File
* Check All
* Uncheck All

Addition function for directory when any clipboard content
* Paste File
 
### File
#### Text File
* Select File
* Copy File
* Cut File
* Rename File
* Move File
* Delete File
* Edit as Text
* Edit Code
* Compress File
* Set Permission
* Download File
* Force Download File
* File Properties

#### Image File
* Select File
* Copy File
* Cut File
* Rename File
* Move File
* Delete File
* Peview Image
* Edit Image
* Compress File
* Set Permission
* Download File
* Force Download File
* Image Properties

#### Video File
* Select File
* Copy File
* Cut File
* Rename File
* Move File
* Delete File
* Play Video
* Compress File
* Set Permission
* Download File
* Force Download File
* File Properties

#### Audio File
* Select File
* Copy File
* Cut File
* Rename File
* Move File
* Delete File
* Play Audio
* Compress File
* Set Permission
* Download File
* Force Download File
* File Properties

#### ZIP File
* Select File
* Copy File
* Cut File
* Rename File
* Move File
* Extract File
* Set Permission
* Download File
* Force Download File
* File Properties

## Drag and Drop
Planetbiru File Manager support drag and drop file for:
* Move file or directory to another directory.
* Upload file from local storage to directory on the server.

## Configuration
Configuration file is conf.php
```php
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


$cfg->users = array(
	array("admin", "admin", "plain"),
	array("masroy", "masroy", "plain")
);
/*
0 = username
1 = password
2 = type of password (plain, md5, sha1)
*/

?>
```
