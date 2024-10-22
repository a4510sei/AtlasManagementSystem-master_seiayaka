<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePostSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('post_sub_categories', function (Blueprint $table){
            $table->integer('post_id')->references('id')->on('post')->onDelete('cascade')->comment('投稿のid')->change(); ;
            $table->integer('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade')->comment('サブカテゴリーid')->change(); ;
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
        Schema::dropIfExists('post_sub_categories');
    }
}
