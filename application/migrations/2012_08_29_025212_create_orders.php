<?php

class Create_Orders {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        // orders
        Schema::create('orders', function($table) {
            $table->increments('id');
            $table->string('entry_id', 30);
            $table->string('name', 60);
            $table->string('email', 255);
            $table->string('market_id', 20);
            $table->decimal('total', 10, 2);
            $table->string('currency', 6);
            $table->string('shipping_name', 60);
            $table->string('shipping_phone', 60);
            $table->string('shipping_country', 20);
            $table->string('shipping_state_or_region', 20);
            $table->string('shipping_city', 20);
            $table->string('shipping_address1', 60);
            $table->string('shipping_address2', 60);
            $table->string('shipping_address3', 60);
            $table->string('shipping_postal_code', 20);
            $table->string('ship_level', 20);
            $table->string('shipment_level', 20);
            $table->string('fulfillment', 20);
            $table->boolean('shipped_by_amazon_tfm');
            $table->string('payment_method', 10);
            $table->string('from', 30);
            $table->string('status', 10);
            $table->string('order_status', 10);
            $table->date('created_at');
        });

        // items
        Schema::create('items', function($table) {
            $table->increments('id');
            $table->string('entry_id', 30); 
            $table->integer('order_id');
            $table->string('name', 255);
            $table->string('sku', 60);
            $table->decimal('price', 10, 2);
            $table->string('currency', 6);
            $table->integer('quantity');
            $table->decimal('shipping_price', 10, 2);
            $table->string('shipping_currency', 6);
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
        Schema::drop('orders');
        Schema::drop('items');
    }

}
