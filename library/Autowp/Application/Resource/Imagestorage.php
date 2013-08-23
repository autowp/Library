<?php

class Autowp_Application_Resource_Imagestorage
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Autowp_Service_ImageStorage
     */
    protected $_imageStorage = null;

    /**
     * @return Autowp_Service_ImageStorage
     */
    public function init()
    {
        return $this->getImageStorage();
    }

    /**
     * @return Autowp_Service_ImageStorage
     */
    public function getImageStorage()
    {
        if (null === $this->_imageStorage) {
            $options = $this->getOptions();
            foreach($options as $key => $option) {
                $options[strtolower($key)] = $option;
            }

            $bootstrap = $this->getBootstrap();
            if ($bootstrap instanceof Zend_Application_Bootstrap_ResourceBootstrapper
                && $bootstrap->hasPluginResource('Db')
            ) {
                $db = $bootstrap->bootstrap('Db')
                    ->getResource('Db');
                if (null !== $db) {
                    $options['dbAdapter'] = $db;
                }
            }

            $this->_imageStorage = new Autowp_Service_ImageStorage($options);
        }
        return $this->_imageStorage;
    }
}