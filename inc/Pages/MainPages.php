<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Pages;

use PhemriseIct\PhemriseWpCf\Base\BaseController;
use PhemriseIct\PhemriseWpCf\Settings\PageSettings;

class MainPages extends BaseController
{
    public $settings;
    public $pages;
    public $subpages;
    public function register(){
        $this->settings = new PageSettings();

        $this->setPages();
        $this->settings->addPages($this->pages)->withSubPage("Dashboard")->addSubPages($this->subpages)->register();

        add_action('admin_init', [$this, 'phemriseHandleFeatureFormSubmit']);
        
    }
    
    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Phemrise WP Plugin',
                'menu_title' => 'Phemrise WP',
                'capability' => 'manage_options',
                'menu_slug' => $this->plugin_slug,
                'callback' => array($this, 'mainPage'),
                'icon_url' => 'dashicons-dashboard', 
                'position' => 110
            ]
        ];
        $this->subpages = [];
        $options = get_option($this->plugin_opt_name);
        if(isset($options['features']) && is_array($options['features'])){
            foreach($options['features'] as $feature => $params){
                if(!is_array($params)) continue; 
                
                if(!$params['enabled']) continue;
                
                array_push($this->subpages, 
                    [
                        'page_title' => $params['page_title'],
                        'menu_title' => $params['name'],
                        'capability' => 'manage_options',
                        'menu_slug' => $params['menu_slug'],
                        'parent_slug' => $this->plugin_slug,
                        'callback' => $this->getPageCallback($feature)
                    ]
                );
            }
        }
    }
    private function getPageCallback($feature_name){
        $features_callbacks = [
            'idx' => [$this, 'idxPage'],
            'affiliate' => [$this, 'affiliatePage']
        ];
        if(!isset($features_callbacks[$feature_name])){
            return [$this, 'mainPage'];
        }
        return $features_callbacks[$feature_name];
    }

    function phemriseHandleFeatureFormSubmit() {
        if (!isset($_POST['pwpcf_features_form_nonce']) || !wp_verify_nonce($_POST['pwpcf_features_form_nonce'], 'phemrise_save_features')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $submitted = $_POST['features'] ?? [];
        $options = get_option($this->plugin_opt_name);

        if(!is_array($options) || !isset($options['features'])) return ;

        foreach ($options['features'] as $key => $feature) {
            $options['features'][$key]['enabled'] = isset($submitted[$key]) ? 1 : 0;
        }

        update_option($this->plugin_opt_name, $options);

        // Optional redirect with admin notice
        wp_redirect(add_query_arg('settings-updated', 'true', $_SERVER['REQUEST_URI']));
        exit;
    }





    public function mainPage()
    {
        return require_once("$this->plugin_path/templates/admin/dashboard.php");
    }

    public function idxPage()
    {
        return require_once("$this->plugin_path/templates/admin/idx-dashboard.php");
    }

    public function affiliatePage()
    {
        return require_once("$this->plugin_path/templates/admin/affiliate-dashboard.php");
    }

}
