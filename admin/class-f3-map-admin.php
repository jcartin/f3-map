<?php 

class F3_Map_Admin {
    private $plugin_name;
    private $version;
    private $options;

    protected $options_page = 'f3_create_admin_page';
    protected $options_group = 'f3-map-options-group';
    protected $options_section = 'f3-map-section';
    protected $options_name = 'f3-map-options';
    protected $option_gmap_api_key = 'gmap_api_key';
    protected $option_css_selector = 'css_selector';

    protected $data = array(
        'gmap_api_key' => '', 
        'css_selector' => '.ao-location'
    );

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function wsc_register_options_page() {
        //add_options_page( page_title, menu_title, capability, menu_slug, function )
        add_options_page( 'F3 Map Settings', 'F3 Map', 'manage_options', $this->options_page, array( $this, 'wsd_create_admin_page' ) );
    }   

    

    public function wsd_create_admin_page() {
        $this->options = get_option( $this->options_name );
        ?>
        <div class="wrap">
            <form method="post">
                <?php settings_fields( $this->options_group ) ?>
                <?php
                do_settings_sections( $this->options_page );
                submit_button();
                ?>
            </form>
        </div>
        <?php

        echo $this->options['css_selector'];
    }

    public function wsc_register_settings() {

        // register_setting( $this->options_group, $this->option_gmap_api_key );
        // register_setting( $this->options_group, $this->option_css_selector );

        register_setting( $this->options_group, $this->options_name, array( $this, 'sanitize' ) );
        add_settings_section( $this->options_section, 'F3 Map Settings', array( $this, 'print_section_info' ), $this->options_page );
        add_settings_field( $this->option_gmap_api_key, 'Google Maps API Key', array( $this, 'google_maps_api' ), $this->options_page, $this->options_section );
        add_settings_field( $this->option_css_selector, 'CSS Selector', array( $this, 'map_selector' ), $this->options_page, $this->options_section );
    }

    public function sanitize($input) {
        $new_input = array();

        if ( isset($input[$this->option_css_selector]) )
            $new_input[$this->option_css_selector] = sanitize_text_field( $input[$this->option_css_selector] );

        if ( isset($input[$this->option_css_selector]) ) 
            $new_input[$this->option_css_selector] = sanitize_text_field( $input[$this->option_css_selector] );

        return $new_input;
    }

    public function print_section_info() {
        print 'Edit the options for the F3 Map workout display..';
    }

    public function google_maps_api() {
        printf(
            '<input type="text" id="gmaps_api_key" name="' . $this->options_name . '[' . $this->option_gmap_api_key . ']" value="%s" />', 
            isset( $this->options[$this->option_gmap_api_key]) ? esc_attr( $this->options[$this->option_gmap_api_key] ) : ''
        );
        echo "<br /><span>This is your Google Maps API key. You should pay attention to the impression counts in order to avoid the map exceeding the impression counts available on the free plan.</span>";
    }

    public function map_selector() {
        printf(
            '<input type="text" id="map_selector" name="' . $this->options_name . '[' . $this->option_css_selector . ']" value="%s" />', 
            isset( $this->options[$this->option_css_selector]) ? esc_attr( $this->options[$this->option_css_selector] ) : ''
        );
        echo "<br /><span>The CSS Selector option can change what jQuery selector is used to find the data that is used to populate the map. There are very few reasons for changing this value.</span>";
    }
}

?>