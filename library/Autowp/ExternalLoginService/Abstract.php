<?php

abstract class Autowp_ExternalLoginService_Abstract
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @param array $options
     * @return string
     */
    abstract public function getLoginUrl(array $options);

    /**
     * @param array $params
     * @return bool
     */
    abstract public function callback(array $params);

    /**
     * @return Autowp_ExternalLoginService_Result
     */
    abstract public function getData();

    public function __construct(array $options)
    {
        $this->_options = $options;
    }
}