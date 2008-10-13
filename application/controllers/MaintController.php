<?php
/**
 * YAG - Yet Another Gallery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Yag
 * @package    Controllers
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

require_once 'Zend/Controller/Action.php';

/**
 * Controller for importing photos
 *
 */
class MaintController extends AbstractController
{
    public function missingimageAction()
    {
        $model   = $this->_getPhotoModel();
        $entries = $model->fetchEntries();

        $missingPhotos = array();

        foreach ($entries as $photo) {
            foreach ($model->getImageVariants() as $styleName => $options) {
                if (false == file_exists($model->getImageFileName($photo, $styleName))) {
                    $missingPhotos[] = $photo;
                    break;
                }
            }
        }

        $this->view->photos = $missingPhotos;
    }

    public function manipulateAction()
    {
        $model   = $this->_getPhotoModel();
        $photo   = $model->fetchEntry($this->getRequest()->getParam('id'));

        if (file_exists($model->getImageFileName($photo))) {
            try {
                $model->applyManipulations($model->getImageFileName($photo));
                $this->view->message = 'Applied manipulations';
            } catch (Gem_Manipulate_Exception $e) {
                $this->view->message = 'Failed to manipulate: ' . $e->getMessage();
            }
        } else {
            $this->view->message = 'Original image does not exists: ' . $model->getImageFileName($photo);
        }
    }
}
