<?php
include_once dirname(__FILE__) . "/functions.php";
include_once dirname(__FILE__) . "/auth.php";
include dirname(__FILE__) . "/conf.php"; //NOSONAR
if ($cfg->authentification_needed && !$userlogin) {
  exit();
}
$filename = PlanetbiruFileManager::path_decode(@$_GET['filepath'], $cfg->rootdir);
$json_exif = "";
if (@$_GET['type'] == 'directory') {
  if (file_exists($filename)) {
    $filectime = date('Y-m-d H:i:s',  filectime($filename));
    $fileatime = date('Y-m-d H:i:s',  fileatime($filename));
    $filemtime = date('Y-m-d H:i:s',  filemtime($filename));
    $fileperms = substr(sprintf('%o', fileperms($filename)), -4);
  }
?>
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
    <tr>
      <td width="30%">Directory Name</td>
      <td>
        <div class="file-name-cropped"><?php echo basename($filename); ?></div>
      </td>
    </tr>
    <tr>
      <td>Created</td>
      <td><?php echo $filectime; ?></td>
    </tr>
    <tr>
      <td>Last Modified</td>
      <td><?php echo $filemtime; ?></td>
    </tr>
    <tr>
      <td>Last Accessed</td>
      <td><?php echo $fileatime; ?></td>
    </tr>
    <tr>
      <td>Permission</td>
      <td><span class="permission-info"><?php echo $fileperms; ?></span></td>
    </tr>
  </table>
<?php
} else if (@$_GET['type'] == 'image') {
  $ft = PlanetbiruFileManager::getMIMEType($filename);
  if (file_exists($filename)) {
    $is = @getimagesize($filename);
    if ($is) {
      if ($is['mime']) {
        $ft->mime = $is['mime'];
      }
      $width = $is[0];
      $height = $is[1];
      if (function_exists("exif_read_data")) {
        $exif = @exif_read_data($filename, 0, true);
        $json_exif = rawurlencode(json_encode(array(PlanetbiruFileManager::get_capture_info($exif))));
        if (isset($exif['IFD0']['Make'], $exif['IFD0']['Model']) && strpos($exif['IFD0']['Model'], $exif['IFD0']['Make']) === 0) {
          $exif['IFD0']['Make'] = '';
        }

        $camera = (isset($exif['IFD0']['Make'])) ? (($exif['IFD0']['Make'] . ' ' . $exif['IFD0']['Model'])) : '-';
        if (isset($exif['IFD0']['Datetime'])) {
          $time_capture = $exif['IFD0']['Datetime'];
        } elseif (isset($exif['EXIF']['DateTimeOriginal'])) {
          $time_capture = $exif['EXIF']['DateTimeOriginal'];
        } else {
          $time_capture = '-';
        }

        if (isset($exif['GPS'])) {
          $gpsinfo = isset($exif['GPS']) ? $exif['GPS'] : null;

          if ($gpsinfo) {
            // Latitude parsing
            $latd = $latm = $lats = 0;
            if (isset($gpsinfo['GPSLatitude'])) {
              $lat = $gpsinfo['GPSLatitude'];
              if (count($lat) > 2) {
                $latd = isset($lat[0][1]) ? $lat[0][0] / $lat[0][1] : 0;
                $latm = isset($lat[1][1]) ? $lat[1][0] / $lat[1][1] : 0;
                $lats = isset($lat[2][1]) ? $lat[2][0] / $lat[2][1] : 0;
              }
            }

            $reallat = PlanetbiruFileManager::dmstoreal($latd, $latm, $lats);
            if (isset($gpsinfo['GPSLatitudeRef']) && stripos($gpsinfo['GPSLatitudeRef'], "S") !== false) {
              $reallat *= -1;
            }
            $latitude = "$latd; $latm; $lats " . (isset($gpsinfo['GPSLatitudeRef']) ? $gpsinfo['GPSLatitudeRef'] : '');
            $latitude = trim($latitude, " ;");

            // Longitude parsing
            $longd = $longm = $longs = 0;
            if (isset($gpsinfo['GPSLongitude'])) {
              $long = $gpsinfo['GPSLongitude'];
              if (count($long) > 2) {
                $longd = isset($long[0][1]) ? $long[0][0] / $long[0][1] : 0;
                $longm = isset($long[1][1]) ? $long[1][0] / $long[1][1] : 0;
                $longs = isset($long[2][1]) ? $long[2][0] / $long[2][1] : 0;
              }
            }

            $reallong = PlanetbiruFileManager::dmstoreal($longd, $longm, $longs);
            if (isset($gpsinfo['GPSLongitudeRef']) && stripos($gpsinfo['GPSLongitudeRef'], "W") !== false) {
              $reallong *= -1;
            }
            $longitude = "$longd; $longm; $longs " . (isset($gpsinfo['GPSLongitudeRef']) ? $gpsinfo['GPSLongitudeRef'] : '');
            $longitude = trim($longitude, " ;");

            // Altitude parsing
            $altitude = 0;
            if (isset($gpsinfo['GPSAltitude']) && count($gpsinfo['GPSAltitude']) > 1) {
              $altitude = $gpsinfo['GPSAltitude'][0] / $gpsinfo['GPSAltitude'][1];
            }
            $altref = isset($gpsinfo['GPSAltitudeRef']) ? $gpsinfo['GPSAltitudeRef'] : '';
          }
        } else {
          $latitude = "-";
          $longitude = "-";
          $altitude = "-";
          $altref = "";
        }
      }
    }
    $filectime = date('Y-m-d H:i:s',  filectime($filename));
    $fileatime = date('Y-m-d H:i:s',  fileatime($filename));
    $filemtime = date('Y-m-d H:i:s',  filemtime($filename));
    $fileperms = substr(sprintf('%o', fileperms($filename)), -4);
    $md5 = md5_file($filename);
    $filesize = filesize($filename);
  }
  $url = $cfg->rooturl . '/' . substr(PlanetbiruFileManager::path_encode($filename, $cfg->rootdir), 5);
?>
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
    <tr>
      <td width="30%">File Name</td>
      <td>
        <div class="file-name-cropped"><a href="<?php echo $url; ?>" target="_blank" title="Click to download"><?php echo basename($filename); ?></a></div>
      </td>
    </tr>
    <tr>
      <td>MIME Type</td>
      <td><?php echo $ft->mime; ?></td>
    </tr>
    <tr>
      <td>Image Width</td>
      <td><?php echo $width; ?></td>
    </tr>
    <tr>
      <td>Image Height</td>
      <td><?php echo $height; ?></td>
    </tr>
    <tr>
      <td>Time Taken</td>
      <td><?php echo $time_capture; ?></td>
    </tr>
    <tr>
      <td>Camera</td>
      <td><span class="capture-info" data-exif="<?php echo $json_exif; ?>"><?php echo $camera; ?></span></td>
    </tr>
    <tr>
      <td>Latitude</td>
      <td><?php echo $latitude; ?></td>
    </tr>
    <tr>
      <td>Longitude</td>
      <td><?php echo $longitude; ?></td>
    </tr>
    <tr>
      <td>Altitude</td>
      <td><?php echo $altitude . " " . $altref; ?></td>
    </tr>
    <tr>
      <td>File Size</td>
      <td><?php echo ($filesize > 0) ? ($filesize . ' bytes') : ($filesize . ' byte'); ?></td>
    </tr>
    <tr>
      <td>MD5</td>
      <td><?php echo $md5; ?></td>
    </tr>
    <tr>
      <td>Created</td>
      <td><?php echo $filectime; ?></td>
    </tr>
    <tr>
      <td>Last Modified</td>
      <td><?php echo $filemtime; ?></td>
    </tr>
    <tr>
      <td>Last Accessed</td>
      <td><?php echo $fileatime; ?></td>
    </tr>
    <tr>
      <td>Permission</td>
      <td><span class="permission-info"><?php echo $fileperms; ?></span></td>
    </tr>
  </table>
<?php
} else {
  $ft = PlanetbiruFileManager::getMIMEType($filename);
  if (file_exists($filename)) {
    $filectime = date('Y-m-d H:i:s',  filectime($filename));
    $fileatime = date('Y-m-d H:i:s',  fileatime($filename));
    $filemtime = date('Y-m-d H:i:s',  filemtime($filename));
    $fileperms = substr(sprintf('%o', fileperms($filename)), -4);
    $md5 = md5_file($filename);
    $filesize = filesize($filename);
  }
  $url = $cfg->rooturl . '/' . substr(PlanetbiruFileManager::path_encode($filename, $cfg->rootdir), 5);
?>
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">
    <tr>
      <td width="30%">File Name</td>
      <td>
        <div class="file-name-cropped"><a href="<?php echo $url; ?>" target="_blank" title="Click to download"><?php echo basename($filename); ?></a></div>
      </td>
    </tr>
    <tr>
      <td>MIME Type</td>
      <td><span class="mime-type" data-content="<?php echo trim($ft->mime); ?>"><?php echo $ft->mime; ?></span></td>
    </tr>
    <tr>
      <td>File Size</td>
      <td><?php echo ($filesize > 0) ? ($filesize . ' bytes') : ($filesize . ' byte'); ?></td>
    </tr>
    <tr>
      <td>MD5</td>
      <td><?php echo $md5; ?></td>
    </tr>
    <tr>
      <td>Created</td>
      <td><?php echo $filectime; ?></td>
    </tr>
    <tr>
      <td>Last Modified</td>
      <td><?php echo $filemtime; ?></td>
    </tr>
    <tr>
      <td>Last Accessed</td>
      <td><?php echo $fileatime; ?></td>
    </tr>
    <tr>
      <td>Permission</td>
      <td><span class="permission-info"><?php echo $fileperms; ?></span></td>
    </tr>
  </table>
<?php
}
?>