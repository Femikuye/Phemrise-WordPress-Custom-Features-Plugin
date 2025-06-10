<div class="wrap">
    <h1>Phemrise WP Custom Features Dashboard</h1>
    <?php
        settings_errors();
        use PhemriseIct\PhemriseWpCf\Base\BaseController;
        $base = new BaseController();
        $options = get_option($base->plugin_opt_name);
    ?>

    <ul class="nav pwpcf-nav-tabs">
        <li class="active">
            <a href="#pwpcf-tab-1">Features</a>
        </li>
        <li class="">
            <a href="#pwpcf-tab-2">About</a>
        </li>
    </ul>
    <div class="pwpcf-tab-content">
        <div id="pwpcf-tab-1" class="pwpcf-tab-pane active">
            <form method="post">
                <?php
                // print_r($options);
                    wp_nonce_field('phemrise_save_features', 'pwpcf_features_form_nonce');

                    if($options && isset($options['features'])){
                        foreach($options['features'] as $key => $feature){
                        ?>
                
                        <div class="form-check">
                            <input class="form-check-input" name="features[<?php echo  esc_attr($key); ?>]" value="1" 
                            type="checkbox" id="feature-<?php echo $key; ?>" <?php  checked($feature['enabled'], 1, true) ?>>
                            <label class="form-check-label" for="feature-<?php echo $key; ?>">
                                <?php echo $feature['name']; ?>
                            </label>
                        </div>
                <?php  
                    }} 
                    submit_button('Save Features');
                ?>
                 
            </form>
        </div>
        <div id="pwpcf-tab-2" class="pwpcf-tab-pane">
            <h3>About</h3>
        </div>
    </div>
</div>