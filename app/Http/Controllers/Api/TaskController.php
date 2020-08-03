<?php

namespace App\Http\Controllers\Api;

use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::select('tasks.*', 'users.name as author_name')
            ->leftJoin('users', 'users.id', '=', 'tasks.author_id')
            ->get();
        return response(['tasks' => $tasks]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['author_id'] = auth()->user()->id;
        $task = Task::create($data);

        return response(['task' => $task]);
    }

    public function update(Request $request)
    {
        $data = $request->except('id');
        Task::where('id', $request->id)->update($data);
        $task = Task::select('tasks.*', 'users.name as author_name')
            ->leftJoin('users', 'users.id', '=', 'tasks.author_id')
            ->where('tasks.id', $request->id)
            ->first();
        return response(['task' => $task]);
    }

    public function destroy(Request $request)
    {
        Task::destroy($request->ids);
        return response(['status' => true]);
    }
}
