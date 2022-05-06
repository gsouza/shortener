<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreationShorttenLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // unikID original_url title access created_at
    public function up()
    {
      Schema::create('link_table', function (Blueprint $table) {
        $table->id();

        $table->string('unique_id');
        $table->string('original_link');
        $table->string('title');
        $table->integer('access');

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
      Schema::dropIfExists('link_table');
    }
}
