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
        
        // Arrgh, not really DRY but what the hell, move to model...
        
        // Created at
        $createdAt = array();
        $prevYear = 0;
        foreach ($model->fetchArchive() as $data) {
            $split = split('-', $data['date']);
            if ($split[0] != $prevYear) {
                $months = array();
            }
            $months[$split[1]] = $data['count'];
            $createdAt[$split[0]] = $months;
            $prevYear = $split[0];
        }

        // Taken at
        $takenAt = array();
        $prevYear = 0;
        foreach ($model->fetchArchive('taken_on') as $data) {
            $split = split('-', $data['date']);
            if ($split[0] != $prevYear) {
                $months = array();
            }
            $months[$split[1]] = $data['count'];
            $takenAt[$split[0]] = $months;
            $prevYear = $split[0];
        }

        $this->view->createdAt = $createdAt;
        $this->view->takenAt = $takenAt;
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

        $by = $this->_getParam('by', null);
        if ($by == 'taken_at') {
          $entries = $model->fetchEntriesByTakenAt($date, $this->_getParam('page', 1));
        } else {
          $entries = $model->fetchEntriesByCreated($date, $this->_getParam('page', 1));
        }
        $this->view->paginator = $entries;
    }
}
