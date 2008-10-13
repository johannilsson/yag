<?php
/**
 * Yag - File Uploading for Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Yag
 * @package    Yag_Manipulator
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

require_once 'Yag/Manipulator/Adapter/Interface.php';
require_once 'Yag/Manipulator/Adapter/Exception.php';

/**
 * @category   Yag
 * @package    Yag_Manipulator
 */
class Yag_Manipulator_Adapter_Thumbnail implements Yag_Manipulator_Adapter_Interface
{
    /**
     * Perfoms manipulation
     *
     * @param string $from
     * @param string $to
     * @param string $options
     * @return void
     */
    public function manipulate($from, $to, $options)
    {
        if (false === class_exists('Thumbnail')) {
            throw new Yag_Manipulator_Adapter_Exception('Class Thumbnail could not be loaded');
        }

        if (!isset($options['geometry'])) {
            throw new Yag_Manipulator_Adapter_Exception('Thumbnail requires the \'geometry\' option to be set');
        }
        $matches = array();
        preg_match('/(c)?([0-9]+)x([0-9]+)/', $options['geometry'], $matches);

        $crop = empty($matches[1]) ? false : true;
        $width = $matches[2];
        $height = $matches[3];

        if (empty($matches[2])) {
            throw new Yag_Manipulator_Adapter_Exception('Invalid geometry pattern \'' . $options['geometry']  . '\'');
        }
        if (empty($matches[3])) {
            throw new Yag_Manipulator_Adapter_Exception('Invalid geometry pattern \'' . $options['geometry'] . '\'');
        }

        $thumbnail = new Thumbnail($from);

        // TODO: Fix error handling around this...
        $quality = 80;
        if (false == $crop) {
            $thumbnail->resize($width, $height);
            $quality = 100;
        } else if ($width == $height) {
            // Well works for now... the crop for ImageTransform is a bit better
            // but who cares?
            $thumbnail->cropFromCenter($width);
        } else {
            $thumbnail->crop(0, 0, $width, $height);
        }

        $thumbnail->save($to, $quality);
    }
}
