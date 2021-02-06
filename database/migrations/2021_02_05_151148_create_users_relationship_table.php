<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_relationship', function (Blueprint $table) {
            $table->unsignedBigInteger('user_sender_id');
            $table->unsignedBigInteger('user_receiver_id');
            $table->tinyInteger('status');
            $table->primary(['user_sender_id', 'user_receiver_id'], 'sender_id_receiver_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_relationship');
    }
}
