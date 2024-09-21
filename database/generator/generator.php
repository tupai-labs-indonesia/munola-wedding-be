<?php

use Core\Database\Generator;
use App\Models\User;
use Core\Valid\Hash;

return new class implements Generator
{
    /**
     * Generate nilai database
     *
     * @return void
     */
    public function run()
    {
        $user = User::find('fikri.backend@gmail.com', 'email');

        if (!$user->exist()) {
            $user = User::create([
                'name' => 'Fikri',
                'email' => 'fikri.backend@gmail.com',
                'password' => Hash::make('tupainibrad')
            ]);
        }


        $user->fill([
            'is_filter' => true,
            'access_key' => Hash::rand(25),
        ])->save();
    }
};
