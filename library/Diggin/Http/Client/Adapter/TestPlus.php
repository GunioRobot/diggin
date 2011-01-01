<?php
/**
 * This class is remodeling of Zend_Http_Client_Adapter_Test
 * 
 * Zend Framework : 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Http\Client\Adapter;

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
 * @subpackage Client_Adapter
 * @copyright  2006-2011 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

// require_once 'Zend/Uri/Http.php';
// require_once 'Zend/Http/Response.php';
// require_once 'Zend/Http/Client/Adapter/Interface.php';

/**
 * A testing-purposes adapter.
 * Advanced & Based :Zend_Http_Client_Adapter_Test
 */
class TestPlus implements \Zend\Http\Client\Adapter\Interface
{
    /**
     * Parameters array
     *
     * @var array
     */
    protected $config = array();

    /**
     * Buffer of responses to be returned by the read() method.  Can be
     * set using setResponse() and addResponse().
     *
     * @var array
     */
    protected $responses = array("HTTP/1.1 400 Bad Request\r\n\r\n");

    /**
     * Current position in the response buffer
     *
     * @var integer
     */
    protected $responseIndex = 0;
    
    /**
     * size of response
     *
     * @var unknown_type
     */
    protected $_resposesize;
    
    /**
     * random status
     *
     * @var array
     */
    protected $_statusRandom;
    
    
    protected $_connectiingTime = 0;
    
	/**     *
     * @var unknown_type
     */
    protected $_randomResponseBodyInterval;

    /**
     * test response Body
     *
     * @var array
     */
    protected $_testResponseBody = array();
    
