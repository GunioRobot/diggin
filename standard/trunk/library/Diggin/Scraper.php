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
 * @package    Diggin_Scraper
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/** Diggin_Scraper_Process_Aggregate */
require_once 'Diggin/Scraper/Process/Aggregate.php';

/** Diggin_Scraper_Context */
require_once 'Diggin/Scraper/Context.php';

/** Zend_Loader_PluginLoader */
require_once 'Zend/Loader/PluginLoader.php';

/**
 * @category  Diggin
 * @package   Diggin_Scraper
 * @copyright 2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license   http://diggin.musicrider.com/LICENSE     New BSD License
 */ 
class Diggin_Scraper extends Diggin_Scraper_Process_Aggregate
{
    /**
     * scraping results
     * 
     * @var array 
     */
    protected $_results;
    
    /**
     * target url of scraping
     * 
     * @var string 
     */
    protected $_url;
    
    /**
     * strategy name to use for changing strategy
     *
     * @param string $_strategyName
     */
    private static $_strategyName;
    
    /**
     * adapter for response
     *
     * @param Diggin_Scraper_Adapter_Interface $_adapter
     */
    private static $_adapter;

    /**
     * strategy for scraping
     *
     * @param Diggin_Scraper_Strategy_Abstract $_strategy
     */
    protected $_strategy = null;
    
    /**
     * helper loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected $_helperLoader;

    /**
     * Getting the URL for scraping
     * 
     * @return string $this->_url
     */
    private function _getUrl()
    {
        return $this->_url;
    }

    /**
     * Set the Url for scraping
     * 
     * @param string $url
     */
    public function setUrl ($url) 
    {
        $this->_url = $url;
    }

    /**
     * HTTP client object to use for retrieving
     *
     * @var Zend_Http_Client
     */
    protected static $_httpClient = null;

    /**
     * Read only properties accessor
     *
     * @param  string $var property to read
     * @return mixed
     */
    public function __get($var)
    {
        return $this->_results[$var];
    }


