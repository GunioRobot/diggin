Diggin - Simplicity PHP Library
===============================
- 2006-2009 Diggin MusicRider.com Projects.

---
"Diggin"とは？
--------------
### 概要 ###

Diggin は主にsasezaki個人によるスパイダー向けライブラリーの総称です。 高品質を謳ったり、パワフルさを持ち合わせている訳ではありません。
多くのライブラリは、ニッチな用途のため万人向けではないでしょう。

### インストール ###

一部Zend Framework(以下、ZF)やPHP拡張に依存しているものがあります。利用の際には、各コンポーネントの必要条件にもとづいて環境を用意してください。(参照：[ZFのインストールガイド](http://framework.zend.com/manual/ja/introduction.installation.html))。Digginのバージョン0.6ではZF1.6以上、0.7ではZF1.9以上が必要となります。

    Diggin の SVN リポジトリの最先端の URL は http://diggin.googlecode.com/svn/standard/trunk/ です。

Zend Framework 同様、 あなたのアプリケーションからフレームワークのクラス群にアクセスできるようにする必要があります。これにはいくつかの方法がありますが、PHP の include_path に Diggin ライブラリへのパスを含める必要があります。

pearを用いたインストール方法も用意しています。

    pear channel-discover openpear.org
    pear install openpear/Diggin-alpha

---
## Diggin_CDDB
- Diggin_CDDB_Application_CDex

## Diggin_Console

## Diggin_Debug

## Diggin_Exception
- [Digginにおける例外](diggin.exception.markdown)

## Diggin_Felica

## Diggin_Http
- Diggin_Http_Client_Adapter_TestPlus
- Diggin_Http_CookieJar_Loader
    - Diggin_Http_CookieJar_Loader_Firefox3
- [Diggin_Http_Response_CharactorEncoding](diggin.http.response.charactorencoding.markdown)

## Diggin_Json
- Diggin_Json_Expr_Webscraperjs

## Diggin_RobotRules

## Diggin_Scraper
- [Diggin_Scraper導入](diggin.scraper.markdown)
- [基本的な使い方](diggin.scraper.usage.markdown)
- [フィルタの利用](diggin.scraper.filter.markdown)
- [多次元配列での取得](diggin.scraper.multiarray.markdown)
- [ヘルパーの利用](diggin.scraper.helper.markdown)
- [図録：Diggin_Scraperで使用されるクラス](diggin.scraper.classes.markdown)
- [コマンドライン用ツール exthtml.php]

## Diggin_Service
- Diggin_Service_Eventcast
- Diggin_Service_Tumblr
- [Diggin_Service_Wedata](diggin.service.wedata.markdown)

## Diggin_Siteinfo

## Diggin_Uri
- [Diggin_Uri_Http](diggin.uri.http.markdown)

## Diggin_Version
- [Digginのバージョンの取得](diggin.version.markdown)

---
## 著作権に関する情報
Digginは、全てのファイルが同一ライセンスではないことに注意してください。(別々のパッケージとして配布しています。)
Diggin_Scraper_Adapter_Htmlscrapingならびに
Diggin_Http_Response_CharactorEncodingについてはLGPLです。
それ以外のものについてはNew BSD Licenseです。

---
## Version 0.7での変更点
0.7ではいくつかの大きな変更を予定しています。

- 追加コンポーネント
    - Diggin_RobotRules
- APIの変更
    - Diggin_Service_Wedataは静的コールが廃止されました。
- Diggin_Scraperでの内部変更
    - Diggin_Scraper各クラスに対する拡張クラスを作成されている方は変更が必要です。
    - アンパサンド(&)のasXMLオーバーライド用、Diggin_Scraper_Wrapper_SimplexmlElementの追加
        - Strategy(Flexible)での各value取得でのエンティティー変換のとりやめ
        - Helper_Simplexml_SimplexmlAbstractの変換のとりやめ
    - Diggin_Scraper_Strategy_Callbackの導入
        - イテレータでの遅延評価のために、Diggin_Scraper_Strategy_Abstract::getValuesが大変更となります。0.6用に作成したユーザースクリプトの取得値が違った場合この箇所が原因となります。
        - Callback導入に伴い、Diggin_Scraper_Strategy_*でのgetValue廃止
    - Diggin_Scraper_Adapter_HtmlscrapingにてDiggin_Http_Response_CharactorEncodingを使用するよう変更。

0.6系統用に以下のブランチが作成されました。但し、アップデートは重要な場合を除き行われません。

    http://diggin.googlecode.com/svn/standard/branches/release-0.6/

### Version 0.7.xにて予定している変更点
- Diggin_Spider_Request_Queueコンポーネント導入(spizer/kumoからのポート)
- Diggin_Scraper_Helper_Simplexml_Pagerizeのリファクタリング
- Diggin_Siteinfoの構成の見直し
- Diggin_RobotRulesのrobots.txt以外の対応

