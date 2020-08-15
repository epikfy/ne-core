<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            $table->integer('type')->nullable();
            $table->string('name',80);
            $table->string('email',80)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('created_by')->nullable();
            $table->string('nationality')->nullable();
            $table->string('legal_id')->nullable();
            $table->string('mobil_phone')->nullable();
            $table->string('status')->nullable();
            $table->string('notes')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
