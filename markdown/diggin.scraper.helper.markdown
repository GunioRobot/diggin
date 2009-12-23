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
- Pagerize
- HeadBaseHref
- Title

### ヘルパー機能の拡張 ###
Diggin_Scraperオブジェクトにて、getHelperLoder()メソッドを呼び出すと内部のZend_Loader_PluginLoaderインスタンスが返却されます。このプラグインローダーを利用してヘルパーの拡張が可能です。
    Zend_Loader_PluginLoaderについては[ZFのドキュメント](http://framework.zend.com/manual/ja/zend.loader.pluginloader.html)を参照してください。
