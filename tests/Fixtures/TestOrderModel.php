<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasOrderStatus;

class TestOrderModel extends Model
{
    use HasOrderStatus;

    protected $table = 'test_order_models';

    protected $fillable = ['name'];
}
