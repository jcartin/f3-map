<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.f3midlands.com
 * @since             1.0.0
 * @package           F3 Map Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       F3 Midlands Map
 * Plugin URI:        https://www.f3midlands.com
 * Description:       Displays a google map with data from a jQuery selector
 * Version:           1.0.0
 * Author:            John Cartin
 * Author URI:        https://www.f3midlands.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       f3-map
 * Domain Path:       /languages
 */

 // if this file is called directly, abort
 if (!defined( 'WPINC')) {
    die;    
 }

 define( 'F3S_PATH', plugin_dir_path( __FILE__ ));

 function activate_f3_map() {
    $wsc_options = get_option('wsc_options');

    if ($wsc_options == false) {
        $wsc_options = array();
    }

    if (!array_key_exists('gmap_api_key', $wsc_options)) {
        $wsc_options['gmap_api_key'] = 'f3_option_group';
    }

    if (!array_key_exists('map_selector', $wsc_options)) {
        $wsc_options['map_selector'] = '.ao-selector';
    }

    update_option('wsc_options', $wsc_options);
 }

 register_activation_hook( __FILE__, 'activate_f3_map' );

 require plugin_dir_path( __FILE__ ) . 'includes/class-f3-map.php';

 function run_f3_map_plugin() {
    $plugin = new F3_Map();
    $plugin->run();
 }

 run_f3_map_plugin();

