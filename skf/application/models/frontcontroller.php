<?php

/**
 *
 * @Front Controller class
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @package Core
 * @Author Kevin Waterson
 *
 */

namespace skf;

class FrontController {

    protected $_controller, $_action, $_params, $_body, $_url;

    public function __construct() {
        // set the controller
        $this->_uri = new uri;

        /*
          if( $this->_uri->fragment(0) && $this->_uri->fragment(0)=='assets' )
          {
          exit;
          }
         */

        if ($this->_uri->fragment(0)) {
            $this->_controller = $this->_uri->fragment(0) . 'Controller';
        } else {
            // get the default controller
            $config = new config;
            $default = $config->config_values['application']['default_controller'] . 'Controller';
            $this->_controller = $default;
        }

        // the action
        if ($this->_uri->fragment(1)) {
            // if fragment(1) is admin, then we load the admin controller
            if ($this->_uri->fragment(1) == 'admin') {
                $admin_controller = $this->_uri->fragment(0) . '_adminController';
                $this->_controller = $admin_controller;
                $this->_action = $this->_uri->fragment(2);
            } else {
                $this->_action = $this->_uri->fragment(1);
            }
        } else {
            $this->_action = 'index';
        }
    }

    /**
     *
     * The route
     *
     * Checks if controller and action exists
     *
     * @access	public
     *
     */
    public function route() {
        // check if the controller exists
        $con = $this->getController();

        $rc = new \ReflectionClass('skf\\' . $con);
        // if the controller exists and implements IController
        if ($rc->implementsInterface('skf\IController')) {
            $controller = $rc->newInstance();
            // check if method exists 
            if ($rc->hasMethod($this->getAction())) {
                // if all is well, load the action
                $method = $rc->getMethod($this->getAction());
            } else {
                // load the default action method
                $config = new config;
                $default = $config->config_values['application']['default_action'];
                $method = $rc->getMethod($default);
            }
            $method->invoke($controller);
        } else {
            throw new \Exception("Interface iController must be implemented");
        }
    }

    /*
      public function getParams()
      {
      return $this->_params;
      }
     */

    /**
     *
     * Gets the controller, sets to default if not available
     *
     * @access	public
     * @return	string	The name of the controller
     *
     */
    public function getController() {
        if (class_exists('skf\\' . $this->_controller)) {
            return $this->_controller;
        } else {
            $config = new config;
            $default = $config->config_values['application']['error_controller'] . 'Controller';
            return $default;
        }
    }

    /**
     *
     * Get the action
     *
     * @access	public
     * @return	string	the Name of the action
     *
     */
    public function getAction() {
        return $this->_action;
    }

    public function getBody() {
        return $this->_body;
    }

    public function setBody($body) {
        $this->_body = $body;
    }

    public function __toString() {
        return $this->getBody();
    }

}

// end of class
