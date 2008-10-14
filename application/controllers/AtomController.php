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

/**
 * AtomController
 *
 */
class AtomController extends AbstractController 
{
    /**
     * Pre-dispatch routines
     * 
     * Makes sure that configured actions are protected by wsse.
     *
     * @return void
     */
    public function preDispatch()
    {
        $config = Zend_Registry::get('atom-config');

        if (false == in_array($this->_request->getActionName(), $config->securedActions->toArray()) 
            || $config->authenticationDisabled) {
            return; // found public action or authentication is disabled
        }

        $adapter = new Yag_Auth_Adapter_Http_Wsse($config->wsse->toArray());   
        $adapter->setRequest($this->_request);
        $adapter->setResponse($this->_response);

        $result = $adapter->authenticate();
        if (false == $result->isValid()) {
            $this->view->errorMessages = $result->getMessages();
            $this->_forward('error');
        }
    }

    /**
     * Init
     * 
     * Disables layout and sets common Content-Type.
     */
    public function init()
    {
        $layout = $this->_helper->getHelper('layout');
        $layout->direct()->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/x.atom+xml', true);
        // Atom 1.0 preparation...
        //$this->_response->setHeader('Content-Type', 'application/atom+xml;type=entry', true);
    }
    
    /**
     * Index action
     */
    public function indexAction() 
    {
        // TODO: This is for atom 1.0
        //$this->_response->setHeader('Content-Type', 'application/atomsvc+xml', true);
    }

    /**
     * Error action.
     *
     */
    public function errorAction() 
    {
        $this->_response->setHeader('Content-Type', 'text/plain', true);
    }

    /**
     * Upload action
     * 
     * Handles atom file uploading, not pretty but gets the work done for now.
     * 
     * @return void
     */
    public function uploadAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $feed = Zend_Feed::importString($this->_request->getRawBody());
        } catch (Exception $e) {
            $this->_response->setHttpResponseCode(400); // 415 unsuported media?
            $this->_response->setHeader('Status', '400 Unable to read feed', true);
            $this->view->errorMessages = array('Unable to read feed');
            return $this->_forward('error');
        }

        $photoModel = $this->_getPhotoModel();
        $entryData = array();
        $id = null;

        foreach ($feed as $entry) {
            //
            // Entry is related to another entry
            // 
            if ('' != ($id = $entry->link('related'))) {
                // Set description
                if ('' != $entry->content() && $entry->content->offsetGet('mode') == 'xml') {
                    $entryData['description'] = $entry->content;
                }
                // Set tags
                if ('' != ($tags = $entry->{"dc:subject"})) {
                    $entryData['tags'] = implode(',', explode(' ', $tags));
                }
                $photoModel->update($entryData, $id);
            // 
            // A new entry is uploaded
            //
            } else if ('' != $entry->content()) {
	            $file = $this->_createTmpFile(base64_decode($entry->content()));
	            $entryData['title']       = $entry->title;
	            $entryData['created_on']  = date('Y-m-d H:i:s', strtotime($entry->issued));

	            $id = $photoModel->add($entryData, $file);
            } else {
                continue; 
            }

            $url = 'http://' . $this->_request->getHttpHost() . $this->_request->getBaseUrl();

            $this->_response->setHttpResponseCode(201);
            $this->_response->setHeader('Status', '201 Created');
            $this->_response->setHeader('Location', $url . $this->_helper->url('view', 'photo', null, array('id' => $id)));

            $this->view->url   = $url;
            $this->view->photo = $photoModel->fetchEntry($id);

            $this->render('upload');
        }
    }

    /**
     * Feed action
     *
     * @return void 
     */
    public function feedAction()
    {
        $this->_forward('list', 'photo', null, array('format' => 'atom'));
    }

    /**
     * Creates a temporary file.
     *
     * @param mixed $data
     * @return string
     */
    private function _createTmpFile($data)
    {
        //$size = getimagesize($data);
        // TODO: Get correct extension
        // TODO: fixed tmp path
        $filename = APPLICATION_PATH . '/../upload/app-' . date('Ymhis', time()) . '.jpg';
        file_put_contents($filename, $data);
        return $filename;
    }
}
