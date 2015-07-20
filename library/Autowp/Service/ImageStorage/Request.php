<?php

class Autowp_Service_ImageStorage_Request
{
    private $_imageId;

    /**
     * @var int
     */
    private $_cropLeft;

    /**
     * @var int
     */
    private $_cropTop;

    /**
     * @var int
     */
    private $_cropWidth;

    /**
     * @var int
     */
    private $_cropHeight;

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
                $this->_raise("Unexpected option '$key'");
            }
        }

        return $this;
    }

    /**
     * @param int $imageId
     * @return Autowp_Service_ImageStorage_Request
     */
    public function setImageId($imageId)
    {
        $this->_imageId = (int)$imageId;

        return $this;
    }

    /**
     * @return int
     */
    public function getImageId()
    {
        return $this->_imageId;
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

    /**
     * @param string $message
     * @throws Autowp_Service_ImageStorage_Exception
     */
    protected function _raise($message)
    {
        throw new Autowp_Service_ImageStorage_Exception($message);
    }
}