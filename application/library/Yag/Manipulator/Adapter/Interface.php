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

/**
 * Interface for manipulators
 * 
 * @category   Yag
 * @package    Yag_Manipulator
 */
interface Yag_Manipulator_Adapter_Interface
{

    /**
     * Perfoms manipulation
     *
     * @param string $from
     * @param string $to
     * @param string $options
     */
    public function manipulate($from, $to, $options);
}
