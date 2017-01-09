<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class PaperInfo extends Model
{
    //
    protected $table = 'paper_info';

    protected $primaryKey = 'paper_id';

    protected $guarded = [];

    public $timestamps = false;
}
