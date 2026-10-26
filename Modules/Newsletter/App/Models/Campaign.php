<?php

namespace Modules\Newsletter\App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'newsletter_campaigns';

    protected $fillable = ['user_id', 'subject', 'body', 'status'];
}
