<?php

// http://d.hatena.ne.jp/sasezaki/20090414/p1

require_once 'Diggin/Scraper/Strategy/Flexible.php';
require_once 'HTML/CSS/Selector2XPath.php';
 
class Diggin_Scraper_Strategy_Selector2XPath extends Diggin_Scraper_Strategy_Flexible
{
    //override
    protected static function _xpathOrCss2Xpath($exp)
    {
        if (preg_match('!^(?:/|id\()!', $exp)) {
            return '.'.$exp;
        } else {
            if ($exp === '.') {
                return $exp;
            } else if (ctype_alnum($exp)) {
                return ".//$exp";
            } else if (0 === strncasecmp('./', $exp, 2)) {
                return $exp;
            } else {
                return '.'.HTML_CSS_Selector2XPath::toXpath($exp);
            }
        }
    }
 
}

//
require_once 'Diggin/Scraper.php';

Diggin_Scraper::changeStrategy('Diggin_Scraper_Strategy_Selector2XPath');

$scraper = new Diggin_Scraper();
$results = $scraper->process('div.photo_main_left img', 'image => @src')
                   ->scrape('http://www.ota-suke.jp/photo/22226/57085');
var_dump($results);


$selector = 'div.photo_main_left img';
echo HTML_CSS_Selector2XPath::toXpath($selector);
