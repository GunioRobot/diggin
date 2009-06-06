<?php
require_once 'Zend/Http/Client.php';
require_once 'Diggin/Http/Response/Encoding.php';

$client = new Zend_Http_Client('http://d.hatena.ne.jp/sasezaki');
$body = $client->request()->getBody();
$contentType = $client->getLastResponse()->getHeader('content-type');
$detect = Diggin_Http_Response_Encoding::detect($body, $contentType);

$encoded = Diggin_Http_Response_Encoding::encodeResponseObject($client->getLastResponse());
var_dump($encoded, PHP_EOL);
var_dump($detect, PHP_EOL);
