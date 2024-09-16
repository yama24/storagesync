<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Native\Laravel\Facades\Notification;

class SettingController extends Controller
{
    public $dir;

    public function __construct()
    {
        //get linux user
        // $user = exec('whoami');
        $home = exec('echo $HOME');
        $this->dir = $home;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();

        $setting = [];
        foreach ($settings as $s) {
            $setting[$s->name] = $s->value;
        }

        // dd($setting);

        // Example usage
        $folders = getFolderRecursive($this->dir, true);

        $home = $this->dir;

        return view('setting', compact('setting', 'folders', 'home'));
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
        // dd($request->all());
        $settings = Setting::all();
        $settings = $settings->keyBy('name');
        foreach ($request->all() as $key => $value) {
            if (!$settings->has($key)) {
                Setting::create([
                    'name' => $key,
                    'value' => $value,
                    'is_boolean' => true,
                ]);
            }
        }

        foreach ($settings as $s) {
            $s->update([
                'value' => $request->input($s->name) ?? '',
            ]);
        }

        Notification::title('Notification Storage Sync')
        ->message('Settings updated!')
        ->show();

        return redirect()->route('setting.index')->with('status', 'Settings updated!');
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

    public function folder(Request $request)
    {
        $folder = $request->input('folder');
        $folder = $this->dir . DIRECTORY_SEPARATOR . $folder;

        $folders = getFolderRecursive($folder, true);

        $data = [
            'status' => true,
            'folders' => $folders,
        ];
        return response()->json($data);
    }
}
