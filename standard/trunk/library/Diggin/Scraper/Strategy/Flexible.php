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
 */

/**
 * @see Diggin_Scraper_Strategy_Abstract
 */
require_once 'Diggin/Scraper/Strategy/Abstract.php';
/**
 * @see Zend_Dom_Query_Css2Xpath
 */
require_once 'Zend/Dom/Query/Css2Xpath.php';

/**
 * @see Diggin_Uri_Http
 */
require_once 'Diggin/Uri/Http.php';

/**
 * @see Diggin_Scraper_Helper_HeadBase
 */
require_once 'Diggin/Scraper/Helper/Simplexml/HeadBaseHref.php';

class Diggin_Scraper_Strategy_Flexible extends Diggin_Scraper_Strategy_Abstract
{
    protected $_baseUrl = null;
    
    public function setBaseUrl($url)
    {
        $this->_baseUrl = $url;
    }

    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }
    
    public function setAdapter(Diggin_Scraper_Adapter_Interface $adapter)
    {
        if (!($adapter instanceof Diggin_Scraper_Adapter_SimplexmlAbstract)) {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            $msg = get_class($adapter).' is not extends ';
            $msg .= 'Diggin_Scraper_Adapter_SimplexmlAbstract';
            throw new Diggin_Scraper_Strategy_Exception($msg);
        }

        $this->_adapter = $adapter;
    }


    public function getAdapter()
    {
        if (!isset($this->_adapter)) {
            /**
             * @see Diggin_Scraper_Adapter_Htmlscraping
             */
            require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';
            $this->_adapter = new Diggin_Scraper_Adapter_Htmlscraping();
        }

        return $this->_adapter;
    }

    
    /**
     * Extarcting values according process
     *
     * @param SimpleXMLElement $values
     * @param Diggin_Scraper_Process $process
     * @return array
     * @throws Diggin_Scraper_Strategy_Exception
     */
    public function extract($values, $process)
    {
        $results = (array) $values->xpath(self::_xpathOrCss2Xpath($process->getExpression()));

        // count($results) === 0 is not found by xpath-node
        // $results[0] === false is not found by attributes
        if (count($results) === 0 or ($results[0] === false)) {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            $exp = self::_xpathOrCss2Xpath($process->getExpression());
            throw new Diggin_Scraper_Strategy_Exception("Couldn't find By Xpath, Process :".$exp);
        }

        return $results;
    }

    protected static function _xpathOrCss2Xpath($exp)
    {
        if (preg_match('/^id\(/', $exp)) {
            return preg_replace("/^id\(((?:'|\")(\w*)(?:'|\"))\)/", '//*[@id=$1]', $exp);
        } else if (preg_match('#^(?:\.$|\./)#', $exp)) {
            return $exp;
        } else if (preg_match('!^/!', $exp)) {
            return '.'.$exp;
        } else {
            if (ctype_alnum($exp)) {
                return ".//$exp";
            } else {
                return '.'.preg_replace('#//+#', '//', str_replace(chr(32), '', Zend_Dom_Query_Css2Xpath::transform($exp)));
            }
        }
    }

    /**
     * Getting Value
     * 'RAW'----
     *   just as SimpleXMlElement
     * 'TEXT' ----  
     *  step1: SimpleXMlElement->asXML
     *  step2: convert special html entitiy
     *  Htmlscaraping is "Replace every '&' with '&amp;'"
     *  @see Diggin_Scraper_Adapter_Htmlscraping
     *  @see http://www.rcdtokyo.com/etc/htmlscraping/#NOTE_ENTITY
     *  step3: strip_tags
     *  step4: triming (without space)
     *   chr(9)  Tab
     *   chr(10) Line Feed (LF) 
     *   chr(13) Carriage Return(CR)
     *   
     *  @see http://en.wikipedia.org/wiki/ASCII
     * 
     * @param array
     * @param Diggin_Scraper_Process
     * @return array
     * @throws Diggin_Scraper_Strategy_Exception
     */
    public function getValue($values, $process)
    {
        if (strtoupper(($process->getType())) === 'RAW') {
            $strings = $values;
        } elseif (strtoupper(($process->getType())) === 'ASXML') {
            $strings = array();
            foreach ($values as $value) {
                array_push($strings, $value->asXML());
            }
        } elseif (strtoupper(($process->getType())) === 'TEXT') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(
                        htmlspecialchars_decode($value->asXML(),
                        ENT_NOQUOTES));
                $value = str_replace(array(chr(9), chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->getType())) === 'DECODE' or 
                  strtoupper(($process->getType())) === 'DISP') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(
                        htmlspecialchars_decode($value->asXML(),
                        ENT_NOQUOTES));
                $value = html_entity_decode(strip_tags($value), ENT_NOQUOTES, 'UTF-8');
                $value = str_replace(array(chr(9), chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->getType())) === 'PLAIN') {
            $strings = array();
            foreach ($values as $value) {
                $value = htmlspecialchars_decode($value->asXML(),
                            ENT_NOQUOTES);
                $value = str_replace(array(chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->getType())) === 'HTML') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(
                        htmlspecialchars_decode($value->asXML(),
                        ENT_NOQUOTES));
                $value = str_replace(array(chr(10), chr(13)),
                                     '', $value);
                $value = preg_replace(array('#^<.*?>#', '#s*</\w+>\n*$#'), '', $value);
                array_push($strings, $value);
            }
        } elseif ((strpos($process->getType(), '@') === 0) and 
                  ($process->getType() == '@href' OR $process->getType() == '@src')) {
            $strings = array();
            
            $headBase = new Diggin_Scraper_Helper_Simplexml_HeadBaseHref(current($values));
            $headBase->setOption(array('baseUrl' => $this->getBaseUrl()));
            $base = $headBase->getBaseUrl();
            foreach ($values as $k => $value) {
                $attribute = $value[substr($process->getType(), 1)];
                if ($attribute === null) continue;
                $strings[$k] = Diggin_Uri_Http::getAbsoluteUrl((string)$attribute, $base);
            }
        } elseif (strpos($process->getType(), '@') === 0) {
            $strings = array();
            foreach ($values as $k => $value) {
                $attribute = $value[substr($process->getType(), 1)];
                if ($attribute === null) continue;
                $strings[$k] = (string)$attribute;
            }
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("Unknown value type :".$process->getType());
        }
        
        return $strings;
    }
}
