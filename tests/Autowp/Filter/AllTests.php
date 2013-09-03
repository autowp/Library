<?php

require_once dirname(__FILE__) . '/../../TestHelper.php';

class Autowp_Filter_AllTests
{

    public static function main()
    {
        /*$parameters = array();

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);*/
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Autowp Library - Autowp_Filter');
        $suite->addTestSuite('Autowp_Filter_FilenameTests');

        return $suite;
    }
}