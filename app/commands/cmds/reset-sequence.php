<?php
/**
 * Reset sequence table(s)
 */
$db = Yii::$app->db;
$command = $db->createCommand();
$tables = empty($params) ? $db->schema->getTableNames() : $params;
foreach ($tables as $tableName) {
    $tableSchema = $db->getTableSchema($tableName);
    if ($tableSchema && $tableSchema->sequenceName) {
        $this->stdout("Reset sequence {$tableName}...\n");
        try {
            $command->resetSequence($tableName)->execute();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}