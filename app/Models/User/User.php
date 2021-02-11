<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'User';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = [
        'id',
        'companyId',
        'email',
        'password',
        'indicMobile',
        'mobile',
        'emailValidated',
        'emailValidatedExp',
        'resetPassword',
        'resetPasswordExp',
        'lastLogin'
    ];

    /*
     * Relation
     */
    public function data()
    {
        return $this->hasOne(UserData::class, 'userId');
    }

    /*
     * Methodes
     */
    public static function get(int $id)
    {
        return self::where('id', $id)
            ->first();
    }
}
