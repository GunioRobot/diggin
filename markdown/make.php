<?php
// Note for publish 
// find ./ -name '*.html' | xargs sed -i 's/\.markdown\"/.html"/g'

require 'lib/markdown.php';
$library = 'http://code.google.com/p/diggin/source/browse/standard/trunk/library';
if (!$argv[1]) die('Need markdownfile');

if(is_dir($argv[1])) {
    foreach(new DirectoryIterator($argv[1]) as $f) {
        if (!is_file($f)) continue;
        makeFile($f);
    }
} elseif (is_file($argv[1])) {
    makeFile($argv[1]);
} else {
    echo 'nothing to do';
}

function makeFile($markdownfile) { 
    if (!preg_match('/.markdown$/', $markdownfile)) return; //die('not markdown suffix');

    $title = 'Diggin リファレンス';
    if ($markdownfile != 'diggin.index.markdown') {
        $title = preg_replace('/\.markdown$/i', '',$markdownfile);
        $title = explode('.', $title);
        foreach ($title as $k => $t) $title[$k]  = ucfirst($t);
        $title = implode('_', $title);
    }

$text = Markdown(file_get_contents($markdownfile));

$head = <<<EOF
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>$title</title>
<link rel="stylesheet" href="./dbstyle.css" type="text/css">
</head>
<body bgcolor="white" text="black" link="#0000FF" vlink="#840084" alink="#0000FF">
EOF;

if ($markdownfile != 'diggin.index.markdown') {
    $link = str_replace('_', '/', $title);
    //$foot = '<hr>'."$library/$link.php";
    $foot = '';
} else {
    $foot = '';
}

$foot .= <<<EOF
<hr />
<center>
<a href="diggin.index.markdown">ホーム</a>
</center>
</body>
</html>
EOF;

$markdownfile = realpath($markdownfile);

$markdownfile = dirname($markdownfile).'/html/'.pathinfo($markdownfile, PATHINFO_FILENAME).'.html';
var_dump($markdownfile);

file_put_contents($markdownfile, $head.$text.$foot);
}
