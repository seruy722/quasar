<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentFiles extends Model
{
    protected $fillable = ['comment_id', 'file_id'];
}
