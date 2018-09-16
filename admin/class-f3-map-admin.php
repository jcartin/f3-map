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

    public function f3_register_options_page() {
        //add_options_page( page_title, menu_title, capability, menu_slug, function )
        add_options_page( 'F3 Map Settings', 'F3 Map', 'manage_options', $this->options_page, array( $this, 'f3_create_admin_page' ) );
    }   

    public function f3_register_settings() {

        register_setting( 'f3-options-group', 'f3-options-name', array( $this, 'sanitize' ) );
        add_settings_section( 'f3-options-section', 'Map Options', array( $this, 'print_section_info' ), 'f3-options-page' );
        add_settings_field( 'f3-gmap-api-key', 'Google Maps API Key', array( $this, 'google_maps_api' ), 'f3-options-page', 'f3-options-section' );
        add_settings_field( 'f3-css-selector', 'AO Location CSS Selector', array( $this, 'map_selector' ), 'f3-options-page', 'f3-options-section' );
        add_settings_field( 'f3-ignore-cache', 'Ignore Cached values?', array( $this, 'ignore_cache'), 'f3-options-page', 'f3-options-section' );

    }

    public function f3_create_admin_page() {
        $this->options = get_option( 'f3-options-name' );
        
        ?>
        <div class="wrap">
            <form method="post">
                <?php settings_fields( 'f3-options-group' ) ?>
                <?php
                do_settings_sections( 'f3-options-page' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function sanitize($input) {
        $new_input = array();

        if ( isset($input['f3-css-selector']) )
            $new_input['f3-css-selector'] = sanitize_text_field( $input['f3-css-selector'] );

        if ( isset($input['f3-gmap-api-key']) ) 
            $new_input['f3-gmap-api-key'] = sanitize_text_field( $input['f3-gmap-api-key'] );

        if ( isset($input['f3-ignore-cache']) )
            $new_input['f3-ignore-cache'] = sanitize_text_field( $input['f3-ignore-cache'] );

        return $new_input;
    }

    public function print_section_info() {
        print 'Edit the options for the F3 Map workout display..';
    }

    public function google_maps_api() {
        // printf(
        //     '<input type="text" id="gmap_api_key" name="' . $this->options_name . '[' . $this->option_gmap_api_key . ']" value="%s" />', 
        //     isset( $this->options[$this->option_gmap_api_key]) ? esc_attr( $this->options[$this->option_gmap_api_key] ) : ''
        // );
        // echo "<br /><span>This is your Google Maps API key. You should pay attention to the impression counts in order to avoid the map exceeding the impression counts available on the free plan.</span>";

        printf(
            '<input type="text" id="f3_gmap_api_key" name="f3-options-name[f3-gmap-api-key]" value="%s" style="width: 400px" />', 
                isset( $this->options['f3-gmap-api-key'] ) ? esc_attr( $this->options['f3-gmap-api-key'] ) : ''
        );
    }

    public function map_selector() {
        // printf(
        //     '<input type="text" id="map_selector" name="' . $this->options_name . '[' . $this->option_css_selector . ']" value="%s" />', 
        //     isset( $this->options[$this->option_css_selector]) ? esc_attr( $this->options[$this->option_css_selector] ) : ''
        // );
        // echo "<br /><span>The CSS Selector option can change what jQuery selector is used to find the data that is used to populate the map. There are very few reasons for changing this value.</span>";
        printf(
            '<input type="text" id="f3_css_selector" name="f3-options-name[f3-css-selector]" value="%s" style="width: 400px" />', 
                isset( $this->options['f3-css-selector'] ) ? esc_attr( $this->options['f3-css-selector'] ) : ''
        );
    }

    public function ignore_cache() {
        printf(
            '<input type="checkbox" id="f3_ignore_cache" name="f3-options-name[f3-ignore-cache]" value="ignore" %s />', 
                strlen( $this->options['f3-ignore-cache'] ) > 0 ? 'checked="checked"' : '' 
        );
    }
}

?>