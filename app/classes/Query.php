<?php

namespace app\classes;

use yii\db\Query as BaseQuery;

/**
 * Description of Query
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Query extends BaseQuery
{
    /**
     *
     * @var array
     */
    public $jsonColumns = [];
    /**
     * 
     * @var \Closure
     */
    public $populateCallback;

    /**
     * {@inheritDoc}
     */
    public function populate($rows): array
    {
        $rows = parent::populate($rows);
        if ($this->jsonColumns) {
            $rows = array_map([$this, 'jsonMap'], $rows);
        }
        if ($this->populateCallback !== null) {
            return array_map($this->populateCallback, $rows);
        } 
        return $rows;
    }

    protected function jsonMap($row)
    {
        foreach ($this->jsonColumns as $field) {
            if (isset($row[$field]) && is_string($row[$field]) &&
                (($v = json_decode($row[$field], true)) !== null || $row[$field] === 'null')) {
                $row[$field] = $v;
            }
        }
        return $row;
    }

    /**
     * {@inheritDoc}
     */
    public function one($db = null)
    {
        $row = parent::one($db);
        if ($row) {
            if ($this->jsonColumns) {
                $row = $this->jsonMap($row);
            }
            if ($this->populateCallback !== null) {
                return call_user_func($this->populateCallback, $row);
            }
        }
        return $row;
    }
}
