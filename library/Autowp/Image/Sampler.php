<?php

class Autowp_Image_Sampler
{
    /**
     * @param Imagick $imagick
     * @param Autowp_Image_Sampler_Format $format
     */
    protected function _convertByInnerFit(Imagick $imagick,
        Autowp_Image_Sampler_Format $format)
    {
        $srcWidth = $imagick->getImageWidth();
        $srcHeight = $imagick->getImageHeight();
        $srcRatio = $srcWidth / $srcHeight;

        $widthLess  = $format->getWidth()  && ($srcWidth  < $format->getWidth() );
        $heightLess = $format->getHeight() && ($srcHeight < $format->getHeight());
        $sizeLess = $widthLess || $heightLess;

        $ratio = $format->getWidth() / $format->getHeight();

        if ($format->getReduceOnly() && $sizeLess) {
            // dont crop
            if (!$heightLess) {
                // resize by height
                $scaleHeight = $format->getHeight();
                $scaleWidth = round($scaleHeight * $srcRatio);
                $imagick->scaleImage(
                    $scaleWidth, $scaleHeight, false
                );
            } elseif (!$widthLess) {
                // resize by width
                $scaleWidth = $format->getWidth();
                $scaleHeight = round($scaleWidth / $srcRatio);
                $imagick->scaleImage(
                    $scaleWidth, $scaleHeight, false
                );
            }
        } else {

            // высчитываем размеры обрезания
            if ($ratio < $srcRatio) {
                // широкая картинка
                $cropWidth = (int)round($srcHeight * $ratio);
                $cropHeight = $srcHeight;
                $cropLeft = (int)floor(($srcWidth - $cropWidth) / 2);
                $cropTop = 0;
            } else {
                // высокая картинка
                $cropWidth = $srcWidth;
                $cropHeight = (int)round($srcWidth / $ratio);
                $cropLeft = 0;
                $cropTop = (int)floor(($srcHeight - $cropHeight) / 2);
            }

            $imagick->setImagePage(0, 0, 0, 0);
            if (!$imagick->cropImage($cropWidth, $cropHeight, $cropLeft, $cropTop)) {
                return $this->_raise("Error crop");
            }

            $imagick->scaleImage(
                $format->getWidth(), $format->getHeight(), false
            );
        }
    }

    /**
     * @param Imagick $imagick
     * @param Autowp_Image_Sampler_Format $format
     */
    protected function _convertByOuterFit(Imagick $imagick,
        Autowp_Image_Sampler_Format $format)
    {
        $srcWidth = $imagick->getImageWidth();
        $srcHeight = $imagick->getImageHeight();
        $srcRatio = $srcWidth / $srcHeight;

        $widthLess  = $format->getWidth()  && ($srcWidth  < $format->getWidth() );
        $heightLess = $format->getHeight() && ($srcHeight < $format->getHeight());
        $sizeLess = $widthLess || $heightLess;

        $ratio = $format->getWidth() / $format->getHeight();

        if ($format->getReduceOnly() && $sizeLess) {
            // dont crop
            if (!$heightLess) {
                // resize by height
                $scaleHeight = $format->getHeight();
                $scaleWidth = round($scaleHeight * $srcRatio);
                $imagick->scaleImage(
                    $scaleWidth, $scaleHeight, false
                );
            } elseif (!$widthLess) {
                // resize by width
                $scaleWidth = $format->getWidth();
                $scaleHeight = round($scaleWidth / $srcRatio);
                $imagick->scaleImage(
                    $scaleWidth, $scaleHeight, false
                );
            }
        } else {
            // высчитываем размеры обрезания
            if ($ratio < $srcRatio) {
                $scaleWidth = $format->getWidth();
                $scaleHeight = round($format->getWidth() / $srcRatio);// добавляем поля сверху и снизу
            } else {
                // добавляем поля по бокам
                $scaleWidth = round($format->getHeight() * $srcRatio);
                $scaleHeight = $format->getHeight();
            }

            $imagick->scaleImage(
                $scaleWidth, $scaleHeight, false
            );
        }

        // extend by bg-space
        $borderLeft = floor(($format->getWidth()  - $imagick->getImageWidth())  / 2);
        $borderTop  = floor(($format->getHeight() - $imagick->getImageHeight()) / 2);

        $imagick->extentImage(
            $format->getWidth(),
            $format->getHeight(),
            -$borderLeft,
            -$borderTop
        );
    }

