<?php 

class F3_Map_Admin {
    private $plugin_name;
    private $version;
    private $options;

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
        add_options_page( 'F3 Map', 'F3 Map', 'manage_options', 'f3-map-options', array( $this, 'f3_create_admin_page' ) );
    }   

    public function f3_create_admin_page() {
        ?>
        <div class="wrap">
            <h2>F3 Map Options</h2>
            <form method="post" action="options.php">
                <?php settings_fields( $this->options_group); ?>
                <?php do_settings_sections( $this->options_group ); ?>
                <table class="form-table">
                    <tr>
                        <th valign="top" scope="row">Google Maps API Key:</th>
                        <td>
                            <input type="text" id="f3-admin-gmap-key" name="<?php echo $this->option_gmap_api_key ?>" value="<?php echo esc_attr(get_option( "gmap_api_key" )) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th valign="top" scope="row">CSS Selector:</th>
                        <td>
                            <input type="text" id="f3-admin-selector" name="<?php echo $this->option_css_selector ?>" value="<?php echo esc_attr(get_option( "css_selector" )) ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save') ?>
            </form>
        </div>
        <?php
    }

    public function f3_handle_post() {
        // check to see if the data is there
        if (isset( $input['gmap_api_key'] ) && isset( $input['map_selector'] )) {
            echo "Settings updated.";
        }
    }

    public function validate($input) {
        $valid = array();
        $valid['gmap_api_key'] = sanitize_text_field( $input['gmap_api_key'] );
        $valid['css_selector'] = sanitize_text_field( $input['css_selector'] );

        if ( strlen( $valid['gmap_api_key'] ) ) {
            add_settings_error( 'gmap_api_key', 'gmap_api_key_error', 'Google Maps API Key is not valid.', 'errors' );

            $valid['gmap_api_key'] = $this->data['gmap_api_key'];
        }

        if ( strlen( $valid['css_selector'] ) ) {
            add_settings_error( 'css_selector', 'css_selector_error', 'CSS Selector is required.', 'error' );

            $valid['css_selector'] = $this->data['css_selector'];
        }

        return $valid;
    }

    public function wsd_create_admin_page() {
        $this->options = get_option( $this->options_group );
        ?>
        <div class="wrap">
            <form method="post">
                <?php settings_fields( $this->options_group ) ?>
                <?php
                settings_fields( $this->options_group );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function wsc_register_settings() {
        //register_setting( 'f3_option_group', 'wsc_options', array( $this, 'sanitize' ) );

        //add_settings_section( 'wsc_options', 'F3 Map Settings', array( $this, 'print_section_info'), 'f3_option_group' );
        //add_settings_field( 'gmap_api_key', 'Google Maps API Key', array( $this, 'google_maps_api' ), 'f3_option_group', 'wsc_options' );
        //add_settings_field( 'map_selector', 'Map jQuery Selector', array( $this, 'map_selector' ), 'f3_option_group', 'wsc_options' );
        //register_setting( option_group, option_name, sanitize_callback )
        // register_setting(
        //     $this->options_group, 
        //     $this->options_name, 
        //     array( $this, 'sanitize' ) 
        // );
        // add_settings_section( id, title, callback, page )
        // add_settings_section(
        //     $this->options_section, 
        //     'F3 Map Settings', 
        //     array( $this, 'print_section_info' ), 
        //     $this->options_section
        // );   
        // add_settings_field( id, title, callback, page, section, args )
        // add_settings_field(
        //     $this->option_css_selector, 
        //     'Map CSS Selector',
        //     array( $this, 'map_selector' ), 
        //     $this->options_section, 
        //     $this->options_group           
        // );      
        // add_settings_field(
        //     $this->option_gmap_api_key, 
        //     'Google Maps API Key',
        //     array( $this, 'google_maps_api' ), 
        //     $this->options_section, 
        //     $this->options_group           
        // );    
        register_setting( $this->options_group, $this->option_gmap_api_key );
        register_setting( $this->options_group, $this->option_css_selector );
    }

    public function sanitize($input) {
        $new_input = array();

        if ( isset($input['gmap_api_key']) )
            $new_input['gmap_api_key'] = sanitize_text_field( $input['gmap_api_key'] );

        if ( isset($input['map_selector']) ) 
            $new_input['map_selector'] = sanitize_text_field( $input['map_selector'] );

        return $new_input;
    }

    public function print_section_info() {
        print 'Get your Google Maps API from google.';
    }

    public function google_maps_api() {
        printf(
            '<input type="text" id="gmaps_api_key" name="' . $this->options_name . '[gmaps_api_key]" value="%s" />', 
            isset( $this->options['gmap_api_key']) ? esc_attr( $this->options['gmap_api_key'] ) : ''
        );
    }

    public function map_selector() {
        printf(
            '<input type="text" id="map_selector" name="' . $this->options_name . '[map_selector]" value="%s" />', 
            isset( $this->options['map_selector']) ? esc_attr( $this->options['map_selector'] ) : ''
        );
    }
}

?>