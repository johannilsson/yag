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
class PhotosController extends AbstractController
{
    /**
     * Init
     *
     */
    public function init()
    {
        $context = array(
            'suffix'  => 'atom', 
            'headers' => array(
                'Content-Type' => 'application/atom+xml',
            ),
        );

        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        if (false === $contextSwitch->hasContext('atom')) {
            $contextSwitch->addContext('atom', $context)
                          ->addActionContext('list', 'atom')
                          ->initContext();
        }
    }

    /**
     * Index action
     *
     * Forwards to list.
     */
    public function indexAction()
    {
        $this->_redirect($this->_helper->url('list'));
    }

    /**
     * List action
     *
     * Forwards to list.
     */
    public function listAction()
    {
        $model   = $this->_getPhotoModel();
        $entries = $model->fetchEntries($this->_getParam('page', 1));

        $this->view->url       = 'http://' . $this->_request->getHttpHost() . $this->_request->getBaseUrl(); 
        $this->view->paginator = $entries;
    }

    public function archiveAction()
    {
        $model = $this->_getPhotoModel();

        $archive = array();
        $prevYear = 0;
        foreach ($model->fetchArchive() as $data) {
            $split = split('-', $data['created_on']);
            if ($split[0] != $prevYear) {
                $months = array();
            }
            $months[$split[1]] = $data['count'];
            $archive[$split[0]] = $months;
            $prevYear = $split[0];
        }

        $this->view->archive = $archive;
    }

    public function bycreatedAction()
    {
        $date = array();
        $date['year']  = $this->_getParam('year', null);
        $date['month'] = $this->_getParam('month', null);
        $date['day']   = $this->_getParam('day', null);

        $model = $this->_getPhotoModel();

        $entries = $model->fetchEntriesByCreated($date, $this->_getParam('page', 1));

        $this->view->paginator = $entries;
    }

    /**
     * Create new photo
     *
     */
    public function newAction()
    {
        require_once APPLICATION_PATH . '/forms/NewPhotoForm.php';
        $form = new NewPhotoForm();
        $form->setAction($this->_helper->url->simple('create'));
        $this->view->form = $form;
    }

    /**
     * Create
     *
     * POST
     */
    public function createAction()
    {
        if (!$this->_request->isPost()) {
            $this->_redirect($this->_helper->url('new'));
        }

        require_once APPLICATION_PATH . '/forms/NewPhotoForm.php';
        $form = new NewPhotoForm();
        $form->setAction($this->_helper->url->simple('create'));
        $this->view->form = $form;

        if (!$form->isValid($this->_request->getPost())) {
            $this->view->messages = $form->getMessages();

            return $this->render('new');
        }

        if (!$form->file->receive()) {
            $this->view->messages = array(array('failed' => 'Failed to upload the image'));
        }

        $adapter = $form->file->getTransferAdapter();
        $file = $adapter->getFileName('file');

        $model = $this->_getPhotoModel();
        $id = $model->add($form->getValues(), $file);

        // Should probably not be here, but it works for now...
        unlink($file);

        $this->_redirect($this->_helper->url('edit', null, null, array('id' => $id)));        
    }

    /**
     * Output form for editing
     *
     * GET
     */
    public function editAction()
    {
        $model = $this->_getPhotoModel();
        $photo = $model->fetchEntry($this->getRequest()->getParam('id'));

        if (null === $photo) {
            return $this->_forward('notfound', 'error');
        }

        $tagNames = array();
        foreach ($model->fetchTags($photo) as $tag) {
            $tagNames[] = $tag->name;
        }

        $form  = $model->getForm();
        $form->setAction($this->_helper->url('update', null, null, array('id' => $photo->id)));
        $form->setDefault('tags', implode(',', $tagNames));
        $form->populate($photo->toArray());

        $this->view->photo       = $photo;
        $this->view->photoForm   = $form;
    }

    /**
     * Updates one photo
     *
     * PUT
     */
    public function updateAction()
    {
        if (!$this->_request->isPost()) {
            $this->_redirect($this->_helper->url('index'));
        }

        $id = $this->getRequest()->getParam('id');

        $model   = $this->_getPhotoModel();
        $photo   = $model->fetchEntry($id);

        if (!$model->update($this->_request->getPost(), $id)) {
            $form  = $model->getForm();
            $form->setAction($this->_helper->url('update', null, null, array('id' => $id)));

            $this->view->photo       = $photo;
            $this->view->photoForm   = $form;
            return $this->render('edit');
        }
        $this->_redirect($this->_helper->url('edit', null, null, array('id' => $id)));
    }

    /**
     * Shows one photo
     *
     * GET
     */
    public function showAction()
    {
        $model   = $this->_getPhotoModel();
        $photo   = $model->fetchEntryByCleanTitle($this->getRequest()->getParam('title'));

        if (null === $photo) {
            return $this->_forward('notfound', 'error');
        }

        $this->view->auth = Zend_Auth::getInstance();
        $this->view->photo  = $photo;
    }

    /**
     * 
     * DELETE
     */
    public function deleteAction()
    {
        $model = $this->_getPhotoModel();

        try
        {
            $model->delete($this->getRequest()->getParam('id'));
            $this->view->message = 'Photo deleted';
        } catch (Exception $e) {
            $this->view->message = 'Could not delete ' . $e->getMessage();
        }
    }
}
