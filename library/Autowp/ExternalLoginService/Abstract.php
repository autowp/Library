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
     * @param array $options
     * @return string
     */
    abstract public function getFriendsUrl(array $options);

    /**
     * @param array $params
     * @return bool
     */
    abstract public function callback(array $params);

    /**
     * @return Autowp_ExternalLoginService_Result
     */
    abstract public function getData();

    /**
     * @param Account_Row $account
     * @return string
     */
    public function getFriends($token, Zend_Cache_Core $cache)
    {
        $cacheName = get_class($this).'_friends_'.sha1($token);
        $friends = $cache->load($cacheName);
        if ($friends !== false) return $friends;

        $friends = $this->serviceFriends($token);

        $cache->save($friends, $cacheName);
        return $friends;
    }

    /**
     * @param Account_Row $account
     * @return string
     */
    abstract public function serviceFriends($token);

    public function __construct(array $options)
    {
        $this->_options = $options;
    }
}