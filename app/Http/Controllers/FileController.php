<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $disk = Storage::disk('gcs');
        } catch (\Throwable $th) {
            //throw $th;
            $disk = null;
        }
        if($disk){
            $metadata = $disk->get($this->folder . '/metadata.json');
        }
        $metadata = json_decode($metadata ?? '[]', true);

        $filetree = [];
        foreach ($metadata as $key => $value) {
            $path = $value['path'];
            $path = explode('/', $path);
            
            //make nested array from path

            $temp = &$filetree;
            $count = count($path);
            foreach ($path as $dir) {
                $count--;
                if($dir == '') {
                    continue;
                }

                if ($count == 0) {
                    $temp['children'][] = $value;
                } else {
                    if(!isset($temp['children'][$dir])) {
                        $temp['children'][$dir] = [
                            'name' => $dir,
                            'type' => 'folder',
                            'children' => []
                        ];
                    }
                    $temp = &$temp['children'][$dir];
                }
            }
        }

        // var_dump($filetree);die;
        return view('file', compact('filetree'));
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
        //
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

    public function download(Request $request){
        $path = $request->input('path');
        $disk = Storage::disk('gcs');
        $fullpath = $this->folder . '/' . $path;
        //remove double slashes
        $fullpath = preg_replace('/\/+/', '/', $fullpath);
        $exists = $disk->exists($fullpath);
        if($exists){
            header('Access-Control-Expose-Headers: Content-Disposition');
            return $disk->download($fullpath);
        }
        return response()->json(['status' => 'error', 'message' => 'File not found']);
    }

    public function view(Request $request){
        $path = $request->input('path');
        $disk = Storage::disk('gcs');
        $fullpath = $this->folder . '/' . $path;
        //remove double slashes
        $fullpath = preg_replace('/\/+/', '/', $fullpath);
        $exists = $disk->exists($fullpath);
        if($exists){
            //get file content
            $url = $disk->temporaryUrl($fullpath, now()->addMinutes(5));

            return redirect($url);
        }
        //return view 404
        return abort(404);
    }
}
