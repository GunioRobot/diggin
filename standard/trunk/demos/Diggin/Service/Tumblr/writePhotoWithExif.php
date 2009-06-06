<?php
//TumblrにExifの写真データつきで投稿
//Pelライブラリも使用
//
//コマンドラインで、>php writePhotoWithExif.php -f photo.jpg
//みたいな形で投稿とか
$email = 'your@mail.com';
$password = 'your_pass';


require_once 'Pel/PelJpeg.php';
require_once 'Zend/Console/Getopt.php';
require_once 'Zend/Debug.php';
require_once 'Diggin/Service/Tumblr/Write.php';
require_once 'Zend/Http/Client.php';
$client = new Zend_Http_Client();
$client->setConfig(array('timeout'=> 500));//ブロードバンドだったらいらないかも


$opt =  new Zend_Console_Getopt('f:');
$file = $opt->f;

$input_jpeg = new PelJpeg($file);
$original = imagecreatefromstring($input_jpeg->getBytes());
$original_w = ImagesX($original);
$original_h = ImagesY($original);

$tumblrWidthMax = 500;

$tumblrHeightMax = 700;
if($original_h > $tumblrHeightMax) {
    $scale = $tumblrHeightMax / $original_h;
}

Zend_Debug::dump($scale);

$scaled_w = $original_w * $scale;
$scaled_h = $original_h * $scale;

$scaled = ImageCreateTrueColor($scaled_w, $scaled_h);
ImageCopyResampled($scaled, $original,
                   0, 0, /* dst (x,y) */
                   0, 0, /* src (x,y) */
                   $scaled_w, $scaled_h,
                   $original_w, $original_h);

$output_jpeg = new PelJpeg($scaled);

$exif = $input_jpeg->getExif();

if ($exif != null) {
  $output_jpeg->setExif($exif);
  
  $tiff = $exif->getTiff();
  $ifd0 = $tiff->getIfd();
  $_exif = $ifd0->getSubIfd(PelIfd::EXIF);
  $data['date_time_original'] = $_exif->getEntry(PelTag::DATE_TIME_ORIGINAL)->getText();
  $data['model'] = $ifd0->getEntry(PelTag::MODEL)->getText();
}

Zend_Debug::dump($data);

if(isset($data)){
    $caption = "This Photo is taken at ".$data['date_time_original']."<br /> with ".$data['model'];
} else {
    $caption = null;
}
Zend_Debug::dump($caption);

$tumblr = new Diggin_Service_Tumblr_Write();

$tumblr->setHttpClient($client);
$tumblr->setAuth($email, $password);
$return = $tumblr->writePhoto('data', $output_jpeg->getBytes(), $caption);
echo "tumblr,posted";