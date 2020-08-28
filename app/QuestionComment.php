<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionComment extends Model
{
    protected $fillable = ['comment_id', 'question_id'];
}
