<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    use HasFactory;

    protected $table = 'Consumer';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = [
        'id',
        'name',
        'ip',
        'token'
    ];

    public function checkIpAuthorized(string $ip): bool
    {
        $authorizedIps = json_decode($this->ip);
        if (array_search('*', $authorizedIps) === false &&
            array_search($ip, $authorizedIps) === false) {
            return false;
        }
        return true;
    }
}
