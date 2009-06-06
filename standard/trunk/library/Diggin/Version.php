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
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @category  Diggin
 * @package   Diggin_Version
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license   http://diggin.musicrider.com/LICENSE     New BSD License
 */
final class Diggin_Version
{
    /**
     * library version
     *
     */
    const VERSION = '0.6.7';

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
