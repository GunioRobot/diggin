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
 * @package    Diggin_Service
 * @subpackage Wedata
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';
require_once 'Diggin/Service/Exception.php';

/**
 * 
 * 
 */
class Diggin_Service_Wedata extends Zend_Service_Abstract
{
    const API_URL = 'http://wedata.net';
    
    //database
    const PATH_GET_DATABASES = '/databases.json';
    const PATH_GET_DATABASE  = '/databases/%s.json';
    const PATH_CREATE_DATABASE = '/databases';
    const PATH_UPDATE_DATABASE = '/databases/%s';
    const PATH_DELETE_DATABASE = '/databases/%s';
    
    //item
    const PATH_GET_ITEMS = '/databases/%s/items.json';//dbname
    const PATH_GET_ITEM  = '/items/%s.json'; //item id
    const PATH_CREATE_ITEM = '/databases/%s/items'; //dbname
    const PATH_UPDATE_ITEM = '/items/%s'; //item id
    const PATH_DELETE_ITEM = '/items/%s'; //item id

    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected static $_client;

    protected static $_params;

    protected static $_decodetype;

    /**
     * Constructs a new Wedata Web Service Client
     *
     * @param array $params parameter acording Wedata
     * @param boolean | string @see Zend_Json
     * @return null
     */
    public function __construct(array $params = null, $decodetype = null)
    {
        self::$_params = $params;
        self::$_decodetype = $decodetype;
    }
    
    protected static function _decode($value)
    {
        if (self::$_decodetype === false) {
            //nothig to do
        } else {
            require_once 'Zend/Json.php';
            if (self::$_decodetype === null) {
                $value = Zend_Json::decode($value, Zend_Json::TYPE_ARRAY);
            } else {
                $value = Zend_Json::decode($value, self::$_decodetype);
            }    
        }
        
        return $value;
    }
    
    public static function getParams()
    {
        return self::$_params;
    }
    
    public static function getParam($key)
    {
        return self::$_params[$key];
    }
    
    /**
     * setting parameter
     * 
     * @param array $params
     */
    public static function setParams(array $params)
    {
        foreach ($params as $key => $value){
            self::$_params[strtolower($key)] = $value;
        }
    }
    
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public static function setParam($key, $value)
    {
        self::$_params[$key] = $value;
    }
    
    /**
     * adding parameter
     * 
     * @param string $key
     * @param string $value
     */
    public static function setParamDatabase($key, $value)
    {
        self::$_params['database'][$key] = $value;
    }
    
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public static function setApikey($key)
    {
        self::$_params['api_key'] = $key;
    }
        
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public static function setDatabaseName($databaseName)
    {
        self::$_params['database']['name'] = $databaseName;
    }

    
    /**
     * Handles all requests to a web service
     * 
     * @param string path
     * @param string Prease,using Zend_Http_Client's const
     * @return mixed
     */
    public static function makeRequest($path, $method, array $params = null)
    {
        self::$_client = self::getHttpClient();
        
        require_once 'Zend/Uri/Http.php';
        $uri = Zend_Uri_Http::factory(self::API_URL);
        $uri->setPath($path);

        if (!is_null($params)) {            
            if ($method == Zend_Http_Client::GET) {
                self::$_client->setParameterGet($params);
            } elseif ($method == Zend_Http_Client::POST) {
                self::$_client->setParameterPost($params);
            } else {
                $uri->setQuery($params);
            }
        }

        self::$_client->setUri($uri->getUri());
        
        $response = self::$_client->request($method);
        
        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Exception
              */
             require_once 'Diggin/Service/Exception.php';
             throw new Diggin_Service_Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        //returning response switching by Reqest Method
        if ($method == Zend_Http_Client::GET) {
            return $response->getBody();
        } else {
            $status = $response->getStatus();
            $headers = $response->getHeaders();
            return array($status, $headers);
        }
    }
    
    public static function getDatabases(array $params = null)
    {
        if ($params) self::setParams($params);
        
        $responseBody = self::makeRequest(self::PATH_GET_DATABASES, Zend_Http_Client::GET, self::$_params);
        
        return self::_decode($responseBody);
    }

