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
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 * @version  $Id$
 */

/**
 * @see Diggin_Scraper_Strategy_Abstract
 */
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Preg extends Diggin_Scraper_Strategy_Abstract 
{

    public function setAdapter(Diggin_Scraper_Adapter_Interface $adapter)
    {
        if (!($adapter instanceof Diggin_Scraper_Adapter_StringAbstract)) {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            $msg = get_class($adapter).'Adapter is not extends ';
            $msg .= 'Diggin_Scraper_Adapter_StringAbstract';
            throw new Diggin_Scraper_Strategy_Exception($msg);
        }

        $this->_adapter = $adapter;
    }

    public function getAdapter()
    {
        if (!isset($this->_adapter)) {
            /**
             * @see Diggin_Scraper_Adapter
             */
            require_once 'Diggin/Scraper/Adapter/Normal.php';
            $this->_adapter = new Diggin_Scraper_Adapter_Normal();
        }

        return $this->_adapter;
    }

    public function extract($string, $process)
    {
        if (is_array($string)) {
            $string = array_shift($string);
            preg_match_all($process->getExpression(), self::cleanString($string) , $results);
        } else {
            preg_match_all($process->getExpression(), self::cleanString($string) , $results);
        }
        return $results;
    }

    /**
     * get value with DSL
     * 
     * @param array
     * @param Diggin_Scraper_Process
     * @return array
     */
    public function getValue($values, $process)
    {
        if (strtoupper(($process->getType())) === 'RAW') {
            $strings = $values;
        } elseif (strtoupper(($process->getType())) === 'TEXT') {
            $strings = array();
            foreach (current($values) as $value) {
                $value = strip_tags($value);
                $value = str_replace(array(chr(10), chr(13)), '', $value);
                array_push($strings, $value);
            }
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("Unknown value type :".$process->getType());
        }

        return $strings;
    }
    
    /**
     * Body Cleaner for easy dealing with regex
     * 
     * @param string
     * @return string
     */
    private static function cleanString($resposeBody)
    {
        $results = str_replace(array(chr(10), chr(13), chr(9)), chr(32), $resposeBody);
        while(strpos($results, str_repeat(chr(32), 2), 0) != FALSE){
            $results = str_replace(str_repeat(chr(32), 2), chr(32), $results);
        }

        return (trim($results));
    }
}
