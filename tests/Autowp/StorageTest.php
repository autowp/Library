<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Autowp_StorageTest::main');
}

require_once dirname(__FILE__).'/../TestHelper.php';


class Autowp_StorageTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function _getStrategy()
    {
        return new Autowp_Service_ImageStorage_NamingStrategy_Pattern(array(
            'dir' => sys_get_temp_dir()
        ));
    }

    public function testShouldNotSwapDotsForSlash()
    {
        $generated = $this->_getStrategy()->generate(array(
            'pattern' => 'just.test'
        ));

        $this->assertSame($generated, 'just.test');
    }

    public function testShouldNotPassDotsAndDoubleDots()
    {
        $strategy = $this->_getStrategy();

        $toCheck = array(
            './test/./test/.'    => 'test/test',
            '../test/../test/..' => 'test/test',
        );

        foreach ($toCheck as $pattern => $result) {
            $generated = $strategy->generate(array(
                'pattern' => $pattern
            ));

            $this->assertSame($generated, $result);
        }
    }

    public function testShouldUseNumbersForEmptyPattern()
    {
        $generated = $this->_getStrategy()->generate(array(
            'pattern'   => '',
            'extension' => 'jpg'
        ));

        $this->assertSame($generated, '0.jpg');
    }
}

if (PHPUnit_MAIN_METHOD == 'Autowp_StorageTest::main') {
    Autowp_Test::main();
}
