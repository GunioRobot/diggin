<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/** Diggin_Scraper_Helper_Simplexml_Simplexml_HeadBaseHref **/
require_once 'Diggin/Scraper/Helper/Simplexml/HeadBaseHref.php';

/**
 * Helper for pagerize info
 *
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */
class Diggin_Scraper_Helper_Simplexml_Pagerize
        extends Diggin_Scraper_Helper_Simplexml_HeadBaseHref
{
    
    const HATOM_PAGEELEMENT = '//*[contains(concat(" ", @class, " "), " hentry ")]';
    const HATOM_NEXTLINK = '//link[contains(concat(" ", translate(normalize-space(@rel),"NEXT","next"), " "), " next ")] | //a[contains(concat(" ", translate(normalize-space(@rel),"NEXT","next"), " "), " next ")]';

    const CACHE_TAG_PREFIX = 'Diggin_Scraper_Helper_Simplexml_Pagerize_';

    /**
     * @var Zend_Cache_Core
     */
    private static $_cache;

    /**
     * @var array
     */
    private static $_siteinfokeys = array(); 

    public function direct($preferHeadBase = true, $preferhAtom = true)
    {
        return $this->getNextLink($preferHeadBase, $preferhAtom);
    }

    /**
     * Sets a cache object
     *
     * @param Zend_Cache_Core $cache
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        self::$_cache = $cache;
    }
    
    public function getNextLink($preferHeadBase = true, $preferhAtom = true)
    {
        $baseurl = $this->getBaseUrl($preferHeadBase);
        if ($preferhAtom) {
            $nextLink = $this->hAtomNextLink();
        }

        // LIFO
        if (count(self::$_siteinfokeys) !== 0) {
            foreach (array_reverse(self::$_siteinfokeys) as $key) {
                $siteinfo = $this->getSiteinfo($key); 
                if ($next = $this->getNextLinkFromSiteinfo($siteinfo, $baseurl)) {
                    $nextLink = $next;
                    break;
                }
            }
        }

        if (($baseurl === null) or ($nextLink === null)) {
            return null;
        } elseif (($baseurl === null) and (null == parse_url($nextLink, PHP_URL_HOST))) {
            return null;
        } elseif ($baseurl === null) {
            return $nextLink; //maybe hAtom only
        }

        require_once 'Diggin/Uri/Http.php';
        return Diggin_Uri_Http::getAbsoluteUrl($nextLink, $baseurl);
    }
    
    //checks, 
    public function hAtomNextLink()
    {
        $nextpageelement = $this->getResource()->xpath(self::HATOM_NEXTLINK);
        if (count($nextpageelement) !== 0) {
            return $nextpageelement[0][@href];
        }

        return null;
    }

    public function getNextLinkFromWedata()
    {
        require_once 'Diggin/Service/Wedata.php';

        if (self::$_cache) {
            if(!$items = self::$_cache->load(self::CACHE_TAG_PREFIX.'wedata_items')) {
                $items = Diggin_Service_Wedata::getItems('AutoPagerize');
                self::$_cache->save($items, self::CACHE_TAG_PREFIX.'wedata_items');
            }
        } else {
            $items = Diggin_Service_Wedata::getItems('AutoPagerize');
        }
    
        return getNextlinkFromSiteInfo($items, $this->getBaseUrl());        
    }

    /**
     * Get next url from siteinfo
     *
     * @param array $items
     * @param string $url base url
     * @return mixed
     */
    protected function getNextlinkFromSiteInfo($items, $url) 
    {
        foreach ($items as $item) {
            //hAtom 対策
            if ('^https?://.' != $item['url'] && (preg_match('>'.$item['url'].'>', $url) == 1)) {
                $nextLinks = $this->getResource()->xpath($item['nextLink']);
                if (count($nextLinks) !== 0) {
                    return $nextLinks[0][@href];
                }
            }
        }
        
        return null;
    }

    public static function appendSiteinfo($prefix, $siteinfo)
    {
        $key = self::CACHE_TAG_PREFIX.$prefix;

        self::$_cache->save($siteinfo, $key);

        array_push(self::$_siteinfokeys, $key);
    }

    public function getSiteinfo($key)
    {
        return self::$_cache->load($key);
    }

}
