<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';

    protected $primaryKey = 'question_id';

    protected $guarded = [];

    public $timestamps = false;
}
