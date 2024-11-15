<?php

namespace App\Http\Controllers;

use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user', 'file')->get();
        $tasks = TaskResource::collection($tasks)->resolve();

        return inertia('Task/Index', compact('tasks'));
    }
}
