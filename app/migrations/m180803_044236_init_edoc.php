<?php

use yii\db\Migration;

/**
 * Class m180803_044236_init_edoc
 */
class m180803_044236_init_edoc extends Migration
{
    public $db = 'dbFile';

    public function up()
    {
        $tableOptions = null;
        $contentType = $this->text();
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $contentType = 'longtext';
        }

        $this->createTable('file', [
            'id' => $this->string(16)->notNull(),
            'type' => $this->string(128)->comment('mime type'),
            'name' => $this->string()->comment('original name'),
            'content' => $this->binary(),
            // status
            'created_at' => $this->timestamp(),
            'created_by' => $this->integer(),
            'updated_at' => $this->timestamp(),
            'updated_by' => $this->integer(),
            
            'PRIMARY KEY ([[id]])'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('file');
    }
}
