<?php

class Autowp_ExternalLoginService_Facebook
    extends Autowp_ExternalLoginService_Abstract
{
    /**
     * @var Autowp_Service_Facebook
     */
    protected $_facebook = null;

    /**
     * @var string
     */
    protected $_imageUrlTemplate =
        'https://graph.facebook.com/%s/picture?type=large';

    /**
     * @return Autowp_Service_Facebook
     */
    protected function _getFacebook()
    {
        if ($this->_facebook === null) {
            $this->_facebook = new Autowp_Service_Facebook($this->_options);
        }
        return $this->_facebook;
    }

    /**
     * @param array $options
     * @return string
     */
    public function getFriendsUrl(array $options)
    {
        $this->_getFacebook()->setPermission(Autowp_Service_Facebook::PERMISSION_FRIENDS);
        return $this->_getFacebook()->getLoginUrl(array(
            'redirect_uri' => $options['redirect_uri']
        ));
    }

    /**
     * @param array $options
     * @return string
     */
    public function getLoginUrl(array $options)
    {
        $this->_getFacebook()
            ->setPermission(Autowp_Service_Facebook::PERMISSION_LOCATION)
            ->setPermission(Autowp_Service_Facebook::PERMISSION_BIRTHDAY);
        return $this->_getFacebook()->getLoginUrl(array(
            'redirect_uri' => $options['redirect_uri']
        ));
    }

    /**
     * @param array $params
     */
    public function callback(array $params)
    {
        $facebook = $this->_getFacebook();

        $redirectUri = $params['redirect_uri'];
        unset($params['redirect_uri']);
        return $facebook->getAccessToken($params, $redirectUri);
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $json = $this->_getFacebook()->api('/me');
        $data = array(
            'externalId' => null,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null,
            'birthday' => null,
            'email' => null,
            'residence' => null,
            'gender' => null
        );
        if (isset($json['id']) && $json['id']) {
            $data['externalId'] = $json['id'];
            $data['photoUrl'] = sprintf($this->_imageUrlTemplate, $json['id']);
        }
        if (isset($json['name']) && $json['name']) {
            $data['name'] = $json['name'];
        }
        if (isset($json['link']) && $json['link']) {
            $data['profileUrl'] = $json['link'];
        }
        if (isset($json['birthday']) && $json['birthday']) {
            $data['birthday'] = new Zend_Date($json['birthday'], 'MM/dd/yyyy');
        }
        if (isset($json['email']) && $json['email']) {
            $data['email'] = $json['email'];
        }
        if (isset($json['location']) && isset($json['location']['name']) && $json['location']['name']) {
            $data['residence'] = $json['location']['name'];
        }
        if (isset($json['gender']) && $json['gender']) {
            $genderTable = new Gender();
            $gender = $genderTable->fetchRow(array("lower(name) = ?" => strtolower($json['gender'])));
            if ($gender) {
                $data['gender'] = $gender->id;
            }
        }
        return new Autowp_ExternalLoginService_Result($data);
    }

    /**
     * @param string $accessToken
     * @return Autowp_ExternalLoginService_Facebook
     */
    public function setAccessToken($accessToken)
    {
        $this->_getFacebook()->setAccessToken($accessToken);

        return $this;
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getFriends()
     * @return array Account_Row
     */
    public function serviceFriends ($token)
    {
        $this->_getFacebook()->setAccessToken($token);

        $limit = 1000;
        $url = '/me/friends?limit='.$limit.'&offset=0';
        $friendsId = array();
        while (true) {
            $response = $this->_getFacebook()->api($url);
            if ($response) {
                foreach($response['data'] as $key => $value) {
                    $friendsId[] = (string)$value['id'];
                }
                if (count($friendsId) == 0) break;
                if (count($friendsId) == $limit && isset($response['paging']['next'])){
                    $url = $response['paging']['next'];
                } else {
                    break;
                }
            } else {
                $message = 'Error requesting data';
                throw new Autowp_ExternalLoginService_Exception($message);
            }
        }
        return $friendsId;
    }
}