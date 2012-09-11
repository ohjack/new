<?php

class Create_Spider {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        // spider_log
        Schema::create('spider_log', function($table) {
            $table->increments('id');
            $table->string('type', 20);
            $table->string('mark', 32);
            $table->date('lasttime');
            $table->date('prevtime');
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
        // Schema::drop('spider_log');
    }

}
