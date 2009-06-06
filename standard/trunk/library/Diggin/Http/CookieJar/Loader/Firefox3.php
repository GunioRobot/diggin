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

/**
 * @see Diggin_Http_CookieJar_Loader_Interface
 */
require_once 'Diggin/Http/CookieJar/Loader/Interface.php';

class Diggin_Http_CookieJar_Loader_Firefox3
      implements Diggin_Http_CookieJar_Loader_Interface
{
    const FILENAME_SQLITE = 'cookies.sqlite';
    const TABLENAME = 'moz_cookies';
    
    /**
     * load sqllite file to cookiejar
     *
     * @param string $path
     * @param string | Zend_Uri_Http | boolean $ref_uri
     *       (falseの場合は全件fetch)
     * @param string $use_topppp_domain //@todo
     * @return mixed Zend_Http_CookieJar | false
     * @throws Diggin_Http_CookieJar_Loader_Exception
     */
    public static function load($path, $ref_uri = true, $use_topppp_domain = false)
    {
        $path = realpath($path);
        
        if (is_dir($path)) {
            $path = $path.DIRECTORY_SEPARATOR.self::FILENAME_SQLITE;
        }
        
        if (!is_file($path)) {
            require_once 'Diggin/Http/CookieJar/Loader/Exception.php';
            throw new Diggin_Http_CookieJar_Loader_Exception('invalid path : '.$path);
        }
        
        if ($ref_uri === true) {
            require_once 'Diggin/Http/CookieJar/Loader/Exception.php';
            throw new Diggin_Http_CookieJar_Loader_Exception('$ref_uri is not set');
        }
                
        if ($ref_uri instanceof Zend_Uri_Http) {
           $host = $ref_uri->getHost();
        } elseif (is_string($ref_uri)) {
           $host = parse_url($ref_uri, PHP_URL_HOST) ? parse_url($ref_uri, PHP_URL_HOST) : $ref_uri;
        }
        
        require_once 'Zend/Db.php';
        try {
            $db = Zend_Db::factory('Pdo_Sqlite', array('dbname' => $path) );
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $select = $db->select()->from(self::TABLENAME);
            if ($ref_uri !== false) {
                $select = $select->where('host = ?', $host)->orWhere('host = ?', '.'.$host);
            
                if (is_string($use_topppp_domain)) {
                    $select = $select->orWhere('host = ?', $use_topppp_domain);
                }
            }
    
            $fetch = $db->fetchAll($select);
        } catch (Zend_Db_Exception $e) {
            require_once 'Diggin/Http/CookieJar/Loader/Exception.php';
            throw new Diggin_Http_CookieJar_Loader_Exception($e);
        }

        if (count($fetch) === 0) {
            return false;
        }
        
        require_once 'Zend/Http/CookieJar.php';
        $cookieJar = new Zend_Http_CookieJar();
        foreach ($fetch as $result) {
            if ($result->name and $result->value and $result->host) {  
                $cookie = new Zend_Http_Cookie($result->name,  //cookie->name
                                               $result->value, //cookie->value
                                               $result->host,   //cookie->domain
                                               $result->expiry, // exipry / cookie->expires
                                               $result->path,   //cookie->path = null, 
                                               (boolean)$result->isSecure);
                $cookieJar->addCookie($cookie);
            }
        }
        
        return $cookieJar;
    }
}
