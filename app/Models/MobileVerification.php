<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MobileVerification extends Model
{
    /**
     * the code expiration by seconds.
     * @const int
     */
    const EXPIRE_DURATION = 4 * 60;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'phone_number',
        'code',
    ];

    /**
     * Check if this code has been expired.
     *
     * @return bool
     */
    public function isExpired()
    {
      return $this->updated_at->addSeconds(static::EXPIRE_DURATION)->isPast();
    }

    /**
     * Return the  remaining time until the code expire by seconds.
     *
     * @return int
     */
    public function remainingUntilCodeExpireBySec()
    {
      return Carbon::now()->diffInSeconds($this->updated_at->addSeconds(static::EXPIRE_DURATION), false);
    }

}
