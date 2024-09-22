<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Table;

return new class implements Migration
{
    /**
     * Jalankan migrasi
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitees', function (Table $table) {
            $table->id();

            $table->string('name', 50);
            $table->string('phone_number', 50);
            $table->string('invitation_link', 255);
            $table->text('whatsapp_link');

            $table->timeStamp();
        });
    }

    /**
     * Kembalikan seperti semula
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invitees');
    }
};
