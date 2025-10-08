<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySetting extends Model
{
    use HasFactory;
    protected $table = 'delivery_settings';
    protected $fillable = ['key', 'value'];
}
