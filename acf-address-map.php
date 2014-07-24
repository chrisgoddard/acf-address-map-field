<?php

/*
Plugin Name: Advanced Custom Fields: ACF Address Map
Plugin URI: http://www.chrisgoddard.me
Description: Full Address field with geo-coded map. Compatible ACF 4 & 5
Version: 1.0.3
Author: Chris Goddard
Author URI: www.chrisgoddard.me
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/



// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-address-map', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 



// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_address_map( $version ) {
	
	include_once('acf-address-map-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_address_map');	




// 3. Include field type for ACF4
function register_fields_address_map() {
	
	include_once('acf-address-map-v4.php');
	
}

add_action('acf/register_fields', 'register_fields_address_map');	



	
?>