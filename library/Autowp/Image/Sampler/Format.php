<?php

/**
 * @author dima
 *
 * @desc Represents a image formating rules
 */
class Autowp_Image_Sampler_Format
{
    const
        FIT_TYPE_INNER = '0', // вписать
        FIT_TYPE_OUTER = '1', // описать
        FIT_TYPE_MAXIMUM = '2';

    /**
     * @var int
     */
    protected $_fitType;

    /**
     * @var int
     */
    protected $_width;

    /**
     * @var int
     */
    protected $_height;

    /**
     * @var string
     */
    protected $_background;

    /**
     * @var int
     */
    protected $_cropLeft;

    /**
     * @var int
     */
    protected $_cropTop;

    /**
     * @var int
     */
    protected $_cropWidth;

    /**
     * @var int
     */
    protected $_cropHeight;

    /**
     * @param array $options
     * @throws Autowp_Image_Sampler_Exception
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @param array $options
     * @return Autowp_Image_Sampler_Format
     * @throws Autowp_Image_Sampler_Exception
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new Autowp_Image_Sampler_Exception("Unexpected option '$key'");
            }
        }

        return $this;
    }

    /**
     * @param int $fitType
     * @throws Autowp_Image_Sampler_Exception
     * @return Autowp_Image_Sampler_Format
     */
    public function setFitType($fitType)
    {
        $fitType = (int)$fitType;
        switch ($fitType) {
            case self::FIT_TYPE_INNER:
            case self::FIT_TYPE_OUTER:
            case self::FIT_TYPE_MAXIMUM:
                $this->_fitType = $fitType;
                break;

            default:
                $message = "Unexpected fit type `$fitType`";
                throw new Autowp_Image_Sampler_Exception($message);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getFitType()
    {
        return $this->_fitType;
    }

    /**
     * @param int $width
     * @throws Autowp_Image_Sampler_Exception
     * @return Autowp_Image_Sampler_Format
     */
    public function setWidth($width)
    {
        $width = (int)$width;
        if ($width < 0) {
            $message = "Unexpected width `$width`";
            throw new Autowp_Image_Sampler_Exception($message);
        }
        $this->_width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @param int $height
     * @throws Autowp_Image_Sampler_Exception
     * @return Autowp_Image_Sampler_Format
     */
    public function setHeight($height)
    {
        $height = (int)$height;
        if ($height < 0) {
            $message = "Unexpected height `$height`";
            throw new Autowp_Image_Sampler_Exception($message);
        }
        $this->_height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @param string $color
     * @throws Autowp_Image_Sampler_Exception
     * @return Autowp_Image_Sampler_Format
     */
    public function setBackground($color)
    {
        $this->_background = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackground()
    {
        return $this->_background;
    }

    /**
     * @param array $crop
     * @return Autowp_Image_Sampler_Format
     */
    public function setCrop(array $crop)
    {
        if (!isset($crop['left'])) {
            return $this->_raise("Crop left not provided");
        }
        $this->setCropLeft($crop['left']);

        if (!isset($crop['top'])) {
            return $this->_raise("Crop top not provided");
        }
        $this->setCropTop($crop['top']);

        if (!isset($crop['width'])) {
            return $this->_raise("Crop width not provided");
        }
        $this->setCropWidth($crop['width']);

        if (!isset($crop['height'])) {
            return $this->_raise("Crop height not provided");
        }
        $this->setCropHeight($crop['height']);

        return $this;
    }

    /**
     * @param int $value
     * @return Autowp_Image_Sampler_Format
     */
    public function setCropLeft($value)
    {
        $value = (int)$value;
        if ($value < 0) {
            return $this->_raise("Crop left cannot be lower than 0");
        }
        $this->_cropLeft = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return Autowp_Image_Sampler_Format
     */
    public function setCropTop($value)
    {
        $value = (int)$value;
        if ($value < 0) {
            return $this->_raise("Crop top cannot be lower than 0");
        }
        $this->_cropTop = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return Autowp_Image_Sampler_Format
     */
    public function setCropWidth($value)
    {
        $value = (int)$value;
        if ($value < 0) {
            return $this->_raise("Crop width cannot be lower than 0");
        }
        $this->_cropWidth = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return Autowp_Image_Sampler_Format
     */
    public function setCropHeight($value)
    {
        $value = (int)$value;
        if ($value < 0) {
            return $this->_raise("Crop height cannot be lower than 0");
        }
        $this->_cropHeight = $value;

        return $this;
    }

    /**
     * @return array|bool
     */
    public function getCrop()
    {
        if (!isset($this->_cropLeft, $this->_cropTop, $this->_cropWidth, $this->_cropHeight)) {
            return false;
        }
        return array(
            'left'   => $this->_cropLeft,
            'top'    => $this->_cropTop,
            'width'  => $this->_cropWidth,
            'height' => $this->_cropHeight
        );
    }
}