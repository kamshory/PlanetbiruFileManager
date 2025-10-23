<?php
include_once __DIR__ . "/functions.php";
include_once __DIR__ . "/auth.php";
if ($cfg->authentification_needed && !$userlogin) {
  include_once __DIR__ . "/tool-login-form.php";
  exit();
}

$dir = trim(stripslashes(@$_GET['dir']), "/");
if (!is_dir(PlanetbiruFileManager::path_decode($dir, $cfg->rootdir))) {
  $dir = '';
}

if (!$dir) {
  $dir =  'base';
}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Planetbiru File Manager</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" href="style/images/icon.png" type="image/jpeg" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="style/file-type.css" />
  <link rel="stylesheet" type="text/css" href="style/style.css" />
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script> <!-- Keep for draggable, droppable, sortable -->
  <script type="text/javascript" src="js/script.js"></script>
  <script type="text/javascript" src="js/overlay-dialog.min.js"></script>
  <script type="text/javascript">
    var vrel = '<?php echo $cfg->rooturl; ?>/';
    var vabs = 'base/';
    window.onload = function() {
      updateToolbarStatus();
      $(document).on('change', 'input[type=checkbox]', function() {
        updateToolbarStatus()
      });
      $(document).on('change', '.checkbox-selector', function() {
        selectAll($(this)[0].checked);
      });
      if (document.images) {
        var preload = new Image(16, 16);
        preload.src = 'style/images/loading.gif';
      }
      setTimeout(function() {
        initContextMenuFile();
        initContextMenuDir();
        initContextMenuFileArea();
        setCheckRelation();
        initDropable();
        initSortable();
        $('#opendir').val('Open');
        preloadImage();
      }, 1000);
      removeCheckboxBorder();
      loadAnimationStop();
      setSize();
      initPermission();
      initEXIF();
      initPreviewImageUpload();
      initDragDropUpload();
      $(window).resize(function() {
        setSize();
      });

      var tgl = cookieRead('togglethumb');
      togglethumb = (tgl == 1) ? true : false;
      if (togglethumb) {
        $('#tb-thumbnail').addClass('tb-selected');
        <?php
        if (!@$cfg->thumbnail_on_load) {
        ?>
          $('.file-table').css('display', 'none');
          openDir();
        <?php
        }
        ?>
      }
    }
  </script>
  <?php
  if (@$_GET['editor'] == 'tiny_mce') {
  ?>
    <script type="text/javascript" src="js/for_tinymce.js"></script>
    <script type="text/javascript">
      function selectFileIndex(url) {
        selectFileForTinyMCE(url);
      }
    </script>
  <?php
  } else {
  ?>
    <script type="text/javascript">
      function selectFileIndex(url) {}
    </script>
  <?php
  }
  ?>

