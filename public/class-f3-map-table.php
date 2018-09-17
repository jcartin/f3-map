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
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-pig-array.php';

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
            wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?callback=F3SetupMap&key=' . $api_key, array( 'jquery', $this->plugin_name ), '', false );
         }
         
         wp_enqueue_script( 'underscore', '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js', array( 'underscore', $this->plugin_name ), '', false );         
     }

     function assemble_marker_link( $row ) {
         return '<a href="javascript:CenterAO(' . $row[LATITUDE] . ', ' . $row[LONGITUDE] . ')" '
            . 'title="Click to view AO on map." '
            . 'alt="View AO on map." '
            . 'class="ao-marker">'
                . '<img src="' . plugins_url('public/imgs/marker.png', dirname(__FILE__)) . '" height="18" width="18" title="Click to view AO on map." />'
            . '</a>';
     }

     function assemble_map_link( $row ) {

        # we should only echo an anchor if the url is set.
        $anchor = $row[GOOGLE_MAPS_LINK];

        if ($anchor) {
            return '<a href="' . $row[GOOGLE_MAPS_LINK] . '"' 
                . ' title="Click to show AO location in google maps (separate tab)"'
                . ' target="_blank" class="ao-map-link"'
                . '>' 
                . $row[LOCATION] 
                . '</a>';
        } else {
            return $row[LOCATION];
        }
        
     }
     
     function assemble_twitter_link($row) {
         $handle = $row[TWITTER_HANDLE];
         $name = $row[TWITTER_NAME];

         if ($handle) {
            return '<a title="Click to visit twitter" target="_blank" href="https://twitter.com/' . $row[TWITTER_HANDLE] . '">' . $row[TWITTER_NAME] . '</a>';
         }

         return $name;
     }

     function assemble_post_link($row) {
         $link = $row[POST_LINK];

         if ($link) {
            return '<a title="Click to visit backblasts for this AO" href="' . $row[POST_LINK] . '">' . $row[WORKOUT_NAME] . '</a>';;
         }

         return $row[WORKOUT_NAME];
     }

     function get_marker_url($row) {
         $style = $row[WORKOUT_STYLE];

         // could be bootcamp, run group, kettlebell, ruck, group run/core, group run/ruck
         if (preg_match('/run/i', $style) == 0) {
            return plugins_url('public/imgs/marker-blue.png', dirname(__FILE__));
         }

        return plugins_url('public/imgs/marker-red.png', dirname(__FILE__));
     }

     public function f3_render_table( $atts ) {
        $this->enqueue_scripts();
        $this->enqueue_styles();

        $options = get_option( 'f3-options-name' );
        $f3_map_selector = $options['f3-css-selector'];

        // explanation of shortcode attributes:
        // spreadsheet: the unauthenticated url for the google spreadsheet to load.
        // title: the bolded table title
        $atts = shortcode_atts( array(
            'spreadsheet' => '', 
            'lat' => 'null', 
            'lng' => 'null', 
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
        <div class="f3-table-title">
            <?php echo $atts['title'] ?>
        </div> 
        <div class="f3-table">
            <div class="f3-table-heading">
                <div class="f3-table-row">
                    <div class="f3-table-cell">&nbsp;</div>
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

            // wrap the CSV row in a PigArray instance. This will help ensure that the indexers 
            // will safely return empty strings for out of bounds access.
            $row = new PigArray($row, '');

            ?>
                <div class="f3-table-row ao-location" data-lat="<?= $row[LATITUDE] ?>" 
                            data-lng="<?= $row[LONGITUDE] ?>" 
                            data-location="<?= $row[LOCATION] ?>" 
                            data-workout="<?= $row[WORKOUT_NAME] ?>" 
                            data-line1="<?= $row[STREET] ?>" 
                            data-line2="<?= $row[CITY] . ', ' . $row[STATE] . ' ' . $row[ZIP] ?>" 
                            data-day="<?= $row[DAY_OF_WEEK] ?>" 
                            data-starttime="<?= $row[START_TIME] ?>" 
                            data-endtime="<?= $row[END_TIME] ?>" 
                            data-style="<?= $row[WORKOUT_STYLE] ?>">
                        <div class="f3-table-cell f3-table-cell-marker" data-label="" data-lat="<?= $row[LATITUDE] ?>" data-lng="<?= $row[LONGITUDE] ?>">
                            <img src="<?= $this->get_marker_url($row) ?>" height="18" width="18" title="Click to view AO on map." />
                        </div>
                        <div class="f3-table-cell f3-table-cell-location" data-label="Location"><?= $this->assemble_map_link($row) ?></div>
                        <div class="f3-table-cell f3-table-cell-workout" data-label="Workout Title"><?= $this->assemble_post_link($row) ?></div>
                        <div class="f3-table-cell f3-table-cell-day" data-label="Day of the Week"><?= $row[DAY_OF_WEEK] ?></div>
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
 }