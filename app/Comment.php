<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['title', 'task_id', 'author_id', 'code_client_id'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'comment_files', 'comment_id', 'file_id');
    }
}
