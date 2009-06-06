<?php
require_once 'Diggin/Felica/Adapter/Sfcpeep.php';
$sfcpeepPath = 'D:\zip\SFCPeep21\SFCPeep.exe';
$sfc = new Diggin_Felica_Adapter_Sfcpeep('SJIS');
$sfc->setSfcpeepPath($sfcpeepPath);
print_r($sfc->getDistinctName());
