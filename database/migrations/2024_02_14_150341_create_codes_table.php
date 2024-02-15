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
        Schema::create('codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email', 100)->unique();
            $table->text('code');
            $table->integer('is_validated')->index()->comment('No=0 / Si=1')->default(0);
            $table->integer('status')->index()->comment('Inactive=0 / Active=1')->default(1);
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
        Schema::dropIfExists('codes');
    }
};
