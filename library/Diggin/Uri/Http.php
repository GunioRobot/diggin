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
 * @package    Diggin_Uri
 * @copyright  2006-2011 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Uri;

class Http
{
    /**
     * Base Uri
     *
     * @var mixed
     */
    protected $_baseUri;

    public function setBaseUri($uri)
    {
        $this->_baseUri = $uri;
    }

    /**
     * Getting "Real" URL not URI
     * Replace HTML'S relative path
     *
     * using pecl_http
     *
     * @param string $target
     * @return string
     */

    public function getAbsoluteUrl($target)
    {
        //using pecl_http
        /*bug!!!!!!!!!
        if (extension_loaded('http')) {
            if (array_key_exists('host', parse_url($target))) {
                return $target;
            } else {
                if (strpos(pathinfo(parse_url($this->_baseUri, PHP_URL_PATH), PATHINFO_DIRNAME), '/') === false) {
                    return http_build_url($this->_baseUri, array("path" => $target),
                    HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
                } else {
                    return http_build_url($this->_baseUri, array("path" => $target),
                    HTTP_URL_JOIN_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
                }
            }
        //Net_URL2 ver 0.2.0
        } else {
        */
            if (!$this->_baseUri instanceof \Net\URL2) {
                // require_once 'Net/URL2.php';
                $this->_baseUri = new \Net\URL2((string)$this->_baseUri);
            }
            return $this->_baseUri->resolve($target)->getUrl();
        //}
    }
}
