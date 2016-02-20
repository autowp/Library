<?php

namespace Autowp\Service\ImageStorage\DbTable;

use Zend_Db_Table_Abstract;

class FormatedImage
    extends Zend_Db_Table_Abstract
{
    protected $_primary = array('image_id', 'format');
}