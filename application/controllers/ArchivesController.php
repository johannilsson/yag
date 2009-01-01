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
 * Controller for archives
 *
 */
class ArchivesController extends AbstractController
{
    /**
     * Index action
     *
     */
    public function indexAction()
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

    /**
     * List action
     *
     * Forwards to list.
     */
    public function listAction()
    {
        $date = array();
        $date['year']  = $this->_getParam('year', null);
        $date['month'] = $this->_getParam('month', null);
        $date['day']   = $this->_getParam('day', null);

        $model = $this->_getPhotoModel();

        $entries = $model->fetchEntriesByCreated($date, $this->_getParam('page', 1));

        $this->view->paginator = $entries;
    }
}
