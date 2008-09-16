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
 * Controller for displaying and editing a photo.
 *
 */
class TagsController extends Zend_Controller_Action
{
    public function showAction()
    {
        $tagModel = new Tags();
        $tag = $tagModel->findByName(urldecode($this->getRequest()->getParam('name')));

        $photos = array();
        if (null !== $tag) {
            $photos = $tag->findPhotosViaTaggedPhotosByTag();
        }
        $this->view->photos = $photos;
    }
}
