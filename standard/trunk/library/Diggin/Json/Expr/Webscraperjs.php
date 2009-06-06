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
 * @package    Diggin_Json
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

require_once 'Diggin/Scraper/Process/Aggregate.php';
class Diggin_Json_Expr_Webscraperjs
{
    const TYPE_ARRAY = 1;
    const TYPE_SCRAPEROBJECT = 2;
    const TYPE_WEBSCRAPERJS = 3;

    /**
     * decode 
     *
     * @param string $encodedValue
     * @param int $objectDecodeType
     * @param int $encodeType
     * @return mixed $decodes
     */
    public static function decode($encodedValue, 
                                  $objectDecodeType = self::TYPE_ARRAY, 
                                  $encodeType = self::TYPE_WEBSCRAPERJS)
    {        
        $decodes = Zend_Json::decode(self::reEncode($encodedValue, $encodeType));
        
        if ($objectDecodeType === self::TYPE_SCRAPEROBJECT) {
            foreach ($decodes as $keys => $decode) {
                if(is_array($decode)) {
                    $scraper = new Diggin_Scraper_Process_Aggregate();
                    foreach (current($decode) as $key => $val) {
                        foreach ($val as $k => $v) {
                            if ((substr($k, -2) == '[]')) {
                                $k = substr($k, 0, -2);
                                $arrayflag = true;
                            } else {
                                $arrayflag = false;
                            }
                            
                            $process =new Diggin_Scraper_Process();
                            $process->setExpression($key);
                            $process->setName($k);
                            $process->setArrayFlag($arrayflag);
                            $process->setType($v);
                            $process->setFilters(false);
                            $scraper->process($process);
                            
                            
                        }
                    }
                    
                    if ((substr(trim(key($decode)), -2) == '[]')) {
                        $name = substr(trim(key($decode)), 0, -2);
                        $arrayflag = true;
                    } else {
                        $name = trim(key($decode));
                        $arrayflag = false;
                    }
                    $p = new Diggin_Scraper_Process();
                    $p->setExpression($keys);
                    $p->setName($name);
                    $p->setArrayFlag($arrayflag);
                    $p->setType($scraper);
                    $p->setFilters(false);
                }
            }
            
            return $p;
        }

        return $decodes;
    }

    /**
     * reEncode
     *
     * @param string $valueToEncode
     * @param int $encodeType
     * @return string $json
     */
    public static function reEncode($valueToEncode, $encodedType = self::TYPE_WEBSCRAPERJS)
    {
        $json = str_replace(array(chr(10), chr(13)), '', $valueToEncode);
        $json = str_replace('  ', '', $json);
        $json = str_replace('\'', '"', $json);
        $pattern = array('/{(\w+)/i', '/,(\w+)/i');
        $replacement = array('{"${1}"', ',"${1}"');
        $json = preg_replace($pattern, $replacement, $json);
        $json = str_replace('"[]', '[]"', $json);
        
        return $json;
    }
}
