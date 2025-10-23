# 🌐 Planetbiru File Manager

**Planetbiru File Manager** is a lightweight, web-based file management system written in **PHP**.
It can be easily integrated into various web applications to manage files and directories directly on the server.

The file manager includes a wide range of essential functions — allowing users to **create**, **upload**, **edit**, **delete**, **move**, **rename**, **compress**, and **extract** files and folders with ease.

It also supports **drag-and-drop** file uploads and requires **no database** to operate, making it highly portable and simple to deploy.

---

## 🚀 Features

* 📄 Create New File
* 📁 Create New Directory
* ⬆️ Upload File
* 🔁 Transfer File
* 🔼 Navigate Up One Directory Level
* 🔃 Refresh File List
* 🔍 Find Files
* ☑️ Check All / Uncheck All
* 📋 Copy / Cut / Paste Files
* ✏️ Rename File or Directory
* 🗑️ Delete File or Directory
* 📦 Compress (ZIP)
* 📂 Extract (Unzip)
* 🔐 Change File Permissions
* 👁️ Switch View Type (List/Grid)
* ❓ Help
* 🚪 Logout

---

## 🖱️ Context Menu

Planetbiru File Manager provides a **context menu** for both **directories** and **files**, depending on the current selection.

### 📂 Directory Context Menu

#### When No File is Selected

* Create New File
* Create New Directory
* Go Up One Level
* Refresh File List
* Change View Type
* Upload File
* Check All

#### When Files Are Selected

* Copy Selected Files
* Cut Selected Files
* Move Selected Files
* Delete Selected Files
* Compress Selected Files
* Set Permissions
* Create New File
* Create New Directory
* Go Up One Level
* Refresh File List
* Change View Type
* Upload File
* Check All / Uncheck All

#### Additional Options (When Clipboard Has Content)

* Paste File
* Empty Clipboard

---

## 📄 File Context Menu

Context menu options vary depending on the file type.

### 📝 Text Files

* Select File
* Copy / Cut / Move / Rename / Delete
* Edit as Text
* Edit as Code
* Compress File
* Set Permission
* Download File / Force Download
* View File Properties

### 🖼️ Image Files

* Select File
* Copy / Cut / Move / Rename / Delete
* Preview Image
* Edit Image
* Compress File
* Set Permission
* Download File / Force Download
* View Image Properties

### 🎬 Video Files

* Select File
* Copy / Cut / Move / Rename / Delete
* Play Video
* Compress File
* Set Permission
* Download File / Force Download
* View File Properties

### 🎵 Audio Files

* Select File
* Copy / Cut / Move / Rename / Delete
* Play Audio
* Compress File
* Set Permission
* Download File / Force Download
* View File Properties

### 🗜️ ZIP Files

* Select File
* Copy / Cut / Move / Rename
* Extract File
* Set Permission
* Download File / Force Download
* View File Properties

---

## 🖱️💾 Drag and Drop Support

Planetbiru File Manager supports intuitive **drag and drop operations**:

* Move files or directories to another folder
* Upload files from local storage to a server directory

---

## ⚙️ Configuration

Configuration settings are defined in the file **`conf.php`**.

```php
<?php
if(!isset($cfg)) $cfg = new StdClass();

// Authentication
$cfg->authentification_needed = true;		
/* When set to true, authentication is required to access the file manager. */

// Root Directory
$cfg->rootdir = dirname(__FILE__)."/content/upload";
/* The root directory where uploaded files are stored.
   It is recommended to protect this directory using .htaccess to prevent PHP execution. */

// Hidden Files and Directories
$cfg->hiddendir = array();
/* List of directories or files under the root that should be hidden and inaccessible. */

// Root URL
$cfg->rooturl = "content/upload";
/* The base URL for uploaded files (can be relative or absolute). */

// Thumbnails
$cfg->thumbnail = true;
$cfg->thumbnail_quality = 75;
$cfg->thumbnail_max_size = 5000000;
/* Enable/disable thumbnails, set quality, and define the maximum file size for generating thumbnails. */

// Permissions
$cfg->readonly = false;
/* If true, users cannot modify files (upload, delete, extract, etc.). */

// Upload Rules
$cfg->allow_upload_all_file = true;
$cfg->allow_upload_image = true;
/* Controls whether users can upload all file types or only images. */

// Cache Settings
$cfg->cache_max_age_file = 3600; // File thumbnail cache (seconds)
$cfg->cache_max_age_dir = 120;   // Directory cache (seconds)

// Security
$cfg->delete_forbidden_extension = true;
$cfg->forbidden_extension = array();
/* Forbidden file extensions are deleted automatically during upload, rename, copy, or extract. */

// Ensure root directory exists
if(strlen(@$cfg->rootdir) && !file_exists($cfg->rootdir)) {
    mkdir($cfg->rootdir);
}

// User Authentication
$cfg->users = array(
    array("kamshory", "{SHA}PUkSovNmiq8EkRemJdSniuPfezM="),
    array("masroy", "{SHA}+Zrs7z0S4C3LtiYLvdNRiciebnM=")
);

// Optionally load from .htpasswd
if(file_exists(dirname(__FILE__)."/.htpasswd")) {
    $cfg->users = array();
    $rows = file(dirname(__FILE__)."/.htpasswd");
    foreach($rows as $line) {
        $line = trim($line);
        if(strlen($line) > 0) {
            $cfg->users[] = explode(":", $line);
        }
    }
}
?>
```

---

## 🔒 Security Notes

* It is recommended to **use `.htaccess`** to prevent the execution of PHP files inside the upload directory.
* You may configure the upload directory **outside the web root** for additional protection.
* Example:

```
/home/username/public_html/         ← Web root
/home/username/public_html/upload/  ← Upload directory
```

To prevent direct access, you can place a restrictive `.htaccess` in `/home/username/public_html/`.

---

## 🧩 Integration

Planetbiru File Manager can be easily embedded into other systems by including or iframe-loading the file manager interface.
You can also integrate authentication mechanisms or restrict access based on user roles from your own system.

---

## 📦 Requirements

* PHP 5.6 or higher
* Web server (Apache, Nginx, or compatible)
* No database required

---

## 🧑‍💻 Author

**Planetbiru Team**
Developed and maintained by [Kamshory](https://github.com/kamshory)


