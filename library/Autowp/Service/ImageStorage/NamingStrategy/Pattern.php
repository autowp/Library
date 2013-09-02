<?php

require_once 'Autowp/Service/ImageStorage/NamingStrategy/Abstract.php';

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
        $pattern = preg_replace('|[' . preg_quote(DIRECTORY_SEPARATOR) . ']+|isu', DIRECTORY_SEPARATOR, $pattern);

        $filter = new Autowp_Filter_Filename_Safe();

        $result = array();
        $patternComponents = explode(DIRECTORY_SEPARATOR, $pattern);
        foreach ($patternComponents as $component) {
            if (!in_array($component, $this->_notAllowedParts)) {
                if ($component) {
                    $filtered = $filter->filter($component);
                    $result[] = $filtered;
                }
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
        $pattern = $this->_normalizePattern($options['pattern']);

        $dir = $this->getDir();
        if (!$dir) {
            throw new Autowp_Service_ImageStorage_Exception("`dir` not initialized");
        }

        $idx = 0;
        do {
            $nameComponents = array();
            if ($pattern) {
                $nameComponents[] = $pattern;
            }
            if ($idx or (!$pattern)) {
                $nameComponents[] = $idx;
            }
            $filename = implode('_', $nameComponents) . ($ext ? '.' . $ext : '');
            $filePath = $dir . DIRECTORY_SEPARATOR . $filename;
            $idx++;
        } while (file_exists($filePath));

        return $filename;
    }
}