    public static function getDatabase($databaseName = null, $page = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($page) self::setParam('page', $page);
        
        $path = sprintf(self::PATH_GET_DATABASE, rawurlencode(self::$_params['database']['name']));
        $responseBody = self::makeRequest($path, Zend_Http_Client::GET, self::$_params);
        
        return self::_decode($responseBody);
    }

    public static function createDatabase(array $params = null)
    {
        if ($params) self::setParams($params);
        
        if(!isset(self::$_params['api_key'])){
            throw new Diggin_Service_Exception('API key is not set ');
        } elseif (!isset(self::$_params['database']['name'])) {
            throw new Diggin_Service_Exception('Database name is not set ');
        } elseif (!isset(self::$_params['database']['required_keys'])) {
            throw new Diggin_Service_Exception('required_keys is not set');
        }
        
        $return = self::makeRequest(self::PATH_CREATE_DATABASE, Zend_Http_Client::POST, self::$_params);
        
        return $return;
    }
    
    
    public static function udpateDatabase($databaseName = null, array $params = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($params) self::setParams($params);
        
        if(!isset(self::$_params['api_key'])){
            throw new Diggin_Service_Exception('API key is not set ');
        } elseif (!isset(self::$_params['database']['required_keys'])) {
            throw new Diggin_Service_Exception('required_keys is not set');
        }

        $path = sprintf(self::PATH_UPDATE_DATABASE, rawurlencode(self::$_params['database']['name']));
        $return = self::makeRequest($path, Zend_Http_Client::PUT, self::$_params);
        
        return $return;
    }
    
    public static function deleteDatabase($databaseName = null, $apiKey = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($apiKey) self::setApikey($apiKey);
        
        if (!isset(self::$_params['database']['name'])) {
            throw new Diggin_Service_Exception('Database name is not set ');
        }
        
        if (isset(self::$_params['api_key'])) {
            $params = array('api_key' => self::$_params['api_key']);
        } else {
            throw new Diggin_Service_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_DELETE_DATABASE, rawurlencode(self::$_params['database']['name']));
        $return = self::makeRequest($path, Zend_Http_Client::DELETE, $params);
        
        return $return;
    }
    
    //////item methods    
    public static function getItems($databaseName = null, $page = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($page) self::setParam('page', $page);
        
        if (isset(self::$_params['page'])) {
            $params = array('page' => self::$_params['page']);
        } else {
            $params = null;
        }
        
        $path = sprintf(self::PATH_GET_ITEMS, rawurlencode(self::$_params['database']['name']));
        $responseBody = self::makeRequest($path, Zend_Http_Client::GET, $params);
        
        return self::_decode($responseBody);
    }

    /**
     * 
     * @param string $itemId
     * @param string $page
     * @return array Decording Result
     */
    public static function getItem($itemId, $page = null)
    {
        //@todo if int set as itemid or string searching itemid by name
        //is_integer($item);
        //is_string($item) ;
        
        if ($page) self::setParam('page', $page);
        
        if (isset(self::$_params['page'])) {
            $params = array('page' => self::$_params['page']);
        } else {
            $params = null;
        }

        $path = sprintf(self::PATH_GET_ITEM, $itemId);
        $responseBody = self::makeRequest($path, Zend_Http_Client::GET, $params);
        
        return self::_decode($responseBody);
    }
    
    public static function insertItem($databaseName = null, array $params = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($params) self::setParams($params);
        
        $path = sprintf(self::PATH_CREATE_ITEM, rawurlencode(self::$_params['database']['name']));
        $return = self::makeRequest($path, Zend_Http_Client::POST, self::$_params);
        
        return $return;
    }
    
    public static function updateItem($itemId, array $params = null)
    {
        if ($params) self::setParams($params);
        
        if (!isset(self::$_params['api_key'])) {
            throw new Diggin_Service_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_UPDATE_ITEM, $itemId);
        $return = self::makeRequest($path, Zend_Http_Client::PUT, self::$_params);
        
        return $return;
    }
    
    public static function deleteItem($itemId, $apiKey = null)
    {
        if ($apiKey) self::setApikey($apiKey);
        
        if (isset(self::$_params['api_key'])) {
            $params = array('api_key' => self::$_params['api_key']);
        } else {
            throw new Diggin_Service_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_DELETE_ITEM, $itemId);
        $return = self::makeRequest($path, Zend_Http_Client::DELETE, $params);
        
        return $return;
    }
}