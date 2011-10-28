<?php
/**
 * Setup autoloading
 */
function DigginTest_Autoloader($class)
{
    $class = ltrim($class, '\\');

    if (!preg_match('#^((Diggin(Test)?)|Zend(Test)?|PHPUnit)(\\\\|_)#', $class)) {
        return false;
    }

    // $segments = explode('\\', $class); // preg_split('#\\\\|_#', $class);//
    $segments = preg_split('#[\\\\_]#', $class); // preg_split('#\\\\|_#', $class);//
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'Zend':
            $file = dirname(__DIR__) . '/library/Zend/';
            break;
        case 'ZendTest':
            // temporary fix for ZendTest namespace until we can migrate files
            // into ZendTest dir
            $file = __DIR__ . '/Zend/';
            break;
        case 'Diggin':
            $file = dirname(__DIR__) . '/library/Diggin/';
            break;
        /**
         * Diggin wouldn't require migrate
        case 'DigginTest':
            // temporary fix for DigginTest namespace until we can migrate files
            // into DigginTest dir
            $file = __DIR__ . '/Diggin/';
            break;
        */
        default:
            $file = false;
            break;
    }

    if ($file) {
        $file .= implode('/', $segments) . '.php';
        if (file_exists($file)) {
            return include_once $file;
        }
    }

    $segments = explode('_', $class);
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'PHPUnit':
            return include_once str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        case 'Zend':
            $file = dirname(__DIR__) . '/library/Zend/';
            break;
        case 'Diggin':
            $file = dirname(__DIR__) . '/library/Diggin/';
            break;
        default:
            return false;
    }
    $file .= implode('/', $segments) . '.php';
    if (file_exists($file)) {
        return include_once $file;
    }

    return false;
}
spl_autoload_register('DigginTest_Autoloader', true, true);

