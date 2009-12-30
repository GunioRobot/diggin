### ブロック構造(多次元配列)での取得 ###

Diggin_Scraperではより強力な取得方法として、perlのWeb::Scraperと同様の、ブロック構造(多次元配列)での取得をサポートしています。

ブロック構造での取得については、Web::Scraper用に記述された以下の記事を参照してください。
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

#### 多次元配列取得の際の制限事項 ####

Diggin_Scraperの現バージョンでは、取得要素が見つからない箇所については、配列のセットに値は格納されず、それ以降のプロセスに対して処理を行いません。
以下に簡単な例を示します。

    $html = <<<HTML
    <html>
    <body>
    <div class="a">
        <a>link </a>
        <a href="2">link2</a>
        <a href="3">link3</a>
    </div>
    <div class="a">
        <ul>
            <li><a href="href1.html">value1</a></li>
            <li>value2</li>
            <li>value3</li>
        </ul>
    </div>
    </body>
    </html>
    HTML;

    $li = new Diggin_Scraper;
    $li->process('//li', 'li[] => TEXT');

    $scraper = new Diggin_Scraper();
    $scraper->setUrl('http://example.com/');
    $scraper->process('//div', array('div[]' => $li))
            ->scrape(array($html));

    var_dump($scraper->div);

HTMLでのdivそれぞれのブロックに対し、liでの取得を行っています。
このコードの実行結果は以下の通りです。

    array(1) {
      [1]=>
      array(1) {
        ["li"]=>
        array(3) {
          [0]=>
          string(6) "value1"
          [1]=>
          string(6) "value2"
          [2]=>
          string(6) "value3"
        }
      }
    }

結果にあるとおり、li要素がない一つ目のdivブロックは格納されていません。また、配列キーにはdiv対象だった配列2つ目のキー'1'として格納されています。

