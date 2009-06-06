<?php
require_once 'Diggin/Scraper.php';

$items = new Diggin_Scraper();
$items->process('div.ttl a', 'url => @href', 'linkText => TEXT')
      ->process('td.prices', 'price => TEXT');

$ebay = new Diggin_Scraper();
$ebay->process('//div[@id="v4-54"]//tbody//tr', array('items[]' => $items))
     ->scrape('http://shop.ebay.com/items/_W0QQ_nkwZappleQ20ipodQ20nanoQQ_armrsZ1QQ_fromZQQ_mdoZ');

var_dump($ebay->items);
