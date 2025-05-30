<?php

namespace Database\Migrations;

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
        Schema::create(
            'url_checks',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('url_id')->constrained('urls');
                $table->integer('status_code')->nullable();
                $table->text('h1')->nullable();
                $table->text('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamp('created_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_checks');
    }
};
