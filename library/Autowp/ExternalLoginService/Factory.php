<?php

class Autowp_ExternalLoginService_Factory
{
    /**
     * @var array
     */
    private $_options;

    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * @param string $service
     * @return Autowp_ExternalLoginService_Abstract
     * @throws Exception
     */
    public function getService($service, $optionsKey, array $options)
    {
        $service = trim($service);
        if (!isset($this->_options[$optionsKey])) {
            throw new Exception("Service '$optionsKey' options not found");
        }

        $filter = new Zend_Filter_Word_DashToCamelCase();

        $className = 'Autowp_ExternalLoginService_' . ucfirst($filter->filter($service));

        $serviceOptions = array_replace($this->_options[$optionsKey], $options);
        $serviceObj = new $className($serviceOptions);

        if (!$serviceObj instanceof Autowp_ExternalLoginService_Abstract) {
            throw new Autowp_ExternalLoginService_Exception(
                "'$className' is not Autowp_ExternalLoginService_Abstract"
            );
        }

        return $serviceObj;
    }

    public function getCallbackUrl()
    {
        if (!isset($this->_options['callback'])) {
            throw new Exception('`callback` not set');
        }

        return $this->_options['callback'];
    }
}