<body class="kamsfilemanager">
  <div id="wrapper" class="container-fluid">
    <div class="toolbar navbar navbar-light bg-light">
      <div id="toolbar-inner" class="toolbar-inner w-100">
        <div id="anim-loader" class="anim-active"></div>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group mr-2" role="group">
                <a class="btn btn-light" href="javascript:createFile()" title="Create New File"><img src="style/images/trans16.gif" class="createfile" alt="New" /></a>
                <a class="btn btn-light" href="javascript:createDirectory()" title="Create New Directory"><img src="style/images/trans16.gif" class="createdir" alt="New" /></a>
                <a class="btn btn-light" href="javascript:uploadFile()" title="Upload File"><img src="style/images/trans16.gif" class="upload" alt="Upload" /></a>
                <a class="btn btn-light" href="javascript:transferFile()" title="Transfer File"><img src="style/images/trans16.gif" class="transfer" alt="Transfer" /></a>
            </div>
            <div class="btn-group mr-2" role="group">
                <a class="btn btn-light" href="javascript:goToUpDir()" title="Go to One Up Level Directory"><img src="style/images/trans16.gif" class="up" alt="Up" /></a>
                <a class="btn btn-light" href="javascript:refreshList()" title="Reload"><img src="style/images/trans16.gif" class="refresh" alt="Reload" /></a>
                <a class="btn btn-light" href="javascript:searchFile()" title="Search"><img src="style/images/trans16.gif" class="search" alt="Search" /></a>
            </div>
            <div class="btn-group mr-2" role="group">
                <a class="btn btn-light" href="javascript:selectAll(1)" title="Check All"><img src="style/images/trans16.gif" class="check" alt="Check" /></a>
                <a class="btn btn-light" href="javascript:selectAll(0)" title="Uncheck All"><img src="style/images/trans16.gif" class="uncheck" alt="Uncheck" /></a>
            </div>
            <div class="btn-group mr-2" role="group">
                <a class="btn btn-light" href="javascript:copySelectedFile()" title="Copy Selected File"><img src="style/images/trans16.gif" class="copy" alt="Copy" /></a>
                <a class="btn btn-light" href="javascript:cutSelectedFile()" title="Cut Selected File"><img src="style/images/trans16.gif" class="cut" alt="Cut" /></a>
                <a class="btn btn-light" href="javascript:moveSelectedFile()" title="Move Selected File"><img src="style/images/trans16.gif" class="move" alt="Move" /></a>
                <a class="btn btn-light" href="javascript:pasteFile()" title="Paste File"><img src="style/images/trans16.gif" class="paste" alt="Paste" /></a>
            </div>
            <div class="btn-group mr-2" role="group">
                <a class="btn btn-light" href="javascript:renameFile()" title="Rename First Selected File"><img src="style/images/trans16.gif" class="rename" alt="Rename" /></a>
                <a class="btn btn-light" href="javascript:deleteSelectedFile()" title="Delete Selected File"><img src="style/images/trans16.gif" class="delete" alt="Delete" /></a>
            </div>
            <div class="btn-group mr-2" role="group">
                <a class="btn btn-light" href="javascript:compressSelectedFile()" title="Compress Selected File"><img src="style/images/trans16.gif" class="compress" alt="Compress" /></a>
                <a class="btn btn-light" href="javascript:extractFile()" title="Extract First Selected File"><img src="style/images/trans16.gif" class="extract" alt="Extract" /></a>
                <a class="btn btn-light" href="javascript:changePermission()" title="Change File Permission"><img src="style/images/trans16.gif" class="permission" alt="Permission" /></a>
            </div>
            <div class="btn-group" role="group">
                <a class="btn btn-light" id="tb-setting" href="javascript:uploadFileSettings()" title="Upload File Setting"><img src="style/images/trans16.gif" class="setting" alt="Settings" /></a>
                <a class="btn btn-light" id="tb-thumbnail" href="javascript:thumbnail()"><img src="style/images/trans16.gif" class="view" alt="View" title="Change View Type" /></a>
                <a class="btn btn-light" href="javascript:about()" title="About"><img src="style/images/trans16.gif" class="help" alt="Help" /></a>
                <a class="btn btn-light tb-hide" id="tb-clipboard" href="javascript:showClipboard()"><img src="style/images/trans16.gif" class="clipboard" alt="Clipboard" title="Show Clipboard" /></a>
                <a class="btn btn-light tb-hide" id="tb-clipboard-empty" href="javascript:emptyClipboard()"><img src="style/images/trans16.gif" class="cleanup" alt="Empty Clipboard" title="Empty Clipboard" /></a>
                <a class="btn btn-light" id="tb-logout" href="logout.php"><img src="style/images/trans16.gif" class="logout" alt="Logout" title="Logout" /></a>
            </div>
        </div>
        <div class="addressbar mt-2">
          <form name="dirform" method="get" enctype="multipart/form-data" action="" onSubmit="return openDir()">
            <div class="input-group">
              <input type="text" class="form-control" name="address" id="address" value="<?php echo $dir; ?>" autocomplete="off" />
              <div class="input-group-append">
                <input type="submit" name="opendir" id="opendir" class="btn btn-secondary" value="Open" />
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
      <div class="row no-gutters file-manager-container">
        <div class="col-md-3 directory-area">
          <div id="directory-container">
            <ul>
              <li class="basedir dir-control" data-file-name="base" data-file-location="">
                <a href="javascript:;" onClick="return openDir('base')">base</a>
                <?php
                include_once __DIR__ . "/tool-load-dir.php";
                ?>
              </li>
            </ul>
          </div>
        </div>

        <div class="col-md-9 file-area">
          <div id="file-container">
            <?php
            include_once __DIR__ . "/tool-load-file.php";
            ?>
          </div>
        </div>
      </div>
  

  <!-- Modal -->
  <div class="modal fade" id="common-dialog" tabindex="-1" role="dialog" aria-labelledby="common-dialog-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="common-dialog-title">Dialog</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="common-dialog-inner">
          ...
        </div>
        <div class="modal-footer" id="common-dialog-footer">
        </div>
      </div>
    </div>
  </div>
  <div id="overlay-container" style="display:none"></div>
</body>

</html>