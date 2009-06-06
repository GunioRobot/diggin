<?php

require_once dirname(__FILE__).'/../../../../TestHelper.php';
require_once 'Diggin/Scraper/Helper/Simplexml/Pagerize.php';

/**
 * Test class for Diggin_Scraper_Helper_Simplexml_Pagerize
 */
class Diggin_Scraper_Helper_Simplexml_Pagerize_NextLinkTest extends PHPUnit_Framework_TestCase
{
    protected $object;
    protected $testBaseUrl;
    
    protected function setUp()
    {

        $this->object = new Diggin_Scraper_Helper_Simplexml_Pagerize($this->getSimpleXml());
        //$this->testBaseUrl = 
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

    private function getSimpleXml2()
    {
        $html = '<html><body>test</body></html>';
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        return @simplexml_import_dom($dom);
    }    
}
