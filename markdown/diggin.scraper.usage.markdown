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

もっとも一般的な取得型は"TEXT"というHTMLソース文字列での取得と、"@href"といった"@属性値"での取得です。属性を指定した場合において、 '@href'と'@src'だった場合は相対パスから絶対パスへと自動変換されます。

フィルタは、取得された各値に対しフィルタリング処理を行います。フィルタリングは「ユーザー定義関数」・「Zend_Filter」・「ユーザ定義フィルタクラス」の順に存在チェックをし処理がコールされます。フィルタはカンマ区切りにより複数指定可能です。上記の"list"プロパティの場合、アンカー文字「RFC 2606」に対し、Zend_Filterの'Digits'フィルタが適用され「2606」という数値を保持したリストが格納されることになります。

スクレイピングの実行は、 scrapeメソッドをコール時に行われます。scrapeメソッドには対象となるサイトのURLを指定します。

---
### 実装例 ###
demos/Diggin/Scraperに格納してあるスクリプト群にて実際の使用例を確認できます。

＊ スクレイパーからリクエストを送信する際は、各サイトの使用許諾をご確認ください。

---
### 標準で利用可能な取得型 ###

標準で使用される取得型は以下の通りです。

取得型        | 説明
------------- | -------------
ASXML         | 単純に指定された要素をSimplexmlのasXml()メソッドにて取得し返却します。(0.7からはエンティティーデコードされています。)
TEXT          | 指定対象のHTML文字列からテキスト部分を取得します。
HTML          | 指定対象のHTML文字列からテキスト部分を取得したあと、外側のタグを削除します。
PLAIN         | rhacoのSimpleTagにおける’PLAIN’に近いテキスト取得を行います。
@"属性"       | 指定対象に対応する「@"属性"」の値を取得します。(@href, @srcについては絶対パスへの変換も行います。)

取得型は、0.6まではDiggin_Scraper_Strategy_*のgetValue()、0.7では各Diggin_Scraper_Evaluator_Abstractを継承した各クラスにあるメソッドにて定義されています。

---

### ブロック構造(多次元配列)での取得 ###

これまであった数多くのスクレイピング用のPHPに作られたライブラリには、スクレイピングが盛んな他の言語圏(RubyやPerl)では既に知れ渡っている概念が欠落していました。それは「ブロック構造」(本マニュアルではそのように定義します）での取得です。

では、ブロック構造での取得とは何でしょう？

すでに、Web::Scraper用に書かれた以下の記事が参考になります。
[Web::Scraper でいい感じのデータ構造になってくれなくて困っているのはどこのどいつだ〜い? アタイだよ!](http://en.yummy.stripper.jp/?eid=800109)

Diggin_Scraperでは、このブロック構造での取得を容易にするために、Diggin_Scraperのインスタンスが配列にセットされた場合、再起的に処理を行います。

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

---
### Diggin_Scraperにおける例外処理 ###

#### レスポンスステータスに対する例外処理 ####
Diggin_Scraperからリクエストを発行した場合には、レスポンスステータスが成功でない場合(Zend_Http_ResponseのisSucces()メソッドにて判定します)は、Diggin_Scraper_Exceptionをスローします。もし、ステータスの有無に関わらずスクレイプ対象にしたい場合、スクレイパーオブジェクトのscrapeメソッド引数にZend_Http_Responseのオブジェクトを渡します。

#### 抽出対象に対する例外 ####
Diggin_Scraperでは、scrapeを実行するプロセスの抽出要素に該当するものが無かった場合Diggin_Scraper_Strategy_Exception例外を投げます。ただし、多次元配列取得用にセットされたプロセス分については、値が格納されません。(数値添字配列から該当のキーがスキップされたものになります。)
これは、該当対象が無かった場合に継続の処理ができないという判断によるものです。コンストラクタにて、この例外を投げないよう変更できます。Web::Scraperでは、該当対象が無かった場合ブランクが格納されます。この仕様の相違については現在標準で例外を投げないよう検討しています。


---
### Diggin_Scraper各クラスの相関 ###
以下の図では、Diggin_ScraperならびにZend Frameworkの各クラスの相関関係を図示しています。(※この図はVersion 0.5の頃に作成したものです)
<img src="scraper.gif" alt="Diggin_Scraper"/>
