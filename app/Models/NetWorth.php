<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NetWorth extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'institution',
        'notes',
        'year',
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
        'net_worth',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'net_worth',
    ];

    /**
     * Define relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
