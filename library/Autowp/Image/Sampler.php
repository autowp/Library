<?php

class Autowp_Image_Sampler
{
    /**
     * @var int
     */
    protected $_resizeFilter = Imagick::FILTER_CUBIC;

    /**
     * @var float
     */
    protected $_resizeBlur = 1;

    /**
     * @param int $filter
     * @return Autowp_Image_Sampler
     */
    public function setResizeFilter($filter)
    {
        $this->_resizeFilter = $filter;

        return $this;
    }

    /**
     * @param float $blur
     * @return Autowp_Image_Sampler
     */
    public function setResizeBlur($blur)
    {
        $this->_resizeBlur = $blur;

        return $this;
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


        $crop = $format->getCrop();

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

        $srcWidth = $imagick->getImageWidth();
        $srcHeight = $imagick->getImageHeight();
        $srcRatio = $srcWidth / $srcHeight;

        if ($format->getWidth() && $format->getHeight()) {
            $ratio = $format->getWidth() / $format->getHeight();

            switch ($format->getFitType()) {
                case Autowp_Image_Sampler_Format::FIT_TYPE_INNER:

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

                    /*$imagick->resizeImage(
                        $format->getWidth(), $format->getHeight(),
                        $this->_resizeFilter, $this->_resizeBlur, false
                    );*/

                    $imagick->scaleImage(
                        $format->getWidth(), $format->getHeight(), false
                    );

                    break;

                case Autowp_Image_Sampler_Format::FIT_TYPE_OUTER:

                    // высчитываем размеры обрезания
                    if ($ratio < $srcRatio) {
                        $scaleWidth = $format->getWidth();
                        $scaleHeight = round($format->getWidth() / $srcRatio);// добавляем поля сверху и снизу
                    } else {
                        // добавляем поля по бокам
                        $scaleWidth = round($format->getHeight() * $srcRatio);
                        $scaleHeight = $format->getHeight();
                    }

                    $imagick->resizeImage(
                        $scaleWidth, $scaleHeight,
                        $this->_resizeFilter, $this->_resizeBlur, false
                    );

                    $imagick->borderImage(
                        $options['background'],
                        $format->getWidth() - $scaleWidth,
                        $format->getHeight() - $scaleHeight
                    );

                    break;

                case Autowp_Image_Sampler_Format::FIT_TYPE_MAXIMUM:

                    // высчитываем размеры обрезания
                    if ($ratio < $srcRatio) {
                        $scaleWidth = $format->getWidth();
                        $scaleHeight = round($format->getWidth() / $srcRatio);
                    } else {
                        // добавляем поля по бокам
                        $scaleWidth = round($format->getHeight() * $srcRatio);
                        $scaleHeight = $format->getHeight();
                    }

                    $imagick->resizeImage(
                        $scaleWidth, $scaleHeight,
                        $this->_resizeFilter, $this->_resizeBlur, false
                    );

                    break;

                default:
                    $this->_raise("Неизвестный FIT_TYPE `{$format->getFitType()}`");
            }
        } else {

            if ($format->getWidth()) {
                $scaleWidth = $format->getWidth();
                $scaleHeight = round($format->getWidth() / $srcRatio);

                $imagick->resizeImage(
                    $scaleWidth, $scaleHeight,
                    $this->_resizeFilter, $this->_resizeBlur, false
                );

            } elseif ($format->getHeight()) {
                // добавляем поля по бокам
                $scaleWidth = round($format->getHeight() * $srcRatio);
                $scaleHeight = $format->getHeight();

                $imagick->resizeImage(
                    $scaleWidth, $scaleHeight,
                    $this->_resizeFilter, $this->_resizeBlur, false
                );
            }

        }
    }

    /**
     * @param Imagick|string $source
     * @param Autowp_Image_Sampler_Format $format
     * @throws Autowp_Image_Sampler_Exception
     */
    public function convertToFile($source, $destFile, Autowp_Image_Sampler_Format $format)
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