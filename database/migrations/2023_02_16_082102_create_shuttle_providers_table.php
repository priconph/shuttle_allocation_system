<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShuttleProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shuttle_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shuttle_provider_name');
            $table->integer('shuttle_provider_capacity');
            $table->string('shuttle_provider_status')->nullable()->default(1)->comment = '0-Not Active, 1-Active';

           // Defaults
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->tinyInteger('is_deleted')->nullable()->default(0)->comment = '0-Active, 1-Deleted';
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
        Schema::dropIfExists('shuttle_providers');
    }
}
