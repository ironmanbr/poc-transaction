<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'document', 'wallet'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    /**
     * @param $value
     */
    public function setDocumentAttribute($value)
    {
        $this->attributes['document'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Checks if it is a store just by checking the size of the document, recommended only for the MVP.
     * @return bool
     */
    public function isStore(): bool
    {
        return strlen($this->attributes['document']) >= 14;
    }

    public function hasBalance($value)
    {
        return ((float) $this->attributes['wallet']) >= $value;
    }
}
