<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_status')->default(0)->comment = '0 - Active, 1 - Expired';
            $table->string('request_type')->default(0)->comment = '1 - Change Schedule, 2 - Shutdown, 3 - Undertime';
            $table->date('alloc_date_start')->nullable();
            $table->date('alloc_date_end')->nullable();
            $table->unsignedBigInteger('requestee_ml_id')->nullable();
            $table->string('alloc_factory')->nullable();
            $table->string('alloc_incoming')->nullable();
            $table->string('alloc_outgoing')->nullable();
            $table->string('alloc_routes_id')->nullable()->comment = 'routes(table) id';
           // Defaults
            $table->unsignedBigInteger('requested_by')->nullable();
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
        Schema::dropIfExists('allocations');
    }
}
