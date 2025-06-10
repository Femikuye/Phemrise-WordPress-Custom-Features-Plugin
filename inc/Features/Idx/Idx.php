<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Features\Idx;

use  PhemriseIct\PhemriseWpCf\Base\BaseController;
use \DOMXPath;
use \DOMDocument;

class Idx extends BaseController
{
    public function __construct(){
        parent::__construct();
    }
    function fetchPageIdxPage($url) {
        $response = wp_remote_get($url);
    
        if (is_wp_error($response)) {
            return false;
        }
    
        return wp_remote_retrieve_body($response);
    }
    function scrapeIdxPageData($html) {
        libxml_use_internal_errors(true); // suppress HTML errors
    
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
    
        $properties = [];
    
        $property_nodes = $xpath->query('//div[contains(@class, "listing-gallery")]');
        
        // return $property_nodes;

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
    
            $properties[] = [
                'property_url' => $property_url,
                'image' => esc_url_raw($image),
                'property_price' => trim($property_price),
                'property_detail' => trim($property_detail),
                'property_address' => trim($property_address),
                'mls_number' => trim($propery_listing_number)
            ];
        }
    
        return $properties;
    }
}
