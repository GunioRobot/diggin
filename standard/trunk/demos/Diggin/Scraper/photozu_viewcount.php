<?php
//photozouでviewが大きい順に表示
require_once 'Diggin/Scraper.php';
$url = 'http://photozou.jp/photo/list/144383/all';

try {
     $thumbnail = new Diggin_Scraper();
     $thumbnail->process('p.photolist_title', 'title => TEXT')
               ->process('a', 'link => @href')
               ->process('span.photolist_view','view => TEXT');
     $scraper = new Diggin_Scraper();
     $scraper->process('//li[@class="thumbnail"]', array('thumbnail[]' => $thumbnail))
             ->scrape($url);
} catch (Diggin_Scraper_Exception $e) {
    die($e->getMessage());
}
//var_dump($scraper->thumbnail);exit;
/**
 * var_dump($scraper->thumbnail);
 * 
  [16]=>
  array(1) {
    ["title"]=>
    string(8) "P1020045"
  }
  [17]=>
  array(2) {
    ["title"]=>
    string(8) "P1020037"
    ["view"]=>
    string(1) "1"
  }
    といった配列が取得できるので、、、以下
 */
$data = $scraper->thumbnail;
foreach ($scraper->thumbnail as $k => $tumb) {
    $title[$k] = $tumb['title'];
    $view[$k] = (isset($tumb['view'])) ? $tumb['view'] : 0;
}
array_multisort($view, SORT_DESC, $title, SORT_ASC, $data);

print_r($data);
