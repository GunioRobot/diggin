### Diggin_Scraperヘルパーの利用 ###
Diggin_Scraperクラスには、目的に特化したスクレイプ結果を提供するヘルパー機能を揃えています。
これは、Zend Frameworkのヘルパーの機構を適用したものです。
スクレイパーオブジェクトから各ヘルパーをコールし使用します。

    <?php
    $scraper = new Diggin_Scraper();
    $scraper->scrape($url);
    var_dump($scraper->title());

上の例のtitleヘルパーはブラウザーのタイトルバーに表示される(であろう)文字列が結果となります。

### 使用可能なヘルパー ###
- Autodiscovery
- pagerize
- HeadBaseHref
- title

### pagerizeの利用 ###

Diggin_Scraper_Helper_Simplexml_Pagerizeは、名前から推測がつくかもしれませんが、[AutoPagerize](http://autopagerize.net/)の方法を流用するためのものです。
cacheをセットしない場合は、hAtomに基づく次ページURLを返却しますが、 cacheとしてSiteInfo情報を配列としてセットすることでそれを用いることができます。キャッシュ機構はZend_Cacheを用います(詳しくはZend_Cacheのマニュアルをご覧ください)。


    //1 Zend_Cache_Coreをセットします
    Diggin_Scraper_Helper_Simplexml_Pagerize::setCache($cache);
    //2 キーを指定してsiteinfo配列をセットします
    Diggin_Scraper_Helper_Simplexml_Pagerize::appendSiteInfo('mysiteinfo',
                                    array(
                                         array('url' => 'http://d.hatena.ne.jp/sasezaki',
                                               'nextLink' => '//a[@class="prev" and last()]'),
                                         array('url' => 'http://framework.zend.com/code/changelog/Standard_Library/',
                                               'nextLink' => '//div[@class="changesetList"][last()]/a')
                                              )
                                    );
    //3 スクレイパーのインスタンスを生成します。
    $scraper = new Diggin_Scraper();
    //4 scapeメソッドにてリソースの整形処理まで済ませます。
    $scraper->scrape($url);
    //5 他のZFのヘルパーと同様にヘルパークラス群は、コール時に動的にメソッドとして働きます。
    var_dump($scraper->pagerize()); // http://d.hatena.ne.jp/sasezaki/?of=3

Diggin_Service_Wedataとの組合わせにより、wedataのAutoPagerizeデーターベースを転用できます。

### ヘルパー機能の拡張 ###
Diggin_Scraperオブジェクトにて、getHelperLoder()メソッドを呼び出すと内部のZend_Loader_PluginLoaderインスタンスが返却されます。このプラグインローダーを利用してヘルパーの拡張が可能です。
    Zend_Loader_PluginLoaderについては[ZFのドキュメント](http://framework.zend.com/manual/ja/zend.loader.pluginloader.html)を参照してください。
