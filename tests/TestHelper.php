<?php


/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting(E_ALL | E_STRICT);

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$zfRoot        = realpath('../../../zendframework/zf1');
$zfCoreLibrary = "$zfRoot/library";
$zfCoreTests   = "$zfRoot/tests";
$autowpLibrary = realpath('../library');


$path = array(
    $zfCoreLibrary,
    $zfCoreTests,
    $autowpLibrary,
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $path));

require_once 'PHPUnit/Autoload.php';
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance()->registerNamespace('Autowp');

//print get_include_path(); exit;

/*
 * Unset global variables that are no longer needed.
 */
unset($zfRoot, $zfCoreLibrary, $zfCoreTests, $path);