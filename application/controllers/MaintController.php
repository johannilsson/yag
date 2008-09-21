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
class MaintController extends Zend_Controller_Action
{
    public function missingimageAction()
    {
        $photoModel = new Photos();
        $photos = $photoModel->fetchAll();

        $missingPhotos = array();

        foreach ($photos as $photo) {
            foreach ($photoModel->attachment['styles'] as $styleName => $options) {
                if (false === $photo->image->__get($styleName)->exists()) {
                    $missingPhotos[] = $photo;
                    break;
                }
            }
        }

        $this->view->photos = $missingPhotos;
    }

    public function manipulateAction()
    {
        $photoModel = new Photos();
        $photo = $photoModel->findOne($this->getRequest()->getParam('id'));
        if (true === $photo->image->exists()) {
            try {
                $photo->image->applyManipulations();
                $this->view->message = 'Applied manipulations';
            } catch (Gem_Manipulate_Exception $e) {
                $this->view->message = 'Failed to manipulate: ' . $e->getMessage();
            }
        } else {
            $this->view->message = 'Original image does not exists';
        }
    }
}
