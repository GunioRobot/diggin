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
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Http\CookieJar\Loader;

/**
 * @see Diggin_Http_CookieJar_Loader_Interface
 */
// require_once 'Diggin/Http/CookieJar/Loader/Interface.php';

class Firefox3
      implements LoaderInterface
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
            // require_once 'Diggin/Http/CookieJar/Loader/Exception.php';
            throw new Exception('invalid path : '.$path);
        }
        
        if ($ref_uri === true) {
            // require_once 'Diggin/Http/CookieJar/Loader/Exception.php';
            throw new Exception('$ref_uri is not set');
        }
                
        if ($ref_uri instanceof \Zend\Uri\Http) {
           $host = $ref_uri->getHost();
        } elseif (is_string($ref_uri)) {
           $host = parse_url($ref_uri, PHP_URL_HOST) ? parse_url($ref_uri, PHP_URL_HOST) : $ref_uri;
        }
        
        // require_once 'Zend/Db.php';
        try {
            $db = \Zend\Db::factory('Pdo_Sqlite', array('dbname' => $path) );
            $db->setFetchMode(\Zend\Db::FETCH_OBJ);
            $select = $db->select()->from(self::TABLENAME);
            if ($ref_uri !== false) {
                $select = $select->where('host = ?', $host)->orWhere('host = ?', '.'.$host);
            
                if (is_string($use_topppp_domain)) {
                    $select = $select->orWhere('host = ?', $use_topppp_domain);
                }
            }
    
            $fetch = $db->fetchAll($select);
        } catch (\Zend\Db\Exception $e) {
            // require_once 'Diggin/Http/CookieJar/Loader/Exception.php';
            throw new Exception($e);
        }

        if (count($fetch) === 0) {
            return false;
        }
        
        // require_once 'Zend/Http/CookieJar.php';
        $cookieJar = new \Zend\Http\CookieJar();
        foreach ($fetch as $result) {
            if ($result->name and $result->value and $result->host) {  
                $cookie = new \Zend\Http\Cookie($result->name,  //cookie->name
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
