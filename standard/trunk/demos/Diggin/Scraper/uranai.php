<?php
require_once 'Diggin/Scraper.php';

$url = 'http://www.fujitv.co.jp/meza/uranai/';

try {
    $ranking = new Diggin_Scraper();
    $ranking->process('.', 'rank => [@background, "Digits"]')
            ->process('img', 'star => @alt', 'image => @src')
            ->process('td.text', 'text => TEXT')
            ->process('//td[contains(@class, "lucky") and (not(contains(@valign, "bottom")))]', 'lucky => TEXT');

    $scraper = new Diggin_Scraper();
    $scraper->process('//td[@class="day" and @height < 100]', 'date => "TEXT"')
            ->process('//table[contains(@background, "item/rank")]', array('ranking[]' => $ranking))
            ->scrape($url);
} catch (Diggin_Scraper_Exception $e) {
    die($e->getMessage());
}

require_once 'Zend/Debug.php';
Zend_Debug::dump($scraper->getResults());
