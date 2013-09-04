<?php

class Autowp_ExternalLoginService_Vk extends Autowp_ExternalLoginService_OAuth
{
    protected $_vkUserId = null;

    public function _processCallback($accessToken, $data)
    {
        if (!isset($data['user_id'])) {
            throw new Autowp_ExternalLoginService_Exception("'user_id' was not provided");
        }

        $this->_vkUserId = $data['user_id'];

        return (bool)$this->_vkUserId;
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

        $uaData['external_id'] = $this->_vkUserId;

        $json = $this->_genericApiCall('https://api.vkontakte.ru/method/getProfiles', array(
            'access_token' => $accessToken,
            'uid'          => $this->_vkUserId,
            'fields'       => 'uid,first_name,last_name,nickname,screen_name,photo_medium'
        ));

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
                $uaData['name'] = $firstName . ($firstName && $lastName ? ' ' : '') . $lastName;
                if (isset($vkUser['screen_name']) && $vkUser['screen_name']) {
                    $uaData['link'] = 'http://vk.com/' . $vkUser['screen_name'];
                }
                if (isset($vkUser['photo_medium']) && $vkUser['photo_medium']) {
                    $uaData['photo'] = $vkUser['photo_medium'];
                }
                break;
            }
        }

        return $uaData;
    }
}