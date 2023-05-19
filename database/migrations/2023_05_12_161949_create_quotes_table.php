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
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('first_title', 255);
            $table->string('second_title', 255);
            $table->string('message', 255);
            $table->integer('number_quote');
            $table->integer('status')->index()->comment('Deleted=0 / Active=1 / Inactive=2')->default(1);
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
