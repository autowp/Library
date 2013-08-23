<?php

class Autowp_Service_ImageStorage_Image
{
    /**
     * @var int
     */
    protected $_width;

    /**
     * @var int
     */
    protected $_height;

    /**
     * @var int
     */
    protected $_filesize;

    /**
     * @var string
     */
    protected $_src;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $defaults = array(
            'width'    => null,
            'height'   => null,
            'filesize' => null,
            'src'      => null
        );

        $options = array_merge($defaults, $options);

        $this->_width    =    (int)$options['width'];
        $this->_height   =    (int)$options['height'];
        $this->_filesize =    (int)$options['filesize'];
        $this->_src      = (string)$options['src'];
    }

    public function toArray()
    {
        return array(
            'width'    => $this->_width,
            'height'   => $this->_height,
            'filesize' => $this->_filesize,
            'src'      => $this->_src
        );
    }
}