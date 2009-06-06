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
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * Auto-filtering values
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */
class Diggin_Scraper_Filter_Iterator extends FilterIterator
{
    /**
     * Filter
     * 
     * @var mixed
     */
    protected $filter;
    
    /**
     * prefix flag
     * 
     * @var string
     */
    protected $prefixFlag;

    /**
     * @param Iterator $iterator
     * @param String $filter
     * @param boolean $prefixFlag
     */
    public function __construct(Iterator $iterator, $filter, $prefixFlag)
    {
        parent::__construct($iterator);
        $this->filter = $filter;
        $this->prefixFlag = $prefixFlag;
    }

    /**
     * accept 
     * 
     * @return boolean
     */
    public function accept()
    {
        $value = $this->current();
        
        if (function_exists($this->filter)) {
            $filterValue = call_user_func($this->filter, $value);
        } else if (!strstr($this->filter, '_')) {
            require_once 'Zend/Filter.php';
            $filterValue = Zend_Filter::get($value, $this->filter);
        } else {
            require_once 'Zend/Loader.php';
            $filter = $this->filter;
            try {
                Zend_Loader::loadClass($filter);
            } catch (Zend_Exception $e) {
                require_once 'Diggin/Scraper/Filter/Exception.php';
                throw new Diggin_Scraper_Filter_Exception("Unable to load filter '$filter': {$e->getMessage()}");
            }
            $filter = new $filter();
            $filterValue = $filter->filter($value);
        }

        if ($this->prefixFlag === true) {
            if ($filterValue != $value) {
                return false;
            } else {
                return true;
            }
        } else {
             if ($filterValue != $value) {
                return true;
            } else {
                return false;
            }
        }
    }
}
