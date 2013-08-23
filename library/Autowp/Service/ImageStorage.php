<?php

class Autowp_Service_ImageStorage
{
    /**
     * Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var Autowp_Service_ImageStorage_DbTable_Image
     */
    protected $_imageTable = null;

    /**
     * @var string
     */
    protected $_imageTableName = 'image';

    /**
     * @var array
     */
    protected $_dirs = array();

    /**
     * @var array
     */
    protected $_formats = array();

    /**
     * @var int
     */
    protected $_fileMode = 0600;

    /**
     * @var int
     */
    protected $_dirMode = 0700;

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
     * @return Autowp_Service_ImageStorage
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                $this->_raise("Unexpected option '$key'");
            }
        }

        return $this;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $dbAdapter
     * @return Autowp_Service_ImageStorage
     */
    public function setDbAdapter(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_db = $dbAdapter;

        return $this;
    }

    /**
     * @param string|int $mode
     * @return Autowp_Service_ImageStorage
     */
    public function setFileMode($mode)
    {
        $this->_fileMode = is_string($mode) ? octdec($mode) : (int)$mode;

        return $this;
    }

    /**
     * @param string|int $mode
     * @return Autowp_Service_ImageStorage
     */
    public function setDirMode($mode)
    {
        $this->_dirMode = is_string($mode) ? octdec($mode) : (int)$mode;

        return $this;
    }

    /**
     * @param array $dirs
     * @return Autowp_Service_ImageStorage
     */
    public function setDirs($dirs)
    {
        $this->_dirs = array();

        foreach ($dirs as $dirName => $dir) {
            $this->addDir($dirName, $dir);
        }

        return $this;
    }

    /**
     * @param string $dirName
     * @param Autowp_Service_ImageStorage_Dir|mixed $dir
     * @return Autowp_Service_ImageStorage
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function addDir($dirName, $dir)
    {
        if (isset($this->_dirs[$dirName])) {
            $this->_raise("Dir '$dirName' alredy registered");
        }
        if (!$dir instanceof Autowp_Service_ImageStorage_Dir) {
            $dir = new Autowp_Service_ImageStorage_Dir($dir);
        }
        $this->_dirs[$dirName] = $dir;

        return $this;
    }

    /**
     * @param array $formats
     * @return Autowp_Service_ImageStorage
     */
    public function setFormats($formats)
    {
        $this->_dirs = array();

        foreach ($formats as $formatName => $format) {
            $this->addFormat($formatName, $format);
        }

        return $this;
    }

    /**
     * @param string $formatName
     * @param Autowp_Service_ImageStorage_Format|mixed $format
     * @return Autowp_Service_ImageStorage
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function addFormat($formatName, $format)
    {
        if (isset($this->_formats[$formatName])) {
            $this->_raise("Format '$formatName' alredy registered");
        }
        if (!$format instanceof Autowp_Service_ImageStorage_Format) {
            $format = new Autowp_Service_ImageStorage_Dir($format);
        }
        $this->_formats[$formatName] = $format;

        return $this;
    }

    /**
     * @param string $dirName
     * @return Autowp_Service_ImageStorage_Dir
     */
    public function getDir($dirName)
    {
        return isset($this->_dirs[$dirName]) ? $this->_dirs[$dirName] : null;
    }

    /**
     * @param string $message
     * @throws Autowp_Service_ImageStorage_Exception
     */
    protected function _raise($message)
    {
        throw new Autowp_Service_ImageStorage_Exception($message);
    }

    /**
     * @return Autowp_Service_ImageStorage_DbTable_Image
     */
    protected function _getImageTable()
    {
        if (null === $this->_imageTable) {
            $this->_imageTable = new Autowp_Service_ImageStorage_DbTable_Image(array(
                Zend_Db_Table_Abstract::ADAPTER => $this->_db,
                Zend_Db_Table_Abstract::NAME    => $this->_imageTableName,
            ));
        }

        return $this->_imageTable;
    }

    /**
     * @param int $imageId
     * @return Autowp_Service_ImageStorage_Image
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function getImage($imageId)
    {
        $imageTable = $this->_getImageTable();

        $imageRow = $imageTable->fetchRow(array(
            'id = ?' => $imageId
        ));

        if (!$imageRow) {
            return null;
        }

        $dir = $this->getDir($imageRow->dir);
        if (!$dir) {
            $this->_raise("Dir '$dir' not defined");
        }

        $dirUrl = $dir->getUrl();

        $src = null;
        if ($dirUrl) {
            $src = $dirUrl . $imageRow->filepath;
        }

        return new Autowp_Service_ImageStorage_Image(array(
            'width'    => $imageRow->width,
            'height'   => $imageRow->height,
            'src'      => $src,
            'filesize' => $imageRow->filesize,
        ));
    }

    /**
     * @param int $imageId
     * @return Autowp_Service_ImageStorage_Image
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function removeImage($imageId)
    {
        $imageTable = $this->_getImageTable();

        $imageRow = $imageTable->fetchRow(array(
            'id = ?' => $imageId
        ));

        if (!$imageRow) {
            $this->_raise("Image '$imageId' not found");
        }

        $dir = $this->_getDir($imageRow->dir);
        if (!$dir) {
            $this->_raise("Dir '$dir' not defined");
        }

        $filepath = implode(DIRECTORY_SEPARATOR, array(
            rtrim($dir->getPath(), DIRECTORY_SEPARATOR),
            $imageRow->filepath
        ));

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        $imageRow->delete();

        return $this;
    }

    protected function _getnerateFilename()
    {

    }

    /**
     * @param string $filename
     * @param string $ext
     * @return string
     */
    protected function _buildFilenamePattern($filename, $ext)
    {
        $extPattern = str_replace('%', '%%', $ext);
        if (strlen($filename) > 0) {
            $filter = new Autowp_Filter_Filename_Safe();
            $filenameSafe = $filter->filter($filename);
            $filenameSafe = basename($filenameSafe);

            $result = str_replace('%', '%%', $filenameSafe) . '%s';
        } else {
            $result = '%s';
        }
        return $result . '.' . $extPattern;
    }

    /**
     * @param string $file
     * @param string $dirName
     * @param array $options
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function addImageFromFile($file, $dirName, array $options = array())
    {
        $defaults = array(
            'name' => null,
            'path' => null
        );
        $options = array_merge($defaults, $options);

        $dir = $this->getDir($dirName);
        if (!$dir) {
            $this->_raise("Dir '$dirName' not defined");
        }

        list($width, $height, $type) = getimagesize($file);
        $width = (int)$width;
        $height = (int)$height;

        if (!$width || !$height) {
            $this->_raise("Failed to get image size of '$file' ($width x $height)");
        }

        $dirPath = $dir->getPath();

        $ext = null;
        switch ($type) {
            case IMAGETYPE_GIF:
                $ext = 'gif';
                break;
            case IMAGETYPE_JPEG:
                $ext = 'jpg';
                break;
            case IMAGETYPE_PNG:
                $ext = 'png';
                break;
            default:
                $this->_raise("Unsupported image type `$type`");
        }

        $idx = 0;
        $filenamePattern = $this->_buildFilenamePattern($options['name'], $ext);
        do {
            $suffix = $idx ? '_' . $idx : '';
            $destFileName = sprintf($filenamePattern, $suffix);
            $destFilePath = $dirPath . DIRECTORY_SEPARATOR . $destFileName;

            $idx++;
        } while (file_exists($destFilePath));

        $destDir = dirname($destFilePath);
        if (!is_dir($destDir)) {
            if (!mkdir($destDir, $this->_dirMode, true)) {
                $this->_raise("Cannot create dir '$destDir'");
            }
        }
        if (!copy($file, $destFilePath)) {
            $this->_raise("Cannot copy file '$file' to '$destFilePath'");
        }
        if (!chmod($destFilePath, $this->_fileMode)) {
            $this->_raise("Cannot chmod file '$destFilePath'");
        }

        $imageTable = $this->_getImageTable();

        $imageRow = $imageTable->fetchNew();
        $imageRow->setFromArray(array(
            'width'    => $width,
            'height'   => $height,
            'dir'      => $dirName,
            'filesize' => filesize($destFilePath),
            'filepath' => $destFileName
        ));

        $imageRow->save();

        return $imageRow->id;
    }
}