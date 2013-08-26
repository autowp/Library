<?php

class Autowp_Service_ImageStorage_NamingStrategy_Serial
    extends Autowp_Service_ImageStorage_NamingStrategy_Abstract
{
    /**
     * Return the complete directory path of a filename (including hashedDirectoryStructure)
     *
     * @param  string $id Cache id
     * @return string Complete directory path
     */
    protected function _path($id, $dir, $deep)
    {
        $partsArray = array();
        $root = $dir;
        if ($deep > 0) {
            $hash = hash('adler32', $id);
            for ($i=0 ; $i < $deep ; $i++) {
                $root = $root . substr($hash, 0, $i + 1) . DIRECTORY_SEPARATOR;
            }
        }
        return $root;
    }

    public function generate($dir, array $options = array())
    {
        $defaults = array(
            'count'         => null,
            'deep'          => null,
            'prefferedName' => null
        );
        $options = array_merge($defaults, $options);

        $count = (int)$options['count'];
        $deep = (int)$options['deep'];
        if ($deep < 0) {
            throw new Autowp_Service_ImageStorage_Exception("Deep cannot be < 0");
        }

        $dirPath = $this->_path($id, $dir, $deep);

        $filter = new Autowp_Filter_Filename_Safe();

        if ($options['prefferedName']) {
            $fileBasename = $filter->filter($options['prefferedName']);
        } else {
            $fileBasename = $count + 1;
        }

        do {
            $filename = $fileBasename . $suffix . $ext;
            $filePath = $dirPath . $filename;
        } while (file_exists($filename));

        return ;
    }
}