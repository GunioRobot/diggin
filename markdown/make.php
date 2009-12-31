<?php
$library = 'http://code.google.com/p/diggin/source/browse/standard/trunk/library';
if (!$argv[1]) die('Need markdownfile');
$markdownfile = $argv[1];
if (!preg_match('/.markdown$/', $markdownfile)) die('not markdown suffix');

$title = 'Diggin リファレンス';
if ($markdownfile != 'diggin.index.markdown') {
    $title = preg_replace('/\.markdown$/i', '',$markdownfile);
    $title = explode('.', $title);
    foreach ($title as $k => $t) $title[$k]  = ucfirst($t);
    $title = implode('_', $title);
}

require 'lib/markdown.php';

$text = Markdown(file_get_contents($argv[1]));

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

$markdownfile = dirname($markdownfile).'/html/'.pathinfo($markdownfile, PATHINFO_FILENAME).'.markdown';
var_dump($markdownfile);

file_put_contents($markdownfile, $head.$text.$foot);
