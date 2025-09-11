<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasCreator;
use Roberts\Support\Traits\HasUpdater;

class Post extends Model
{
    use HasCreator;
    use HasFactory;
    use HasUpdater;

    protected $guarded = [];
}
