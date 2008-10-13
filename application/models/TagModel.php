<?php

require_once 'AbstractModel.php';

class TagModel extends AbstractModel
{
    /** 
     * Retrieve table object 
     * 
     * @return 
     */  
     public function getTable() 
     {
        if (null === $this->_table) { 
            require_once APPLICATION_PATH . '/models/DbTable/Tags.php'; 
            $this->_table = new Tags; 
        } 
        return $this->_table; 
    } 

     public function getForm() 
     {
        if (null === $this->_form) { 
            require_once APPLICATION_PATH . '/forms/TagForm.php'; 
            $this->_form = new TagForm; 
        } 
        return $this->_form; 
    }

    public function fetchEntryByName($name)
    {
        $table = $this->getTable();
        $select = $table->select()->where('name = ?', $name);

        return $table->fetchRow($select);
    }

}
