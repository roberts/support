<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasModeratorStatus;

class TestModeratorModel extends Model
{
    use HasModeratorStatus;

    protected $table = 'test_moderator_models';

    protected $fillable = ['name'];
}
