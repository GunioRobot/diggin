<?php
//copy from ->detect()
$responseBody = pack("C2", 0x87, 0x40);
if (extension_loaded('mbstring')) {
    if (!$encoding = false) {
//oops, Can I kill "mb_detect_order" ? - without runkit_function_remove, or  comment
//        @mb_detect_order('ASCII, JIS, UTF-8, EUC-JP, SJIS');
        if (false === $encoding = @mb_preferred_mime_name(@mb_detect_encoding($responseBody))) {
            require_once 'Diggin/Http/Response/Encoding/Exception.php';
            throw new Diggin_Http_Response_Encoding_Exception('Failed detecting character encoding.');
        }
    }
}
var_dump(mb_detect_encoding($responseBody));
var_dump($encoding);