<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
    protected $fillable = ['source_type','line_user_id','line_room_id','line_group_id','user_id'];
}
