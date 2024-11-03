<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PostSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('post_sub_category', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->comment('id');
            $table->integer('post_id')->references('id')->on('post')->onDelete('cascade')->comment('投稿のid')->change(); ;
            $table->integer('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade')->comment('サブカテゴリーid')->change(); ;
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
        //
        Schema::dropIfExists('post_sub_category');
    }
}
