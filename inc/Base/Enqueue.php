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
        wp_register_style('pwpcf_bootstrap_css', '//cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        wp_enqueue_style('pwpcf_bootstrap_css');
        wp_enqueue_style('pwpcf_admin_base_style', $this->plugin_url . 'assets/admin/css/styles.css');
        wp_enqueue_script('pwpcf_admin_base_script', $this->plugin_url . 'assets/admin/js/scripts.js');
    }
    function ui_enqueue()
    {
        wp_register_style('pwpcf_bootstrap_css', '//cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        wp_enqueue_style('pwpcf_bootstrap_css');
        wp_enqueue_style('pwpcf_admin_base_style', $this->plugin_url . 'assets/front/css/styles.css');
    }
}
