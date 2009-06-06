<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_CDDB
 * @subpackage Disc
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */
class Diggin_CDDB_Disc_Encoder
{
    const TYPE_ARRAY  = 1;
    const TYPE_OBJECT = 0;
    
    /**
     * Enter description here...
     *
     * @param mixed $valueToEncode
     * @param string $encoding mb converting To
     * @return $str
     */
    public static function encode($valueToEncode, $encoding = 'SJIS')
    {
        if ($valueToEncode instanceof Net_CDDB_Disc) {
            $str = $valueToEncode->toString();
        } elseif (is_array($valueToEncode)) {
            //@todo simple implode
            require_once 'Net/CDDB/Disc.php';
            $valueToEncode = new Net_CDDB_Disc($valueToEncode);
            $str = $valueToEncode->toString();
        }
        
        if ($encoding !== 'utf8') {
           $str = mb_convert_encoding($str, $encoding, 'utf8');
        }
        
        return $str;
    }
}