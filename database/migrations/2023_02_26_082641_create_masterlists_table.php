<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masterlists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('masterlist_employee_number')->nullable();
            $table->string('masterlist_employee_type')->nullable()->comment = '1-Pricon, 2-Subcon';
            $table->string('masterlist_status')->nullable()->default(1)->comment = '0-Not Active, 1-Active';
            $table->string('masterlist_incoming')->nullable();
            $table->string('masterlist_outgoing')->nullable();
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
        Schema::dropIfExists('masterlists');
    }
}
