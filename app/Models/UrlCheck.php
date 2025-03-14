<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlCheck extends Model
{
    use HasFactory;

    protected $fillable = ['url_id', 'status_code', 'h1', 'title', 'description', 'created_at'];

    public $timestamps = false;
}
