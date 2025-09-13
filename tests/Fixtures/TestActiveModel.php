<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasActiveStatus;

class TestActiveModel extends Model
{
    use HasActiveStatus;

    protected $table = 'test_active_models';

    protected $fillable = ['name'];
}
