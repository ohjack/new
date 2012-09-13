<?php

class Create_Mapping {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        // mapping
        Schema::create('sku_map', function($table) {
            $table->increments('id');
            $table->string('original_sku', 60);
            $table->string('target_sku', 60);
            $table->string('product_name', 60);      // 品名
            $tbale->decimal('product_price', 10, 2); // 价值
            $table->string('logistics', 20);
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        // reset
        Schema::drop('sku_map');
    }

}
