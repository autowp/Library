<?php

class Autowp_Application_Resource_Externalloginservice
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Autowp_ExternalLoginService_Factory
     */
    protected $_factory = null;

    /**
     * @return Autowp_ExternalLoginService_Factory
     */
    public function init()
    {
        return $this->getFactory();
    }

    /**
     * @return Autowp_ExternalLoginService_Factory
     */
    public function getFactory()
    {
        if (null === $this->_factory) {
            $this->_factory = new Autowp_ExternalLoginService_Factory($this->getOptions());
        }
        return $this->_factory;
    }
}