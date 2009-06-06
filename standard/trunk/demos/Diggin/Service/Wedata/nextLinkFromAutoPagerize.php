<?php
$url = 'http://twitter.com/sasezaki';
$cahe_dir = './temp/'; // キャッシュファイルを書き込むディレクトリ

//////////////
require_once 'Diggin/Service/Wedata.php';
require_once 'Zend/Cache.php';

$frontendOptions = array(
    'lifetime' => 86400, // キャッシュの有効期限を 24 時間とします
    'automatic_serialization' => true,
);
$backendOptions = array(
    'cache_dir' => $cahe_dir // キャッシュファイルを書き込むディレクトリ
);

/**
 * Get next url from wedata('AutoPagerize')
 *
 * @param array $items
 * @param string $url base url
 * @return void
 */
function getNextlink($items, $url) {
    foreach ($items as $item) {
    $pattern = '#'.$item['data']['url'].'#i';
        //hAtom 対策
        if ('^https?://.' != $item['data']['url'] && (preg_match($pattern, $url) == 1)) {
            $nextLink = $item['data']['nextLink'];
            return $nextLink;
        }
    }
    
    return false;
}


//main
$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
if(!$items = $cache->load('wedata_items')) {
    $items = Diggin_Service_Wedata::getItems('AutoPagerize');
    $cache->save($items, 'wedata_items');
}
$nextLink = getNextlink($items, $url);
if($nextLink === false) {
    echo 'not found from wedata with url:'.$url;exit;
}

require_once 'Diggin/Debug.php';

Diggin_Debug::dump($nextLink);