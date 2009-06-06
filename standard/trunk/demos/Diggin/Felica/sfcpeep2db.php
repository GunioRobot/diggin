<?php
//suicaもしくは、pasmoからの取得情報をDB(Mysqlなど)に保存するサンプルです。
//$parametersと、$sfcpeepPathの設定をしておいてください。
$parameters = array(
    'username' => 'mysql_username',
    'password' => 'mysql_password',
    'dbname'   => 'test'
);

$sfcpeepPath = 'D:\SFCPeep21\SFCPeep.exe';

//////////////////////////////////////////
require_once 'Diggin/Felica/Adapter/Sfcpeep.php';
require_once 'Zend/Db/Adapter/Mysqli.php';
require_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Debug.php';


$sfc = new Diggin_Felica_Adapter_Sfcpeep();
$sfc->setSfcpeepPath($sfcpeepPath);
$sfc->getSfcPeep();

$felicaIdm = $sfc->felicaIdm;
$sfcpeepArraryData = $sfc->sfcpeepArray;
    
class Sfcpeep extends Zend_Db_Table_Abstract
{
    protected $_primary = array('felica_Idm' ,'history_num');    
}

try {
    $db = Zend_Db::factory('Pdo_Mysql', $parameters);
    $select = "select max(history_num) from sfcpeep";
    $stmt = $db->query($select);
    $select = $stmt->fetchAll();
    if($select[0]["max(history_num)"]) {
        $max = $maxHistoryNum[0]["max(history_num)"];
    } else {
        $max = 0;
    };
    
    $sfcpeepTable = new sfcpeep(array('db' => $db));

    foreach ($sfcpeepArraryData as $sfcpeepData) {
        
        if($felicaIdm.$sfcpeepData["historyNum"] > $felicaIdm.$max) {
            $insertData = array(
                'felica_Idm'        => $felicaIdm,
                'history_num'       => $sfcpeepData["historyNum"],
                'terminal_code'     => $sfcpeepData["terminalCode"],
                'process'           => $sfcpeepData["process"],
                'in_line_code'      => $sfcpeepData["inLineCode"],
                'in_station_code'   => $sfcpeepData["inStationCode"],
                'in_company_name'   => $sfcpeepData["inCompanyName"],
                'in_station_name'   => $sfcpeepData["inStationName"],
                'out_line_code'     => $sfcpeepData["outLineCode"],
                'out_station_code'  => $sfcpeepData["outStationCode"],
                'out_company_name'  => $sfcpeepData["outCompanyName"],
                'out_station_name'  => $sfcpeepData["outStationName"],
                'balance'           => $sfcpeepData["balance"],
            );
            
            $sfcpeepTable->insert($insertData);
        }
    }

    Zend_Debug::dump('insert Completed');
    
} catch (Zend_Db_Adapter_Exception $e) {
    Zend_Debug::dump("ID かパスワードが間違っている、あるいは RDBMS が起動していないなど");
    Zend_Debug::dump($db->getConnection());  
} catch (Zend_Exception $e) {
    Zend_Debug::dump(" factory() が指定したアダプタクラスを読み込めなかったなど"); 
}
