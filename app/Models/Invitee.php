<?php

namespace App\Models;

use Core\Model\Model;

final class Invitee extends Model
{
    protected $table = 'invitees';

    protected $fillable = [
        'name',
        'phone_number',
        'invitation_link',
        'whatsapp_link',
    ];

}
