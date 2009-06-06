<?php
require_once 'Diggin/Scraper.php';
try {
    //発表タイトル
    $titles = new Diggin_Scraper();
    $titles->process('//h4', 'title[] => TEXT');
    
    //参加者
    $members = new Diggin_Scraper();
    $members->process('/td[1]', "name => TEXT")
            ->process('/td[2]', "comment => TEXT, Digits")
            ->process('/td[3]', "party => TEXT")
            ->process('/td[4]', 'timestamp => TEXT');

    $scraper = new Diggin_Scraper();
    $scraper->process('//div[@id="content"]/div[3]', array('titles' => $titles))
            ->process('//div[@id="content"]/div/table/tr[@class="odd" or @class="even"]', array('members[]' => $members))
            ->scrape('http://events.php.gr.jp/events/show/67');
} catch (Exception $e) {
    die($e);
}

var_dump($scraper->getResults());