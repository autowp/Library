<?php

class Autowp_ExternalLoginService_Vk
    extends Autowp_ExternalLoginService_LeagueOAuth2
{
    protected function _createProvider()
    {
        return new League\OAuth2\Client\Provider\Google([
            'clientId'       => $this->_options['clientId'],
            'clientSecret'   => $this->_options['clientSecret'],
            'redirectUri'    => $this->_options['redirect_uri'],
            'urlAuthorize'   => 'https://oauth.vk.com/authorize',
            'urlAccessToken' => 'https://oauth.vk.com/access_token',
        ]);
    }

    protected function _getAuthorizationUrl()
    {
        return $this->_getProvider()->getAuthorizationUrl();
    }

    protected $_vkUserId = null;

    public function callback(array $params)
    {
        $result = parent::callback($params);

        if ($result) {
            $this->_vkUserId = $params['user_id'];
        }

        return $result;
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $fields = array_merge($this->defaultUserFields, $this->userFields);
        return 'https://api.vkontakte.ru/method/getProfiles?' . http_build_query([
            'uid'    => $this->_vkUserId,
            'fields' => 'uid,first_name,last_name,nickname,screen_name,photo_medium'
        ]);
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $provider = $this->_getProvider();

        $ownerDetails = $provider->getResourceOwner($this->_accessToken);

        $data = array(
            'externalId' => $this->_vkUserId,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null
        );

        $json = $ownerDetails->toArray();

        if (!isset($json['response'])) {
            throw new Autowp_ExternalLoginService_Exception('Key "response" not found');
        }

        $vkUsers = $json['response'];
        foreach ($vkUsers as $vkUser) {
            if ($vkUser['uid'] == $this->_vkUserId) {
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
                break;
            }
        }

        return new Autowp_ExternalLoginService_Result($data);
    }
}