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
        $this->_redirect($this->_helper->url->simple('list'));
    }

    /**
     * Create new photo form 
     *
     */
    public function newAction()
    {
        $form = new NewPhotoForm();
        $form->setAction($this->_helper->url->simple('new'));
        $this->view->form = $form;
        
        $formData = array();

        if ($this->_request->isPost()) {
            $formData = array(
                'file'        => $form->getValue('file'),
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
     * Output form for editing
     *
     */
    public function editAction()
    {
        $photos = new Photos();
        $photo = $photos->findOne($this->getRequest()->getParam('id'));

        $tagSet = $photo->findTagsViaTaggedPhotosByPhoto();
        $tagNames = array();
        foreach ($tagSet as $tag)
        {
           $tagNames[] = $tag->name; 
        }

        $photoForm = new PhotoForm();
        $photoForm->setDefault('tags', implode(',', $tagNames));
        $photoForm->populate($photo->toArray());
        $photoForm->setAction($this->_helper->url->simple('update'));

        $this->view->photo       = $photo;
        $this->view->photoForm   = $photoForm;
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
                $photo->taken_on    = $photoForm->getValue('taken_on');
                $photo->title       = $photoForm->getValue('title');
                $photo->description = $photoForm->getValue('description');

                $photo->save();

                // Has tags?
                if ('' != ($tags = $photoForm->getValue('tags'))) {
                    $taggedPhotos = new TaggedPhotos();
                    $taggedPhotos->assocciatePhotoWith($photo, explode(',', $tags));
                }
            }
        }

        $photoForm->populate($formData);

        $this->view->photoForm = $photoForm;
        $this->view->photo     = $photo;

        $this->render('edit');
    }

    /**
     * Replace one photo with antother
     */
    public function replaceAction()
    {
        $photos = new Photos();
        $photo = $photos->findOne(1/*$this->getRequest()->getParam('id')*/);

        $uploadForm = new UploadPhotoForm();
        $uploadForm->setDefault('id', $photo->id);
        $uploadForm->setAction($this->_helper->url->simple('replace'));
        $this->view->form = $uploadForm;

        if ($this->_request->isPost()) {
            $formData = array(
                'id'    => $this->_request->getParam('id'),
                'file'  => $uploadForm->getValue('file'),
            );

            if ($uploadForm->isValid($formData)) {
                $photo->file = $uploadForm->getValue('file');
                $photo->save();
            }
        }
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

        $photo   = $photos->fetchRow($photos->select()->where('id = ?', $this->getRequest()->getParam('id')));

        $details = $longitude = $latitude = null;
        $detailSet = $photo->findPhotoDetailsByPhoto();
        if (1 == count($detailSet)) {
            $details = $detailSet->getRow(0);

            if (null !== $details->place_id 
            && null !== ($place = $details->findParentRow('Places'))) {
                $latitude = new Yag_GeoCode($place->latitude);
                $longitude = new Yag_GeoCode($place->longitude);
            }
        }

        $this->view->longitude  = $longitude;
        $this->view->latitude   = $latitude;
        $this->view->details    = $details;
        $this->view->photo      = $photo; 
    }

    /**
     * Shows previous and next photos. 
     *
     */
    public function streamAction()
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $album  = (string) $this->getRequest()->getParam('album', '');

        $photos = new Photos();

        $current = $photos->fetchRow($photos->select()->where('id = ?', $id));

        $this->view->previous = $photos->getNeighbour($current, 'previous');
        $this->view->next     = $photos->getNeighbour($current, 'next');
    }
}
