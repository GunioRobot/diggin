<?php

/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Tool
 * @subpackage Framework
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Tool\Framework\Client\Console;

/**
 * @see Zend_Tool_Framework_Client_Console_ArgumentParser
 */
// require_once 'Zend/Tool/Framework/Client/Console/ArgumentParser.php';

/**
 * @category   Diggin
 * @package    Diggin_Tool
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 * 
 * This code was mostly adapted from Zend_Tool_Framework_Client_Console_ArgumentParser - a part of the Zend Framework
 * Copyright (c) 2005-2010 Zend Technologies USA Inc., licensed under the
 * New BSD License. See http://framework.zend.com for more information.
 */

class ArgumentParser extends \Zend\Tool\Framework\Client\Console\ArgumentParser
{
    private $_shName;

    public function setShName($shName)
    {
        $this->_shName = $shName;
    }

    /**
     * _createHelpResponse
     *
     * @param unknown_type $options
     */
    protected function _createHelpResponse($options = array())
    {
        // require_once 'Diggin/Tool/Framework/Client/Console/HelpSystem.php';
        $helpSystem = new HelpSystem();
        $helpSystem->setShName($this->_shName);
        $helpSystem->setRegistry($this->_registry);

        if (isset($options['error'])) {
            $helpSystem->respondWithErrorMessage($options['error']);
        }

        if (isset($options['actionName']) && isset($options['providerName'])) {
            $helpSystem->respondWithSpecialtyAndParamHelp($options['providerName'], $options['actionName']);
        } elseif (isset($options['actionName'])) {
            $helpSystem->respondWithActionHelp($options['actionName']);
        } elseif (isset($options['providerName'])) {
            $helpSystem->respondWithProviderHelp($options['providerName']);
        } else {
            $helpSystem->respondWithGeneralHelp();
        }

    }

}
