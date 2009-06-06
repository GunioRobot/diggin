<?php
require_once 'Diggin/Service/Tumblr/Read.php';

$tumblr = new Diggin_Service_Tumblr_Read();
$tumblr->setTarget('sasezaki');
var_dump($tumblr->getApiUrl());
var_dump($tumblr->getTotal());