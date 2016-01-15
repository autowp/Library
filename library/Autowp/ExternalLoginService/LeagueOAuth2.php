<?php

abstract class Autowp_ExternalLoginService_LeagueOAuth2
    extends Autowp_ExternalLoginService_Abstract
{
    /**
     * @var League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $_provider;

    /**
     * @var string
     */
    protected $_accessToken;

    /**
     * @return Zend_Session_Namespace
     */
    protected function _getOauthSession()
    {
        return new Zend_Session_Namespace(__CLASS__);
    }

    abstract protected function _createProvider();

    /**
     * @return League\OAuth2\Client\Provider\AbstractProvider
     */
    protected function _getProvider()
    {
        if (!$this->_provider) {
            $this->_provider = $this->_createProvider();
        }

        return $this->_provider;
    }

    abstract protected function _getAuthorizationUrl();

    public function getLoginUrl()
    {
        $provider = $this->_getProvider();

        $authUrl = $this->_getAuthorizationUrl();

        $this->_getOauthSession()->state = $provider->getState();

        return $authUrl;
    }

    public function callback(array $params)
    {
        if ($this->_getOauthSession()->state !== $params['state']) {
            throw new Exception("State is invalid");
        }

        $provider = $this->_getProvider();

        $this->_accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $params['code']
        ]);

        return $this->_accessToken;
    }
}