<?php

use yii\db\Migration;

/**
 * Class m180803_053808_init_audit
 */
class m180803_053808_init_audit extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('audit_trail', [
            'id' => $this->bigPrimaryKey(),
            'table' => $this->string(64)->notNull(),
            'row_id' => $this->integer(),
            'row_id_str' => $this->string(64),
            'action' => $this->string(64)->notNull(), // CREATE, UPDATE, DELETE,
            'time' => $this->timestamp(),
            'user_id' => $this->integer(),
            'note' => $this->string(),
            'before' => $this->json(),
            'after' => $this->json(),
            ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('audit_trail');
    }
}
