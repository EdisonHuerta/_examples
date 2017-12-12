<?php

namespace skf;

class config {
    /*
     * @var string $config_file
     */

    private $config_file = '/config/config.ini.php';

    /*
     * @var array $config_values; 
     */
    public $config_values = array();

    /**
     *
     * Constructor, duh!
     *
     */
    public function __construct() {
        $this->config_values = parse_ini_file(APP_PATH . $this->config_file, true);

        // check if there is a module config file
        $uri = new uri;
        $module_conf = APP_PATH . '/modules/' . $uri->fragment(0) . '/config/config.ini.php';
        if (file_exists($module_conf)) {
            $module_array = parse_ini_file($module_conf, true);
            $this->config_values = array_merge($this->config_values, $module_array);
        }
    }

    /**
     * @get a config option by key
     *
     * @access public
     *
     * @param string $key:The configuration setting key
     *
     * @return string
     *
     */
    public function getValue($key) {
        return $this->config_values[$key];
    }

    /**
     *
     * @__clone
     *
     * @access private
     *
     */
    private function __clone() {
        
    }

}
