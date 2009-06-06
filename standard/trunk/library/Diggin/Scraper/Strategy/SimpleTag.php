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
//require_once 'rhaco/Rhaco.php';
//Rhaco::import("tag.model.SimpleTag");
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_SimpleTag extends Diggin_Scraper_Strategy_Abstract
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

    public function extract($values, $process)
    {
        
        if(!is_string($values)) $values = $values->plain;
        
        SimpleTag::setof($markup, $values);
        $results = $markup->getIn($process->getExpression());
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
            foreach($values as $value) {
                $strings[] = $value->value;
            }
        } elseif (strpos($process->getType(), '@') === 0) {
            $strings = array();
            foreach ($values as $value) {
                foreach($value->parameterList as $parameter) {
                    if($parameter->id == substr($process->getType(), 1)) {
                        $strings[] = $parameter->value;
                    }
                }
            }
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("can not understand type :".$process->getType());
        }
        
        return $strings;
    }
}
