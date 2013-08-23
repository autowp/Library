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
     * @param array $options
     */
    public function __construct(array $options)
    {
        $defaults = array(
            'path' => null,
            'dir'  => null
        );

        $options = array_merge($defaults, $options);

        $this->setPath($options['path']);
        $this->setUrl($options['url']);
    }

    /**
     * @param string $path
     * @return Autowp_Service_ImageStorage_Dir
     */
    public function setPath($path)
    {
        if (!is_string($path)) {
            $message = "Path must be a string";
            throw new Autowp_Service_ImageStorage_Exception($message);
        }

        $path = trim($path);

        if (!$path) {
            $message = "Path cannot be empty, '$path' given";
            throw new Autowp_Service_ImageStorage_Exception($message);
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
}