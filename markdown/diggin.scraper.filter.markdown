### Diggin_Scraperにおけるフィルタの利用 ###

スクレイプした時の抽出した値に対しては、フィルタリングを行ってその値を利用するということも多いでしょう。例えば、「1000円」という文字列から数値のみ取り出すといった場合です。あるいは、取得要素に指定したものからさらに絞り込みをかけるという場合もあります。プロセスの指定の際に、これらのフィルタリングを設定できます。

フィルタは、前者(文字列置換)の場合は英数字で始まる文字列もしくはlamda、絞り込みのための場合は記号で始まるルールにそったものに適用されます。フィルタのコールは、Diggin_Scraper_Filterクラスにて設定されています。

#### コールされる文字列用フィルタ ####

##### Zend_Filter #####

単純な文字列フィルタリングにおいて、Zend_Filterの各クラスのコールは簡単です。プロセス設定のさいに、Zend_Filter_というプリフィクスを除いたものを
指定します。

    $scraper->process('//p', 'key => [TEXT, Digits]') //Zend_Filter_Digitsがコールされます。

##### ユーザー定義関数

プロセスに、同名のユーザー定義関数が存在した場合、そちらを優先して使用します。(＊現在は、名前空間には対応していません。)

    function rot13($value){return str_rot13($value);}
    $scraper->process('//p', 'key => [TEXT, rot13]')

##### ユーザ定義フィルタクラス

フィルタに、英数字に_(アンダーバー)が組み合わされたキーワードが設定されており、Zend_Filter_Intefaceを実装したクラスが存在した場合、それをフィルタとして用います。

##### 匿名関数・無名関数

匿名関数(lambda)を使用する場合、以下のように設定します。

    $lambda = create_function('$v', 'return str_rot13($v);');
    $scraper->process('//p', "key[] => TEXT, $lambda")

0.7からは無名関数も使用できます。ただし、"@href, $func") といった匿名関数のようにプロセスでの変数名指定では行えません。Diggin_Scraperのprocess引数には、Diggin_Scraper_Processインスタンスも処理するためそれを利用します。

    // プロセスのインスタンスを生成し、必要な条件をセットします。
    $process = new Diggin_Scraper_Process;
    $filter = function () {return ;}
    // フィルタの設定は配列で行います。
    $process->setFilters(array($filter));
    // プロセスを、使用するDiggin_Scraperに設定します。
    $scraper = new Diggin_Scraper;
    $scraper->process($process)

#### 絞り込み用フィルタ ####

プロセスの取得要素による取得されたものからさらに個別の絞り込み(例えば正規表現にマッチするものかどうかなど)を行いたい場合もあるでしょう。フィルタの文字列の最初と最後を特定の記号で囲むことによって、絞り込みが行えます。これは、SPLのFilterIterator(あるいはその派生クラスRegexIteratorなど)を利用するものです。

##### 正規表現による絞り込み #####

正規表現による絞り込みを行う場合は、デリミタによってモードが設定されます。詳しくは、[RegexIterator]()の項を参照してください。
デリミタと設定モード

- / あるいは #
    - MATCHと同等です。

- $
    - GET_MATCHと同等です。(このデリミタは実験的なもの)

対象とするHTMLと実行結果

    $html = <<<HTML
    <html>
    CD売上
    <div>
        <span>アーティスト - タイトル</span>
        <span>Perfume / クラフトワーク音頭</span>
        <span>ランキング：1位</span>
    </div>
    </html>
    HTML;

    // /あるいは#でのデリミタ(MATCH)
    $scraper->process('span', 'cd[] => "TEXT", #([\w]+)\s+/#')

    array(1) {
        [1]=>
        string(37) "Perfume / クラフトワーク音頭"
    }

    // $でのデリミタ (GET_MATCH)
    $scraper->process('span', 'cd[] => "TEXT", $([\w]+)\s+/$')
    array(1) {
        [1]=>
        array(2) {
            [0]=>
            string(9) "Perfume /"
            [1]=>
            string(7) "Perfume"
        }
    }

