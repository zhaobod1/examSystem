<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class QuestConfig extends Model
{
    //
	//
	protected $table = 'QuestConfig';

	protected $primaryKey = 'id';

	protected $guarded = [];

	public $timestamps = false;
}
