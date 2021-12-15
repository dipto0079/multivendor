<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    //
    public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function getQuestion(){
        return $this->belongsTo(Question::class,'question_id');
    }
}
