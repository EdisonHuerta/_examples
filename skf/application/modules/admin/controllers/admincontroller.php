<?php

/**
 * File containing the admin controller
 *
 * @package SKF
 * @copyright Copyright (C) 2015 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class adminController extends baseController implements IController {

    public $db;

    public function __construct() {
        parent::__construct();
        // a new config
        $config = new config;
        $this->view->version = $config->config_values['application']['version'];
        $db_type = $config->config_values['database']['db_type'];
        $db_host = $config->config_values['database']['db_host'];
        $db_name = $config->config_values['database']['db_name'];
        $db_user = $config->config_values['database']['db_user'];
        $db_pass = $config->config_values['database']['db_pass'];
        $db_port = $config->config_values['database']['db_port'];
        // uncomment the line below to connect to database
        $this->db = new db($db_type, $db_host, $db_name, $db_user, $db_pass, $db_port);
    }

    public function index() {
        /*         * * a new view instance ** */
        $tpl = new view;

        /*         * * turn caching on for this page ** */
        $tpl->setCaching(true);

        /*         * * set the template dir ** */
        $tpl->setTemplateDir(APP_PATH . '/modules/admin/views');

        /*         * * layout template ** */
        $this->view->layout_file = 'admin_index_layout.phtml';

        /*         * * a view variable ** */
        $this->view->title = 'Admin';

        /*         * * the cache id is based on the file name ** */
        $cache_id = md5('admin/index.phtml');

        /*         * * fetch the template ** */
        $this->content = $tpl->fetch('index.phtml', $cache_id);
    }

    public function buildDao() {
        /*         * * layout template ** */
        $this->view->layout_file = 'admin_layout.phtml';

        /*         * * a new view instance ** */
        $tpl = new view;

        /*         * * turn caching on for this page ** */
        // $view->setCaching(true);

        /*         * * set the template dir ** */
        $tpl->setTemplateDir(APP_PATH . '/modules/admin/views');

        /*         * * a view variable ** */
        $this->view->title = 'Admin';

        /*         * * Alert box ** */
        $tpl->alert_status = 'info';
        $tpl->alert_message = 'Rebuild Data Access Objects.';
        // a new config
        $config = new config;
        $db_type = $config->config_values['database']['db_type'];
        $db_host = $config->config_values['database']['db_host'];
        $db_name = $config->config_values['database']['db_name'];
        $db_user = $config->config_values['database']['db_user'];
        $db_pass = $config->config_values['database']['db_pass'];
        $db_port = $config->config_values['database']['db_port'];

        $this->view->version = $config->config_values['application']['version'];

        $tpl->table_name = '';
        $tpl->table_name_error = '';

        $tpl->alert_status = 'info';
        $tpl->alert_message = 'Generate Data Access Objects';

        try {
            if (isset($_POST['table_name']) && $_POST['table_name'] == '') {
                $db = new \skf\db($db_type, $db_host, $db_name, $db_user, $db_pass, $db_port);
                // form posted without table name, renew ALL
                // dao objects
                $dao = new \skf\dao($db);
                $dao->generateDAO();
                $tpl->alert_status = 'success';
                $tpl->alert_message = 'All DAOs successfully updated';
            }

            if (isset($_POST['table_name']) && $_POST['table_name'] != '') {
                // form post with table name, rebuild this
                // table only
                $table_name = $_POST['table_name'];
                $dao = new \skf\dao($this->db, null);
                $dao->generateDAO();
                $tpl->alert_status = 'success';
                $tpl->alert_message = "DAO for Table '$table_name' successfully updated";
            }
        } catch (\Exception $e) {
            $tpl->alert_status = 'danger';
            $tpl->alert_message = 'Could not rebuild DAO: ' . $e->getMessage();
        }

        /*         * * the cache id is based on the file name ** */
        $cache_id = md5('admin/builddao.php');

        /*         * * fetch the template ** */
        $this->content = $tpl->fetch('builddao.phtml', $cache_id);
    }

}
