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

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

class Yag_Auth_Adapter_Http_Wsse implements Zend_Auth_Adapter_Interface
{
    /**
     * Reference to the HTTP Request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Reference to the HTTP Response object
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * The protection realm to use
     *
     * @var string
     */
    protected $_realm;

    /**
     * identity
     *
     * @var mixed
     */
    private $_identity;

    /**
     * credential
     *
     * @var mixed
     */
    private $_credential;

    /**
     * Constructor
     *
     * @param  array $config Configuration settings:
     *    'realm' => <string>
     *    'identity' => <string> The identity to match
     *    'credential' => <string> The credential to match
     * @throws Zend_Auth_Adapter_Exception
     * @return void
     */
    public function __construct(array $config)
    {
        if (empty($config['identity'])) {
            throw new Zend_Auth_Adapter_Exception('Config key identity is required');
        } else {
            $this->_identity = $config['identity'];
        }

        if (empty($config['credential'])) {
            throw new Zend_Auth_Adapter_Exception('Config key credential is required');
        } else {
            $this->_credential = $config['credential'];
        }

        // Taken from Zend_Auth_Adapter_Http
        // Double-quotes are used to delimit the realm string in the HTTP header,
        // and colons are field delimiters in the password file.
        if (empty($config['realm']) ||
            !ctype_print($config['realm']) ||
            strpos($config['realm'], ':') !== false ||
            strpos($config['realm'], '"') !== false) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception('Config key \'realm\' is required, and must contain only printable '
                                                . 'characters, excluding quotation marks and colons');
        } else {
            $this->_realm = $config['realm'];
        }
    }
    
    /**
     * Enter description here...
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if ('' == ($authHeader = $this->_request->getServer("HTTP_X_WSSE"))) {
            $this->_response->setHttpResponseCode(401, 'Unauthorized', true);
            $this->_response->setHeader('WWW-Authenticate', 'WSSE realm="' . $this->_realm . '", profile="UsernameToken"');
	        return new Zend_Auth_Result(
	            Zend_Auth_Result::FAILURE_UNCATEGORIZED,
	            array(),
	            array('Missing WSSE Header')
	        );
        }

        $headerParts = $this->parseWsseHeader($authHeader);
        $passwordDigest = $this->createPasswordDigest($headerParts['nonce'], $headerParts['created'], $this->_credential);

        if ($passwordDigest == $headerParts['digest'] && $this->_identity == $headerParts['username']) {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_identity);
        }

        $this->_response->setHttpResponseCode(401, 'Unauthorized', true);

        return new Zend_Auth_Result(
            Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
            array(),
            array('Unauthorized')
        );
    }

    /**
     * Parse the Wsse header and return the parts
     *
     * @param string $header
     * @return array Parts of the wsse header.
     */
    public function parseWsseHeader($header)
    {
        $headerParts = array(
            'username' => '',
            'digest'   => '',
            'nonce'    => '',
            'created'  => '',
        );

        // What shoudl we match? quick and dirty for know.
        $pattern = '\"(.*)\"';
        $regexp = sprintf('/Username=%s, PasswordDigest=%s, Nonce=%s, Created=%s/', 
            $pattern, $pattern, $pattern, $pattern);

        preg_match($regexp, $header, $matches);

        if (isset($matches[1])) {
            $headerParts['username'] = $matches[1];
        }
        if (isset($matches[2])) {
            $headerParts['digest'] = $matches[2];
        }
        if (isset($matches[3])) {
            $headerParts['nonce'] = $matches[3];
        }
        if (isset($matches[4])) {
            $headerParts['created'] = $matches[4];
        }

        return $headerParts;
    }

    /**
     * Creates a password digest
     *
     * @param string $nonce
     * @param string $created
     * @param string $password
     * @return string
     */
    public function createPasswordDigest($nonce, $created, $password)
    {
        $passwordDigest = base64_encode(pack("H*",sha1(base64_decode($nonce) . $created . $password)));
        return $passwordDigest;
    }

    /**
     * Setter for the Request object
     *
     * @param  Zend_Controller_Request_Http $request
     * @return Zend_Auth_Adapter_Http Provides a fluent interface
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;

        return $this;
    }

    /**
     * Getter for the Request object
     *
     * @return Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Setter for the Response object
     *
     * @param  Zend_Controller_Response_Http $response
     * @return Zend_Auth_Adapter_Http Provides a fluent interface
     */
    public function setResponse(Zend_Controller_Response_Http $response)
    {
        $this->_response = $response;

        return $this;
    }

    /**
     * Getter for the Response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
