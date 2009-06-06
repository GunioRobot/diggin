<?php


/**
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

error_reporting( E_ALL | E_STRICT );


//
$path = explode(PATH_SEPARATOR, get_include_path());

$digginRoot = dirname(dirname(dirname(__FILE__)));
array_push($path, "$digginRoot/library");
array_push($path, "$digginRoot/tests");
set_include_path(implode(PATH_SEPARATOR, $path));

//var_dump($path);

//if (is_readable($digginCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
//    require_once $digginCoreTests . DIRECTORY_SEPARATOR . 'Diggin' .DIRECTORY_SEPARATOR.'TestConfiguration.php';
//} else {
//    require_once $digginCoreTests . DIRECTORY_SEPARATOR . 'Diggin' . DIRECTORY_SEPARATOR .'TestConfiguration.php.dist';
//}

unset($digginRoot,$path);


