<?php

use yii\db\Migration;

/**
 * Class m180803_044136_init_system
 */
class m180803_044136_init_system extends Migration
{
    public function up()
    {
        $tableOptions = null;
        $contentType = $this->text();
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $contentType = 'longtext';
        }

        $this->createTable('lookup', [
            'group' => $this->string(64),
            'key' => $this->string(64),
            'value' => $this->string(),
            'description' => $this->string()->null(),

            'PRIMARY KEY ([[group]], [[key]])',
        ], $tableOptions);

        $this->createTable('auto_number', [
            'id' => $this->string(32)->notNull(),
            'counter' => $this->integer()->defaultValue(1),
            'updated_at' => $this->timestamp(),
            // primary ke
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);

        $this->createTable('socket_io', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string()->notNull(),
            'room' => $this->string(),
            'payload' => $this->json(),
            'key' => $this->bigInteger(),
            'expire' => $this->bigInteger(),
        ], $tableOptions);

        $this->createTable('notification', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->string(),
            'url' => $this->string(),
            'data' => $this->json(),
            'time' => $this->timestamp(),
            'is_read' => $this->boolean()->notNull()->defaultValue(false),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('notification');
        $this->dropTable('socket_io');
        $this->dropTable('auto_number');
        $this->dropTable('lookup_str');
        $this->dropTable('lookup');
    }
}
