<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $table = 'enquiries';

    protected $fillable = ['full_name', 'phone_no', 'email_id', 'subject', 'message', 'status'];
}
