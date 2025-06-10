<?php

/**
 * @package PhemriseWpCf
 */
/*
 Plugin Name: Phemrise WP Custom Features
 Plugin URI: https://phemrise.com
 Description: This plugin is designed to add custom features to WordPress websites
 Author: Phemrise ICT
 Version: 1.0.0
 Author URI: https://phemrise.com
 License: GPLv2 or later
 Text Domain: Phemrise
 */
/*

*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

defined('ABSPATH') or die("No direct access allowed");

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

//  Activation
function activate_phemrise_wp_cf()
{
    PhemriseIct\PhemriseWpCf\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_phemrise_wp_cf');

// Deactivation
function deactivate_phemrise_wp_cf()
{
    PhemriseIct\PhemriseWpCf\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_phemrise_wp_cf');

if (class_exists('PhemriseIct\\PhemriseWpCf\\Init')) {
    PhemriseIct\PhemriseWpCf\Init::register_services();
} 
