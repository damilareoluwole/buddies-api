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
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('otp')->nullable();
            $table->string('verified_at')->nullable();
            $table->tinyInteger('onboardingInitiate')->default(false);
            $table->tinyInteger('onboardingOtp')->default(false);
            $table->tinyInteger('onboardingPrivacy')->default(false);
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