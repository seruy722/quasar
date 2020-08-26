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
    public function query()
    {
        return Comment::with('files')
            ->select('comments.*', 'users.name as author_name', 'codes.code as code_client_name')
            ->leftJoin('users', 'users.id', '=', 'comments.author_id')
            ->leftJoin('codes', 'codes.id', '=', 'comments.code_client_id');
    }

    public function getTaskComment($id)
    {
        return response(['comments' => $this->query()->where('task_id', $id)->get()]);
    }

    public function store(Request $request)
    {
        $filesIds = [];
        foreach ($request->allFiles() as $file) {
            $fileName = $file->getClientOriginalName();
            $path = Storage::disk('public')->put('/images', $file);
            $fileData = File::create(['name' => $fileName, 'size' => $file->getSize(), 'ext' => $file->getClientOriginalExtension(), 'path' => $path]);
            array_push($filesIds, $fileData->id);
        }
        $saveArr = [
            'author_id' => auth()->user()->id,
        ];
        if ($request->title) {
            $saveArr['title'] = $request->title;
        }
        if ($request->task_id) {
            $saveArr['task_id'] = $request->task_id;
        }
        if ($request->code_client_id) {
            $saveArr['code_client_id'] = $request->code_client_id;
        }
        if ($request->created_at) {
            $saveArr['created_at'] = date('Y-m-d H:i:s', strtotime($request->created_at));
        }
        $comment = Comment::create($saveArr);
        foreach ($filesIds as $id) {
            CommentFiles::create(['comment_id' => $comment->id, 'file_id' => $id]);
        }

        if ($request->task_id) {
            return response(['comment' => $this->query()->where('task_id', $request->task_id)->where('comments.id', $comment->id)->first()]);
        }

        return response(['comment' => $this->query()->where('task_id', 0)->where('comments.id', $comment->id)->first()]);
    }

    public function getDocumentsComment()
    {
        return response(['documents' => $this->query()->where('task_id', 0)->get()]);
    }

    public function removeCommentFile(Request $request)
    {
        Storage::disk('public')->delete('/' . $request->filePath);
        File::destroy($request->fileId);
        CommentFiles::where('file_id', $request->fileId)->delete();
        return response(['comment' => $this->query()->where('comments.id', $request->commentId)->first()]);
    }

    public function deleteComments(Request $request)
    {
        $ids = $request->ids;
        $comments = $this->query()->whereIn('comments.id', $ids)->get();
        CommentFiles::whereIn('comment_id', $ids)->delete();
        foreach ($comments as $comment) {
            if (!empty($comment->files)) {
                foreach ($comment->files as $file) {
                    Storage::disk('public')->delete('/' . $file->path);
                    File::destroy($file->id);
                }
            }
            $comment->delete();
        }
        return response(['status' => true]);
    }

    public function addFileToComment(Request $request)
    {
        $filesIds = [];
        foreach ($request->allFiles() as $file) {
            $fileName = $file->getClientOriginalName();
            $path = Storage::disk('public')->put('/images', $file);
            $fileData = File::create(['name' => $fileName, 'size' => $file->getSize(), 'ext' => $file->getClientOriginalExtension(), 'path' => $path]);
            array_push($filesIds, $fileData->id);
        }
        $saveArr = [
            'author_id' => auth()->user()->id,
        ];
        if ($request->title) {
            $saveArr['title'] = $request->title;
        }
        if ($request->task_id) {
            $saveArr['task_id'] = $request->task_id;
        }
        if ($request->code_client_id) {
            $saveArr['code_client_id'] = $request->code_client_id;
        }
        if ($request->created_at) {
            $saveArr['created_at'] = date('Y-m-d H:i:s', strtotime($request->created_at));
        }
        foreach ($filesIds as $id) {
            CommentFiles::create(['comment_id' => $request->comment_id, 'file_id' => $id]);
        }

        return response(['comment' => $this->query()->where('task_id', $request->task_id)->where('comments.id', $request->comment_id)->first()]);
    }

    public function updateCommentData(Request $request)
    {
        $saveArr = [
            'author_id' => auth()->user()->id,
        ];
        if ($request->title) {
            $saveArr['title'] = $request->title;
        }
        if ($request->task_id) {
            $saveArr['task_id'] = $request->task_id;
        }
        if ($request->code_client_id) {
            $saveArr['code_client_id'] = $request->code_client_id;
        }
        if ($request->created_at) {
            $saveArr['created_at'] = date('Y-m-d H:i:s', strtotime($request->created_at));
        }

        Comment::where('id', $request->comment_id)->update($saveArr);

        return response(['comment' => $this->query()->where('task_id', $request->task_id)->where('comments.id', $request->comment_id)->first()]);
    }
}
