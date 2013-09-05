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
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $json = $this->_getFacebook()->api('/me');

        $data = array(
            'externalId' => null,
            'name'       => null,
            'profileUrl' => null,
            'photoUrl'   => null
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
}