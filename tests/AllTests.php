<?php

require_once 'TestHelper.php';

//require_once 'Autowp/Test.php';

//class_exists('PHPUnit_Framework_TestFailure'); // fix loading problem
//class_exists('PHPUnit_Util_Filter');

class AllTests
{

    public static function main()
    {
        /*$parameters = array();

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);*/
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Autowp Library');

        $suite = new PHPUnit_Framework_TestSuite('Autowp Library - Autowp_Xxx');
        $suite->addTestSuite('Autowp_FilterTest');
        $suite->addTestSuite('Autowp_StorageTest');

        return $suite;
    }
}