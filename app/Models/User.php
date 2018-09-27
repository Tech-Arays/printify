<?php

namespace App\Models;;

use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    const STATUS_NEW    = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_BANNED = 'banned';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'two_factor_reset_code',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function setPassword($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function createUser()
    {
        $this->status = static::STATUS_NEW;
        $result = $this->save();

        \Event::fire(new \App\Events\User\UserCreatedEvent($this));

        return $result;
    }

    
    public function isOwnerOf($related)
    {
        return $this->id == $related->user_id;
    }

    public function isAdmin()
    {
        $emails = explode(',', getenv('ADMIN_USERNAMES'));
        return in_array($this->email, $emails);
    }

    public static function getNotBanned()
    {
        return static::where('status', '!=', static::STATUS_BANNED)
            ->get();
    }

    /************
     * Decorators
     */

    public function getStatusName()
    {
        return static::statusName($this->status);
    }

    public function getName()
    {
        return $this->first_name.' '.$this->last_name;
    }
    
}
