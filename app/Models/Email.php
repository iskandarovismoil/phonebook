<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_id',
        'email'
    ];

    public function contact() : BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