    /**
     * @param Imagick $imagick
     * @param Autowp_Image_Sampler_Format $format
     */
    protected function _convertByMaximumFit(Imagick $imagick,
        Autowp_Image_Sampler_Format $format)
    {
        $srcWidth = $imagick->getImageWidth();
        $srcHeight = $imagick->getImageHeight();
        $srcRatio = $srcWidth / $srcHeight;

        $widthLess  = $format->getWidth()  && ($srcWidth  < $format->getWidth() );
        $heightLess = $format->getHeight() && ($srcHeight < $format->getHeight());
        $sizeLess = $widthLess || $heightLess;

        $ratio = $format->getWidth() / $format->getHeight();

        if ($format->getReduceOnly() && $sizeLess) {

            if (!$heightLess) {
                // resize by height
                $scaleHeight = $format->getHeight();
                $scaleWidth = round($scaleHeight * $srcRatio);
                $imagick->scaleImage(
                    $scaleWidth, $scaleHeight, false
                );
            } elseif (!$widthLess) {
                // resize by width
                $scaleWidth = $format->getWidth();
                $scaleHeight = round($scaleWidth / $srcRatio);
                $imagick->scaleImage(
                    $scaleWidth, $scaleHeight, false
                );
            }

        } else {

            // высчитываем размеры обрезания
            if ($ratio < $srcRatio) {
                $scaleWidth = $format->getWidth();
                $scaleHeight = round($format->getWidth() / $srcRatio);
            } else {
                // добавляем поля по бокам
                $scaleWidth = round($format->getHeight() * $srcRatio);
                $scaleHeight = $format->getHeight();
            }

            $imagick->scaleImage(
                $scaleWidth, $scaleHeight, false
            );

        }
    }

    /**
     * @param Imagick $imagick
     * @param Autowp_Image_Sampler_Format $format
     */
    protected function _convertByWidth(Imagick $imagick,
        Autowp_Image_Sampler_Format $format)
    {
        $srcWidth = $imagick->getImageWidth();
        $srcRatio = $srcWidth / $imagick->getImageHeight();

        $widthLess = $srcWidth < $format->getWidth();

        if ($format->getReduceOnly() && $widthLess) {
            $scaleWidth = $srcWidth;
        } else {
            $scaleWidth = $format->getWidth();
        }

        $scaleHeight = round($scaleWidth / $srcRatio);

        $imagick->scaleImage($scaleWidth, $scaleHeight, false);
    }

    /**
     * @param Imagick $imagick
     * @param Autowp_Image_Sampler_Format $format
     */
    protected function _convertByHeight(Imagick $imagick,
        Autowp_Image_Sampler_Format $format)
    {
        $srcHeight = $imagick->getImageHeight();
        $srcRatio = $imagick->getImageWidth() / $srcHeight;

        $heightLess = $format->getHeight() && ($srcHeight < $format->getHeight());

        $ratio = $format->getWidth() / $format->getHeight();

        if ($format->getReduceOnly() && $heightLess) {
            $scaleHeight = $srcHeight;
        } else {
            $scaleHeight = $format->getHeight();
        }

        $scaleWidth = round($scaleHeight * $srcRatio);

        $imagick->scaleImage(
            $scaleWidth, $scaleHeight, false
        );
    }

