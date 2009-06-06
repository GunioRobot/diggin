<?php

//@see http://0-oo.net/sbox/php-tool-box/html-sql-all-php-functions

ini_set("memory_limit","50000000");
require_once 'Diggin/Scraper.php';

function replace($val) {
	return str_replace('()', '', $val);
}

$scraper = new Diggin_Scraper();
$scraper->process('//dd[@class="indexentry"]//a', 'funcs[] => "TEXT", /^(?!.*(::|->)).*$/, replace')
        ->scrape('http://jp2.php.net/manual/ja/indexes.php');
        
print_r($scraper->funcs);


/**

Array
(
    [0] => __halt_compiler
    [1] => abs
    [2] => acos
    [3] => acosh
    [4] => addcslashes
    [5] => addslashes
    [6] => aggregate
    [7] => aggregate_info
    [8] => aggregate_methods
    [9] => aggregate_methods_by_list
    [10] => aggregate_methods_by_regexp
    [11] => aggregate_properties
    [12] => aggregate_properties_by_list
    [13] => aggregate_properties_by_regexp
    [14] => aggregation_info
    [15] => apache_child_terminate
    [16] => apache_get_modules
    [17] => apache_get_version
 */