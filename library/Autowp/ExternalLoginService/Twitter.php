<?php

class Autowp_ExternalLoginService_Twitter
    extends Autowp_ExternalLoginService_Abstract
{
    /**
     * @var Zend_Oauth_Consumer
     */
    protected $_consumer = null;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session = null;

    /**
     * @var Zend_Oauth_Token_Access
     */
    protected $_accessToken = null;

    public function getSession()
    {
        if (!$this->_session) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }

        return $this->_session;
    }

    public function getConsumer(array $options = array())
    {
        if (!$this->_consumer) {
            $consumerOptions = array(
                'requestScheme'   => Zend_Oauth::REQUEST_SCHEME_HEADER,
                'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
                'accessTokenUrl'  => 'https://api.twitter.com/oauth/access_token',
                'siteUrl'         => "http://twitter.com/oauth",
                'consumerKey'     => $this->_options['consumerKey'],
                'consumerSecret'  => $this->_options['consumerSecret'],
            );
            if (isset($options['redirect_uri'])) {
                $consumerOptions['callbackUrl'] = $options['redirect_uri'];
            }
            $this->_consumer = new Zend_Oauth_Consumer($consumerOptions);
        }

        return $this->_consumer;
    }

    /**
     * @param array $options
     * @return string
     */
    public function getLoginUrl(array $options)
    {
        $consumer = $this->getConsumer(array(
            'redirect_uri' => $options['redirect_uri']
        ));
        $this->getSession()->requestToken = $consumer->getRequestToken();

        return $consumer->getRedirectUrl();
    }

    /**
     * @param array $params
     */
    public function callback(array $params)
    {
        if (isset($params['denied']) && $params['denied']) {
            return false;
        }

        $session = $this->getSession();

        if (!isset($session->requestToken)) {
            $message = 'Request token not set';
            throw new Autowp_ExternalLoginService_Exception($message);
        }

        $consumer = $this->getConsumer(array(
            'redirect_uri' => $params['redirect_uri']
        ));
        $this->_accessToken = $consumer->getAccessToken($params, $session->requestToken);

        unset($session->requestToken);

        return (bool)$this->_accessToken;
    }

    /**
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData()
    {
        $twitter = new Zend_Service_Twitter(array(
            'username'        => $this->_accessToken->getParam('screen_name'),
            'accessToken'     => $this->_accessToken,
            'oauthOptions'    => array(
                'consumerKey'    => $this->_options['consumerKey'],
                'consumerSecret' => $this->_options['consumerSecret'],
            )
        ));
        $response = $twitter->account->verifyCredentials();

        if (!$response->isSuccess()) {
            $message = 'Error requesting data';
            throw new Autowp_ExternalLoginService_Exception($message);
        }

        $values = $response->toValue();

        $imageUrl = null;
        if ($values->profile_image_url) {
            $imageUrl = str_replace('_normal', '', $values->profile_image_url);
        }

        $data = array(
            'externalId' => $values->id,
            'name'       => $values->name,
            'profileUrl' => 'http://twitter.com/' . $values->screen_name,
            'photoUrl'   => $imageUrl
        );

        return new Autowp_ExternalLoginService_Result($data);
    }
}