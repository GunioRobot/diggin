<?php
$path = 'd:\zip\cdex_151\LocalCDDB';

require_once 'Diggin/CDDB/Application/CDex.php';

try {
    $cdex = new Diggin_CDDB_Application_CDex();
    $cdex->setLocalCDDBDirPath($path);
    
    $disc = array(
    'dyear' => "2007",
    'dgenre' =>"Unknown!!", 
    'tracks' => array('たいとる１','タイトル２','たいとる　３','タイトル４')
    );
    
//    var_dump($cdex->getLastDisc());
    
//    var_dump($cdex->getRewriteStr($cdex->getLastFile(), $points, $disc));
//    exit;
    var_dump($cdex->rewriteLastRecord($disc));
} catch (Diggin_Exception $e) {
    die($e->getMessage());
}