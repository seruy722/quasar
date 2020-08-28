<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title', 'status', 'author_id', 'responsible_id', 'code_client_id'];

    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'question_comments', 'question_id', 'comment_id');
    }
}
