<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Features\Idx;

use  PhemriseIct\PhemriseWpCf\Base\BaseController;
use \DOMXPath;
use \DOMDocument;

// https://teetimesolutions.georgiamls.com/203-blue-ridge-court-augusta-ga-30907/10540599
class Idx extends BaseController
{
    public $property_post_name;
    public $property_post_single_name;
    public $property_post_type;
    public $idx_website = "https://teetimesolutions.georgiamls.com";
    public $idx_page_fetching_settings_name = "pwpcf_idx_fetching_settings";
    public function __construct(){
        parent::__construct();

        $this->property_post_name = "IDX Properties";
        $this->property_post_single_name = "IDX Property";
        $this->property_post_type = "idx-properties";
    }
    function fetchPageIdxPage($url) {
        $response = wp_remote_get($url);
    
        if (is_wp_error($response)) {
            return false;
        }
    
        $html = wp_remote_retrieve_body($response);
        return $this->scrapeIdxPageData($html);
    }
    function scrapeIdxPageData($html) {
        libxml_use_internal_errors(true); // suppress HTML errors
    
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
    
        $properties = [];
    
        $property_nodes = $xpath->query('//div[contains(@class, "listing-gallery")]');

        $pagination = $xpath->query('//div[contains(@class, "listing-pagination-container")]');

        $next_page_link = null;
        $pagination_node_list =  [];
        foreach($pagination as $node){
            if(!$xpath->evaluate('.//a', $node)) continue;
            $pagination_node_list = $xpath->evaluate('.//a', $node) ?? []; //->item(0)?->getAttribute('href') ?? '';
        }
        for($i = 0; $i < count($pagination_node_list); $i++){
            if($pagination_node_list->item($i)->textContent == 'Next' || $pagination_node_list->item($i)->textContent == 'next'){
                $next_page_link = $pagination_node_list->item($i)?->getAttribute('href') ?? null;
            }
        }
        
        if(!empty($next_page_link)){
            if(strpos($next_page_link, "http") === false){
                $next_page_link = $this->idx_website.$next_page_link;
            }
        }

        foreach ($property_nodes as $node) {
            // $title = $xpath->evaluate('.//h2[contains(@class, "property-title")]', $node)->item(0)->textContent ?? '';
            $property_url = $xpath->evaluate('.//a', $node)->item(0)?->getAttribute('href') ?? '';
            $propery_listing_number = '';
            if(!empty($property_url)){
                $exploded_link = explode("/", $property_url);
                $arr_count = count($exploded_link);
                $propery_listing_number = $arr_count > 0 ? $exploded_link[$arr_count - 1] : '';
            }
            $property_price = $xpath->evaluate('.//p', $node)->item(0)->textContent ?? '';
            $property_detail = $xpath->evaluate('.//p', $node)->item(1)->textContent ?? '';
            $property_address = $xpath->evaluate('.//p', $node)->item(2)->textContent ?? '';
            $property_fit = $xpath->evaluate('.//p', $node)->item(4)->textContent ?? '';
            $image = $xpath->evaluate('.//img', $node)->item(0)?->getAttribute('src') ?? '';
            // $address = $xpath->evaluate('.//div[contains(@class, "address")]', $node)->item(0)->textContent ?? '';
            
            if(strpos($property_url, "http") === false){
                $property_url = $this->idx_website.$property_url;
            }
            
            $properties[] = [
                'property_url' => $property_url,
                'thumbnail' => esc_url_raw($image),
                'property_price' => $this->cleanPriceToFloat(trim($property_price)),
                'property_detail' => trim($property_detail),
                'property_address' => trim($property_address),
                'mls_number' => trim($propery_listing_number),
                'description' => '',
                'status' => 'active'
            ];
        }
    
        return [
            'data' => $properties,
            'next_page' => $next_page_link
        ];
    }

    
    function insertNewProperty($property) {
        // Prevent duplicate using MLS Number
        $existing = get_posts([
            'post_type' => $this->property_post_type,
            'meta_key' => 'pwpcf_mls_number',
            'meta_value' => $property['mls_number'],
            'post_status' => 'any',
            'numberposts' => 1,
        ]);
    
        if (!empty($existing)) {
            return false; // Already exists
        }
    
        // Insert the property as a post
        $post_id = wp_insert_post([
            'post_type' => $this->property_post_type,
            'post_title' => $property['property_address'],
            'post_content' => $property['description'],
            'post_status' => 'publish',
        ]);
    
        if (is_wp_error($post_id)) {
            return false;
        }
    
        // Save fields as post meta
        update_post_meta($post_id, 'pwpcf_mls_number', $property['mls_number']);
        update_post_meta($post_id, 'pwpcf_property_url', esc_url_raw($property['property_url']));
        update_post_meta($post_id, 'pwpcf_property_thumbnail', esc_url_raw($property['thumbnail']));
        update_post_meta($post_id, 'pwpcf_property_status', $property['status']);
        update_post_meta($post_id, 'pwpcf_property_price', $property['property_price']);
        update_post_meta($post_id, 'pwpcf_property_address', $property['property_address']);
        update_post_meta($post_id, 'pwpcf_property_details', $property['property_detail']);
    
        return $post_id;
    }
    public function fetchAndSafePropertiesFromIdx($url = null){
        $idx = new Idx();
        // $url = 'https://teetimesolutions.georgiamls.com/real-estate/search-action.cfm?city=Augusta';        
        // Augusta, Atlanta        
        $next_page_link = null;
        $all_data = [];
        do{
            $idx_page_data = $this->fetchPageIdxPage($url);
            if(!empty($idx_page_data)){
                $next_page_link = $idx_page_data['next_page'];
                $url = $next_page_link;
                // $all_data[] = $idx_page_data['data'];
                foreach($idx_page_data['data'] as $property){
                    $this->insertNewProperty($property);
                }
            }
        }while($next_page_link !== null);
    }
    function cleanPriceToFloat($price_string) {
        // Remove anything that's not a digit or a dot
        $cleaned = preg_replace('/[^\d.]/', '', $price_string);
    
        // Convert to float
        return floatval($cleaned);
    }
}
