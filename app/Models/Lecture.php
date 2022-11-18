<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Comment;
use App\Models\Conference;

class Lecture extends Model
{
    use HasFactory;

    const MIN_DURATION = 5;
    const MAX_DURATION = 60;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'lecture_start',
        'lecture_end',
        'hash_file_name',
        'original_file_name',
        'conference_id',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lecture_start' => 'datetime',
        'lecture_end' => 'datetime',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
