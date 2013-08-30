<?php

class Autowp_Service_ImageStorage_NamingStrategy_Serial
    extends Autowp_Service_ImageStorage_NamingStrategy_Abstract
{
    const ITEM_PER_DIR = 1000;

    /**
     * @var int
     */
    protected $_deep = 0;

    /**
     * @param int $deep
     * @throws Autowp_Service_ImageStorage_Exception
     * @return Autowp_Service_ImageStorage_NamingStrategy_Serial
     */
    public function setDeep($deep)
    {
        $deep = (int)$deep;
        if ($deep < 0) {
            throw new Autowp_Service_ImageStorage_Exception("Deep cannot be < 0");
        }
        $this->_deep = $deep;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeep()
    {
        return $this->_deep;
    }

    /**
     * Return the complete directory path of a filename (including hashedDirectoryStructure)
     *
     * @param  string $id Cache id
     * @return string Complete directory path
     */
    protected function _path($index, $deep)
    {
        $chars = strlen(self::ITEM_PER_DIR - 1); // use log10, fkn n00b
        $path = '';
        if ($deep > 0) {
            $cur = floor($index / self::ITEM_PER_DIR);
            for ($i=0 ; $i < $deep ; $i++) {
                $div = floor($cur / self::ITEM_PER_DIR);
                $mod = $cur - $div * self::ITEM_PER_DIR;
                $path = sprintf('%0'.$chars.'d', $mod) . DIRECTORY_SEPARATOR . $path;
                $cur = $div;
                //$root = $root . substr($hash, 0, $i + 1) . DIRECTORY_SEPARATOR;
            }
        }
        return $path;
    }

    /**
     * @param string $dir
     * @param array $options
     * @see Autowp_Service_ImageStorage_NamingStrategy_Abstract::generate()
     */
    public function generate(array $options = array())
    {
        $defaults = array(
            'extenstion'    => null,
            'count'         => null,
            'prefferedName' => null,
        );
        $options = array_merge($defaults, $options);

        $count = (int)$options['count'];
        $ext = (string)$options['extension'];

        $index = $count + 1;

        $dir = $this->getDir();
        if (!$dir) {
            throw new Autowp_Service_ImageStorage_Exception("`dir` not initialized");
        }

        $dirPath = $this->_path($index, $this->_deep);

        $filter = new Autowp_Filter_Filename_Safe();

        if ($options['prefferedName']) {
            $fileBasename = $filter->filter($options['prefferedName']);
        } else {
            $fileBasename = $index;
        }

        $idx = 0;
        do {
            $suffix = $idx ? '_' . $idx : '';
            $filename = $fileBasename . $suffix . ($ext ? '.' . $ext : '');
            $filePath = $dir . DIRECTORY_SEPARATOR . $dirPath . $filename;
            $idx++;
        } while (file_exists($filePath));

        return $dirPath . $filename;
    }
}