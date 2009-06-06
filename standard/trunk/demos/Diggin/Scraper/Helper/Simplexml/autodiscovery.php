<?php

require_once 'Diggin/Scraper/Helper/Simplexml/Autodiscovery.php';


$url = 'http://kokogiko.net';
$dom = @DOMDocument::loadHTML(file_get_contents($url = 'http://kokogiko.net'));
$helper = new Diggin_Scraper_Helper_Simplexml_Autodiscovery(simplexml_import_dom($dom));

var_dump($helper->discovery());

require_once 'Diggin/Scraper.php';

$scraper = new Diggin_Scraper();
$scraper->scrape($url);

print_r($scraper->autodiscovery('atom'));