<?php
/*
Plugin Name: Cart66 to Mailchimp
Plugin URI: http://howtononprofit.wordpress.com/2013/02/23/wordpress-plugin-cart66-to-mailchimp/
Description: Send customer emails to a Mailchimp list at the completion of a Cart66 transaction. Customer will receive a confirmation email from Mailchimp to opt-in. 
Author: John Daskovsky	
Version: 1.4
Author URI: http://howtononprofit.wordpress.com/

Copyright 2013  John Daskovsky

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

// Discover plugin path even if symlinked
if(!defined('CART66_TO_MAILCHIMP_PATH')) {
	$this_plugin_file = __FILE__;
	if (isset($plugin)) {
	  $this_plugin_file = $plugin;
	}
	elseif (isset($mu_plugin)) {
	  $this_plugin_file = $mu_plugin;
	}
	elseif (isset($network_plugin)) {
	  $this_plugin_file = $network_plugin;
	}
	define('CART66_TO_MAILCHIMP_PATH', WP_PLUGIN_DIR . '/' . basename(dirname($this_plugin_file)));
}

//Primary function of the plugin that sends the email to mailchimp
function cart66_to_mailchimp($orderInfo){
	require_once(CART66_TO_MAILCHIMP_PATH . "/mcinc/MCAPI.class.php");
		
	$apikey = get_option('cart66_to_mailchimp_apikey');
	$listId = get_option('cart66_to_mailchimp_listid');
		
	$api = new MCAPI($apikey);
	// By default this sends a confirmation email - you will not see new members
	// until the link contained in it is clicked!
	
	$merge_vars = array('FNAME'=>$orderInfo[bill_first_name], 'LNAME'=>$orderInfo[bill_last_name]);
	
	$retval = $api->listSubscribe( $listId, $orderInfo[email], $merge_vars);
}
add_action( 'cart66_after_order_saved' , 'cart66_to_mailchimp' );

//Create plugin Options page
function cart66_to_mailchimp_init()
{
	register_setting('cart66_to_mailchimp_options','cart66_to_mailchimp_apikey');
	register_setting('cart66_to_mailchimp_options','cart66_to_mailchimp_listid');
}
add_action('admin_init','cart66_to_mailchimp_init');

function cart66_to_mailchimp_option_page()
{
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Cart66 to Mailchimp Options Page</h2>
	<p>Welcome to the Cart66 to Mailchimp Integration Plugin.</p>
	<form action="options.php" method="post" id="cart66-to-mailchimp-options-form">
	<?php 
		settings_fields('cart66_to_mailchimp_options'); 
	?>
	<h3><label for="cart66_to_mailchimp_apikey">Mailchimp API Key: </label> <input
		type="text" id="cart66_to_mailchimp_apikey" name="cart66_to_mailchimp_apikey"
		value="<?php echo esc_attr( get_option('cart66_to_mailchimp_apikey') ); ?>" /></h3>
	<h3><label for="cart66_to_mailchimp_listid">Mailchimp List ID: </label> <input
		type="text" id="cart66_to_mailchimp_listid" name="cart66_to_mailchimp_listid"
		value="<?php echo esc_attr( get_option('cart66_to_mailchimp_listid') ); ?>" /></h3>
	<?php submit_button(); ?>
	</form>
	</div>
	<?php
}

function cart66_to_mailchimp_plugin_menu()
{
	add_options_page('Cart66 to Mailchimp','Cart66 to Mailchimp', 'manage_options', 'cart66-to-mailchimp-plugin', 'cart66_to_mailchimp_option_page');
}
add_action('admin_menu', 'cart66_to_mailchimp_plugin_menu');
	
//Uninstall Function
register_uninstall_hook(__FILE__,'cart66_to_mailchimp_uninstall');

function cart66_to_mailchimp_uninstall()
{
	delete_option( 'cart66_to_mailchimp_apikey' );
	delete_option( 'cart66_to_mailchimp_listid' );
}

?>
