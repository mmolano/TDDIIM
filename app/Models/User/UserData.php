<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'UserData';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = [
        'userId',
        'firstName',
        'lastName',
        'dateOfBirth',
        'gender',
        'language'
    ];

    /*
     * Relation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
