<?php

abstract class Autowp_Service_ImageStorage_NamingStrategy_Abstract
{
    /**
     * @var string
     */
    protected $_dir = null;

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
     * @return Autowp_Service_ImageStorage_NamingStrategy_Abstract
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
     * @param string $dir
     * @return Autowp_Service_ImageStorage_NamingStrategy_Abstract
     */
    public function setDir($dir)
    {
        $this->_dir = rtrim($dir, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->_dir;
    }

    /**
     * @param array $options
     * @return string
     */
    abstract public function generate(array $options = array());
}