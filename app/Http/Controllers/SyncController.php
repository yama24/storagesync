<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Native\Laravel\Facades\Notification;

class SyncController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $folder = Setting::where('name', 'fullpath')->first()->value;

        $files = getFilesRecursive($folder);

        // foreach ($files as $key => $file) {
        //     $exists = Storage::disk('gcs')->exists($this->folder . '/' . $file);
        //     if ($exists) {
        //         unset($files[$key]);
        //     }
        // }

        return view('sync', compact('files', 'folder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $filepath = $request->input('file');

        //read file content
        $file = file_get_contents($filepath);
        $filesize = filesize($filepath);
        $lastmodified = filemtime($filepath);

        $fileprops = [
            'name' => basename($filepath),
            'size' => $filesize,
            'type' => mime_content_type($filepath),
            'lastmodified' => date('Y-m-d H:i:s', $lastmodified),
            'path' => $filepath,
        ];

        // $gcsconfig = config('filesystems.disks.gcs');
        // dd($gcsconfig);

        $disk = Storage::disk('gcs');

        $exists = $disk->exists($this->folder . '/' . $filepath);

        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'File already exists']);
        }
        //upload file to gcs
        $disk->put($this->folder . '/' . $filepath, $file);

        //get metadata.jso file from gcs
        $exists = $disk->exists($this->folder . '/metadata.json');
        if (!$exists) {
            $data = [];
            $data[] = $fileprops;
            $disk->put($this->folder . '/metadata.json', json_encode($data));
        } else {
            $data = json_decode($disk->get($this->folder . '/metadata.json'), true);

            //check if file already exists in metadata.json
            $found = false;
            foreach ($data as $key => $value) {
                if ($value['path'] == $fileprops['path']) {
                    $data[$key] = $fileprops;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $data[] = $fileprops;
                $disk->put($this->folder . '/metadata.json', json_encode($data));
            }
        }
        // sleep(1);

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function finish(Request $request){
        Notification::title('Notification Storage Sync')
        ->message('Files synced successfully!')
        ->show();
    }
}
