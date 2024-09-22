<?php

namespace App\Request;

use Core\Valid\Form;

class InsertInviteeRequest extends Form
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'phone_number' => ['required'],
            'base_url' => ['required'],
            "type" => ['required']
        ];
    }
}
