<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Features\Idx;

use  PhemriseIct\PhemriseWpCf\Features\Idx\Idx;
use \WP_Query;

class PropertiesPostType extends Idx
{
    public function register(){
        add_action("init", array($this, 'registerPropertiesPostType'));

        add_action('admin_init', [$this, 'phemriseHandleIdxSettingsSubmit']);
        add_action('admin_init', [$this, 'fetchMlsWebsite']);
        add_action('admin_init', [$this, 'scheduledFetchingTask']);

        add_shortcode('pwpcf-idx-properties', array($this, 'idxPropertyListingsShortcode'));
    }

    
    public function phemriseHandleIdxSettingsSubmit() {
        if (!isset($_POST['pwpcf_idx_settings_form_nonce']) || !wp_verify_nonce($_POST['pwpcf_idx_settings_form_nonce'], 'pwpcf_save_idx_settings')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $submitted = $_POST['features'] ?? [];
        $settings = get_option($this->idx_page_fetching_settings_name);

        if(!$settings || !is_array($settings)){
            $settings = [];
        };

        if(isset($_POST['idx-base-url']) && !empty($_POST['idx-base-url'])){
            $settings['base_url'] = sanitize_text_field($_POST['idx-base-url']);
        }

        if(isset($_POST['idx-property-search-cities']) && !empty($_POST['idx-property-search-cities'])){
            $settings['cities'] = sanitize_text_field($_POST['idx-property-search-cities']);
        }

        if(isset($_POST['idx-search-endpoint-url']) && !empty($_POST['idx-search-endpoint-url'])){
            $settings['search_endpoint'] = sanitize_text_field($_POST['idx-search-endpoint-url']);
        }

        update_option($this->idx_page_fetching_settings_name, $settings);

        // Optional redirect with admin notice
        wp_redirect(add_query_arg('settings-updated', 'true', $_SERVER['REQUEST_URI']));
        exit;
    }

    public function fetchMlsWebsite(){
        if (!isset($_POST['pwpcf_fetch_properties_form_nonce']) || !wp_verify_nonce($_POST['pwpcf_fetch_properties_form_nonce'], 'pwpcf_fetch_properties')) {
            return;
        }
        $settings = get_option($this->idx_page_fetching_settings_name);

        if(empty($settings) || !isset($settings['search_endpoint'])){
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if(!isset($_POST['idx-property-city']) || empty($_POST['idx-property-city'])){
            return;
        }
        $search_city = sanitize_text_field($_POST['idx-property-city']);
        $search_url = $settings['search_endpoint'];
        $search_url .= "?city=".$search_city;
        
        $this->fetchAndSafePropertiesFromIdx($search_url);

        // Optional redirect with admin notice
        wp_redirect(add_query_arg('Properties data fetched successfuly', 'true', $_SERVER['REQUEST_URI']));
        exit;
    }

    public function scheduledFetchingTask(){
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $settings = get_option($this->idx_page_fetching_settings_name);
        if(empty($settings) || !isset($settings['search_endpoint'])){
            return;
        }

        if(empty($settings) || !isset($settings['cities'])){
            return;
        }
        
        if(isset($settings['last_run_time']) && !empty($settings['last_run_time'])){
            $one_day_different = 60*60*24;
            if((time() - $settings['last_run_time']) < $one_day_different){
                return;
            }
        }

        $search_city = "";

        $cities_array = explode(",", $settings['cities']);

        if(empty($cities_array)) return;

        if(isset($settings['last_city_fetched']) && in_array($settings['last_city_fetched'], $cities_array)){
            $last_city_fetched_possition = array_search($settings['last_city_fetched'], $cities_array);
            if($last_city_fetched_possition >= (count($cities_array) - 1)){
                $last_city_fetched_possition = 0;
            }else{
                $last_city_fetched_possition++;
            }
            $search_city = $cities_array[$last_city_fetched_possition];
        }

        if(empty($search_city)){
            $search_city = $cities_array[0];
        }

        $settings['last_city_fetched'] = $search_city;
        $settings['last_run_time'] = time();
        update_option($this->idx_page_fetching_settings_name, $settings);


        $search_url = $settings['search_endpoint'];
        $search_url .= "?city=".$search_city;
        $this->fetchAndSafePropertiesFromIdx($search_url);
    }

    public function registerPropertiesPostType()
    {
        $post = [
            'post_type' => $this->property_post_type,
            'name' => $this->property_post_name,
            'single_name' => $this->property_post_single_name,
        ];
        register_post_type(
            $post['post_type'],
            array(
                'labels' => array(
                    'name' =>  $post['name'],
                    'singular_name' =>  $post['single_name']
                ),
                'public' =>  true,
                'has_archive' =>  true,
                'all_items'             => 'All ' . $post['single_name'],
                'add_new_item'          => 'Add New ' . $post['single_name'],
                'add_new'               => 'Add New',
                'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
                'show_in_rest'          => true,
                'rewrite' => ['slug' => $post['post_type']],
            )
        );
    }

    public function idxPropertyListingsShortcode($atts) {
        ob_start();

        // $city = isset($_POST['pwpcf_search_property_city']) ? $_POST['pwpcf_search_property_city'] : '';
        // $min_price = isset($_POST['pwpcf_search_property_min_price']) ? $_POST['pwpcf_search_property_min_price'] : '';
        // $max_price = isset($_POST['pwpcf_search_property_max_price']) ? $_POST['pwpcf_search_property_max_price'] : '';

        $city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
        $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';
        $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
        $paged = isset($_GET['next_page']) ? max(1, intval($_GET['next_page'])) : 1;
        // Search form
        ?>
        <form method="get" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="city" class="form-control" placeholder="Enter City" value="<?php echo esc_attr($city); ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="<?php echo esc_attr($min_price); ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="<?php echo esc_attr($max_price); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100">Search Properties</button>
                </div>
            </div>
        </form>
        <?php
        
            // Handle form input
            $city = sanitize_text_field($city);
            $min_price = floatval($min_price);
            $max_price = floatval($max_price);
            // Build query
            $meta_query = [];

            if ($city) {
                $meta_query[] = [
                    'key' => 'pwpcf_property_address',
                    'value' => $city,
                    'compare' => 'LIKE'
                ];
            }
            if ($min_price) {
                $meta_query[] = [
                    'key' => 'pwpcf_property_price',
                    'value' => $min_price,
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                ];
            }

            if ($max_price) {
                $meta_query[] = [
                    'key' => 'pwpcf_property_price',
                    'value' => $max_price,
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                ];
            }

            $query = new WP_Query([
                'post_type' => $this->property_post_type,
                'post_status' => 'publish',
                'posts_per_page' => 12,
                'paged' => $paged,
                'meta_query' => $meta_query
            ]);

            echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    $price = get_post_meta(get_the_ID(), 'pwpcf_property_price', true);
                    $address = get_post_meta(get_the_ID(), 'pwpcf_property_address', true);
                    $thumbnail = get_post_meta(get_the_ID(), 'pwpcf_property_thumbnail', true);
                    $property_url = get_post_meta(get_the_ID(), 'pwpcf_property_url', true);
                    $property_details = get_post_meta(get_the_ID(), 'pwpcf_property_details', true);
                    $property_url = !empty($property_url) ? $property_url : "#!";

                    if(strpos($thumbnail, "http") === false){
                        $thumbnail = $this->idx_website.$thumbnail;
                    }
                    ?>

                    <div class="col">
                        <div class="card h-100">
                            <div class="pwpcf-idx-property-photo-container">
                                <?php if ($thumbnail): ?>
                                    <a target="_blank" href="<?php echo $property_url ?>">
                                        <img src="<?php echo esc_url($thumbnail); ?>" class="pwpcf-card-img-top" alt="<?php echo the_title(); ?>">
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <a target="_blank" href="<?php echo $property_url ?>">
                                    <h3 class="card-title"><?php the_title(); ?></h3>
                                </a>
                                <p class="card-text"><?php echo esc_html($property_details); ?></p>
                                <h5 class="card-text"><strong>$<?php echo number_format(floatval($price)); ?></strong> </h5>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                wp_reset_postdata();

                 // Pagination
                $total_pages = $query->max_num_pages;

                if ($total_pages > 1) {
                    $current_page = max(1, $paged);

                    echo '<div class="col"><ul class="pagination pwpcf-idx-property-paging justify-content-center">';

                    $links = paginate_links([
                        'base' => add_query_arg('next_page', '%#%'),
                        'format' => '',
                        'current' => $current_page,
                        'total' => $total_pages,
                        'type' => 'array',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    ]);

                    foreach ($links as $link) {
                        // Detect active page
                        $active = strpos($link, 'current') !== false ? ' active' : '';
                        echo '<li class="page-item' . $active . '">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
                    }

                    echo '</ul></div>';
                }

            } else {
                echo '<div class="col"><h3>No properties found.</h3></div>';
            }
            echo '</div>';
            
            
        return ob_get_clean();
    }

}

