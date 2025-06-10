<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Features\Idx;

use PhemriseIct\PhemriseWpCf\Base\BaseController;

class Engine extends BaseController
{
    public function register(){
        $base = new BaseController();
        $opt_name = $base->plugin_opt_name;
        $options = get_option($opt_name);
        if(empty($options)) return;

        if(!is_array($options)) return;

        if(!isset($options['features']['idx'])) return;

        if(!$options['features']['idx']) return; 
        
    }
}
