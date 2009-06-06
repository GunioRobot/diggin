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
 * @package    Diggin_Scraper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @see Diggin_Scraper_Adapter_Interface
 */
require_once 'Diggin/Scraper/Adapter/Interface.php';

abstract class Diggin_Scraper_Adapter_StringAbstract implements Diggin_Scraper_Adapter_Interface
{
    
    protected abstract function getString($response);

    /**
     * Reading Response as SimpleXmlElement
     * 
     * @param object $response
     * @return SimplXmlElement
     */
    final public function readData($response)
    {
        $string = $this->getString($response);
        if (!is_string($string)) {
            require_once 'Diggin/Scraper/Adapter/Exception.php';
            throw new Diggin_Scraper_Adapter_Exception('adapter getString not return String');
        }
        
        return $string;
    }
}