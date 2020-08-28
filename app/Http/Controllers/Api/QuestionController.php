<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use App\CommentFiles;
use App\File;
use App\Question;
use App\QuestionComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function query()
    {
        return Question::with(['comments' => function ($query) {
            $query->select('comments.*', 'users.name as author_name')
                ->leftJoin('users', 'users.id', '=', 'comments.author_id');
        }, 'comments.files'])
            ->select('questions.*', 'users.name as author_name', 'codes.code as code_client_name', 'responsible.name as responsible_name')
            ->leftJoin('users', 'users.id', '=', 'questions.author_id')
            ->leftJoin('users as responsible', 'responsible.id', '=', 'questions.responsible_id')
            ->leftJoin('codes', 'codes.id', '=', 'questions.code_client_id');
    }

    public function index()
    {
        return response(['questions' => $this->query()->get()]);
    }

    public function store(Request $request)
    {
        $saveData = ['author_id' => auth()->user()->id, 'code_client_id' => $request->code_client_id, 'title' => $request->title, 'status_id' => $request->status_id, 'responsible_id' => $request->responsible_id];
        $question = Question::create($saveData);

        return response(['question' => $this->query()->where('questions.id', $question->id)->first()]);
    }

    public function updateQuestion(Request $request)
    {
        Question::where('id', $request->id)->update($request->except('id'));
        return response(['question' => $this->query()->where('questions.id', $request->id)->first()]);
    }

    public function deleteQuestions(Request $request)
    {
        $questionIds = $request->ids;
        $questions = $this->query()->whereIn('questions.id', $questionIds)->get();

        foreach ($questions as $question) {
            QuestionComment::where('question_id', $question->id)->delete();
            foreach ($question->comments as $comment) {
                CommentFiles::where('comment_id', $comment->id)->delete();
                foreach ($comment->files as $file) {
                    Storage::disk('public')->delete('/' . $file->path);
                    File::destroy($file->id);

                }
                $comment->delete();
            }
            $question->delete();
        }
        return response()->json(null, 201);
    }

    public function addQuestionComment(Request $request)
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
        if ($request->code_client_id) {
            $saveArr['code_client_id'] = $request->code_client_id;
        }
        if ($request->question_id) {
            $saveArr['question_id'] = $request->question_id;
        }
        $comment = Comment::create($saveArr);
        foreach ($filesIds as $id) {
            CommentFiles::create(['comment_id' => $comment->id, 'file_id' => $id]);
        }
        QuestionComment::create(['comment_id' => $comment->id, 'question_id' => $request->question_id]);

        return response(['question' => $this->query()->where('questions.id', $request->question_id)->first()]);
    }
}
