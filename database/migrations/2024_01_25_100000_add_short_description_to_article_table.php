<?php

use Lunar\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table($this->prefix.'articles', function (Blueprint $table) {
            $table->text('short_description')->nullable();
        });
    }

    public function down()
    {
        Schema::table($this->prefix.'articles', function ($table) {
            $table->dropColumn('short_description');
        });
    }
};
