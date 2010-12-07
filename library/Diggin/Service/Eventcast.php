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
 * @subpackage Eventcast
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Service;

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';

/**
 * @see Zend_Uri_Http
 */
require_once 'Zend/Uri/Http.php';

/**
 * 
 * @see http://clip.eventcast.jp/webservices/api.html
 */
class Eventcast extends \Zend\Service\Abstract
{
    const API_URL = 'http://clip.eventcast.jp/api/v1/Search?';
    
    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected static $_client;

    /**
     * default parameter
     *
     * @var array
     */
    protected static $_parameter = array('Sort' => 'date',
                                         'Order' => 'asc',
                                         'Start' => 1,
                                         'Results' => 50,
                                         'Trim' => 0,
                                         'Format' => 'php');
    
    /**
     * set Parameter
     *
     * @param array $parameter
     */
    public static function setParameter(array $parameter)
    {
    	$parameter = array_change_key_case($parameter, CASE_LOWER);
    	
    	self::checkParameter($parameter);
        self::$_parameter = array_merge(array_change_key_case(self::$_parameter, CASE_LOWER), 
                                        $parameter);
    }
    
    /**
     * @todo
     *
     * @param unknown_type $parameter
     */
    public static function checkParameter($parameter)
    {
    	if (isset($parameter['start']) && !ctype_digit($parameter['start'])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Exception("start is not digit : ".$parameter['start']);
    	}
    }
    
    /**
     * getting parameter for eventcast
     * if start or end date not set 
     * 
     * @return array self::$_parameter
     */
    public static function getParameter()
    {
    	/**
    	 * @see Zend_Date
    	 */
    	require_once 'Zend/Date.php';
    	
    	$date = new \Zend\Date();
        if (!array_key_exists(strtolower('startdate'), self::$_parameter)) {
        	self::$_parameter['StartDate'] = (string) $date->get('yyyy/MM/dd');
        }
        
        if (!array_key_exists(strtolower('enddate'), self::$_parameter)) {
        	$date->addMonth(1);
            self::$_parameter['EndDate'] = (string) $date->get('yyyy/MM/dd');
        }
        
        $params = self::$_parameter;
        self::$_parameter = array();

        foreach ($params as $k => $v) {
            if (strtolower($k) === 'startdate') {
                self::$_parameter['StartDate'] = $v;
            } else if (strtolower($k) === 'enddate') {
                self::$_parameter['EndDate'] = $v;
            } else {
                self::$_parameter[ucfirst($k)] = $v;
            }
        }
        
        return self::$_parameter;
    }
    
    public static function request($parameter = array())
    {
        if ($parameter) self::setParameter($parameter);
        
        self::$_client = self::getHttpClient();
        
        $uri = \Zend\Uri\Http::factory(self::API_URL);
        $uri->setQuery(self::getParameter());

        self::$_client->setUri($uri);
        
        $response = self::$_client->request(\Zend\Http\Client::GET);
        
        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Exception
              */
             require_once 'Diggin/Service/Exception.php';
             throw new Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        if (self::$_parameter['Format'] === 'php') {
            //@todo return resultset implements Iterator
            return unserialize($response->getBody());
        }
        
        return $response->getBody();
    }
    
}