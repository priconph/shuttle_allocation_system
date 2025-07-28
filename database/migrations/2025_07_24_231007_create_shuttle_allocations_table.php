<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShuttleAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shuttle_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_status')->nullable()->default(1)->comment = '0-Not Active, 1-Active';
            $table->string('employee_number')->nullable();
            $table->string('employee_type')->nullable()->comment = '1-Pricon, 2-Subcon';
            $table->string('alloc_incoming')->nullable();
            $table->string('alloc_outgoing')->nullable();
            $table->string('routes_id')->nullable()->comment = 'routes(table) id';
            $table->string('systemone_hris_id')->nullable()->comment = 'db_hris tbl_EmployeeInfo(table) id';
            $table->string('systemone_subcon_id')->nullable()->comment = 'db_subcon tbl_EmployeeInfo(table) id';

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
        Schema::dropIfExists('shuttle_allocations');
    }
}
