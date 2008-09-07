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
 * @version
 */

require_once 'Zend/Controller/Action.php';

class AlbumController extends Zend_Controller_Action
{
    public function init()
    {
        
    }

    /**
     * Index action
     *
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * List all albums
     *
     */
    public function listAction()
    {
        $albums = new Albums();
        $albumSet = $albums->fetchAll();

        $paginator  = Zend_Paginator::factory($albumSet);
        $paginator->setItemCountPerPage(5)
            ->setPageRange(8)
            ->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }

    /**
     * Shows photos in one album
     *
     */
    public function showAction()
    {
        $albums = new Albums();
        $dataFilter = $albums->filterData(array('name' => $this->getRequest()->getParam('name')));
        $album = $albums->findByName($dataFilter->name);
        $photoId = $this->getRequest()->getParam('photo');

        if (false == $album instanceof Yag_Db_Table_Row)
        {
            throw new Exception('Unkown album');
        }

        $photoSet = $album->findPhotosViaAlbumsPhotosByAlbum();

        $paginator = Zend_Paginator::factory($photoSet);
        $paginator->setItemCountPerPage(5)
            ->setPageRange(8)
            ->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator= $paginator;
        $this->view->name = $dataFilter->name;
    }

    /**
     * Create new album form
     *
     */
    public function newAction()
    {
        $form = new AlbumForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/album/create');
        $this->view->form = $form;
    }

    /**
     * Create a new album
     *
     */
    public function createAction()
    {
        $form = new AlbumForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/album/create');
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData))
            {
                $albums = new Albums();
                $row = $albums->createRow();
                $row->name = $form->getValue('name');
                $row->description = $form->getValue('description');
                $row->created_on = date('Y-m-d H:i:s', time());
                $row->save();
                return $this->_forward('new');
            }
        }

        $form->populate($formData);
        $this->render('new');
    }

}