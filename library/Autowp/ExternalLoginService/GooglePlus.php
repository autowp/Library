<?php

class Autowp_ExternalLoginService_GooglePlus
    extends Autowp_ExternalLoginService_OAuth
{
    /**
     * @var string
     */
    protected $_accessToken;

    public function _processCallback($accessToken, $data)
    {
        $this->_accessToken = $accessToken;

        return (bool)$this->_accessToken;
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $data = array(
            'externalId' => null,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null
        );

        $json = $this->_genericApiCall('https://www.googleapis.com/plus/v1/people/me', array(
            'access_token' => $this->_accessToken,
            'fields'       => 'id,displayName,url,image(url)'
        ));

        if (isset($json['id']) && $json['id']) {
            $data['externalId'] = $json['id'];
        }
        if (isset($json['displayName']) && $json['displayName']) {
            $data['name'] = $json['displayName'];
        }
        if (isset($json['url']) && $json['url']) {
            $data['profileUrl'] = $json['url'];
        }
        if (isset($json['image']['url']) && $json['image']['url']) {
            $data['photoUrl'] = $json['image']['url'];
        }

        return new Autowp_ExternalLoginService_Result($data);
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