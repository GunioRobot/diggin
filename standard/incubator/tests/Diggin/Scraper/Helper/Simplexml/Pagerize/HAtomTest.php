<?php

require_once dirname(__FILE__).'/../../../../TestHelper.php';
require_once 'Diggin/Scraper/Helper/Simplexml/Pagerize.php';

/**
 * Test class for Diggin_Scraper_Helper_Simplexml_Pagerize
 */
class Diggin_Scraper_Helper_Simplexml_Pagerize_HAtomTest extends PHPUnit_Framework_TestCase
{
    protected $object;
    
    protected function setUp()
    {
    }
    
    public function testHAtomNextLink()
    {
        $this->object = new Diggin_Scraper_Helper_Simplexml_Pagerize($this->getSimpleXml());
        $this->assertNotNull($this->object->hAtomNextLink());
        $this->object = new Diggin_Scraper_Helper_Simplexml_Pagerize($this->getSimpleXml2());
        $this->assertNull($this->object->hAtomNextLink());
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
