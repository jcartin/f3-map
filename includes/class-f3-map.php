<?php 

/** 
 * This file defines the core f3 strava club listing
 */

 class F3_Map {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'f3-map';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-f3-map-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-f3-map-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-f3-club-public.php';

        $this->loader = new F3_map_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new F3_Map_Admin( $this->plugin_name, $this->version );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'f3_register_options_page' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'f3_register_settings' );
    }

    private function define_public_hooks() {
        $plugin_public = new F3_Map_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        $this->loader->add_action( 'wp_print_scripts', $this, 'inspect_scripts' );
        
        if (!is_admin()) {
            $this->loader->add_shortcode( 'f3_map', $plugin_public, 'f3_render_map' );
            $this->loader->add_shortcode( 'f3_table', $plugin_public, 'f3_render_table' );
        }
    }

    public function inspect_scripts() {
        global $wp_scripts;
        echo PHP_EOL.'<!-- Script Handles: ';
        foreach ( $wp_scripts->queue as $handle ) :
            echo $handle . ' || ';
        endforeach;
        echo ' -->'.PHP_EOL;
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
 }