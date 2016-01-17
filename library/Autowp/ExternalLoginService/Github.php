<?php

class Autowp_ExternalLoginService_Github
    extends Autowp_ExternalLoginService_LeagueOAuth2
{
    protected function _createProvider()
    {
        return new League\OAuth2\Client\Provider\Github([
            'clientId'     => $this->_options['clientId'],
            'clientSecret' => $this->_options['clientSecret'],
            'redirectUri'  => $this->_options['redirect_uri']
        ]);
    }

    protected function _getAuthorizationUrl()
    {
        return $this->_getProvider()->getAuthorizationUrl();
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData(array $options)
    {
        $provider = $this->_getProvider();

        $ownerDetails = $provider->getResourceOwner($this->_accessToken);
        $data = $ownerDetails->toArray();

        return new Autowp_ExternalLoginService_Result(array(
            'externalId' => $data['id'],
            'name'       => $data['name'],
            'profileUrl' => $data['html_url'],
            'photoUrl'   => $data['avatar_url']
        ));
    }

    public function getFriendsUrl(array $options)
    {
        throw new Autowp_ExternalLoginService_Exception("Not implemented");
    }

    public function serviceFriends($token)
    {
        throw new Autowp_ExternalLoginService_Exception("Not implemented");
    }
}