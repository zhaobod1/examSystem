<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class PaperQuestion extends Model
{
    //
    protected $table = 'paper_questions';

    protected $primaryKey = 'quest_id';

    protected $guarded = [];

    public $timestamps = false;
}
