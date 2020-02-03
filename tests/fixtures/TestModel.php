<?php

namespace Antonrom\ModelChangesHistory\Tests\fixtures;

use Antonrom\ModelChangesHistory\Traits\HasChangesHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestModel extends Model
{
    use HasChangesHistory, SoftDeletes;

    protected $fillable = ['title', 'body', 'password'];

    protected $hidden = ['password'];

    protected $guarded = [];

    public $timestamps = false;
}
