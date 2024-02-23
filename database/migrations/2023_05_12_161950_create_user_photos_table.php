<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_photos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'fk_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('photo_id');
            $table->foreign('photo_id', 'fk_photo_id')->references('id')->on('photos');

            $table->unsignedBigInteger('quote_id');
            $table->foreign('quote_id', 'fk_quote_id')->references('id')->on('quotes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_photos');
    }
};
