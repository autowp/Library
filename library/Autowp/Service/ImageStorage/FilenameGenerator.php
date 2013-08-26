<?php

class Autowp_Service_ImageStorage_FilenameGenerator
{
    /**
     * @var string
     */
    protected $_dir;

    /**
     * @var string
     */
    protected $_prefferedName;

    /**
     * @var string
     */
    protected $_ext;

    /**
     * @var int
     */
    protected $_interation = 0;

    /**
     * @var Autowp_Filter_Filename_Safe
     */
    protected $_filter = null;

    /**
     * @param string $prefferedName
     * @param string $ext
     */
    public function __construct($dir, $prefferedName, $ext)
    {
        $this->_dir = rtrim($dir, DIRECTORY_SEPARATOR);
        $this->_prefferedName = (string)$prefferedName;
        $this->_ext = (string)$ext;

        $this->_filter = new Autowp_Filter_Filename_Safe();
    }

    /**
     * @param string $filename
     * @param string $ext
     * @return string
     */
    protected function _buildFilenamePattern()
    {
        if (strlen($this->_prefferedName) > 0) {

            $filenameSafe = $this->_filter->filter($this->_prefferedName);
            $filenameSafe = basename($filenameSafe);

            $result = str_replace('%', '%%', $filenameSafe) . '%s';
        } else {
            $result = '%s';
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getNext()
    {
        if (!$this->_ext) {
            throw new Autowp_Service_ImageStorage_Exception("Extenstion not set");
        }

        $filenamePattern = $this->_buildFilenamePattern();

        do {
            $this->_interation++;

            if ($filenamePattern == '%s') {
                $destBaseName = $this->_interation;
            } else {
                $suffix = $this->_interation ? '_' . $this->_interation : '';
                $destBaseName = sprintf($filenamePattern, $suffix);
            }
            $destFileName = $destBaseName . '.' . $this->_ext;

            $destFilePath = $this->_dir . DIRECTORY_SEPARATOR . $destFileName;
        } while (file_exists($destFilePath));

        return $destFileName;
    }
}