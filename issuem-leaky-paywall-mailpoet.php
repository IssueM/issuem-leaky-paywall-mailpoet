<?php
/**
 * Main PHP file used to for initial calls to IssueM's Leak Paywall classes and functions.
 *
 * @package IssueM's Leak Paywall - MailPoet
 * @since 1.0.0
 */
 
/*
Plugin Name: IssueM's Leaky Paywall - MailPoet
Plugin URI: http://issuem.com/
Description: A premium leaky paywall add-on for WordPress and IssueM.
Author: IssueM Development Team
Version: 1.0.0
Author URI: http://issuem.com/
Tags:
*/

//Define global variables...
if ( !defined( 'ISSUEM_STORE_URL' ) )
	define( 'ISSUEM_STORE_URL',				'http://issuem.com' );
	
define( 'ISSUEM_LP_MP_NAME', 		'Leaky Paywall - Subscriber MailPoet' );
define( 'ISSUEM_LP_MP_SLUG', 		'issuem-leaky-paywall-mailpoet' );
define( 'ISSUEM_LP_MP_VERSION', 	'1.0.0' );
define( 'ISSUEM_LP_MP_DB_VERSION', 	'1.0.0' );
define( 'ISSUEM_LP_MP_URL', 		plugin_dir_url( __FILE__ ) );
define( 'ISSUEM_LP_MP_PATH', 		plugin_dir_path( __FILE__ ) );
define( 'ISSUEM_LP_MP_BASENAME', 	plugin_basename( __FILE__ ) );
define( 'ISSUEM_LP_MP_REL_DIR', 	dirname( ISSUEM_LP_MP_BASENAME ) );

/**
 * Instantiate Pigeon Pack class, require helper files
 *
 * @since 1.0.0
 */
function issuem_leaky_paywall_mailpoet_plugins_loaded() {
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'issuem/issuem.php' ) )
		define( 'ISSUEM_ACTIVE_LP_MP', true );
	else
		define( 'ISSUEM_ACTIVE_LP_MP', false );
		
	if ( is_plugin_active( 'wysija-newsletters/index.php' ) ) {
	
		require_once( 'class.php' );
	
		// Instantiate the Pigeon Pack class
		if ( class_exists( 'IssueM_Leaky_Paywall_MailPoet' ) ) {
			
			global $dl_pluginissuem_leaky_paywall_mailpoet;
			
			$dl_pluginissuem_leaky_paywall_mailpoet = new IssueM_Leaky_Paywall_MailPoet();
			
			require_once( 'functions.php' );
				
			//Internationalization
			load_plugin_textdomain( 'issuem-lp-mp', false, ISSUEM_LP_MP_REL_DIR . '/i18n/' );
				
		}
	
	}

}
add_action( 'plugins_loaded', 'issuem_leaky_paywall_mailpoet_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init