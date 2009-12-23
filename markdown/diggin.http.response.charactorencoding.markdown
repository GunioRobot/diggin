Diggin_Http_Response_CharactorEncoding
======================================

### HTTPレスポンスに対する文字コードの変換 ###
Diggin_Http_Response_CharactorEncodingは、HttpレスポンスオブジェクトからUTF-8へ自動変換を行うのを目的としたものです。
エンコードの取得はHttpレスポンスヘッダ、metaタグ、(mbstringが利用可能な場合)レスポンスボディの順に行います。
各レスポンスインスタンスから、getBodyされたときにUTF-8に自動変換するためのラッパーを用意しています。(現在はZend_Http_Responseのみ)
＊HTMLScraping Classをベースにしています。

参考コード

    $url = 'http://ugnews.net/'; //任意のUTF-8以外のサイトを設定してみてください。
    $client = new Zend_Http_Client($url);
    $response = $client->request();
 
    $wrapper = Diggin_Http_Response_CharactorEncoding::createWrapper($response);
    var_dump($wrapper instanceof Zend_Http_Response); //true
    var_dump($wrapper->getBody()); //結果はUTF-8にて取得できているでしょう。

またこのコンポーネントは、Diggin_Scraper_Adapterの各クラスにて利用されます。
