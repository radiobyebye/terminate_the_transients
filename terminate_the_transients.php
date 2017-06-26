<?php 

/*
Plugin Name: Terminate the Transients
Plugin URI:  http://omgtechnical.com
Description: Deletes all transient data stored in the WP database
Version:     1.0
Author:      Chase Townsend
Author URI:  http://omgtechnical.com
License:     GPL2 etc
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/




add_action('admin_menu', 'ttt_settings_menu');

function ttt_settings_menu() {
	add_menu_page('Terminate the Transients Settings', 'Terminate the Transients', 'manage_options', 'ttt-admin', 'ttt_settings_page', 'dashicons-hammer');
}



function ttt_settings_page() {
  //check for permissions
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have permission to access this page')    );
  }
  echo '<div class="wrap"><h2>Terminate the Transients!</h2>';
  ttt_get_transients();
  $currentTransientCount = ttt_get_transients();
  echo '<br><p>Total transients in WP database : ' . $currentTransientCount;

  // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['delete_button']) && check_admin_referer('delete_button_clicked')) {
    // the button has been pressed AND we've passed the security check
    ttt_delete_transients();
  }

  echo '<form action="admin.php?page=ttt-admin" method="post">';

  wp_nonce_field('delete_button_clicked');
  echo '<input type="hidden" value="true" name="delete_button" />';
  submit_button('Terminate the Transients!');
  echo '</form>';

    // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['generate_button']) && check_admin_referer('generate_button_clicked')) {
    // the button has been pressed AND we've passed the security check
    ttt_generate_transients();
  }

  echo '<form action="admin.php?page=ttt-admin" method="post">';

  wp_nonce_field('generate_button_clicked');
  echo '<input type="hidden" value="true" name="generate_button" />';
  submit_button('Generate the Transients!');
  echo '</form>';

  echo '</div>';
}

//Gets current total of transients in database
function ttt_get_transients() {
	global $wpdb;
	$totalTransients = $wpdb->get_var( "SELECT COUNT(*) FROM `wp_options` WHERE `option_name` LIKE ('%\_transient\_%');");
	return $totalTransients;
}

//Function to generate a bunch of transients so we can verify that the plugin does what it should do. 
function ttt_generate_transients(){
	$createRecords = 0;
	while ($createRecords < 100){
	$transientName = 'oh_boy' .  $createRecords;
	set_transient( $transientName, 'Pretty Awesome', 28800 ); // Site Transient
    $createRecords += 1;
	}
}

//Deletes the transients 
function ttt_delete_transients() {
	global $wpdb;
	$wpdb->query( "DELETE FROM `wp_options` WHERE `option_name` LIKE ('%\_transient\_%')", OBJECT );
}

