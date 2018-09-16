<?php 
/** 
 * 
 */

 class F3_Map_Map {
     public $plugin_name;
     
     private $version;
     private $athlete;

     public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
     }

     public function enqueue_styles() {
         wp_enqueue_style($this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/f3-map-public.css', array(), $this->version, 'all');
     }

     public function f3_render_map( $atts ) {
        #$this->enqueue_scripts();
        $this->enqueue_styles();
        
        $options = get_option( 'f3-options-name' );
        $f3_map_selector = $options['f3-css-selector'] ?? '.ao-location';

        $atts = shortcode_atts( array(
            'lat' => 'null', 
            'lng' => 'null', 
        ), $atts );

        ?>
        <div class="f3-map-wrapper">
            <div id="f3-map" class="f3-map" data-selector="<?php echo $f3_map_selection ?>"></div>
            <div class="f3-map-instructions">
                Click on a pin to see the workouts and times for that location.
            </div>
        </div>
        <div class="f3-map-details">
            <div class="f3-map-details-header">
                <div class="f3-map-details-location" id="f3-map-details-location">Dreher High School</div>
                <div class="f3-map-details-address" id="f3-map-details-address">
                    3319 Millwood Ave
                    Columbia, SC 20205
                </div>
            </div>
            <div class="f3-table">
                <div class="f3-table-heading">
                    <div class="f3-table-row">
                        <div class="f3-table-cell">
                            Sunday
                        </div>
                        <div class="f3-table-cell">
                            Monday
                        </div>
                        <div class="f3-table-cell">
                            Tuesday
                        </div>
                        <div class="f3-table-cell">
                            Wednesday
                        </div>
                        <div class="f3-table-cell">
                            Thursday
                        </div>
                        <div class="f3-table-cell">
                            Friday
                        </div>
                        <div class="f3-table-cell">
                            Saturday
                        </div>
                    </div>
                </div>
                <div class="f3-table-body">
                    <div class="f3-table-row">
                        <div class="f3-table-cell f3-calendar-row" data-label="Sunday">
                            <span id="f3-map-detail-sunday"></span>
                        </div>
                        <div class="f3-table-cell f3-calendar-row" data-label="Monday">
                            <span id="f3-map-detail-monday"></span>
                        </div>
                        <div class="f3-table-cell f3-calendar-row" data-label="Tuesday">
                            <span id="f3-map-detail-tuesday"></span>
                        </div>
                        <div class="f3-table-cell f3-calendar-row" data-label="Wednesday">
                        <span id="f3-map-detail-wednesday"></span>
                        </div>
                        <div class="f3-table-cell f3-calendar-row" data-label="Thursday">
                        <span id="f3-map-detail-thursday"></span>
                        </div>
                        <div class="f3-table-cell f3-calendar-row" data-label="Friday">
                        <span id="f3-map-detail-friday"></span>
                        </div>
                        <div class="f3-table-cell f3-calendar-row" data-label="Saturday">
                        <span id="f3-map-detail-saturday"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="f3-map-details-config" 
            data-selector="<?php echo $f3_map_selector ?>" 
            data-lat="<?php echo $atts["lat"] ?>" 
            data-lng="<?php echo $atts["lng"] ?>"></div>

        <?php
        
     }
 }