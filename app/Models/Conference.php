<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Country;

class Conference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'date',
        'latitude',
        'longitude',
        'country_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function isUserAttached(User $user)
    {
        return $this->users->contains($user);
    }
}
