<?php 
/*
Plugin Name: Ticket List
Description: Show list ticket
Version: 1.0.0
Author: Rodrigo Teles
*/



header('Access-Control-Allow-Origin: *');  




/**
 * Call to Service API
 */
function ticket_list_request() {	
	$dateFrom = date('m/d/Y');		
	$url = 'http://travellogix.api.test.conceptsol.com/api/Ticket/Search';	
	$authorization = ticket_list_get_authorization();	
	$body = array(
		'Language' => 'ENG',
		'Currency' => 'USD',
		'destination' => 'MCO',
		'DateFrom' => $dateFrom,
		'DateTo' => date('m/d/Y', strtotime("$dateFrom +1 day")),
		'Occupancy' => array(
			'AdultCount' => 1,
			'ChildCount' => 1,
			'ChildAges' => array(10)
		)
	);

	$body_json = json_encode($body);

	$response = wp_remote_request( esc_url_raw($url), array(
		'method' => 'POST',
		'headers' => array(
			'Content-Type' => 'application/json',
			'Authorization' => $authorization
		),
		'body' => $body_json
	) );

	$response_body = json_decode(wp_remote_retrieve_body( $response ));	

	$data = [];

	$data['code'] = $response_body->Code;

	foreach($response_body->Result as $key => $value) {
		$data['result'][$key]['Destination'] = $value->TicketInfo->Destination->Code;
		$data['result'][$key]['Name'] = $value->TicketInfo->Name;

		for($i = 0; $i < count($value->TicketInfo->ImageList); $i++) {
			if ($value->TicketInfo->ImageList[$i]->Type === 'S') {
				$data['result'][$key]['Photos'][] = $value->TicketInfo->ImageList[$i];
			}
		}
	}

	return $data;
};

function ticket_list_get_authorization() {
	$url = 'http://travellogix.api.test.conceptsol.com/Token';
	$raw = 'grant_type=password&username=test1%40test2.com&password=Aa234567%21';
	
	$authorization_request = wp_remote_post( esc_url_raw( $url ), array(
		'body' => $raw
	));

	$response = json_decode( wp_remote_retrieve_body( $authorization_request ) );

	$token_type = $response->token_type;

	$bearer = $token_type . ' ' . $response->access_token;

	return $bearer;
}

function ticket_list_scripts() {
	wp_enqueue_style( 'ticket-list-style', plugin_dir_url( __FILE__ ) . 'ticket-list-style.css' );

	wp_enqueue_script( 'react', plugin_dir_url( __FILE__ ) . 'react/node_modules/react/umd/react.production.min.js', array(), false, true );
	wp_enqueue_script( 'react-app', plugin_dir_url( __FILE__ ) . 'react/dist/main.js', array(), false, true );
}
add_action( 'wp_enqueue_scripts', 'ticket_list_scripts' );






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