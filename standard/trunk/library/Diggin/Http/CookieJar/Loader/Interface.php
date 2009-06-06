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
 * @package    Diggin_Http
 * @subpackage CookieJar_Loader
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

interface Diggin_Http_CookieJar_Loader_Interface
{
    public static function load($path, $ref_uri = true, $use_topppp_domain = false); 
}