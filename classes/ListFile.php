<?php

/**
 * Class to list files and directories from a given location.
 * It recursively fetches file and directory details, such as name, size, type, permissions, and modification time.
 */
class ListFile {
    public $location;
    public $resultFile = array();
    public $resultDir = array();
    private $cfg;

    /**
     * Constructor to initialize the location and trigger the file listing.
     *
     * @param stdClass $cfg
     * @param string|null $location Directory location to list files from. If null, no files are listed on creation.
     */
    public function __construct($cfg, $location = null)
    {
        $this->cfg = $cfg;
        if ($location !== null) {
            $this->location = $location;
            $this->findAll($location);
        }
    }

    /**
     * Recursively finds all files and directories within a given location.
     * For each file, it collects file details such as name, size, MIME type, and modification time.
     *
     * @param string $location Path of the directory to scan.
     */
    public function findAll($location)
    {        
        // Ensure the location exists and is a directory
        if (file_exists($location) && $handle = opendir($location)) {

            // Loop through all files and directories in the current location
            while (false !== ($ufile = readdir($handle))) {
                $fn = "$location/$ufile";

                // Skip the special directories '.' and '..'
                if ($ufile == "." || $ufile == "..") {
                    continue;
                }

                $filetype = filetype($fn);
                unset($obj);

                // Process files
                if ($filetype == "file") {
                    $this->processFile($fn, $this->cfg);
                }
                // Process directories
                else if ($filetype == "dir") {
                    $this->processDirectory($fn, $this->cfg);

                    // Recursively find files and directories within subdirectories
                    $lv = new ListFile($this->cfg, $fn);
                    foreach ($lv->resultFile as $val) {
                        $this->resultFile[] = $val;
                    }
                    foreach ($lv->resultDir as $val) {
                        $this->resultDir[] = $val;
                    }
                }
            }
        }
    }

    /**
     * Processes a file to extract its details like MIME type, size, and modification time.
     *
     * @param string $fn The file path.
     * @param object $cfg Configuration object to use for URL and directory encoding.
     */
    private function processFile($fn, $cfg)
    {
        $ft = PlanetbiruFileManager::getMIMEType($fn);
        $obj = array();
        $obj['url'] = $cfg->rooturl . '/' . substr(PlanetbiruFileManager::path_encode($fn, $cfg->rootdir), 5);
        $obj['path'] = PlanetbiruFileManager::path_encode($fn, $cfg->rootdir);
        $obj['location'] = PlanetbiruFileManager::path_encode(dirname($fn), $cfg->rootdir);
        $obj['name'] = basename($fn);
        $fs = filesize($fn);
        $obj['filesize'] = $fs;
        
        // Format file size
        if ($fs >= 1048576) {
            $obj['size'] = number_format($fs / 1048576, 2, '.', '') . 'M';
        } else if ($fs >= 1024) {
            $obj['size'] = number_format($fs / 1024, 2, '.', '') . 'K';
        } else {
            $obj['size'] = $fs;
        }
        
        $obj['type'] = $ft->mime;
        $obj['extension'] = $ft->extension;
        $obj['permission'] = substr(sprintf('%o', fileperms($fn)), -4);
        $fti = filemtime($fn);
        $obj['filemtime'] = '<span title="' . date('Y-m-d H:i:s', $fti) . '">' . date('y-m-d', $fti) . '</span>';

        // Process image-specific properties
        if ((stripos($obj['type'], 'image') !== false || stripos($obj['type'], 'application/x-shockwave-flash') !== false) && $obj['filesize'] <= $cfg->thumbnail_max_size) {
            $this->processImage($fn, $obj);
        } else {
            $obj['image_width'] = 0;
            $obj['image_height'] = 0;
        }

        $this->resultFile[] = $obj;
    }

    /**
     * Processes the image file to get its dimensions.
     *
     * @param string $fn The image file path.
     * @param array &$obj The object to store the processed image details.
     */
    private function processImage($fn, &$obj)
    {
        try {
            $is = @getimagesize($fn);
            if ($is) {
                $obj['image_width'] = $is[0];
                $obj['image_height'] = $is[1];
                if (stripos($is['mime'], 'image') === 0) {
                    $obj['type'] = $is['mime'];
                }
            } else {
                $obj['image_width'] = 0;
                $obj['image_height'] = 0;
            }
        } catch (Exception $e) {
            $obj['image_width'] = 0;
            $obj['image_height'] = 0;
        }
    }

    /**
     * Processes a directory to extract its details.
     *
     * @param string $fn The directory path.
     * @param object $cfg Configuration object to use for URL and directory encoding.
     */
    private function processDirectory($fn, $cfg)
    {
        $obj = array();
        $obj['path'] = PlanetbiruFileManager::path_encode($fn, $cfg->rootdir);
        $obj['location'] = PlanetbiruFileManager::path_encode(dirname($fn), $cfg->rootdir);
        $obj['name'] = basename($fn);
        $obj['type'] = 'dir';
        $obj['permission'] = substr(sprintf('%o', fileperms($fn)), -4);
        $fti = filemtime($fn);
        $obj['filemtime'] = '<span title="' . date('Y-m-d H:i:s', $fti) . '">' . date('y-m-d', $fti) . '</span>';

        $this->resultDir[] = $obj;
    }
}
