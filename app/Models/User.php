<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Conference;
use App\Models\Country;
use App\Models\Lecture;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ACCESS_TOKEN = 'access_token';

    const ADMIN_TYPE = 'admin';
    const LISTENER_TYPE = 'listener';
    const ANNOUNCER_TYPE = 'announcer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'type',
        'birthdate',
        'country_id',
        'phone',
        'email',
        'password',
        'zoom_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date'
    ];

    public function conferences()
    {
        return $this->belongsToMany(Conference::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function favoriteLectures()
    {
        return $this->belongsToMany(Lecture::class);
    }

    public function inFavoriteLectures($lectureId)
    {
        return $this->favoriteLectures->contains($lectureId);
    }

    public function isAdmin()
    {
        return $this->type === User::ADMIN_TYPE;
    }

    public function isListener()
    {
        return $this->type === User::LISTENER_TYPE;
    }

    public function isAnnouncer()
    {
        return $this->type === User::ANNOUNCER_TYPE;
    }
}
