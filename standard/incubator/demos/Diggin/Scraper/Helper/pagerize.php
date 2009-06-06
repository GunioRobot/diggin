<?php
$path = explode(PATH_SEPARATOR, get_include_path());
array_push($path, '/home/kazusuke/dev/workspace/Diggin/standard/incubator/library/');
set_include_path(implode(PATH_SEPARATOR, $path));



$url = (isset($argv[1]) ? $argv[1]: 'http://d.hatena.ne.jp/sasezaki');

require_once 'Diggin/Scraper.php';
require_once 'Diggin/Scraper/Helper/Simplexml/Pagerize.php';

//1 Zend_Cache_Coreをセットします
Diggin_Scraper_Helper_Simplexml_Pagerize::setCache(getCache());
//2 キーを指定してsiteinfo配列をセットします
Diggin_Scraper_Helper_Simplexml_Pagerize::appendSiteInfo('mysiteinfo', 
                                array(
                                     array('url' => 'http://d.hatena.ne.jp/sasezaki', 
                                           'nextLink' => '//a[@class="prev" and last()]'),
                                     array('url' => 'http://erosite',
                                           'nextLink' => '//a')
                                          )
                                );
//3 スクレイパーのインスタンスを生成します。
$scraper = new Diggin_Scraper();
//4 scapeメソッドにてリソースの整形処理まで済ませます。
$scraper->scrape($url);
//5 他のZFのヘルパーと同様にヘルパークラス群は、コール時に動的にメソッドとして働きます。
var_dump($scraper->title());
var_dump($scraper->pagerize());

function getCache($cache_dir = '.', $frontendOptions= null)
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
    
    require_once 'Zend/Cache.php';
    return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
}

