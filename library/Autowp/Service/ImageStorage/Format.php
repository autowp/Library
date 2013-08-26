<?php

class Autowp_Service_ImageStorage_Format
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
     * @param array $options
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @param array $options
     * @return Autowp_Service_ImageStorage_Format
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new Autowp_Service_ImageStorage_Exception("Unexpected option '$key'");
            }
        }

        return $this;
    }

    /**
     * @param int $fitType
     * @throws Autowp_Service_ImageStorage_Exception
     * @return Autowp_Service_ImageStorage_Format
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
                throw new Autowp_Service_ImageStorage_Exception($message);
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
     * @throws Autowp_Service_ImageStorage_Exception
     * @return Autowp_Service_ImageStorage_Format
     */
    public function setWidth($width)
    {
        $width = (int)$width;
        if ($width < 0) {
            $message = "Unexpected width `$width`";
            throw new Autowp_Service_ImageStorage_Exception($message);
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
     * @throws Autowp_Service_ImageStorage_Exception
     * @return Autowp_Service_ImageStorage_Format
     */
    public function setHeight($height)
    {
        $height = (int)$height;
        if ($height < 0) {
            $message = "Unexpected height `$height`";
            throw new Autowp_Service_ImageStorage_Exception($message);
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
     * @throws Autowp_Service_ImageStorage_Exception
     * @return Autowp_Service_ImageStorage_Format
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
}