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
    public function getLoginUrl(array $options)
    {
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

        return (bool)$facebook->getAccessToken($params, $redirectUri);
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return array
     */
    public function getData()
    {
        $json = $this->_getFacebook()->api('/me');

        $uaData = array(
            'external_id' => null,
            'name'        => null,
            'link'        => null,
            'photo'       => null
        );
        if (isset($json['id']) && $json['id']) {
            $uaData['external_id'] = $json['id'];
            $uaData['photo'] = sprintf($this->_imageUrlTemplate, $json['id']);
        }
        if (isset($json['name']) && $json['name']) {
            $uaData['name'] = $json['name'];
        }
        if (isset($json['link']) && $json['link']) {
            $uaData['link'] = $json['link'];
        }

        return $uaData;
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
}