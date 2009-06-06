<?php
$xhtmls = file('sample');

$xhtml = file_get_contents('sample');
//$xhtml = $xhtmls['2'];

//$pattern = array('/\sxmlns(:[A-Za-z])*?="[^"]+"/', 
//                                   "/\sxmlns(:[A-Za-z])*?='[^']+'/");


$pattern = array('/\sxmlns:?[A-Za-z]*="[^"]+"/', "/\sxmlns:?[A-Za-z]*='[^']+'/");

//preg_match($pattern[0], $xhtml, $m);
$responseBody = preg_replace($pattern[0], '', $xhtml);

print_r($responseBody);

//__COMPILER_HALT_OFFSET__));
