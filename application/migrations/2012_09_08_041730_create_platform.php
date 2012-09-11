<?php

class Create_Platform {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        // platform
        Schema::create('platform', function($table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('option', 300);
        });

        // users_platform
        Schema::create('users_platform', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('platform_id');
            $table->string('option', 300);
            $table->date('created_at');
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
        //Schema::drop('platform');
        //Schema::drop('users_platform');
    }

}
