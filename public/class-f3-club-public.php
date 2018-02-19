<?php 

const GOOGLE_MAPS_LINK = 0;
const LOCATION = 1;
const STREET = 2;
const CITY = 3;
const STATE = 4;
const ZIP = 5;
const LATITUDE = 6;
const LONGITUDE = 7;
const REGION = 8;
const WORKOUT_NAME = 9;
const DAY_OF_WEEK = 10;
const START_TIME = 11;
const END_TIME = 12;
const WORKOUT_STYLE = 13;
const TWITTER_HANDLE = 14;
const TWITTER_NAME = 15;
const POST_LINK = 16;

/** 
 * 
 */

 class F3_Map_Public {
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

     public function enqueue_scripts() {
         $options = get_option( 'f3-options-name' );
         $api_key = $options['f3-gmap-api-key'];

         // insert the google maps api scripts, only if the maps api key is configured.
         wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/f3-map-public.js', array( 'jquery' ), $this->version, false );
         if ( strlen( $api_key ) > 0 ) {
            wp_enqueue_script( 'f3_google_maps', '//maps.googleapis.com/maps/api/js?key=' . $api_key, array( 'jquery', $this->plugin_name ), '', false );
         }
         
         wp_enqueue_script( 'f3_underscore', '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js', array( 'underscore', $this->plugin_name ), '', false );         
     }


     function assemble_map_link( $row ) {
        // <a href="https://goo.gl/maps/r3ADP" target="_blank" class="ao-location" data-lat="33.998525" data-lng="-80.994108" 
        // data-location="Dreher High School" data-workout="amble" data-line1="3319 Millwood Ave" data-line2="Columbia, SC 29205">Dreher High School</a><br />
        echo '<a href="' . $row[GOOGLE_MAPS_LINK] . '" target="_blank" class="ao-location"'
            . ' data-lat="' . $row[LATITUDE] . '"'
            . ' data-lng="' . $row[LONGITUDE] . '"'
            . ' data-location="' . $row[LOCATION] . '"'
            . ' data-workout="' . $row[WORKOUT_NAME] . '"' 
            . ' data-line1="' . $row[STREET] . '"' 
            . ' data-line2="' . $row[CITY] . ', ' . $row[STATE] . ' ' . $row[ZIP] . '"' 
            . ' data-day="' . $row[DAY_OF_WEEK] . '"' 
            . ' data-starttime="' . $row[START_TIME] . '"' 
            . ' data-endtime="' . $row[END_TIME] . '"' 
            . '>' 
            . $row[LOCATION] 
            . '</a>';
     }
     
     function assemble_twitter_link($row) {
         return '<a target="_blank" href="https://twitter.com/' . $row[TWITTER_HANDLE] . '">' . $row[TWITTER_NAME] . '</a>';
     }

     function assemble_post_link($row) {
         return '<a href="' . $row[POST_LINK] . '">' . $row[WORKOUT_NAME] . '</a>';;
     }

     public function f3_render_table( $atts ) {
        $options = get_option( 'f3-options-name' );
        $f3_map_selector = $options['f3-css-selector'];

        $atts = shortcode_atts( array(
            'spreadsheet' => 'https://docs.google.com/spreadsheets/d/1z4LuujrE9P9Wk4q6YNIKVaGBXTPbRAxX-0SkFSmVt18/gviz/tq?tqx=out:csv&sheet=Data', 
            'lat' => '34.0088279', 
            'lng' => '-80.99547369999999', 
            'title' => ''
        ), $atts );

        if (strlen($atts['spreadsheet']) == 0) {
            echo "Cannot render the data without specifying a shreadsheet url.";
            return;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $atts['spreadsheet']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $csv = curl_exec($ch);
        $data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $csv));

        ob_start();

        ?>
        <div class-"f3-table-title">
            <?php echo $atts['title'] ?>
        </div> 
        <div class="f3-table">
            <div class="f3-table-heading">
                <div class="f3-table-row"   >
                    <div class="f3-table-cell">Location</div>
                    <div class="f3-table-cell">Workout Title</div>
                    <div class="f3-table-cell">Day of the Week</div>
                    <div class="f3-table-cell">Start Time</div>
                    <div class="f3-table-cell">End Time</div>
                    <div class="f3-table-cell">Workout Style</div>
                    <div class="f3-table-cell">AOQ</div>
                </div>
            </div>
            <div class="f3-table-body">
        <?php
        $index = 0;

        foreach ($data as $row) {
            $index++;
            if ($index == 1) continue;

            ?>
                <div class="f3-table-row">
                        <div class="f3-table-cell f3-table-cell-location" data-label="Location"><?= $this->assemble_map_link($row) ?></div>
                        <div class="f3-table-cell f3-table-cell-workout" data-label="Workout Title"><?= $this->assemble_post_link($row) ?></div>
                        <div class="f3-table-cell f3-table-cell-day" data-layout="Day of the Week"><?= $row[DAY_OF_WEEK] ?></div>
                        <div class="f3-table-cell f3-table-cell-start" data-label="Start Time"><?= $row[START_TIME] ?></div>
                        <div class="f3-table-cell f3-table-cell-end" data-label="End Time"><?= $row[END_TIME] ?></div>
                        <div class="f3-table-cell f3-table-cell-style" data-label="Workout Style"><?= $row[WORKOUT_STYLE] ?></div>
                        <div class="f3-table-cell f3-table-cell-twitter" data-label="AOQ"><?= $this->assemble_twitter_link($row) ?></div>
                </div>
            <?php 
        }
        ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
     }

     public function f3_render_map( $atts ) {
        $options = get_option( 'f3-options-name' );
        $f3_map_selector = $options['f3-css-selector'];
        ?>
        <div class="f3-map-wrapper">
            <div id="f3-map" class="f3-map" data-selector="<?php echo $f3_map_selection ?>"></div>
        </div>
        <div class="f3-map-details">
            <div>
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
                        <div class="f3-table-cell">
                            <span id="f3-map-detail-sunday"></span>
                        </div>
                        <div class="f3-table-cell">
                            <span id="f3-map-detail-monday"></span>
                        </div>
                        <div class="f3-table-cell">
                            <span id="f3-map-detail-tuesday"></span>
                        </div>
                        <div class="f3-table-cell">
                        <span id="f3-map-detail-wednesday"></span>
                        </div>
                        <div class="f3-table-cell">
                        <span id="f3-map-detail-thursday"></span>
                        </div>
                        <div class="f3-table-cell">
                        <span id="f3-map-detail-friday"></span>
                        </div>
                        <div class="f3-table-cell">
                        <span id="f3-map-detail-saturday"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            jQuery(function() {
                F3_InitMap('<?php echo $f3_map_selector ?>');
            });
        </script>

        <?php
        
     }
 }