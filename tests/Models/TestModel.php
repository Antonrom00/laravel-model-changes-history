<?php

namespace Antonrom\ModelChangesHistory\Tests\Models;

use Antonrom\ModelChangesHistory\Traits\HasChangesHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestModel extends Model
{
    use HasChangesHistory, SoftDeletes;

    protected $fillable = ['title', 'body'];

    protected $guarded = [];
}
