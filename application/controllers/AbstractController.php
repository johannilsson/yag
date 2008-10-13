<?php

abstract class AbstractController extends Zend_Controller_Action
{
    protected $_photoModel = null;

    protected $_tagModel = null;

    protected function _getPhotoModel() 
    { 
        if (null === $this->_photoModel) { 
            require_once APPLICATION_PATH . '/models/PhotoModel.php'; 
            $this->_photoModel = new PhotoModel; 
        } 
        return $this->_photoModel; 
    }

    protected function _getTagModel() 
    { 
        if (null === $this->_tagModel) { 
            require_once APPLICATION_PATH . '/models/TagModel.php'; 
            $this->_tagModel = new TagModel; 
        } 
        return $this->_tagModel; 
    }
}

