<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/includes
 * @author     Adrian Rodriguez
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin deactivation class.
 */
class ARS_Constellation_Client_Portal_Deactivator {

	/**
	 * Process on plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		/**
		 * Flush the rewrite rules.
		 *
		 * Unset the rewrite rule associated with the
		 * accp-clientfiles dir (needed as using only flush_rewrite_rules
		 * will not work during the deactivation process), then
		 * flush the rewrite rules.
		 */
		global $wp_rewrite;

		$accp_admin = new ARS_Constellation_Client_Portal_Admin( ACCP_PLUGIN_VERSION, ACCP_PLUGIN_NAME );
		$regex_path = $accp_admin->accp_get_clientfile_dir_rewrite_regex_path();

		unset( $wp_rewrite->non_wp_rules[ $regex_path ] );

		flush_rewrite_rules();

		/**
		 * Clear the automated email cron job if it is scheduled.
		 */
		if ( wp_next_scheduled( 'accp_automated_email_cron' ) ) {

			wp_clear_scheduled_hook( 'accp_automated_email_cron' );

		}
	}
}
