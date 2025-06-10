<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Base;

class BaseController
{
    public $plugin_opt_name;
    public $plugin_path;
    public $plugin_name;
    public $plugin_url;
    public $plugin_slug;
    function __construct(){
        $this->plugin_opt_name = "phemrise_wp_cf_options"; 
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_name =  plugin_basename(dirname(__FILE__, 3)) . '/Phemrise-WP-Custom-Features.php';
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin_slug = "phemrise_wp_custom_features";
    }
}