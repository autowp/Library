<?php

class Autowp_ExternalLoginService_Vk
    extends Autowp_ExternalLoginService_LeagueOAuth2
{
    protected function _createProvider()
    {
        return new Autowp\OAuth2\Client\Provider\Vk([
            'clientId'     => $this->_options['clientId'],
            'clientSecret' => $this->_options['clientSecret'],
            'redirectUri'  => $this->_options['redirect_uri'],
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

        if (isset($options['language'])) {
            $provider->setLang($options['language']);
        }

        $ownerDetails = $provider->getResourceOwner($this->_accessToken);

        $data = array(
            'externalId' => null,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null
        );

        $vkUser = $ownerDetails->toArray();

        if (isset($vkUser['uid']) && $vkUser['uid']) {
            $data['externalId'] = $vkUser['uid'];
        }

        $firstName = false;
        if (isset($vkUser['first_name']) && $vkUser['first_name']) {
            $firstName = $vkUser['first_name'];
        }
        $lastName = false;
        if (isset($vkUser['last_name']) && $vkUser['last_name']) {
            $lastName = $vkUser['last_name'];
        }
        $data['name'] = $firstName . ($firstName && $lastName ? ' ' : '') . $lastName;
        if (isset($vkUser['screen_name']) && $vkUser['screen_name']) {
            $data['profileUrl'] = 'http://vk.com/' . $vkUser['screen_name'];
        }
        if (isset($vkUser['photo_medium']) && $vkUser['photo_medium']) {
            $data['photoUrl'] = $vkUser['photo_medium'];
        }

        return new Autowp_ExternalLoginService_Result($data);
    }

    public function serviceFriends($token)
    {
        throw new Exception("Not implemented");
    }

    public function getFriendsUrl()
    {
        throw new Exception("Not implemented");
    }
}