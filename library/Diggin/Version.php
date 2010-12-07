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
 * @package    Diggin_Version
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin;

/**
 * @category  Diggin
 * @package   Diggin_Version
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license   http://diggin.musicrider.com/LICENSE     New BSD License
 */
final class Version
{
    /**
     * library version
     *
     */
    const VERSION = '0.8.0dev';

    /**
     * Compare the specified library version string $version
     * with the current Diggin_Version::VERSION of the Diggin.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @return boolean           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        return version_compare($version, self::VERSION);
    }
}
