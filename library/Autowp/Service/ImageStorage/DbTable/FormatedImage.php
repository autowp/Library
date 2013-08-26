<?php

class Autowp_Service_ImageStorage_DbTable_FormatedImage
    extends Zend_Db_Table_Abstract
{
    protected $_primary = array('image_id', 'format');
}