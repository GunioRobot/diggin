<?php

require_once dirname(__FILE__).'/../../../../TestHelper.php';
require_once 'Diggin/Scraper/Helper/Simplexml/Pagerize.php';
require_once 'Diggin/Service/Wedata.php';
require_once 'Zend/Cache.php';
require_once 'Zend/Json.php';
require_once 'Zend/Json/Decoder.php';

/**
 * Test class for Diggin_Scraper_Helper_Simplexml_Pagerize
 */
class Diggin_Scraper_Helper_Simplexml_Pagerize_NextLinkWithCacheTest extends PHPUnit_Framework_TestCase
{
    protected $object;
    protected $testBaseUrl;
    
    protected function setUp()
    {
        $mysiteinfo = '[{"url":"http://example.com", "nextLink":"//a"},{"url":"http://test.org/", "nextLink":"//a"}]';
        if (!is_array($jsonarray = Zend_Json::decode($mysiteinfo))) {
            die($jsonarray);
        }
        Diggin_Scraper_Helper_Simplexml_Pagerize::setCache($cache = $this->getCacheCore('_files/'));

        if (!$siteinfo = $cache->load(Diggin_Scraper_Helper_Simplexml_Pagerize::CACHE_TAG_PREFIX.'wedata')) {
            $siteinfo = Diggin_Service_Wedata::getItems('AutoPagerize');
        }

        Diggin_Scraper_Helper_Simplexml_Pagerize::appendSiteInfo('wedata', new SiteInfo($siteinfo));
        Diggin_Scraper_Helper_Simplexml_Pagerize::appendSiteInfo('mine',   new SiteInfo($jsonarray));
        $this->object = new Diggin_Scraper_Helper_Simplexml_Pagerize($this->getSimpleXml());
        $this->object->setOption(array('baseUrl' => 'http://example.com/test/'));
    }
    
    public function testGetNextLink()
    {
        $this->assertNotNull($this->object->getNextLink());
    }
    
    private function getSimpleXml()
    {
        $html = file_get_contents(dirname(__FILE__).'/_files/wedatanet.html');
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        
        return @simplexml_import_dom($dom);
    }

    function getCacheCore($cache_dir, $frontendOptions= null)
    {
        //cache
        if($frontendOptions === null) {
            $frontendOptions = array(
                'lifetime' => 86400,
            'automatic_serialization' => true

            );
        }

        $backendOptions = array(
            'cache_dir' => $cache_dir
        );
         
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        
        return $cache;
    }

    public function siteinfoJson()
    {
        return <<<JSON
[{url:'http://(.*).google.+/(search).+',nextLink:'id("navbar")//td[last()]/a',pageElement:'//div[@id="res"]/div',exampleUrl:'http://www.google.com/search?q=nsIObserver',},{url:'http://images\\.google\\.(\\w|\\.)+/images\\?',nextLink:'id("navbar")//td[last()]/a',pageElement:'id("navbar")/preceding-sibling::*',exampleUrl:''}]
JSON;
    }

    public static function siteinfo2Json($siteinfo)
    {
        //$siteinfo
    }
    

//memo 
//http://groups.google.co.jp/group/autopagerize/browse_thread/thread/c640f6c230a1a116
}

class Siteinfo extends ArrayIterator
{
    public function current()
    {
        $curerent = parent::current();
        if (is_array($curerent) && array_key_exists('data', $curerent)){
            return $curerent['data'];
        }

        return $curerent;
    }
}
