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

            // add_action('init', 'register_style', 999);
            // function register_style() {
            //     // wp_register_style( 'guidle_style', plugins_url('/css/guidle-events.css', __FILE__), false, '1.0.0', 'all');
            //     wp_register_style( 'guidle_style', plugins_url('/css/gdl-list.css', __FILE__), false, '1.0.0', 'all');
            // }

            add_action('wp_enqueue_scripts', 'enqueue_guidle_style', 999);
            function enqueue_guidle_style(){
                wp_register_style( 'guidle_style', plugins_url('/css/gdl-list.css', __FILE__), true, '1.0.0', 'all');
                wp_enqueue_style( 'guidle_style' );
            }

            function create_block_guidle_events_block_init() {
                register_block_type( __DIR__ . '/build' );
            }
            add_action( 'init', 'create_block_guidle_events_block_init' );
        }

    }
    $guidleEventsPlugin = new GuidleEventsPlugin;
    $guidleEventsPlugin->initialize();
 }
 
