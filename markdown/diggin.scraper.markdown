Diggin_Scraper
==============

### 導入 ###

Diggin_Scraper コンポーネントは、WEBデータのスクレイピングに必要となる取得処理・整形処理・抽出処理を提供します。

WEBデータの取得にはZend FrameworkのZend_Http_Clientを利用するため、パスワード認証が必要なWEBページの取得・プロキシ利用・テストなどが容易に行えます。（詳しくは [Zend_Http](http://framework.zend.com/manual/ja/zend.http.html)のリファレンスをご参照ください。）

未整形(汚い)のHTMLに対して、Diggin_Scraperは標準で[HTMLScraping Class](http://www.rcdtokyo.com/etc/htmlscraping/)を改変したアダプターを利用することにより整形処理を行います。整形方法ならびに必要なモジュールはHTMLScraping Classのドキュメントをご参照下さい。

抽出方法定義はPerlのWeb::Scraperモジュールを強く意識した作りになっており、Web::Scraper用のDSLをちょっとした変更でDiggin_Scraperでも利用ができるでしょう。

スクレイピングならびにスパイダリングに関しては知っておくべきルールなどがあります。スパイダリングに関する良書は、[Spidering hacks(O'reilly)](http://www.oreilly.co.jp/books/4873111870/)(ISBN-10 4873111870; ISBN-13 978-4873111872)です。

### 推奨環境 ###
Diggin_Scraperは標準では、tidy拡張モジュールが使用できることを前提としています。また、URL相対パス解決のために、Net_URL2を用います。以下のコマンドにてインストールが可能です。

    pear install Net_Url2-beta

