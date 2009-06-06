<?php
require_once '/home/kazusuke/dev/php/libs/rhaco/Rhaco.php';
require_once 'Diggin/Scraper.php';

Diggin_Scraper::changeStrategy('Diggin_Scraper_Strategy_SimpleTag');

$scraper = new Diggin_Scraper();
$scraper->process('a', 'test => Raw');
//        ->scrape('http://musicrider.com/');
//
//var_dump($scraper);