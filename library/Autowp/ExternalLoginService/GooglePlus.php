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
     * @return array
     */
    public function getData()
    {
        $uaData = array(
            'external_id' => null,
            'name'        => null,
            'link'        => null,
            'photo'       => null
        );

        $json = $this->_genericApiCall('https://www.googleapis.com/plus/v1/people/me', array(
            'access_token' => $this->_accessToken,
            'fields'       => 'id,displayName,url,image(url)'
        ));

        if (isset($json['id']) && $json['id']) {
            $uaData['external_id'] = $json['id'];
        }
        if (isset($json['displayName']) && $json['displayName']) {
            $uaData['name'] = $json['displayName'];
        }
        if (isset($json['url']) && $json['url']) {
            $uaData['link'] = $json['url'];
        }
        if (isset($json['image']['url']) && $json['image']['url']) {
            $uaData['photo'] = $json['image']['url'];
        }

        return $uaData;
    }
}