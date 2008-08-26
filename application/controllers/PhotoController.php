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
class PhotoController extends Zend_Controller_Action
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
        $contextSwitch->addContext('atom', $context)
                      ->addActionContext('list', 'atom')
                      ->initContext();
    }

    /**
     * Index action
     *
     * Forwards to list.
     */
    public function indexAction()
    {
        $this->_redirect($this->_helper->url->simple('list'));
    }

    /**
     * Create new photo form 
     *
     */
    public function newAction()
    {
        $form = new UploadForm();
        $form->setAction($this->_helper->url->simple('create'));
        $this->view->form = $form;
    }

    /**
     * Output form for editing
     *
     */
    public function editAction()
    {
        $photos = new Photos();
        $photo = $photos->findOne($this->getRequest()->getParam('id'));
        
        $albumSet = $photo->findAlbumsViaAlbumsPhotosByPhoto();
        $albumNames = array();
        foreach ($albumSet as $album)
        {
           $albumNames[] = $album->name; 
        }

        $photoForm = new PhotoForm();
        $photoForm->setDefault('albums', implode(',', $albumNames));
        $photoForm->populate($photo->toArray());

        $photoForm->setAction($this->_helper->url->simple('update'));

        $this->view->photo     = $photo;
        $this->view->photoForm = $photoForm;
    }

    /**
     * Updates one photo
     *
     */
    public function updateAction()
    {
        $photos = new Photos();
        $photo = $photos->findOne($this->getRequest()->getParam('id'));

        $photoForm = new PhotoForm();
        $photoForm->setAction($this->_helper->url->simple('update'));

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if ($photoForm->isValid($formData)) {
                // If it is an image upload set the photo.
                if ($photoForm->getElement('file')->getTransferAdapter()->isReceived()) {
                    $photo->file = $photoForm->getValue('file');
                }

                $photo->taken_on    = $photoForm->getValue('taken_on');
                $photo->title       = $photoForm->getValue('title');
                $photo->description = $photoForm->getValue('description');

                $photo->save();
                $photos->assocciateWith($photo, explode(',', $photoForm->getValue('albums')));
            }
        }

        $photoForm->populate($formData);

        $this->view->photoForm = $photoForm;
        $this->view->photo     = $photo;

        $this->render('edit');
    }

    /**
     * Create a new photo.
     *
     */
    public function createAction()
    {
        $form = new UploadForm();
        $form->setAction($this->_helper->url->simple('create'));
        $this->view->form = $form;

        $formData = array();

        if ($this->_request->isPost()) {
            $formData = array(
                'file'        => $this->_request->getParam('file'),
                'title'       => $this->_request->getParam('title'),
                'description' => $this->_request->getParam('description'),
            );

            if ($form->isValid($formData)) {
                $photos = new Photos();

                $photo = $photos->createRow();
                $photo->file        = $form->getValue('file');
                $photo->created_on  = date('Y-m-d H:i:s', time());
                $photo->title       = $form->getValue('title');
                $photo->description = $form->getValue('description');
                $photo->save();
            }
        }

        $form->populate($formData);
    }

    /**
     * Lists all photos
     *
     */
    public function listAction()
    {
        $photos = new Photos();

        $photoSet = $photos->fetchAll($photos->select()->order('created_on desc'));

        $paginator  = Zend_Paginator::factory($photoSet);
        $paginator->setItemCountPerPage(5)
            ->setPageRange(8)
            ->setCurrentPageNumber($this->_getParam('page'));

        $this->view->url       = 'http://' . $this->_request->getHttpHost() . $this->_request->getBaseUrl(); 
        $this->view->paginator = $paginator;
    }

    /**
     * Shows one photo
     *
     */
    public function showAction()
    {
        $photos = new Photos();

        $photo   = $photos->findOne($this->getRequest()->getParam('id'));
        $detailSet = $photo->findPhotoDetailsByPhoto();

        $details = null;
        if (1 == count($detailSet)) {
            $details = $detailSet->getRow(0);
        }

        $this->view->details = $details;
        $this->view->photo   = $photo; 
    }
}
