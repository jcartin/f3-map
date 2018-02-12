<?php 

/**
 * 
 */

 // if uninstall not called from wordpress then exit
 if (!defined('WP_UNINSTALL_PLUGIN')) {
     exit;
 }

 delete_option('wsc_options');