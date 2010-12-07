<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Siteinfo
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Siteinfo;

class Iterator extends \ArrayIterator
{
    public function current()
    {
        $current = parent::current();
        if (is_array($current) && array_key_exists('data', $current)){
            return $current['data'];
        }

        return $current;
    }
}


