<?php
include_once dirname(__FILE__) . "/functions.php";
include_once dirname(__FILE__) . "/auth.php";
include dirname(__FILE__) . "/conf.php"; //NOSONAR
$notInstalled = 'Not Installed';
$installed = 'Installed';
if ($cfg->authentification_needed && !$userlogin) {
  exit();
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dialog-table dialog-about">
  <tr>
    <td width="40%">File Upload</td>
    <td><?php echo ini_get('file_uploads') ? 'Enabled' : 'Disabled'; ?></td>
  </tr>
  <tr>
    <td>Max. File Upload</td>
    <td><?php echo ini_get('max_file_uploads'); ?></td>
  </tr>
  <tr>
    <td>Max. File Upload Size</td>
    <td><?php echo ini_get('upload_max_filesize'); ?></td>
  </tr>
  <tr>
    <td>Max. Post Size</td>
    <td><?php echo ini_get('post_max_size'); ?></td>
  </tr>
  <tr>
    <td>PHP-ZIP</td>
    <td><?php echo class_exists('ZipArchive') ? $installed : $notInstalled; ?> (Required)</td>
  </tr>
  <tr>
    <td>PHP-GD</td>
    <td><?php echo function_exists('imagecreate') ? $installed : $notInstalled; ?> (Required)</td>
  </tr>
  <tr>
    <td>PHP-EXIF</td>
    <td><?php echo function_exists('exif_read_data') ? $installed : $notInstalled; ?> (Optional)</td>
  </tr>
  <tr>
    <td>Authentification</td>
    <td><?php echo (@$cfg->authentification_needed) ? 'Needed' : 'Not Needed'; ?></td>
  </tr>
  <tr>
    <td>Permission Directory</td>
    <td><span class="permission-info"><?php echo substr(sprintf('%o', fileperms(@$cfg->rootdir)), -4); ?></span></td>
  </tr>
  <tr>
    <td>Read Only Mode</td>
    <td><?php echo (@$cfg->readonly) ? 'Yes' : 'No'; ?></td>
  </tr>
</table>