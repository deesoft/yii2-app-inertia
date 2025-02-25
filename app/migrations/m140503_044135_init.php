<?php

use yii\db\Migration;

/**
 * Class m180803_054135_init_user
 */
class m140503_044135_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string(64),
            'fullname' => $this->string(),
            'avatar' => $this->string(),
            // status
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp(),
            'created_by' => $this->integer(),
            'updated_at' => $this->timestamp(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->createTable('open_auth', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string(128),
            'source_id' => $this->string(128),

            'FOREIGN KEY ([[user_id]]) REFERENCES {{user}} ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $dataType = $this->binary();
        switch ($this->db->driverName) {
            case 'sqlsrv':
            case 'mssql':
            case 'dblib':
                $dataType = $this->text();
                break;
        }

        $this->createTable('cache', [
            'id' => $this->string(128)->notNull(),
            'expire' => $this->integer(),
            'data' => $dataType,

            'PRIMARY KEY ([[id]])',
        ], $tableOptions);

        $this->createTable('session', [
            'id' => $this->string()->notNull(),
            'expire' => $this->integer(),
            'data' => $dataType,
            
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('session');
        $this->dropTable('cache');
        $this->dropTable('open_auth');
        $this->dropTable('user');
    }
}
