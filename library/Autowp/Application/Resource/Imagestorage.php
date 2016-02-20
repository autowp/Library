<?php

use Autowp\Service\ImageStorage;

class Autowp_Application_Resource_Imagestorage
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var ImageStorage
     */
    private $_imageStorage = null;

    /**
     * @return ImageStorage
     */
    public function init()
    {
        return $this->getImageStorage();
    }

    /**
     * @return ImageStorage
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

            $this->_imageStorage = new ImageStorage($options);
        }
        return $this->_imageStorage;
    }
}