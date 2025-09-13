<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasApprovalStatus;

class TestApprovalModel extends Model
{
    use HasApprovalStatus;

    protected $table = 'test_approval_models';

    protected $fillable = ['name', 'status'];
}
