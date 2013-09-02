<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Autowp_FilterTest::main');
}

require_once dirname(__FILE__).'/../TestHelper.php';


class Autowp_FilterTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testShouldNotSwapDotsForSlash()
    {
        $filter = new Autowp_Filter_Filename_Safe();

        $this->assertSame($filter->filter('just.test'), 'just.test');
    }

    public function testShouldReplaceDotAndDoubleDotsButNotTripleDots()
    {
        $filter = new Autowp_Filter_Filename_Safe();

        $this->assertSame($filter->filter('.'), '_');
        $this->assertSame($filter->filter('..'), '__');
        $this->assertSame($filter->filter('...'), '...');
    }

    public function testShouldReplaceEmptyStringForUnderscore()
    {
        $filter = new Autowp_Filter_Filename_Safe();

        $this->assertSame($filter->filter(''), '_');
    }
}

if (PHPUnit_MAIN_METHOD == 'Autowp_FilterTest::main') {
    Autowp_Test::main();
}
