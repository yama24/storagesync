<?php

namespace App\Http\Controllers;

use App\Models\Setting;

abstract class Controller
{
    public $folder;

    public function __construct()
    {
        $setting = Setting::all()->keyBy('name');

        //setup gcs filesystem
        config(['filesystems.disks.gcs' => [
            'driver' => 'gcs',
            'key_file_path' => null,
            'key_file' => json_decode($setting['gcs_credentials']->value, true),
            'project_id' => $setting['gcs_project_id']->value,
            'bucket' => $setting['gcs_bucket']->value,
            'path_prefix' => '',
            'storage_api_uri' => null,
            'api_endpoint' => null,
            'visibility' => 'private',
            'visibility_handler' => null,
            'metadata' => ['cacheControl' => 'public,max-age=86400'],
        ]]);

        $this->folder = $setting['gcs_folder']->value;
    }

}


function getFolderRecursive($dir, $is_single = false)
{
    $result = [];
    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        //continue if private folder
        if (strpos($item, '.') === 0) {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            if ($is_single) {
                $result[] = $item;
            } else {
                $result[$item] = getFolderRecursive($path);
            }
        }
    }

    return $result;
}


function getFilesRecursive($dir, $is_single = false)
{
    $result = [];
    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        //continue if private folder
        if (strpos($item, '.') === 0) {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            if (!$is_single) {
                // $result[$item] = getFilesRecursive($path);
                $result = array_merge($result, getFilesRecursive($path));
            }
        } else {
            $filepath = $dir . DIRECTORY_SEPARATOR . $item;
            //remove duplicate slash
            $filepath = preg_replace('#/+#', '/', $filepath);
            $result[] = $filepath;
        }
    }

    return $result;
}