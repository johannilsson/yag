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

require_once 'AbstractController.php';

/**
 * Controller for displaying and editing a photo.
 *
 */
class TagsController extends AbstractController
{
/*
    public function newAction()
    {
        $form = $this->_getTagModel()->getForm();
        $form->setAction($this->_helper->url->simple('create'));
        $this->view->form = $form;    
    }

    public function createAction()
    {
        if (!$this->_request->isPost()) {
            $this->_redirect($this->_helper->url('new'));
        }
    }
*/
    public function showAction()
    {
        $model = $this->_getTagModel();
        $tag = $model->fetchEntryByName(
            urldecode($this->getRequest()->getParam('name')));

        if (null === $tag) {
            return $this->_forward('notfound', 'error');
        }

        $photos = $this->_getPhotoModel()->fetchEntriesByTag(
              $tag->id, $this->_getParam('page', 1));

        $this->view->tag = $tag;
        $this->view->photos = $photos;
    }
}
