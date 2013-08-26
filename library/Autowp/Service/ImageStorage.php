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
     * @var Autowp_Service_ImageStorage_DbTable_FormatedImage
     */
    protected $_formatedImageTable = null;

    /**
     * @var string
     */
    protected $_formatedImageTableName = 'formated_image';

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
     * @var string
     */
    protected $_formatedImageDirName = null;

    /**
     * @var int
     */
    protected $_resizeFilter = Imagick::FILTER_CUBIC;

    /**
     * @var float
     */
    protected $_resizeBlur = 1;

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
     * @param string $tableName
     * @return Autowp_Service_ImageStorage
     */
    public function setImageTableName($tableName)
    {
        $this->_imageTableName = $tableName;

        return $this;
    }

    /**
     * @param string $tableName
     * @return Autowp_Service_ImageStorage
     */
    public function setFormatedImageTableName($tableName)
    {
        $this->_formatedImageTableName = $tableName;

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
     * @param string $dirName
     * @return Autowp_Service_ImageStorage_Dir
     */
    public function getDir($dirName)
    {
        return isset($this->_dirs[$dirName]) ? $this->_dirs[$dirName] : null;
    }

    /**
     * @param array $formats
     * @return Autowp_Service_ImageStorage
     */
    public function setFormats($formats)
    {
        $this->_formats = array();

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
            $format = new Autowp_Service_ImageStorage_Format($format);
        }
        $this->_formats[$formatName] = $format;

        return $this;
    }

    /**
     * @param string $dirName
     * @return Autowp_Service_ImageStorage_Format
     */
    public function getFormat($formatName)
    {
        return isset($this->_formats[$formatName]) ? $this->_formats[$formatName] : null;
    }

    /**
     * @param string $dirName
     * @return Autowp_Service_ImageStorage
     */
    public function setFormatedImageDirName($dirName)
    {
        $this->_formatedImageDirName = $dirName;

        return $this;
    }

    /**
     * @param int $filter
     * @return Project_Image_Sampler
     */
    public function setResizeFilter($filter)
    {
        $this->_resizeFilter = $filter;

        return $this;
    }

    /**
     * @param float $blur
     * @return Project_Image_Sampler
     */
    public function setResizeBlur($blur)
    {
        $this->_resizeBlur = $blur;

        return $this;
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
     * @return Autowp_Service_ImageStorage_DbTable_FormatedImage
     */
    protected function _getFormatedImageTable()
    {
        if (null === $this->_formatedImageTable) {
            $this->_formatedImageTable = new Autowp_Service_ImageStorage_DbTable_FormatedImage(array(
                Zend_Db_Table_Abstract::ADAPTER => $this->_db,
                Zend_Db_Table_Abstract::NAME    => $this->_formatedImageTableName,
            ));
        }

        return $this->_formatedImageTable;
    }

    /**
     * @param Zend_Db_Table_Row $imageRow
     * @return Autowp_Service_ImageStorage_Image
     */
    protected function _buildImageResult(Zend_Db_Table_Row $imageRow)
    {
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
    public function getImage($imageId)
    {
        $imageTable = $this->_getImageTable();

        $imageRow = $imageTable->fetchRow(array(
            'id = ?' => $imageId
        ));

        if (!$imageRow) {
            return null;
        }

        return $this->_buildImageResult($imageRow);
    }

    /**
     * @param int $imageId
     * @param string $format
     * @return Autowp_Service_ImageStorage_Image
     */
    public function getFormatedImage($imageId, $formatName)
    {
        if (!$imageId) {
            $this->_raise("ImageId not provided");
        }

        $imageTable = $this->_getImageTable();

        $destImageRow = $imageTable->fetchRow(
            $imageTable->select(true)
                ->join(
                    array('f' => $this->_formatedImageTableName),
                    $this->_imageTableName . '.id = f.formated_image_id',
                    null
                )
                ->where('f.image_id = ?', $imageId)
                ->where('f.format = ?', (string)$formatName)
        );

        if (!$destImageRow) {

            // find source image
            $imageRow = $this->_getImageTable()->fetchRow(array(
                'id = ?' => $imageId
            ));
            if (!$imageRow) {
                $this->_raise("Image `$imageId` not found");
            }

            $dir = $this->getDir($imageRow->dir);
            if (!$dir) {
                $this->_raise("Dir '$dir' not defined");
            }

            $srcFilePath = $dir . DIRECTORY_SEPARATOR . $imageRow . $imageRow->filepath;

            $imagick = new Imagick();
            $imagick->readImage($srcFilePath);

            // format
            $format = $this->getFormat($formatName);
            if (!$format) {
                $this->_raise("Format `$formatName` not found");
            }

            $this->applyFormat($imagick, $format);

            // store result
            $formatedImageId = $this->addImageFromImagick(
                $imagick, $this->_formatedImageDirName,
                array(
                    'name' => $imageId,
                    'path' => implode(DIRECTORY_SEPARATOR, array(
                        $formatName,
                        $imageId % 1000
                    ))
                )
            );

            $imagick->clear();

            $formatedImageTable = $this->_getFormatedImageTable();
            $formatedImageRow = $formatedImageTable->createRow(array(
                'format'            => (string)$formatName,
                'image_id'          => $imageId,
                'formated_image_id' => $formatedImageId
            ));

            // result
            $destImageRow = $this->_getImageTable()->fetchRow(array(
                'id = ?' => $formatedImageId
            ));
        }

        return $this->_buildImageResult($destImageRow);
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

    /**
     * @param Autowp_Service_ImageStorage_Dir $dir
     * @param string $ext
     * @param array $options
     * @return string
     * @throws Autowp_Service_ImageStorage_Exception
     */
    protected function _createImagePath(Autowp_Service_ImageStorage_Dir $dir, $ext, array $options = array())
    {
        $defaults = array(
            'name' => null,
            'path' => null
        );
        $options = array_merge($defaults, $options);

        $dirPath = $dir->getPath();

        $pathComponents = explode(DIRECTORY_SEPARATOR, $options['path']);
        $filter = new Autowp_Filter_Filename_Safe();
        $filteredPath = array();
        foreach ($pathComponents as $pathComponent) {
            if (strlen($pathComponent)) {
                $pathComponent = $filter->filter($pathComponent);
                $filteredPath[] = $pathComponent;
            }
        }

        $filteredPath = implode(DIRECTORY_SEPARATOR, $filteredPath) . DIRECTORY_SEPARATOR;

        $filenameGenerator = new Autowp_Service_ImageStorage_FilenameGenerator(
            $dirPath . $filteredPath, $options['name'], $ext);

        $destFileName = $filenameGenerator->getNext();
        $destFilePath = $dirPath . $filteredPath . DIRECTORY_SEPARATOR . $destFileName;

        $destDir = dirname($destFilePath);
        if (!is_dir($destDir)) {
            if (!mkdir($destDir, $this->_dirMode, true)) {
                $this->_raise("Cannot create dir '$destDir'");
            }
        }

        return $destFileName;
    }

    /**
     * @param string $dirName
     * @param string $fileDirPath
     * @param int $width
     * @param int $height
     */
    protected function _storeImageToDb($dirName, $fileDirPath, $width, $height)
    {
        $dir = $this->getDir($dirName);
        if (!$dir) {
            $this->_raise("Dir '$dirName' not defined");
        }

        $filePath = $dir->getPath() . DIRECTORY_SEPARATOR . $fileDirPath;

        $imageRow = $this->_getImageTable()->createRow(array(
            'width'    => $width,
            'height'   => $height,
            'dir'      => $dirName,
            'filesize' => filesize($filePath),
            'filepath' => $fileDirPath,
            'date_add' => new Zend_Db_Expr('now()')
        ));
        $imageRow->save();

        return $imageRow->id;
    }

    /**
     * @param string $path
     * @throws Autowp_Service_ImageStorage_Exception
     */
    protected function _chmodFile($path)
    {
        if (!chmod($path, $this->_fileMode)) {
            $this->_raise("Cannot chmod file '$path'");
        }
    }

    /**
     * @param string $blob
     * @param string $dirName
     * @param array $options
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function addImageFromBlob($blob, $dirName, array $options = array())
    {
        $imagick = new Imagick();
        $imagick->readImageBlob($blob);
        $imageId = $this->addImageFromImagick($imagick, $dirName, $options);
        $imagick->clear();

        return $imageId;
    }

    /**
     * @param Imagick $imagick
     * @param string $dirName
     * @param array $options
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function addImageFromImagick(Imagick $imagick, $dirName, array $options = array())
    {
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        if (!$width || !$height) {
            $this->_raise("Failed to get image size ($width x $height)");
        }

        $dir = $this->getDir($dirName);
        if (!$dir) {
            $this->_raise("Dir '$dirName' not defined");
        }
        $destFileName = $this->_createImagePath($dir, 'jpg', $options);

        $dirPath = $dir->getPath();
        $destFilePath = $dirPath . DIRECTORY_SEPARATOR . $destFileName;

        if (!$imagick->writeImage($destFilePath)) {
            $this->_raise("Cannot save imagick to '$destFilePath'");
        }
        $this->_chmodFile($destFilePath);

        return $this->_storeImageToDb($dirName, $destFileName, $width, $height);
    }

    /**
     * @param string $file
     * @param string $dirName
     * @param array $options
     * @throws Autowp_Service_ImageStorage_Exception
     */
    public function addImageFromFile($file, $dirName, array $options = array())
    {
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

        $destFileName = $this->_createImagePath($dir, $ext, $options);

        $dirPath = $dir->getPath();
        $destFilePath = $dirPath . DIRECTORY_SEPARATOR . $destFileName;

        if (!copy($file, $destFilePath)) {
            $this->_raise("Cannot copy file '$file' to '$destFilePath'");
        }
        $this->_chmodFile($destFilePath);

        return $this->_storeImageToDb($dirName, $destFileName, $width, $height);
    }

    /**
     * @param Imagick $imagick
     * @param Autowp_Service_ImageStorage_Format $format
     * @throws Autowp_Service_ImageStorage_Exception
     */
    protected function _applyFormat(Imagick $imagick,
        Autowp_Service_ImageStorage_Format $format)
    {
        $bg = $format->getBackground();
        if (!$bg) {
            $bg = 'transparent';
        }
        $imagick->setBackgroundColor($bg);

        $srcWidth = $imagick->getImageWidth();
        $srcHeight = $imagick->getImageHeight();
        $srcRatio = $srcWidth / $srcHeight;

        $widthLess = $format->getWidth() && ($srcWidth <= $format->getWidth());
        $heightLess = $format->getHeight() && ($srcHeight <= $format->getHeight());

        if ( !( $widthLess && $heightLess && $options['is_not_increase'] ) ) {
            if ($format->getWidth() && $format->getHeight()) {
                $ratio = $format->getWidth() / $format->getHeight();

                switch ($format->getFitType()) {
                    case self::FIT_TYPE_INNER:

                        // высчитываем размеры обрезания
                        if ($ratio < $srcRatio) {
                            // широкая картинка
                            $cropWidth = round($srcHeight * $ratio);
                            $cropHeight = $srcHeight;
                            $cropLeft = floor(($srcWidth - $cropWidth) / 2);
                            $cropTop = 0;
                        } else {
                            // высокая картинка
                            $cropWidth = $srcWidth;
                            $cropHeight = round($srcWidth / $ratio);
                            $cropLeft = 0;
                            $cropTop = floor(($srcHeight - $cropHeight) / 2);
                        }

                        $imagick->cropImage($cropWidth, $cropHeight, $cropLeft, $cropTop);
                        $imagick->resizeImage(
                            $format->getWidth(), $format->getHeight(),
                            $this->_resizeFilter, $this->_resizeBlur, false
                        );

                        break;

                    case self::FIT_TYPE_OUTER:

                        // высчитываем размеры обрезания
                        if ($ratio < $srcRatio) {
                            $scaleWidth = $format->getWidth();
                            $scaleHeight = round($format->getWidth() / $srcRatio);// добавляем поля сверху и снизу
                        } else {
                            // добавляем поля по бокам
                            $scaleWidth = round($format->getHeight() * $srcRatio);
                            $scaleHeight = $format->getHeight();
                        }

                        $imagick->resizeImage(
                            $scaleWidth, $scaleHeight,
                            $this->_resizeFilter, $this->_resizeBlur, false
                        );

                        $imagick->borderImage(
                            $options['background'],
                            $format->getWidth() - $scaleWidth,
                            $format->getHeight() - $scaleHeight
                        );

                        break;

                    case self::FIT_TYPE_MAXIMUM:

                        // высчитываем размеры обрезания
                        if ($ratio < $srcRatio) {
                            $scaleWidth = $format->getWidth();
                            $scaleHeight = round($format->getWidth() / $srcRatio);
                        } else {
                            // добавляем поля по бокам
                            $scaleWidth = round($format->getHeight() * $srcRatio);
                            $scaleHeight = $format->getHeight();
                        }

                        $imagick->resizeImage(
                            $scaleWidth, $scaleHeight,
                            $this->_resizeFilter, $this->_resizeBlur, false
                        );

                        break;

                    default:
                        $this->_raise("Неизвестный FIT_TYPE `{$format->getFitType()}`");
                }
            } else {

                if ($format->getWidth()) {
                    $scaleWidth = $format->getWidth();
                    $scaleHeight = round($format->getWidth() / $srcRatio);
                } else {
                    // добавляем поля по бокам
                    $scaleWidth = round($format->getHeight() * $srcRatio);
                    $scaleHeight = $format->getHeight();
                }

                $imagick->resizeImage(
                    $scaleWidth, $scaleHeight,
                    $this->_resizeFilter, $this->_resizeBlur, false
                );
            }

        }
    }

    /**
     * @param array $options
     * @return Autowp_Service_ImageStorage
     */
    public function flush(array $options)
    {
        $defaults = array(
            'format' => null,
            'image'  => null,
        );

        $options = array_merge($defaults, $options);

        $select = $this->_getFormatedImageTable()->select(true);

        if ($options['format']) {
            $select->where($this->_formatedImageTableName . '.format = ?', (string)$options['format']);
        }

        if ($options['image']) {
            $select->where($this->_formatedImageTableName . '.image_id = ?', (int)$options['image']);
        }

        return $this;
    }
}