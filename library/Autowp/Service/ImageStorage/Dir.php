<?php

class Autowp_Service_ImageStorage_Dir
{
    /**
     * @var string
     */
    protected $_path;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var Autowp_Service_ImageStorage_NamingStrategy_Abstract
     */
    protected $_namingStrategy;

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
     * @return Autowp_Service_ImageStorage_Dir
     * @throws Autowp_Service_ImageStorage_Exception
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
     * @param string $path
     * @return Autowp_Service_ImageStorage_Dir
     */
    public function setPath($path)
    {
        if (!is_string($path)) {
            return $this->_raise("Path must be a string");
        }

        $path = trim($path);

        if (!$path) {
            return $this->_raise("Path cannot be empty, '$path' given");
        }

        $this->_path = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @param string $url
     * @return Autowp_Service_ImageStorage_Dir
     */
    public function setUrl($url)
    {
        if (isset($url)) {
            $this->_url = (string)$url;
        } else {
            $this->_url = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string|array|Autowp_Service_ImageStorage_NamingStrategy_Abstract $strategy
     * @throws Autowp_Service_ImageStorage_Exception
     * @return Autowp_Service_ImageStorage_Dir
     */
    public function setNamingStrategy($strategy)
    {
        if (!$strategy instanceof Autowp_Service_ImageStorage_NamingStrategy_Abstract) {
            if (is_array($strategy)) {
                $strategyName = $strategy['strategy'];
                $options = isset($strategy['options']) ? $strategy['options'] : array();
            } else {
                $strategyName = $strategy;
                $options = array();
            }

            $className = 'Autowp_Service_ImageStorage_NamingStrategy_' . ucfirst($strategyName);
            $strategy = new $className($options);
            if (!$strategy instanceof Autowp_Service_ImageStorage_NamingStrategy_Abstract) {
                return $this->_raise("$className is not naming strategy");
            }
        }

        $strategy->setDir($this->_path);

        $this->_namingStrategy = $strategy;

        return $this;
    }

    /**
     * @return Autowp_Service_ImageStorage_NamingStrategy_Abstract
     */
    public function getNamingStrategy()
    {
        return $this->_namingStrategy;
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