    /**
     * Adapter constructor
     *
     * @param string responseBody (optional)
     * @param string responseHeader (optional)
     */
    public function __construct($responseBody = null, $responseHeader = null)
    {
        if (is_null($responseBody)) {
            $responseBody = '<html lang="ja">'.PHP_EOL.
                           '<head>'.PHP_EOL.
                           '<body>'.PHP_EOL.
                           'this is testplus<br />'.PHP_EOL.
                           '</body>'.PHP_EOL.
                           '</html>';
        }
        
        if (is_null($responseHeader)) {
           $responseHeader = 
           "HTTP/1.1 200 OK"        ."\r\n".
           "Date: Sat, 02 Aug 2008 15:17:11 GMT"."\r\n".
           "Server: Apache/2.2.6 (Win32) mod_ssl/2.2.6 OpenSSL/0.9.8e PHP/5.2.5"."\r\n".
           "Last-modified: Sun, 29 Jun 2008 21:20:50 GMT"."\r\n".
           "Accept-ranges: bytes"   . "\r\n" .
           "Content-length: 1000"   . "\r\n" .
           "Connection: close"      . "\r\n" .
           "Content-type: text/html; charset=utf-8;";
           
        }
        
        $this->setResponse($responseHeader."\r\n\r\n".$responseBody);
    }

    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array())
    {
        if (! is_array($config)) {
            // require_once 'Diggin/Http/Client/Adapter/Exception.php';
            throw new Exception(
                '$config expects an array, ' . gettype($config) . ' recieved.');
        }

        foreach ($config as $k => $v) {
            $this->config[strtolower($k)] = $v;
        }
    }

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     * @param int     $timeout
     */
    public function connect($host, $port = 80, $secure = false)
    { 
        //fake time out
        sleep($this->_connectiingTime);
        if ($this->_connectiingTime > (int) $this->config['timeout']) {
            // require_once 'Diggin/Http/Client/Adapter/Exception.php';
            throw new Exception('Unable to set the connection timeout(fake)');
        }
    }

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param Zend_Uri_Http $uri
     * @param string        $http_ver
     * @param array         $headers
     * @param string        $body
     * @return string Request as string
     */
    public function write($method, $uri, $http_ver = '1.1', $headers = array(), $body = '')
    {
        $host = $uri->getHost();
            $host = (strtolower($uri->getScheme()) == 'https' ? 'sslv2://' . $host : $host);

        // Build request headers
        $path = $uri->getPath();
        if ($uri->getQuery()) $path .= '?' . $uri->getQuery();
        $request = "{$method} {$path} HTTP/{$http_ver}\r\n";
        foreach ($headers as $k => $v) {
            if (is_string($k)) $v = ucfirst($k) . ": $v";
            $request .= "$v\r\n";
        }

        // Add the request body
        $request .= "\r\n" . $body;

        // Do nothing - just return the request as string
        
        return $request;
    }

    /**
     * Return the response set in $this->setResponse()
     *
     * @return string
     */
    public function read()
    {
        if ($this->responseIndex >= count($this->responses)) {
            $this->responseIndex = 0;
        }
        $response_str = $this->responses[$this->responseIndex];
        $this->responseIndex++;
        
        if ($this->_statusRandom)
        {
            
            
            $keys = array();
            $start_index = 0;
            foreach ($this->_statusRandom as $code => $num) {
                $keys = array_merge($keys, array_fill($start_index, $num, $code));
                $start_index = $start_index + $num;
            }            
            
            shuffle($keys);
            $code = current($keys);
            
            $headers = \Zend\Http\Response::extractHeaders($response_str);
            $body = \Zend\Http\Response::extractBody($response_str);
            $response = new \Zend\Http\Response($code, $headers, $body);
            
            $response_str = $response->asString();            
        }
        
        if ($this->_randomResponseBodyInterval)
        {
            if(time() % 2 == 1) {
                $code = \Zend\Http\Response::extractCode($response_str);
                $headers = \Zend\Http\Response::extractHeaders($response_str);
                $response = new \Zend\Http\Response($code, $headers, $this->_testResponseBody[1]);
                $response_str = $response->asString();
            }
        }
        
        if ($this->_resposesize)
        {
            $response_str = str_pad($response_str, $this->_resposesize);
        }
             
        return $response_str;
    }

    /**
     * Close the connection (dummy)
     *
     */
    public function close()
    { }

    /**
     * Set the HTTP response(s) to be returned by this adapter
     *
     * @param Zend_Http_Response|array|string $response
     */
    public function setResponse($response)
    {
        if ($response instanceof \Zend\Http\Response) {
            $response = $response->asString();
        }

        $this->responses = (array)$response;
        $this->responseIndex = 0;
    }

    /**
     * Add another response to the response buffer.
     *
     * @param string $response
     */
    public function addResponse($response)
    {
        $this->responses[] = $response;
    }

    /**
     * Sets the position of the response buffer.  Selects which
     * response will be returned on the next call to read().
     *
     * @param integer $index
     */
    public function setResponseIndex($index)
    {
        if ($index < 0 || $index >= count($this->responses)) {
            // require_once 'Diggin/Http/Client/Adapter/Exception.php';
            throw new Exception(
                'Index out of range of response buffer size');
        }
        $this->responseIndex = $index;
    }
    
    /**
     * Sets fake response size(by strlen oops..) 
     *
     * @param int $size
     */
    public function setResponseSize($size)
    {
        $this->_resposesize = $size;
    }
    
    /**
     * statusがkeyで、頻度をvalueにセットした配列を渡す
     * 
     * @param mixed 
     */
    public function setStatusRandom(array $config = 
                                        array(200 => 10, 404 => 1, 500 =>1))
    {
        
        //$responseCode = Zend_Http_Response::responseCodeAsText();
        //@todoステータスコードにないものをキーに設定したときexception
        
        $this->_statusRandom = $config;
    }
    
    /**
     * set connect time
     *
     * @param int $time 
     */
    public function setConnectingTime($time)
    {
        $this->_connectiingTime = $time;
    }
    
    public function setResponseBodyRandom($timeInterval = 5, $addResponseBody = null)
    {
        $this->_randomResponseBodyInterval = $timeInterval;
        

        //@todo array_shift(func_get_args)        
        if (is_null($addResponseBody)) {
        $addResponseBody = '<html lang="ja">'.
                           '<head>'.
                           '<body>'.
                           'this is testplus!!!!!<br />'.
                           '</body>'.
                           '</html>';
        }

      $this->_testResponseBody[1] = $addResponseBody;
    }
    
    /**
     * Getting Zend_Http_Client
     *
     * @param string $url
     * @return Zend_Http_Client
     */
    public function getAsClient($url = 'http://example.net'){
        // require_once 'Zend/Http/Client.php';
        return new \Zend\Http\Client($url, array('adapter' => $this));
    }
}