    /**
     * @param Imagick $source
     * @param array|Autowp_Image_Sampler_Format $format
     * @throws Autowp_Image_Sampler_Exception
     */
    public function convertImagick(Imagick $imagick, $format)
    {
        if (!$format instanceof Autowp_Image_Sampler_Format) {
            if (is_array($format)) {
                $format = new Autowp_Image_Sampler_Format($format);
            } else {
                return $this->_raise("Unexpected type of format");
            }
        }

        if ($quality = $format->getQuality()) {
            $imagick->setImageCompressionQuality($quality);
        }

        $crop = false;
        if (!$format->getIgnoreCrop()) {
            $crop = $format->getCrop();
        }

        if ($crop) {
            $cropSet = isset($crop['width'], $crop['height'], $crop['left'], $crop['top']);
            if (!$cropSet) {
                return $this->_raise('Crop parameters not properly set');
            }

            $cropWidth  = (int)$crop['width'];
            $cropHeight = (int)$crop['height'];
            $cropLeft   = (int)$crop['left'];
            $cropTop    = (int)$crop['top'];

            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();

            $leftValid = ($cropLeft >= 0) && ($cropLeft < $width );
            if (!$leftValid) {
                return $this->_raise("Crop left out of bounds ('$cropLeft')");
            }

            $topValid = ($cropTop >= 0) && ($cropTop < $height);
            if (!$topValid) {
                return $this->_raise("Crop top out of bounds ('$cropTop')");
            }

            $right = $cropLeft + $cropWidth;
            $widthValid  = ($cropWidth > 0) && ($right <= $width );
            if (!$widthValid) {
                return $this->_raise("Crop width out of bounds ('$cropLeft + $cropWidth')");
            }

            $bottom = $cropTop + $cropHeight;
            $heightValid = ($cropHeight > 0) && ($bottom <= $height);
            if (!$heightValid) {
                return $this->_raise("Crop height out of bounds ('$cropTop + $cropHeight')");
            }
            $imagick->cropImage($cropWidth, $cropHeight, $cropLeft, $cropTop);
        }

        $bg = $format->getBackground();
        if (!$bg) {
            $bg = 'transparent';
        }
        $imagick->setBackgroundColor($bg);
        $imagick->setImageBackgroundColor($bg);


        if ($format->getWidth() && $format->getHeight()) {
            switch ($format->getFitType()) {
                case Autowp_Image_Sampler_Format::FIT_TYPE_INNER:
                    $this->_convertByInnerFit($imagick, $format);
                    break;

                case Autowp_Image_Sampler_Format::FIT_TYPE_OUTER:
                    $this->_convertByOuterFit($imagick, $format);
                    break;

                case Autowp_Image_Sampler_Format::FIT_TYPE_MAXIMUM:
                    $this->_convertByMaximumFit($imagick, $format);
                    break;

                default:
                    $this->_raise("Unexpected FIT_TYPE `{$format->getFitType()}`");
            }
        } else {

            if ($format->getWidth()) {
                $this->_convertByWidth($imagick, $format);
            } elseif ($format->getHeight()) {
                $this->_convertByHeight($imagick, $format);
            }

        }

        if ($format->getStrip()) {
            $imagick->stripImage();
        }

        if ($imageFormat = $format->getFormat()) {
            $imagick->setImageFormat($imageFormat);
        }
    }

    /**
     * @param Imagick|string $source
     * @param array|Autowp_Image_Sampler_Format $format
     * @throws Autowp_Image_Sampler_Exception
     */
    public function convertToFile($source, $destFile, $format)
    {
        if ($source instanceof Imagick) {
            $imagick = clone $source; // to prevent modifying source
        } else {
            $imagick = new Imagick();
            if (!$imagick->readImage($source)) {
                return $this->_raise("Error read image from `$source`");
            }
        }

        if (!$destFile) {
            return $this->_raise("Dest file not set");
        }

        $this->convertImagick($imagick, $format);

        if (!$imagick->writeImage($destFile)) {
            return $this->_raise("Error write image to `$destFile`");
        }

        $imagick->clear();
    }

    /**
     * @param string $message
     * @throws Autowp_Image_Sampler_Exception
     */
    protected function _raise($message)
    {
        throw new Autowp_Image_Sampler_Exception($message);
    }
}