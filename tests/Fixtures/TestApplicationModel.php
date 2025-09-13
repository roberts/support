<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasApplicationStatus;

class TestApplicationModel extends Model
{
    use HasApplicationStatus;

    protected $table = 'test_application_models';

    protected $fillable = ['name', 'status'];
}
