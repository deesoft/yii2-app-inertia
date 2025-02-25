<?php

use yii\db\Migration;

/**
 * Class m180803_044137_init_queue
 */
class m180803_044137_init_queue extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('queue', [
            'id' => $this->primaryKey(),
            'channel' => $this->string()->notNull(),
            'job' => $this->binary()->notNull(),
            'pushed_at' => $this->integer()->notNull(),
            'ttr' => $this->integer()->notNull(),
            'delay' => $this->integer()->notNull(),
            'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
            'reserved_at' => $this->integer(),
            'attempt' => $this->integer(),
            'done_at' => $this->integer(),
            ], $tableOptions);

        $this->createIndex('channel', 'queue', 'channel');
        $this->createIndex('priority', 'queue', 'priority');
        $this->createIndex('reserved_at', 'queue', 'reserved_at');
    }

    public function down()
    {
        $this->dropTable('queue');
    }
}
