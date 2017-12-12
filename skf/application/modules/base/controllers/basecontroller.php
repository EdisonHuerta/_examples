<?php

namespace skf;

class baseController {

    protected $breadcrumbs, $view, $content = null;

    public function __construct() {
        $this->view = new \skf\view;

        /** * create the bread crumbs ** */
        $bc = new \skf\breadcrumbs;
        // $bc->setPointer('->');
        $bc->crumbs();
        $this->view->breadcrumbs = $bc->breadcrumbs;

        // a new menu instance
        $menu = new \skf\menuReader(APP_PATH . '/modules');

        // $uri = \skf\uri::getInstance();
        $uri = new uri;

        $this->view->menu = $menu;
        // javascript loader
        $fragment = $uri->fragment(0);
        $module = empty($fragment) ? 'index' : $uri->fragment(0);
        // did somebody try to pass a dodgey controller value in the url?
        $path = APP_PATH . "/modules/$module/assets/js";
        if (is_dir($path)) {
            $js_loader = new asset_loader(APP_PATH . "/modules/$module/assets/js");
            $this->view->javascript = $js_loader;
        } else {
            $this->view->javascript = '';
        }

        // css loader
        $module = empty($fragment) ? 'index' : $uri->fragment(0);
        // did somebody try to pass a dodgey controller value in the url?
        $path = APP_PATH . "/modules/$module/assets/css";
        if (is_dir($path)) {
            $css_loader = new asset_loader(APP_PATH . "/modules/$module/assets/css");
            $this->view->css = $css_loader;
        } else {
            $this->view->css = '';
        }
    }

    public function __destruct() {
        if (!is_null($this->content)) {
            $this->view->content = $this->content;
            $result = $this->view->fetch(APP_PATH . '/layouts/' . $this->view->layout_file);
            $fc = new FrontController;
            $fc->setBody($result);
            echo $fc;
        }
    }

}
