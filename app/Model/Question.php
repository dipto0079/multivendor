<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // Get Question Answer
    public function getQuestionAnswer(){
        return $this->hasMany(QuestionAnswer::class,'question_id');
    }

    // Get User
    public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }

}
