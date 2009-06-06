<?php
$apiKey ='';

require_once 'Diggin/Service/Wedata.php';

$wedata = new Diggin_Service_Wedata();
$wedata->setApikey($apiKey);

$wedata->setParamDatabase('name', 'testtest database5678');
$wedata->setParamDatabase('required_keys', 'url sitename');
$wedata->setParamDatabase('optional_keys', 'option');
$wedata->setParamDatabase('permit_other_keys', true);
//$a = $wedata->createDatabase();
//$a = $wedata->deleteDatabase();
//$b = $wedata->insertItem('testtest database5678', 
//        array('name' => 'testing',
//	  		  'data'=> array('url' => 'http://test.org/', 'sitename' => 'hogeeeee')));
//$b = $wedata->deleteItem('1285');

var_dump($a, $b);
