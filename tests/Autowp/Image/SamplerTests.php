<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Autowp_Image_SamplerTests::main');
}

require_once dirname(__FILE__).'/../../TestHelper.php';


class Autowp_Image_SamplerTests extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testShouldResizeOddWidthPictureStrictlyToTargetWidthByOuterFitType()
    {
        $sampler = new Autowp_Image_Sampler();

        $file = dirname(__FILE__) . '/_files/Towers_Schiphol_small.jpg';

        $imagick = new Imagick();

        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'     => Autowp_Image_Sampler_Format::FIT_TYPE_OUTER,
            'width'      => 102,
            'height'     => 149,
            'background' => 'red'
        ));
        $this->assertSame($imagick->getImageWidth(), 102);
        $imagick->clear();
    }

    public function testShouldResizeOddHeightPictureStrictlyToTargetHeightByOuterFitType()
    {
        $sampler = new Autowp_Image_Sampler();

        $file = dirname(__FILE__) . '/_files/Towers_Schiphol_small.jpg';

        $imagick = new Imagick();

        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_OUTER,
            'width'      => 101,
            'height'     => 150,
            'background' => 'red'
        ));

        $this->assertSame($imagick->getImageHeight(), 150);
        $imagick->clear();
    }
}

if (PHPUnit_MAIN_METHOD == 'Autowp_Image_SamplerTests::main') {
    Autowp_Test::main();
}
