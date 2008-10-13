<?php
/**
 * Gem - File Uploading for Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Gem
 * @package    Gem_Manipulator
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */
 
/**
 * Main endpoint for file manipulation
 */
class Yag_Manipulator
{
    /**
     * Manipulator
     *
     * @var string
     */
    protected $_manipulator;

    /**
     * Constructor
     *
     * @param string $manipulator
     */
    public function __construct($manipulator)
    {
        $this->_manipulator = $manipulator;
    }

    /**
     * Manipulates
     *
     * @return Gem_Manipulator
     */
    public function manipulate($from, $to, $options)
    {
        $manipulator = $this->getManipulatorInstance($this->_manipulator);
        $manipulator->manipulate($from, $to, $options);

        return $this;
    }

    /**
     * Returns an manipulator instance based on its name.
     *
     * @param string $manipulator
     * @return Gem_Manipulator_Adapter_Interface
     */
    static public function getManipulatorInstance($manipulator)
    {
        $args = array();

        if (is_array($manipulator)) {
            $args = $manipulator;
            $manipulator = array_shift($args);
        }

        // TODO: Move to allow other plugins...
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Yag_Manipulator_Adapter', 'Yag/Manipulator/Adapter/');
        $className = $loader->load($manipulator);

        $class = new ReflectionClass($className);

        if (!$class->implementsInterface('Yag_Manipulator_Adapter_Interface')) {
            require_once 'Yag/Manipulator/Exception.php';
            throw new Gem_Manipulator_Exception('Manipulator must implement interface "Yag_Manipulator_Adapter_Interface".');
        }

        if ($class->hasMethod('__construct')) {
            $object = $class->newInstanceArgs($args);
        } else {
            $object = $class->newInstance();
        }

        return $object;
    }
}
