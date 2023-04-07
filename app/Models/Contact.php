<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'patronymic',
        'birthdate',
    ];

    public function numbers() : HasMany
    {
        return $this->hasMany(Number::class);
    }

    public function emails() : HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
