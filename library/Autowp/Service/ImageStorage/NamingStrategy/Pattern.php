<?php

class Autowp_Service_ImageStorage_NamingStrategy_Pattern
    extends Autowp_Service_ImageStorage_NamingStrategy_Abstract
{
    protected $_notAllowedParts = array('.', '..');

    /**
     * @param string $pattern
     * @return string
     */
    protected function _normalizePattern($pattern)
    {
        foreach($this->_notAllowedParts as $part) {
            $pattern = str_replace($part, DIRECTORY_SEPARATOR, $pattern);
        }

        $pattern = preg_replace('|[' . preg_quote(DIRECTORY_SEPARATOR) . ']+|isu', DIRECTORY_SEPARATOR, $pattern);

        $filter = new Autowp_Filter_Filename_Safe();

        $result = array();
        $pattrenComponents = explode(DIRECTORY_SEPARATOR, $pattern);
        foreach ($pattrenComponents as $component) {
            if ($component) {
                $filtered = $filter->filter($component);
                $result[] = $filtered;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $result);
    }

    /**
     * @param string $dir
     * @param array $options
     * @see Autowp_Service_ImageStorage_NamingStrategy_Abstract::generate()
     */
    public function generate(array $options = array())
    {
        $defaults = array(
            'pattern'   => null,
            'extension' => null
        );
        $options = array_merge($defaults, $options);

        $ext = (string)$options['extension'];
        $pattren = $this->_normalizePattern($options['pattern']);

        $dir = $this->getDir();
        if (!$dir) {
            throw new Autowp_Service_ImageStorage_Exception("`dir` not initialized");
        }

        $idx = 0;
        do {
            $suffix = $idx ? '_' . $idx : '';
            $filename = $pattren . $suffix . ($ext ? '.' . $ext : '');
            $filePath = $dir . DIRECTORY_SEPARATOR . $filename;
            $idx++;
        } while (file_exists($filePath));

        return $filename;
    }
}