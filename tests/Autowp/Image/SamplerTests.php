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

    public function testReduceOnlyWithInnerFitWorks()
    {
        $sampler = new Autowp_Image_Sampler();

        $file = dirname(__FILE__) . '/_files/Towers_Schiphol_small.jpg';

        $imagick = new Imagick();

        // both size less
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 150,
            'height'     => 200,
            'reduceOnly' => true
        ));

        $this->assertSame($imagick->getImageWidth(), 101);
        $this->assertSame($imagick->getImageHeight(), 149);
        $imagick->clear();

        // width less
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 150,
            'height'     => 100,
            'reduceOnly' => true
        ));

        $this->assertSame($imagick->getImageWidth(), 68);
        $this->assertSame($imagick->getImageHeight(), 100);
        $imagick->clear();

        // height less
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 50,
            'height'     => 200,
            'reduceOnly' => true
        ));

        $this->assertSame($imagick->getImageWidth(), 50);
        $this->assertSame($imagick->getImageHeight(), 74);
        $imagick->clear();

        // not less
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 50,
            'height'     => 100,
            'reduceOnly' => true
        ));

        $this->assertSame($imagick->getImageWidth(), 50);
        $this->assertSame($imagick->getImageHeight(), 100);
        $imagick->clear();


        // both size less, reduceOnly off
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 150,
            'height'     => 200,
            'reduceOnly' => false
        ));

        $this->assertSame($imagick->getImageWidth(), 150);
        $this->assertSame($imagick->getImageHeight(), 200);
        $imagick->clear();

        // width less, reduceOnly off
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 150,
            'height'     => 100,
            'reduceOnly' => false
        ));

        $this->assertSame($imagick->getImageWidth(), 150);
        $this->assertSame($imagick->getImageHeight(), 100);
        $imagick->clear();

        // height less, reduceOnly off
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 50,
            'height'     => 200,
            'reduceOnly' => false
        ));

        $this->assertSame($imagick->getImageWidth(), 50);
        $this->assertSame($imagick->getImageHeight(), 200);
        $imagick->clear();

        // not less, reduceOnly off
        $imagick->readImage($file); //101x149
        $sampler->convertImagick($imagick, array(
            'fitType'    => Autowp_Image_Sampler_Format::FIT_TYPE_INNER,
            'width'      => 50,
            'height'     => 100,
            'reduceOnly' => false
        ));

        $this->assertSame($imagick->getImageWidth(), 50);
        $this->assertSame($imagick->getImageHeight(), 100);
        $imagick->clear();
    }
}

if (PHPUnit_MAIN_METHOD == 'Autowp_Image_SamplerTests::main') {
    Autowp_Test::main();
}
