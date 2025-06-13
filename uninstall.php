<?php

/**
 * Trigger this file on Plugin uninstall
 * @package PhemriseWpCf
 */

defined('WP_UNINSTALL_PLUGIN') or die;

$idx_property_post_type = 'idx-properties';

$plugin_primary_opt_name = 'phemrise_wp_cf_options';

$idx_fetchin_opt_name = 'pwpcf_idx_fetching_settings';

delete_option( $plugin_primary_opt_name );

delete_option( $idx_fetchin_opt_name );


global $wpdb;
$posts_table =  $wpdb->prefix . "posts";
$posts_meta_table =  $wpdb->prefix . "postmeta";
$term_relationships =  $wpdb->prefix . "term_relationships";
$wpdb->query("DELETE FROM $posts_table WHERE post_type = '$idx_property_post_type' ");
$wpdb->query("DELETE FROM $posts_meta_table WHERE post_id NOT IN(SELECT id FROM wp_posts)");
$wpdb->query("DELETE FROM $term_relationships WHERE object_id NOT IN(SELECT id FROM wp_posts)");
