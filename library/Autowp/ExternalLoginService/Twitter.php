<?php

class Autowp_ExternalLoginService_Twitter extends Autowp_ExternalLoginService_Abstract
{
    /**
     *
     * @var Zend_Oauth_Consumer
     */
    protected $_consumer = null;

    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     *
     * @var Zend_Oauth_Token_Access
     */
    protected $_accessToken = null;

    /**
     * @var string
     */
    private $_state = null;

    protected function _getSession()
    {
        return $this->_session ? $this->_session : $this->_session = new Zend_Session_Namespace(
                'Twitter');
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
                'consumerSecret'  => $this->_options['consumerSecret']
            );
            if (isset($this->_options['redirect_uri'])) {
                $consumerOptions['callbackUrl'] = $this->_options['redirect_uri'];
            }
            $this->_consumer = new Zend_Oauth_Consumer($consumerOptions);
        }

        return $this->_consumer;
    }

    public function getState()
    {
        return $this->_state;
    }

    /**
     *
     * @param array $options
     * @return string
     */
    public function getLoginUrl()
    {
        $consumer = $this->getConsumer(array(
            'redirect_uri' => $this->_options['redirect_uri']
        ));

        $requestToken = $consumer->getRequestToken();

        $this->_state = $requestToken->getToken();

        $this->_getSession()->requestToken = $requestToken;
        return $consumer->getRedirectUrl();
    }

    /**
     *
     * @param array $options
     * @return string
     */
    public function getFriendsUrl(array $options)
    {
        $consumer = $this->getConsumer(array(
            'redirect_uri' => $this->_options['redirect_uri']
        ));
        $this->_getSession()->requestToken = $consumer->getRequestToken();
        return $consumer->getRedirectUrl();
    }

    /**
     *
     * @param array $params
     */
    public function callback(array $params)
    {
        if (isset($params['denied']) && $params['denied']) {
            return false;
        }
        $session = $this->_getSession();
        if (! isset($this->_getSession()->requestToken)) {
            $message = 'Request token not set';
            throw new Autowp_ExternalLoginService_Exception($message);
        }

        $consumer = $this->getConsumer(array(
            'redirect_uri' => $this->_options['redirect_uri']
        ));
        $this->_accessToken = $consumer->getAccessToken($params,
                $this->_getSession()->requestToken);
        unset($this->_getSession()->requestToken);

        return $this->_accessToken;
    }

    /**
     *
     * @see Autowp_ExternalLoginService_Abstract::getData()
     * @return Autowp_ExternalLoginService_Result
     */
    public function getData(array $options)
    {
        $twitter = new Zend_Service_Twitter(array(
            'username'     => $this->_accessToken->getParam('screen_name'),
            'accessToken'  => $this->_accessToken,
            'oauthOptions' => array(
                'consumerKey'    => $this->_options['consumerKey'],
                'consumerSecret' => $this->_options['consumerSecret']
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

    /**
     *
     * @see Autowp_ExternalLoginService_Abstract::getFriends()
     * @return array Account_Row
     */
    public function serviceFriends($token)
    {
        $this->_accessToken = $token;

        $twitter = new Zend_Service_Twitter(array(
            'username'     => $this->_accessToken->getParam('screen_name'),
            'accessToken'  => $this->_accessToken,
            'oauthOptions' => array(
                'consumerKey'    => $this->_options['consumerKey'],
                'consumerSecret' => $this->_options['consumerSecret']
            )
        ));
        $cursor = - 1;
        $friendsId = array();
        $count = 1000;
        while (true) {
            $client = $twitter->getHttpClient()->setUri('https://api.twitter.com/1.1/friends/ids.json');
            $client->setParameterGet(array(
                "user_id" => $this->_accessToken->getParam('user_id'),
                'cursor'  => $cursor,
                'count'   => $count
            ));
            $client->setEncType();
            $response = $client->request('GET');
            if ($response->isSuccessful()) {
                $response = Zend_Json::decode($response->getBody());
                foreach ($response['ids'] as &$value) {
                    $friendsId[] = (string) $value;
                }
                if (count($response['ids']) != $count)
                    break;
                $cursor ++;
            }
            if (! $response->isSuccess()) {
                $message = 'Error requesting data';
                throw new Autowp_ExternalLoginService_Exception($message);
            }
        }
        return $friendsId;
    }

    public function setAccessToken(array $params)
    {
        $this->_accessToken = new Zend_Oauth_Token_Access();
        $this->_accessToken->setParams($params);
        return $this;
    }
}