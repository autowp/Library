<?php

abstract class Autowp_ExternalLoginService_Abstract
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @return string
     */
    abstract public function getState();

    /**
     * @param array $options
     * @return string
     */
    abstract public function getLoginUrl();

    /**
     * @return string
     */
    abstract public function getFriendsUrl();

    /**
     * @param array $params
     * @return bool
     */
    abstract public function callback(array $params);

    /**
     * @return Autowp_ExternalLoginService_Result
     */
    abstract public function getData(array $options);

    /**
     * @return string
     */
    abstract public function getFriends();

    public function __construct(array $options)
    {
        $this->_options = $options;
    }
}