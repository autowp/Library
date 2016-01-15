<?php

class Autowp_ExternalLoginService_GooglePlus
    extends Autowp_ExternalLoginService_LeagueOAuth2
{
    protected function _createProvider()
    {
        return new League\OAuth2\Client\Provider\Google([
            'clientId'     => $this->_options['clientId'],
            'clientSecret' => $this->_options['clientSecret'],
            'redirectUri'  => $this->_options['redirect_uri'],
            'userFields'   => ['id', 'displayName', 'url', 'image(url)']
            //'hostedDomain' => 'example.com',
        ]);
    }

    protected function _getAuthorizationUrl()
    {
        return $this->_getProvider()->getAuthorizationUrl(array(
            'scope' => 'https://www.googleapis.com/auth/plus.me'
        ));
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $provider = $this->_getProvider();

        $data = array(
            'externalId' => null,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null
        );

        $ownerDetails = $provider->getResourceOwner($this->_accessToken);

        $ownerDetailsArray = $ownerDetails->toArray();

        $data['externalId'] = $ownerDetailsArray['id'];

        return new Autowp_ExternalLoginService_Result(array(
            'externalId' => $ownerDetailsArray['id'],
            'name'       => $ownerDetailsArray['displayName'],
            'profileUrl' => $ownerDetailsArray['url'],
            'photoUrl'   => $ownerDetailsArray['image']['url']
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