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
 * Controller for errors
 *
 */
class ErrorController extends AbstractController
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        $message = '';
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');
                $message = "404 Not Found";
                //return $this->_render('notfound');
                break;
            default:
                // application error; display error page, but don't change
                // status code
                // Log the exception:
                $exception = $errors->exception;
                /*
                $log = new Zend_Log(
                    new Zend_Log_Writer_Stream(
                        '/tmp/applicationException.log'
                    )
                );
                $log->debug($exception->getMessage() . "\n" . 
                            $exception->getTraceAsString());
                */
                $message = $exception->getMessage();
                break;
        }

        $photoModel = $this->_getPhotoModel();
        try {
          $this->view->photos = $photoModel->fetchEntries(1);
        } catch (Exception $e) {
          ;
        }

        $this->view->message = $message;
    }

    public function notfoundAction()
    {
        $this->getResponse()
             ->setRawHeader('HTTP/1.1 404 Not Found');
        $this->view->message = "404 Not Found";

        $photoModel = $this->_getPhotoModel();
        $this->view->photos = $photoModel->fetchEntries(1);
    }
}
