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
 * @package    Diggin_Service
 * @subpackage Tumblr
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Service\Tumblr;

/**
 * @see Zend_Service_Abstract
 */
// require_once 'Zend/Service/Abstract.php';

/**
 * @category   Diggin
 * @package    Diggin_Service_Tumblr
 * @subpackage Tumblr_Read
 * @author     sasezaki
 * @see http://www.tumblr.com/api
 *
 * @todo readのモデル
 */

class Read extends \Zend\Service\Abstract
{
    
    const API_URL = 'http://%s.tumblr.com/api/read';
    
    const READ_NUM_MAX = 50;
    
    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected $_client;

    private $_apiUrl;
    
    /**
     * Microtime of last request
     *
     * @var float
     */
    protected static $_lastRequestTime = 0;

    /**
     * Constructs a new Tumblr Web Services Client
     *
     * @param  string $target subdomain of tumblr OR url
     * @return null
     */
    public function __construct($target = null)
    {
    
        if (parse_url($target, PHP_URL_HOST)) {
           $apiUrl = $target;
        } else {
            $apiUrl = sprintf(self::API_URL, $target);
        }
    
        $this->_apiUrl = $apiUrl;
    }

    /**
     * Set target
     *
     * @param  string $target subdomain
     */
    public function setTarget($target)
    {
        $this->_apiUrl = sprintf(self::API_URL, $target);
    }
    
    
    public function setApiUrl($url)
    {
        $this->_apiUrl = $url;
    }
    
    public function getApiUrl()
    {
        return $this->_apiUrl;
    }

    public function getTotal()
    {
        return $this->countTotal($this->makeRequest());
    }
    
    public function countTotal(\DOMDocument $dom)
    {
        $rootNode = $dom->documentElement;

        if ($rootNode->nodeName == 'tumblr') {
            $childNodes = $rootNode->childNodes;

            for ($i = 0; $i < $childNodes->length; $i++) {
                $currentNode = $childNodes->item($i);
                if ($currentNode->nodeName == 'posts') {
                    (integer)$total = $currentNode->getAttribute('total');
                }
            }
        } else {
            /**
             * @see Diggin_Service_Tumblr_Exception
             */
            // require_once 'Diggin/Service/Tumblr/Exception.php';
            throw new Exception('Tumblr web service has returned something odd!');
        }

        return $total;
    }
    
    /*
     * get 'posts' as array
     * 
     * @param array $parms
     * @return array $posts
     */
    public function getPosts ($parms = array(), $maxWidth = 500)
    {
        $start = 0;
        $loop = 1;
        $postsArr = array();
        
        if (isset($parms['start'])) {
            $start = $parms['start'];
        }
        
        if ($parms['num'] > self::READ_NUM_MAX) {
            $loop += floor($parms['num']/self::READ_NUM_MAX);
            $parms['num'] = self::READ_NUM_MAX;
        }
        
        for ($i = 0; $i < $loop; $i++) {
            $parms['start'] = $i * self::READ_NUM_MAX + $start;
            $response = $this->makeRequest($parms);
            $postsArr = $postsArr + $this->_xmlResponseToPostArray($response, $maxWidth);
        }
                    
        return $postsArr;
    }
    
    public function dumpAsXmls($path = '/workspace/', $filePrefix = 'tumblr_', $parms = array())
    {
         
        if (!$parms['num']) {
            $parms['num'] = $this->getTotal(); 
        }      

        $start = 0;
        $num = 50;
        $loop = 1;
        $postsArr = array();
        
        if ($parms['start']) {
            $start = $parms['start'];
        }
        $paramNum = $parms['num'];
        if ($paramNum >= 50) {
            $loop += floor($parms['num']-1/$num);
            $parms['num'] = 50;
        }
        
        for ($i = 0; $i < $loop; $i++) {
            $parms['start'] = $i * $num + $start;
            //if ($i + 1 == $loop and ($mod = fmod($parmNum, $num)) !== 0) $parms['num'] = $mod;
            $response = $this->makeRequest($parms);
            $response->save($path.$filePrefix.$i.'.xml');
        }
        
    }
    
    
    //This is test method
    public function getAllPhotoUrl()
    {
        $parms['type'] = 'photo';
        
        $parms['num'] = $this->getTotal();
        $posts = $this->getPosts($parms);
        foreach ($posts as $post) {
            $arrPhotoUrl[] = $post['photo-url'];
        }     
         
        return $arrPhotoUrl;
    }
    
        
    /**
     * Handles all GET requests to a web service
     *
     * @param  array  $parms Array of GET parameters
     * @return mixed  decoded response from web service
     * @throws Diggin_Service_Tumblr_Exception
     */
    public function makeRequest(array $parms = array())
    {
        // if previous request was made less then 1 sec ago
        // wait until we can make a new request
        $timeDiff = microtime(true) - self::$_lastRequestTime;
        if ($timeDiff < 1) {
            usleep((1 - $timeDiff) * 1000000);
        }

        $this->_client = self::getHttpClient();
        $this->_client->setUri(self::getApiUrl());
        if (isset($parms)) {
            $this->_client->setParameterGet($parms);
        }
        
        self::$_lastRequestTime = microtime(true);
        $response = $this->_client->request();
        
        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Tumblr_Exception
              */
            // require_once 'Diggin/Service/Tumblr/Exception.php';
             throw new Exception("Http client reported an error: '{$response->getMessage()}'");
        }

        $responseBody = $response->getBody();
        
        $dom = new \DOMDocument() ;
    
           if (!@$dom->loadXML($responseBody)) {
               /**
                * @see Diggin_Service_Tumblr_Exception
                */
               // require_once 'Diggin/Service/Tumblr/Exception.php';
               throw new Exception('XML Error');
           }
    
        return $dom;

    }
    
    /**
     * Transform XML string to array
     *
     * @param  DOMDocument $response
     * @param  string      $maxWidth
     * @return array
     */
    protected static function _xmlResponseToPostArray(\DOMDocument $response, $maxWidth = '500')
    {
        $child = 'posts';   //childには　tumblelog, posts
        $arrOut = array();
        $rootNode = $response->documentElement;
        
        $childNodes = $rootNode->childNodes;

        for ($i = 0; $i < $childNodes->length; $i++) {
                $currentNode = $childNodes->item($i);
                if ($currentNode->nodeName == $child) {
                    
                    for ($n = 0; $n < $currentNode->childNodes->length; $n++){
                        $postNode = $currentNode->childNodes->item($n);
                        
                        $id = $postNode->getAttribute('id');
                        $arrOut[$id]['id'] = $postNode->getAttribute('id');
                        $arrOut[$id]['url'] = $postNode->getAttribute('url');
                        $arrOut[$id]['type'] = $postNode->getAttribute('type');
                        $arrOut[$id]['date'] = $postNode->getAttribute('date');

                        for ($m = 0; $m < $postNode->childNodes->length; $m++){
                           $postChildNode = $postNode->childNodes->item($m);
                           if ($postChildNode->nodeName == 'photo-url') {
                               if ($postChildNode->getAttribute('max-width') == $maxWidth){
                                    $arrOut[$id][$postChildNode->nodeName] = $postChildNode->nodeValue;
                               }
                           } else {
                               $arrOut[$id][$postChildNode->nodeName] = $postChildNode->nodeValue;
                           }
                        }
                    }
                }

        }
        
        return $arrOut;
    }

}