    public function __construct()
    {
        //initialize helper
        $this->_helperLoader = 
            new Zend_Loader_PluginLoader(array(
            'Diggin_Scraper_Helper_Simplexml' => 'Diggin/Scraper/Helper/Simplexml'));
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.
     *
     * @param  Zend_Http_Client $httpClient
     * @return null
     */
    public static function setHttpClient(Zend_Http_Client $httpClient)
    {
        self::$_httpClient = $httpClient;
    }

    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client_Abstract
     */
    public static function getHttpClient()
    {
        if (!self::$_httpClient instanceof Zend_Http_Client) {
            /**
             * @see Zend_Http_Client
             */
            require_once 'Zend/Http/Client.php';
            self::$_httpClient = new Zend_Http_Client();
        }

        return self::$_httpClient;
    }

    /**
     * changing Strartegy
     * 
     * @param string $strategyName
     * @param Diggin_Scraper_Adapter_Interface $adapter
     */
    public static function changeStrategy($strategyName, $adapter = null)
    {
        if ($strategyName === false) {
            self::$_strategyName = null;
            self::$_adapter = null;
        } else {
            self::$_strategyName = $strategyName;
            self::$_adapter = $adapter;
        }
    }

    /**
     * calling this scraper's strategy
     * 
     * @param Zend_Http_Response $response
     * @param string $strategyName
     * @param Object Diggin_Scraper_Adapter_Interface (optional)
     * @throws Diggin_Scraper_Exception
     */
    private function _callStrategy($response, $strategyName, $adapter = null)
    {
        require_once 'Zend/Loader.php';

        try {
            Zend_Loader::loadClass($strategyName);
        } catch (Zend_Exception $e) {
            require_once 'Diggin/Scraper/Exception.php';
            throw new Diggin_Scraper_Exception("Unable to load strategy '$strategyName': {$e->getMessage()}");
        }

        $strategy = new $strategyName($response);
        if (method_exists($strategy, 'setBaseUrl')) {
            $strategy->setBaseUrl($this->_getUrl());
        }
        if ($adapter) $strategy->setAdapter($adapter);
        if (method_exists($strategy->getAdapter(), 'setConfig')) {
            $strategy->getAdapter()->setConfig(array('url' => $this->_getUrl()));
        }

        $this->_strategy = $strategy;
    }

    /**
     * Return this scraper's strategy
     * 
     * @param Zend_Http_Response $response
     * @return Diggin_Scraper_Strategy
     */
    public function getStrategy($response)
    {
        if (!$this->_strategy instanceof Diggin_Scraper_Strategy_Abstract) {
            /**
             * @see Diggin_Scraper_Strategy_Abstract
             */
            require_once 'Diggin/Scraper/Strategy/Flexible.php';
            $strategy = new Diggin_Scraper_Strategy_Flexible($response);
            $strategy->setBaseUrl($this->_getUrl());
            $strategy->getAdapter()->setConfig(array('url' => $this->_url));
            
            $this->_strategy = $strategy;
        }
        
        return $this->_strategy;
    }
    
    /**
     * making request
     * 
     * @param string $url
     * @return Zend_Http_Response $response
     * @throws Diggin_Scraper_Exception
     */
    protected function _makeRequest($url = null)
    {
        $client = self::getHttpClient();
        
        if ($url) {
            $this->setUrl($url);
            $client->setUri($url);
        } else {
            $this->setUrl($client->getUri());
        }

        $response = $client->request();

        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Scraper_Exception
              */
             require_once 'Diggin/Scraper/Exception.php';
             throw new Diggin_Scraper_Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        return $response;
    }

    /**
     * Get response via mixed pattern
     * 
     * @param mixed
     */
    protected function getResponse($resource)
    {
        //psuedo reponse
        if (is_array($resource) and !isset($resource['body'])) {
            $resource['body'] = $resource['0'];
            if (!array_key_exists('header', $resource)) {
                $resource['header'] = "HTTP/1.1 200 OK\r\nContent-type: text/html";
            }
            $responseStr = $resource['header']."\r\n\r\n".$resource['body'];
            require_once 'Zend/Http/Response.php';
            $resource = Zend_Http_Response::fromString($responseStr);
        }
        
        // if set uri
        if (!$resource instanceof Zend_Http_Response) {
            $resource = $this->_makeRequest($resource);
        }
        
        return $resource;
    }
    
    /**
     * scraping
     * 
     * @param (string | Zend_Http_Response | array) $resource
     *      setting URL, Zend_Http_Response, array($html)
     * @param string (if $resource is not URL, please set URL for recognize)
     * @return array $this->results Scraping data.
     * @throws Diggin_Scraper_Exception
     *          Diggin_Scraper_Strategy_Exception
     *          Diggin_Scraper_Adapter_Exception
     */
    public function scrape($resource = null, $baseUrl = null)
    {
        $resource = $this->getResponse($resource);
        
        if (isset($baseUrl)) {
            $this->setUrl($baseUrl);
        }

        if (!is_null(self::$_strategyName)) {
            $this->_callStrategy($resource, self::$_strategyName, self::$_adapter);
        }
        $context = new Diggin_Scraper_Context($this->getStrategy($resource));
        foreach ($this as $process) {
            $values = $this->_strategy->getValues($context, $process);
            $this->_results[$process->getName()] = $values;
        }

        return $this->_results;
    }
    
    /**
     * Get scraping results
     * 
     * @return mixed
     */
    public function getResults()
    {
        return $this->_results; 
    }


    /**
     * get this helper's plugin loader
     *
     * @return Zend_Loader_PluginLoader 
     */
    public function getHelperLoader()
    {
        return $this->_helperLoader;
    }

    /**
     * getHelper() - get Helper by name
     *
     * @param string $name
     * @return Diggin_Scraper_Helper_HelperAbstract
     */
    public function getHelper($name)
    {
        $class = $this->getHelperLoader()->load($name);

        return new $class($this->_strategy->readResource(), 
                          array('baseUrl' => $this->_getUrl(),
                                'preAmpFilter' => true)
                          );
    }

    /**
     * call helper direct
     *
     * @param string $method
     * @param array $args
     */ 
    public function __call($method, $args)
    {
        $helper = $this->getHelper($method);
        if (!method_exists($helper, 'direct')) {
            require_once 'Diggin/Scraper/Exception.php';
            throw new Diggin_Scraper_Exception('Helper "'.$method.'" does not support overloading via direct()');
        }

        return call_user_func_array(array($helper, 'direct'), $args);
    }
}
