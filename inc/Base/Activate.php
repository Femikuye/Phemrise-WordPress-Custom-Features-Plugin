<?php

/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Base;

class Activate 
{
    
    public static function activate()
    {
        flush_rewrite_rules();
        $opt_name = 'phemrise_wp_cf_options';
        if (!get_option($opt_name)) {
            $options = [
                'features' => [
                    'idx' => [
                        'enabled' => 0, 
                        'name' => 'Realty IDX', 
                        'page_title' => 'Realaty IDX Dashboard',
                        'menu_slug' => 'phemrise_wp_custom_idx'
                    ],
                    'affiliate' => [
                        'enabled' => 0, 
                        'name' => 'WP Affiliate', 
                        'page_title' => 'WP Affiliate Dashboard',
                        'menu_slug' => 'phemrise_wp_custom_affiliate'
                    ]
                ]
            ];
            update_option($opt_name, $options);
        }
    }
}