<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

use App\Models\Conference;
use App\Models\Comment;
use App\Models\User;

use Carbon\Carbon;

class Lecture extends Model
{
    use HasFactory;

    const MIN_DURATION = 5;
    const MAX_DURATION = 60;

    const TYPE = 'lecture';

    const WAITING_STATUS = 'Waiting';
    const STARTED_STATUS = 'Started';
    const ENDED_STATUS = 'Ended';

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
        'category_id',
        'is_online',
        'zoom_meeting_id',
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status'];

    /**
     * Get the lecture's status.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                $lectureStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->lecture_start);
                $lectureEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->lecture_end);
                $now = Carbon::now();

                if ($now->lt($lectureStartDate)) {
                    return Lecture::WAITING_STATUS;
                }
                if ($now->gt($lectureEndDate)) {
                    return Lecture::ENDED_STATUS;
                }

                return Lecture::STARTED_STATUS;
            },
        );
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
