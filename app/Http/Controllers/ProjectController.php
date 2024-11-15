<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ImportStoreRequest;
use App\Jobs\ImportProjectExcelFileJob;
use App\Models\File;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        return inertia('Project/Index');
    }

    public function import()
    {
        return inertia('Project/Import');
    }

    public function importStore(ImportStoreRequest $request)
    {
        $data = $request->validated();

        // $file = Storage::disk('public')->put('/files', $data['file']);
        // File::create([
        //     'path' => $file,
        //     'mime_type' => $data['file']->getClientOriginalExtension(),
        //     'title' => $data['file']->getClientOriginalExtension()
        // ]);
        $file = File::putAndCreate($data['file']);
        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => auth()->id(),

        ]);
        ImportProjectExcelFileJob::dispatchSync($file->path, $task);
    }
}
