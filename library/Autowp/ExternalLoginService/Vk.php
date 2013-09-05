<?php

class Autowp_ExternalLoginService_Vk extends Autowp_ExternalLoginService_OAuth
{
    protected $_vkUserId = null;

    protected $_accessToken = null;

    /**
     * @see Autowp_ExternalLoginService_OAuth::_processCallback()
     */
    public function _processCallback($accessToken, $data)
    {
        if (!isset($data['user_id'])) {
            throw new Autowp_ExternalLoginService_Exception("'user_id' was not provided");
        }

        $this->_accessToken = $accessToken;
        $this->_vkUserId = $data['user_id'];

        return (bool)$this->_vkUserId;
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $data = array(
            'externalId' => $this->_vkUserId,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null
        );

        $json = $this->_genericApiCall('https://api.vkontakte.ru/method/getProfiles', array(
            'access_token' => $this->_accessToken,
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