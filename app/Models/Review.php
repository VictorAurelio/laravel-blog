<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
      'rate',
      'commentary',
      'user_id',
      'post_id'
    ];

    public function post() {
      return $this->belongsTo(Post::class);
    }

    public function user() {
      return $this->belongsTo(User::class);
    }
}
