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
class Yag_Manipulator_Adapter_ImageTransform implements Yag_Manipulator_Adapter_Interface
{
    /**
     * Perfoms manipulation
     *
     */
    public function manipulate($from, $to, $options)
    {
        if (!isset($options['geometry'])) {
            throw new Yag_Manipulator_Adapter_Exception('ImageTransform requires the \'geometry\' option to be set');
        }
        $matches = array();
        preg_match('/(c)?([0-9]+)x([0-9]+)/', $options['geometry'], $matches);

        if (empty($matches[2])) {
            throw new Yag_Manipulator_Adapter_Exception('Invalid geometry pattern \'' . $options['geometry']  . '\'');
        }
        if (empty($matches[3])) {
            throw new Yag_Manipulator_Adapter_Exception('Invalid geometry pattern \'' . $options['geometry'] . '\'');
        }

        /*
         * Needs to be required here, otherwise getting "Cannot access self:: when
         * no class scope is active" from PEAR, probably only in autoload context.
         */
        require_once 'Image/Transform.php';

        /*
         * Here comes the ugly pear integration...
         */
        $imageTransform = Image_Transform::factory('GD');
        if (PEAR::isError($imageTransform)) {
            throw new Yag_Manipulator_Adapter_Exception($imageTransform->getMessage());
        }

        $response = $imageTransform->load($from);
        if (PEAR::isError($response)) {
            throw new Yag_Manipulator_Adapter_Exception($response->getMessage());
        }

        if (empty($matches[1])) {
            $this->_fit($imageTransform, $matches[2], $matches[3]);        
        } else {
            $this->_cropResize($imageTransform, $matches[2], $matches[3]);        
        }

        $response = $imageTransform->save($to);
        if (PEAR::isError($response)) {
            throw new Yag_Manipulator_Adapter_Exception($response->getMessage());
        }
    }

    /**
     * Fit
     * 
     * @param $imageTransform Image_Transform
     * @param $width int
     * @param $height int
     */
    private function _fit($imageTransform, $width, $height)
    {
        $response = $imageTransform->fit($width, $height);
        if (PEAR::isError($response)) {
            throw new Yag_Manipulator_Adapter_Exception($response->getMessage());
        }
    }

    /**
     * Crop and resize
     * 
     * @param $imageTransform Image_Transform
     * @param $width int
     * @param $height int
     */
    private function _cropResize($imageTransform, $width, $height)
    {
        $imageWidth = $imageTransform->getImageWidth();
        $imageHeight = $imageTransform->getImageHeight();

        $dimensions = $this->_extractDimensions($imageWidth, $imageHeight, $width, $height);
        $newWidth = $dimensions['width'];
        $newHeight = $dimensions['height'];

        $minNewLength = $newWidth <= $newHeight ? $newWidth : $newHeight;
        $minLength = $width <= $height ? $width : $height;

        // Compensate on some square image crops
        if ($width == $height && $minNewLength < $minLength) {
            $diff = $minLength - $minNewLength;
            $newWidth = $dimensions['width'] + $diff;
            $newHeight = $dimensions['height'] + $diff;
        }

        $response = $imageTransform->resize($newWidth, $newHeight);
        if (PEAR::isError($response)) {
            throw new Yag_Manipulator_Adapter_Exception($response->getMessage());
        }

        $response = $imageTransform->crop($width, $height);
        if (PEAR::isError($response)) {
            throw new Yag_Manipulator_Adapter_Exception($response->getMessage());
        }
    }

    /**
     * 
     */
    private function _extractDimensions($imageWidth, $imageHeight, $width, $height)
    {
        $aspectRatio = $imageWidth / $imageHeight;

        $newWidth = round(($height * $aspectRatio));
        $newHeight = $height;

        return array(
            'width'  => $newWidth,
            'height' => $newHeight, 
        );
    }
}
