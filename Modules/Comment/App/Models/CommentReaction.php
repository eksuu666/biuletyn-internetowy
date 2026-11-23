<?php

namespace Modules\Comment\App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentReaction extends Model
{
    protected $fillable = ['comment_id', 'actor', 'reaction'];
}
