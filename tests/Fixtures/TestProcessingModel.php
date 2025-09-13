<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasProcessingStatus;

class TestProcessingModel extends Model
{
    use HasProcessingStatus;

    protected $table = 'test_processing_models';

    protected $fillable = ['name', 'status'];
}
