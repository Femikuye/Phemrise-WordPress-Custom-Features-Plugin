<?php

/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Base;

use PhemriseIct\PhemriseWpCf\Base\BaseController;

class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
        add_action('wp_enqueue_scripts', array($this, 'ui_enqueue'));
    }
    public function admin_enqueue()
    {
        // wp_enqueue_script('media-upload');
        // wp_enqueue_media();
        wp_enqueue_style('pwpcf_admin_base_style', $this->plugin_url . 'assets/admin/css/styles.css');
        wp_enqueue_script('pwpcf_admin_base_script', $this->plugin_url . 'assets/admin/js/scripts.js');
    }
    function ui_enqueue()
    {
        // wp_enqueue_script('media-upload');
        // wp_enqueue_media();
        // wp_enqueue_style('mypluginstyle', $this->plugin_url . 'assets/front/styles.css');
        // wp_enqueue_script('mypluginscript', $this->plugin_url . 'assets/front/script.js');
    }
}
