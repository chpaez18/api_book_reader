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
        Schema::create('user_quotes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'fk_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('quote_id');
            $table->foreign('quote_id', 'fk_quote_id')->references('id')->on('quotes');

            $table->integer('is_completed')->default(0);
            $table->string('mood', 255)->nullable();
            $table->text('quote_description')->nullable();
            $table->text('words')->nullable();

            $table->unsignedBigInteger('photo_id')->nullable();
            $table->foreign('photo_id', 'fk_photo_id')->references('id')->on('photos');

            $table->integer('status')->index()->comment('Deleted=0 / Active=1 / Inactive=2')->default(1);
            $table->date('date')->nullable();

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
        Schema::dropIfExists('user_quotes');
    }
};
