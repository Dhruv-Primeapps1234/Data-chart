<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetUser extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'last_login', 'created_at', 'updated_at'];
}
