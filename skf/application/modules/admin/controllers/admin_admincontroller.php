<?php

namespace skf;

class admin_adminController extends baseController implements IController {

    /**
     *
     * Constructor, duh
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * The index function
     *
     */
    public function index() {
        /*         * * a new view instance ** */
        $tpl = new view;

        /*         * * turn caching on for this page ** */
        // $view->setCaching(true);

        /*         * * set the template dir ** */
        $tpl->setTemplateDir(APP_PATH . '/modules/admin/views');

        /*         * * a view variable ** */
        $this->view->title = 'Admin Admin';

        // a new config
        $config = new config;
        $this->view->version = $config->config_values['application']['version'];

        /*         * * the cache id is based on the file name ** */
        $cache_id = md5('admin/admin_index.php');

        /*         * * fetch the template ** */
        $this->content = $tpl->fetch('admin_index.phtml', $cache_id);
    }

}

// end of class
