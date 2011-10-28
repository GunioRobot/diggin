<?php
/**
 *
 * @category   Diggin
 * @package    Diggin_Service
 * @subpackage Tumblr
 * @license
 * @version    $Id:$
 */

/**
 * @namespace
 */
namespace Diggin\Service\Tumblr;
use Diggin\Service;

/**
 * = how to use=
 * // require_once 'Diggin/Service/Tumblr/Write.php';
 * $tumblr = new Diggin_Service_Tumblr_Write();
 *         //$client = new Zend_Http_Client();
 *         //$client->setConfig(array('timeout'=> 500));
 *         //$tumblr->setHttpClient($client);
 * $tumblr->setAuth('sample@sample.com', 'password');
 * $tumblr->writeRegular('title', 'body messages');
 */

/** Zend_Service_Abstract */
// require_once 'Zend/Service/Abstract.php';

/**
 * @category   Diggin
 * @package    Diggin_Service
 * @subpackage Tumblr
 * @author     kazusuke <sasezaki@gmail.com>
 */

class Write extends \Zend\Service\Abstract
{
    const DIGGIN_SERVICE_TUMBLR = 'DIGGIN_SERVICE_TUMBLR';

    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected $_client;

    /**
     * Your Tumblr Email
     *
     * @var string
     */
    protected $_tumblrEmail;

    /**
     * Your Tumblr Password
     *
     * @var string
     */
    protected $_tumblrPassword;

    /**
     * Checking Type
     *
     * @var string
     */
    protected $_checkType;

    /**
     * Sets up for Tumblr
     *
     * @param  string $tumblrEmail
     * @param  string $$tumblrPassword
     * @return null
     */
    public function __construct($tumblrEmail = null, $tumblrPassword = null)
    {
        $this->setAuth($tumblrEmail, $tumblrPassword);
    }

    /**
     * Set E-mail address & Password For Tumblr
     *
     * @param string $tumblrEmail
     * @param string $tumblrPassword
     * @return
     */
    public function setAuth($tumblrEmail, $tumblrPassword)
    {
        $this->_tumblrEmail     = (string) $tumblrEmail;
        $this->_tumblrPassword  = (string) $tumblrPassword;

        return $this;
    }

    /**
     * Checking Option before Posting Tumblr
     *
     * @param $checkType (Full, STRICT, NONE)
     * @return Diggin_Service_Tumblr Provides a fluent interface
     */
    public function setChecked($checkType = null)
    {
        $this->_checkType = $checkType;

        return $this;
    }


    /**
     * Private method that queries
     *
     * @param  array  $posts
     * @throws Zend_Http_Client_Exception
     * @throws Diggin_Service_Exception
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    private function write($posts, $generator = null)
    {
        $posts['email'] = $this->_tumblrEmail;
        $posts['password'] = $this->_tumblrPassword;

        foreach ($posts as $p => $var) {
            if(trim($var)) {
                $this->posts[strtolower($p)] = $var;
            }
        }

        if(!isset($generator)) {
            $generator = self::DIGGIN_SERVICE_TUMBLR;
        } elseif (isset($generator) && strlen($generator) <= 64) {
            $posts['generator'] = $generator;
        } else {
            /**
             * @see Diggin_Service_Exception
             */
            // require_once 'Diggin/Service/Exception.php';
            $exception = new Service\Exception('Generator Name is longed');
            throw $exception;
        }

        $this->_client = self::getHttpClient();
        $this->_client->setUri('http://www.tumblr.com/api/write');
        $this->_client->setParameterPost($posts);

        $request  = $this->_client->request('POST');
        $response = $request->getStatus();

        if ($response == 403) {
            throw new \Zend\Http\Client\Exception('Your email address or password were incorrect');
        } else if ($response == 400) {
            throw new \Zend\Http\Client\Exception('There was at least one error while trying to save your post');
        } else if ($request->isError()) {
            throw new \Zend\Http\Client\Exception('The tumblr.com returned unknown status code');
        }

        return $this;
    }

    /**
     * write by 'Regular'
     *
     * @param string $postTitle (optional)
     * @param string $postBody
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    public function writeRegular($postTitle = null, $postBody = null)
    {
        $posts['type'] = 'regular';

        if(trim($postTitle) == null && trim($postBody) == null) {
            /**
             * @see Diggin_Service_Exception
             */
            // require_once 'Diggin/Service/Exception.php';
            $exception = new Service\Exception('Requires at least one');
            throw $exception;
        }

        $posts['title'] = $postTitle;
        $posts['body']  = $postBody;

        return $this->write($posts);
    }

    /**
     * write by 'Photo'
     *
     * @param string type ('source' or 'data')
     * @param string $sourceOrData
     *             source - The URL of the photo to copy.
     *             data - The binary data of the requested image.
     * @param string $caption (optional)
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    public function writePhoto($type, $sourceOrData, $caption = null)
    {

        $posts['type'] = 'photo';

        if (strtolower($type) == 'source'){
            $posts['source'] = $sourceOrData;
        } elseif (strtolower($type) == 'data'){
            $posts['data'] = $sourceOrData;
        } else {
            // require_once 'Diggin/Service/Exception.php';
            $exception = new Service\Exception('Requires either source or data');
            throw $exception;
        }

        $posts['caption']  = $caption;

        return $this->write($posts);
    }

    /**
     * write by 'quote'
     *
     * @param string $quote
     * @param string $source (optional) : <a href="source"></a>
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    public function writeQuote($quote, $source = null)
    {
        $posts['type'] = 'quote';
        $posts['quote']  = $quote;
        $posts['source']  = $source;

        return $this->write($posts);
    }

    /**
     * write by 'Link'
     *
     * @param string $name (optional)
     * @param string $url
     * @param string $descrption (optional)
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    public function writeLink($name = null, $url, $descrption = null)
    {
        $posts['type'] = 'link';
        $posts['name']  = $name;
        $posts['url'] = $url;
        $posts['descrption']  = $descrption;

        return $this->write($posts);
    }

    /**
     * write by 'conversation '
     *
     * @param string $title (optional)
     * @param string $conversation
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    public function writeConversation($title = null, $conversation)
    {
        $posts['type'] = 'conversation';
        $posts['title']  = $title;
        $posts['conversation'] = $conversation;

        return $this->write($posts);
    }

    /**
     * write by 'Video'
     *
     * @param string $embed
     * @param string $caption (optional)
     * @return Diggin_Service_Tumblr_Write Provides a fluent interface
     */
    public function writeVideo($embed, $caption = null)
    {
        $posts['type'] = 'video';
        $posts['embed']  = $embed;
        $posts['caption'] = $caption;

        return $this->write($posts);
    }

}
