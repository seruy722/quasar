<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use App\CommentFiles;
use App\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function query($id)
    {
        return Comment::with('files')
            ->select('comments.*', 'users.name as author_name')
            ->leftJoin('users', 'users.id', '=', 'comments.author_id')
            ->where('task_id', $id);
    }

    public function index($id)
    {
        return response(['comments' => $this->query($id)->get()]);
    }

    public function store(Request $request)
    {
        $filesIds = [];
        foreach ($request->allFiles() as $file) {
            $fileHash = 'img_' . date("Y-m-d") . '_' . microtime(true);
            $path = Storage::disk('public')->put('/images', $file);
            $fileData = File::create(['name' => $fileHash, 'size' => $file->getSize(), 'ext' => $file->getClientOriginalExtension(), 'path' => $path]);
            array_push($filesIds, $fileData->id);
        }
        $comment = Comment::create(['title' => $request->title, 'task_id' => $request->task_id, 'author_id' => auth()->user()->id]);
        foreach ($filesIds as $id) {
            CommentFiles::create(['comment_id' => $comment->id, 'file_id' => $id]);
        }
        return response(['comment' => $this->query($request->task_id)->where('comments.id', $comment->id)->first()]);
    }
}
