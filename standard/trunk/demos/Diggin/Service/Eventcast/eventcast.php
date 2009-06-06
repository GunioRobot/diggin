<?php
require_once 'Diggin/Service/Eventcast.php';

$ec = new Diggin_Service_Eventcast();
$ec->setParameter(array('OrdeR' => 'DESC', 'Format' => 'xml', 'Results' => 5));
var_dump($ec->request());