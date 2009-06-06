<?php
/**
 * This class is remodeling of Zend_Debug
 * 
 * Zend Framework : 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

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
 * @package    Diggin_Debug
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

class Diggin_Debug
{
    /**
     * @var string
     */
    protected static $_sapi = null;
    
    /**
     * @var string
     */
    protected static $_os = null;

    /**
     * Get the current value of the debug output environment.
     * This defaults to the value of PHP_SAPI.
     *
     * @return string;
     */
    public static function getSapi()
    {
        if (self::$_sapi === null) {
            self::$_sapi = PHP_SAPI;
        }
        return self::$_sapi;
    }

    /**
     * Set the debug ouput environment.
     * Setting a value of null causes Zend_Debug to use PHP_SAPI.
     *
     * @param string $sapi
     * @return null;
     */
    public static function setSapi($sapi)
    {
        self::$_sapi = $sapi;
    }

    public static function getOs()
    {
        if (self::$_os === null) {
            self::$_os = PHP_OS;
        }
        return self::$_os;
    }
    
    
    public static function dump($var, $configs = array())
    {
        if (self::getOs() === 'WINNT') {
        
            $config = array(
                        'label'        => null,
                        'echo'         => TRUE,
                        'toEncoding'   => 'sjis',
                        'fromEncoding' => 'utf-8',
                        'start'        => 0,
                        'length'       => 80000,
            );
        } else {
             $config = array(
                        'label'        => null,
                        'echo'         => TRUE,
                        'start'        => 0,
                        'length'       => 80000,
            );
        }

        foreach ($configs as $conf => $setting) {
            $config[strtolower($conf)] = $setting;
        }

        // format the label
        $label = ($config['label']===null) ? '' : rtrim($config['label']) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (isset($config['toEncoding']) and isset($config['fromEncoding'])) {
            $output = mb_convert_encoding($output, $config['toEncoding'], $config['fromEncoding']);
        }
        $output= substr($output, $config['start'], $config['length']);

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (self::getSapi() == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES)
                    . '</pre>';
        }

        if ($config['echo'] === TRUE) {
            echo($output);
        }
        return $output;
    }

}
