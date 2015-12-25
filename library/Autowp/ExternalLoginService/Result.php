<?php

class Autowp_ExternalLoginService_Result
{
    /**
     * @var string
     */
    protected $_externalId = null;

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string
     */
    protected $_profileUrl = null;

    /**
     * @var string
     */
    protected $_photoUrl = null;

    /**
     * @var Zend_Date
     */
    protected $_birthday = null;
    
    /**
     * @var string
     */
    protected $_email = null;
    
    /**
     * @var string
     */
    protected $_residence = null;
    
    
    /**
     * @var int
     */
    protected $_gender = null;
    
    /**
     * @param array $options
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @param array $options
     * @return Autowp_ExternalLoginService_Result
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                $message = "Unexpected option '$key'";
                throw new Autowp_ExternalLoginService_Exception($message);
            }
        }

        return $this;
    }

    /**
     * @param string $externalId
     * @return Autowp_ExternalLoginService_Result
     */
    public function setExternalId($externalId)
    {
        $this->_externalId = (string)$externalId;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->_externalId;
    }

    /**
     * @param string $name
     * @return Autowp_ExternalLoginService_Result
     */
    public function setName($name)
    {
        $this->_name = (string)$name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $profileUrl
     * @return Autowp_ExternalLoginService_Result
     */
    public function setProfileUrl($profileUrl)
    {
        $profileUrl = (string)$profileUrl;

        if ($profileUrl) {
            if (Zend_Uri::check($profileUrl)) {
                $this->_profileUrl = $profileUrl;
            } else {
                $message = "Invalid profile url `$profileUrl`";
                throw new Autowp_ExternalLoginService_Exception($message);
            }
        } else {
            $this->_profileUrl = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getProfileUrl()
    {
        return $this->_profileUrl;
    }

    /**
     * @param string $photoUrl
     * @return Autowp_ExternalLoginService_Result
     */
    public function setPhotoUrl($photoUrl)
    {
        $photoUrl = (string)$photoUrl;

        if ($photoUrl) {
            if (Zend_Uri::check($photoUrl)) {
                $this->_photoUrl = $photoUrl;
            } else {
                $message = "Invalid profile url `$photoUrl`";
                throw new Autowp_ExternalLoginService_Exception($message);
            }
        } else {
            $this->_photoUrl = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->_photoUrl;
    }
}