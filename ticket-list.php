<?php 
/*
Plugin Name: Ticket List
Description: Show list ticket
Version: 1.0.0
Author: Rodrigo Teles
*/



header('Access-Control-Allow-Origin: *');  



/**
 *  WP Shotcode
 */
function ticket_list( $atts ) {
	return '<div id="ticket-list-details">***</div>';
}
add_shortcode( 'ticket-list', 'ticket_list' );

/**
 * Register a new endpoint that accepts the GET method
 */
add_action( 'rest_api_init', function() {
	register_rest_route( 'ticket-list/v1', '/test', array(
		'method' => 'GET',
		'callback' => 'ticket_list_request'
	) );
} );

 ?>