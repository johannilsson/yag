<?php

abstract class AbstractModel
{
    protected $_table = null;

    protected $_form = null;

    /** 
     * Retrieve table object 
     * 
     * @return 
     */  
    public abstract function getTable();

    public abstract function getForm();

    protected function _getValues(array $data)
    {
        $form = $this->getForm();

        $belongTo = $form->getElementsBelongTo();
        if (!empty($belongTo) && array_key_exists($belongTo, $data)) {
            $data = $data[$belongTo];
        }

        if (!$form->isValid($data)) {
            return false;
        }

        $values = $form->getValues();
        if (!empty($belongTo)) {
            $values = $values[$belongTo];
        }

        return $values;
    }

    protected function _getTableValues(array $data)
    {
        // Strip the passed data from values that can not be saved.
        $fieldNames = $this->getTable()->getCols();
        foreach ($data as $column => $value) {
            if (!in_array($column, $fieldNames)) {
                unset($data[$column]);
            }
        }
        return $data;
    }

}
