<?php

class Autowp_ExternalLoginService_Linkedin
    extends Autowp_ExternalLoginService_LeagueOAuth2
{
    protected function _createProvider()
    {
        return new League\OAuth2\Client\Provider\LinkedIn([
            'clientId'     => $this->_options['clientId'],
            'clientSecret' => $this->_options['clientSecret'],
            'redirectUri'  => $this->_options['redirect_uri']
        ]);
    }

    protected function _getAuthorizationUrl()
    {
        return $this->_getProvider()->getAuthorizationUrl();
    }

    protected function _getFriendsAuthorizationUrl()
    {
        throw new Autowp_ExternalLoginService_Exception("Not implemented");
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData(array $options)
    {
        $provider = $this->_getProvider();

        $ownerDetails = $provider->getResourceOwner($this->_accessToken);

        return new Autowp_ExternalLoginService_Result(array(
            'externalId' => $ownerDetails->getId(),
            'name'       => trim($ownerDetails->getFirstname() . ' ' . $ownerDetails->getLastname()),
            'profileUrl' => $ownerDetails->getUrl(),
            'photoUrl'   => $ownerDetails->getImageurl()
        ));
    }

    public function getFriendsUrl()
    {
        throw new Autowp_ExternalLoginService_Exception("Not implemented");
    }

    public function getFriends()
    {
        throw new Autowp_ExternalLoginService_Exception("Not implemented");
    }
}