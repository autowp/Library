<?php

class Autowp_ExternalLoginService_Factory
{
    /**
     * @var array
     */
    protected $_options;

    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * @param string $service
     * @return Autowp_ExternalLoginService_Abstract
     * @throws Exception
     */
    public function getService($service)
    {
        $service = trim($service);
        if (!isset($this->_options[$service])) {
            throw new Exception("Service '$service' not found");
        }

        $filter = new Zend_Filter_Word_DashToCamelCase();

        $className = 'Autowp_ExternalLoginService_' . ucfirst($filter->filter($service));

        $serviceObj = new $className($this->_options[$service]);

        if (!$serviceObj instanceof Autowp_ExternalLoginService_Abstract) {
            throw new Autowp_ExternalLoginService_Exception(
                "'$className' is not Autowp_ExternalLoginService_Abstract"
            );
        }

        return $serviceObj;
    }
}