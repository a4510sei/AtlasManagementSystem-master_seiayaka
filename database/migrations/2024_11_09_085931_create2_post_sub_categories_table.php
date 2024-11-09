<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create2PostSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_sub_categories', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->comment('id');
            $table->integer('post_id')->index()->references('id')->on('post')->onDelete('cascade')->comment('投稿のid');
            $table->integer('sub_category_id')->index()->references('id')->on('sub_categories')->onDelete('cascade')->comment('サブカテゴリーid');
            $table->timestamp('created_at')->nullable()->comment('登録日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_sub_categories');
    }
}
