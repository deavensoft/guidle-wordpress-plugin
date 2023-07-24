<?php

/**
 * Plugin Name: Guidle Events
 * Description: Guidle Events
 */

 if(!defined('ABSPATH')) {
    die('You cannot be here');
 }

 if (!class_exists('GuidleEventsPlugin')) {
    class GuidleEventsPlugin {

        public function __construct() {
            define('GUIDLE_EVENTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
            require_once(GUIDLE_EVENTS_PLUGIN_PATH . '/vendor/autoload.php');
        }

        public function initialize() {
            include_once GUIDLE_EVENTS_PLUGIN_PATH . 'includes/utilities.php';
            // include_once GUIDLE_EVENTS_PLUGIN_PATH . 'includes/options-page.php';
            include_once GUIDLE_EVENTS_PLUGIN_PATH . 'includes/guidle-events-list.php';
        }

    }
    $guidleEventsPlugin = new GuidleEventsPlugin;
    $guidleEventsPlugin->initialize();
 }
 
