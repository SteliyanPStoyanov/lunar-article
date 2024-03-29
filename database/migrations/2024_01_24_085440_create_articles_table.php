<?php

use Lunar\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->prefix . 'articles', function (Blueprint $table) {
            $table->id();
            $table->string('status')->index();
            $table->string('title')->index();
            $table->text('description');
            $table->json('attribute_data');
            $table->foreignId('article_category_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix.'articles');
    }
};
