<?php
/**
 * 
 * ここのフォルダ（実行phpファイルと同じフォルダ）に
 * SFCPeepに添付されているStationCode.mdbを置いてください。
 * 
 */

require_once 'Diggin/Felica/Adapter/Sfcpeep.php';
//sfcpeep.exeへの実行パスを指定してください。
$sfcpeepPath = 'D:\zip\SFCPeep21\SFCPeep.exe';

//

$sfc = new Diggin_Felica_Adapter_Sfcpeep('SJIS');
$sfc->setSfcpeepPath($sfcpeepPath);

print_r($sfc->getFelicaIdm());
echo ("\n");

$sfc->getSfcPeep();
print_r($sfc);
