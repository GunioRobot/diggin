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
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

require_once 'Diggin/Scraper/Helper/HelperAbstract.php';

abstract class Diggin_Scraper_Helper_Simplexml_SimplexmlAbstract extends Diggin_Scraper_Helper_HelperAbstract
{
    
    public function setPreAmpFilter($flag)
    {
        $this->setOption(array('preAmpFilter' => $flag));
        
        return $this;
    }

    public function getPreAmpFilter()
    {
        if (array_key_exists('preAmpFilter', $this->_option)) {
            return $this->_option['preAmpFilter'];
        }

        return false;
    }
    
    protected function asString($sxml)
    {
        if ($this->getPreAmpFilter() === true) {
            if (!is_array($sxml)) {
                return htmlspecialchars_decode($sxml->asXML(),
                            ENT_NOQUOTES);
            } else {
                $ret = array();
                foreach ($sxml as $s) {
                    $ret[] = htmlspecialchars_decode($s->asXML(),
                                ENT_NOQUOTES);
                }
                return $ret;
            }
        } else {
            if (!is_array($sxml)) {
                if (count($sxml) === 0 and key($sxml) === 0) {
                    return (string)$sxml;
                } else {
                    return $sxml->asXML();
                }
            } else {
                //not implement
                throw new InvalidArgumentException();
            }
        }
    }
}
