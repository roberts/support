<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasPublishingStatus;

class TestPublishingModel extends Model
{
    use HasPublishingStatus;

    protected $table = 'test_publishing_models';

    protected $fillable = ['name', 'status'];
}
