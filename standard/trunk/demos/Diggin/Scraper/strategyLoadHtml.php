<?php
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>サッカーニュース</title>
<body>
<ul class="news">
  <li>
    <a href="http://sports.livedoor.com/article/vender-15.html">C・ロナウドが休日返上宣言！></a>
  </li>
  <li>
    <a href="http://sportsnavi.yahoo.co.jp/soccer/italia.html">イタリア代表のドナドーニ監督「アイルランドを甘く見てはいない」</a>
  </li>
  <li>
    <a href="http://sportsnavi.yahoo.co.jp/soccer/barce.html">バルセロナが前回王者セビージャを下す＝スペイン国王杯</a>
  </li>
  <li>
    <a href="http://sportsnavi.yahoo.co.jp/soccer/index.html">ユベントス奮闘、５vs３でエンポリを下す＝イタリア杯</a>
  </li>
</ul>
</body>
</html>
HTML;
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
$adapter = new Zend_Http_Client_Adapter_Test();
$adapter->setResponse(
    "HTTP/1.1 200 OK"        . "\r\n" .
    "Content-type: text/xml" . "\r\n" .
                               "\r\n" .
    $html);
$test = new Zend_Http_Client($url = 'http://www.yahoo.jp', array('adapter' => $adapter));


require_once 'Diggin/Scraper.php';
$scraper = new Diggin_Scraper();
$scraper->setHttpClient($test);

$items = new Diggin_Scraper();
$items->process("a", "title => 'TEXT'", "link => '@href'");

$scraper->changeStrategy('Diggin_Scraper_Strategy_Flexible', new Diggin_Scraper_Adapter_Loadhtml());
$scraper->process("ul.news>li", array('result[]' => $items))
        ->scrape("http://localhost/~tobe/news_sample.html");
var_dump($scraper->getResults());