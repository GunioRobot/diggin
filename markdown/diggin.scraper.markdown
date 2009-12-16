Diggin_Scraper
==============

### 導入 ###

Diggin_Scraper コンポーネントは、WEBデータのスクレイピングに必要となる取得処理・整形処理・抽出処理を提供します。

WEBデータの取得にはZend FrameworkのZend_Http_Clientを利用するため、パスワード認証が必要なWEBページの取得・プロキシ利用・テストなどが容易に行えます。（詳しくは [Zend_Http](http://framework.zend.com/manual/ja/zend.http.html)のリファレンスをご参照ください。）

未整形(汚い)のHTMLに対して、Diggin_Scraperはデフォルトで[HTMLScraping Class](http://www.rcdtokyo.com/etc/htmlscraping/)を改変したアダプターを利用することにより整形処理を行います。整形方法ならびに必要なモジュールはHTMLScraping Classのドキュメントをご参照下さい。

抽出方法定義はPerlのWeb::Scraperモジュールを強く意識した作りになっており、Web::Scraper用のDSLをちょっとした変更でDiggin_Scraperでも利用ができるように開発中です。

### スクレーパーの基本的な使用法 ###

Diggin_Scraperでは、スクレイパーのインスタンスを生成後、processメソッドに抽出方法をセットします。
processメソッドには、多種多様な抽出方法が指定可能ですが、ここでは、Diggin_Scraperがデフォルトで用意している指定方法について解説します。

- 第一引数には、抽出要素を差し示すクエリー(CSSやXpath)を設定します。
- 第二引数以降には、その抽出対象のプロパティ名(任意)と取得型(「表示されている文字列」として、「HTMLソース文字列」としてあるいは「取得したSimplexmlそのまま」かなど)、そして必要であればフィルタ名を以下のようにすべて文字列として囲みます。

        $scraper = new Diggin_Scraper();
        $scraper->process('//p//a', 'list[]      => TEXT,   Digits') 
              //->process('抽出要素', 'プロパティ => 取得型, フィルタ名') 
                ->scrape('http://example.net/');

上記において、「=>」はプロパティ名と取得型を分ける区切り文字列、またそれ以降のカンマ(,)は取得型とフィルタ名を分ける区切り文字列として処理されます。
取得した要素は、スクレイパーオブジェクトのresultsプロパティに全てそのキーに対するリストとして格納されます。'[]'をプロパティ名の後ろに記述しなかった場合、取得された最初の要素一つがプロパティキー名に対する連想配列として格納されます。
また各キー要素は、マジックメソッド__getの利用により、スクレイパーオブジェクトのプロパティとして取得することも可能です。つまり上記の"list"プロパティを指定した場合、scrapeメソッド実行後は、以下のように取得することが可能です。

    $results = $scraper->getResults(); $results['list'];
    //あるいは
    $scraper->list;

取得型は、Diggin_Scraper_Strategy_Abstractを継承した各クラス(デフォルトでは Diggin_Scraper_Strategy_Flexible)のgetValueメソッドに定義された取得型が使用されます。もっとも一般的な取得型は"TEXT"というHTMLソース文字列での取得と、"@href"といった"@属性値"での取得です。属性を指定した場合において、 '@href'と'@src'だった場合は相対パスから絶対パスへと自動変換されます。

フィルタは、取得された各値に対しフィルタリング処理を行います。フィルタリングは「ユーザー定義関数」・「Zend_Filter」・「ユーザ定義フィルタクラス」の順に存在チェックをし処理がコールされます。フィルタはカンマ区切りにより複数指定可能です。上記の"list"プロパティの場合、アンカー文字「RFC 2606」に対し、Zend_Filterの'Digits'フィルタが適用され「2606」という数値を保持したリストが格納されることになります。

スクレイピングの実行は、 scrapeメソッドをコール時に行われます。scrapeメソッドには対象となるサイトのURLを指定します。

---

### ブロック構造での取得 ###

これまであった数多くのスクレイピング用のPHPに作られたライブラリには、スクレイピングが盛んな他の言語圏(RubyやPerl)では既に知れ渡っている概念が欠落していました。それは「ブロック構造」(本マニュアルではそのように定義します）での取得です。

では、ブロック構造での取得とは何でしょう？

すでに、Web::Scraper用に書かれた以下の記事が参考になります。
[Web::Scraper でいい感じのデータ構造になってくれなくて困っているのはどこのどいつだ〜い? アタイだよ!](http://en.yummy.stripper.jp/?eid=800109)

Diggin_Scraperでは、このブロック構造での取得を容易にするために、Diggin_Scraperのインスタンスが配列にセットされた場合、再起的に処理を行うようにしています。

    //まず、再起処理用のDiggin_Scraperインスタンスを生成します。
    $ranking = new Diggin_Scraper();

    //次に、scrapeメソッドを実行するDiggin_Scraperクラスでのprocessメソッドと同様に取得要素を指定します。
    $ranking->process('.', 'rank => [@background, "Digits"]')
            ->process('img', 'star => @alt', 'image => @src')
            ->process('td.text', 'text => TEXT')
            ->process('.//td[contains(@class, "lucky") and (not(contains(@valign, "bottom")))]', 'lucky => TEXT');

    //上記で作った"$ranking"を、Diggin_Scraperのprocessメソッドにて連想配列としてセットします。
    $scraper = new Diggin_Scraper();
    $scraper->process('//td[@class="day" and @height < 100]', 'date => "TEXT"')
            ->process('//table[contains(@background, "item/rank")]', array('ranking[]' => $ranking))
            ->scrape($url);

### フィルタの利用 ###

### 標準で利用可能な取得型 ###



取得型        | 説明
------------- | -------------
ASXML         | 単純に指定された要素をSimplexmlのasXml()メソッドにて取得し返却します。
TEXT          | 指定対象のHTML文字列からテキスト部分を取得します。
HTML          | 指定対象のHTML文字列からテキスト部分を取得したあと、外側のタグを削除します。
PLAIN         | rhacoのSimplexmlにおける’PLAIN’に近いテキスト取得を行います。


