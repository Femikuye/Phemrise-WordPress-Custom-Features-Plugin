<?php ?>
<div class="wrap">
    <h1>IDX Dashboard</h1>
    <?php
        settings_errors();
        use PhemriseIct\PhemriseWpCf\Features\Idx\Idx;
        $idx = new Idx();
        $options = get_option($idx->idx_page_fetching_settings_name);
    ?>

    <ul class="nav pwpcf-nav-tabs">
        <li class="active">
            <a href="#pwpcf-tab-1">Settings</a>
        </li>
        <li class="">
            <a href="#pwpcf-tab-2">Data Scrapping</a>
        </li>
    </ul>
    <div class="pwpcf-tab-content">
        <div id="pwpcf-tab-1" class="pwpcf-tab-pane active">
            <div class="row">
                <div class="col-md-6">
                    <form method="post">
                    <?php
                        wp_nonce_field('pwpcf_save_idx_settings', 'pwpcf_idx_settings_form_nonce');
                    ?>
                    <div class="mb-3">
                        <label class="form-label" for="idx-base-url">
                            IDX Website
                        </label>
                        <input class="form-control" name="idx-base-url" value="<?php echo isset($options['base_url']) ? $options['base_url'] : '' ?>"
                        type="text" id="idx-base-url" placeholder="Enter the IDX Website URL">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="idx-search-endpoint-url">
                            Property Search url
                        </label>
                        <input class="form-control" name="idx-search-endpoint-url" value="<?php echo isset($options['search_endpoint']) ? $options['search_endpoint'] : '' ?>"
                        type="text" id="idx-search-endpoint-url" placeholder="Enter the IDX search endpoint">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="idx-property-search-cities">
                            Properties Location (Cities) <small>Comma separated</small>
                        </label>
                        <input class="form-control" name="idx-property-search-cities" value="<?php echo isset($options['cities']) ? $options['cities'] : '' ?>" 
                        type="text" id="idx-property-search-cities" placeholder="Augusta, Atlanta">
                    </div>
                    <?php  submit_button('Save Settings'); ?>                 
                </form>
                </div>
            </div>
        </div>
        <div id="pwpcf-tab-2" class="pwpcf-tab-pane">
            <h3>Scrap properties data from the MLS website</h3>
            <div class="row">
                <div class="col-md-6">
                    <form method="post">
                    <?php
                        wp_nonce_field('pwpcf_fetch_properties', 'pwpcf_fetch_properties_form_nonce');
                    ?>
                    <div class="mb-3">
                        <label class="form-label" for="idx-property-city">
                            City <small>Enter the city to fetch the data from</small>
                        </label>
                        <input class="form-control" name="idx-property-city"
                        type="text" id="idx-property-city" placeholder="Enter city">
                    </div>
                    <?php  submit_button('Fetch Data'); ?>                 
                </form>
                </div>
            </div>
        </div>
    </div>
</div>