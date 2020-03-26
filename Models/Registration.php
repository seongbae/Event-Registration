<?php

namespace App\Modules\Event\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Event\Models\Event;

class Registration extends Model
{
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
