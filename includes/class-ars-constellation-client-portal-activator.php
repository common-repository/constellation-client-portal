<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation.
 */
class ARS_Constellation_Client_Portal_Activator {

	/**
	 * Plugin activation functionality.
	 */
	public static function activate() {

		/**
		 * Add file protection rewrite rules
		 * and flush the rules.  We define the rules
		 * once on activation, then on init with ARS_Constellation_Client_Portal_Admin.
		 * We also flush the rewrite rules once on plugin activation.
		 */
		self::accp_add_and_flush_rewrites_on_activate();

		/**
		 * Add an option to indicate that the plugin was just activated.
		 * This triggers plugin initialization functions.
		 */
		update_option( 'accp_plugin_activation', 'just-activated' );

		/**
		 * Check for enabled scheduled emails, and schedule
		 * the cron job if needed.  Needed in cases where the plugin
		 * was deactivated, then reactivated.
		 */
		if ( class_exists( 'ARS_Constellation_Client_Portal_Pro_Email' ) ) {

			$pro_emails = new ARS_Constellation_Client_Portal_Pro_Email( ACCP_PLUGIN_VERSION, ACCP_PLUGIN_NAME );

			$enabled_options = $pro_emails->accp_get_enabled_automated_email_option_names();

			if ( ! empty( $enabled_options ) && false !== $enabled_options ) {

				if ( ! wp_next_scheduled( 'accp_automated_email_cron' ) ) {

					$schedule = 'twicedaily';

					wp_schedule_event( time(), $schedule, 'accp_automated_email_cron' );

				}
			}
		}
	}


	/**
	 * Add the file protection rewrite rules
	 * and flush the rules.
	 */
	public static function accp_add_and_flush_rewrites_on_activate() {

		$accp_admin = new ARS_Constellation_Client_Portal_Admin( ACCP_PLUGIN_VERSION, ACCP_PLUGIN_NAME );

		/**
		 * Add the file protection rewrite rules.
		 */
		$accp_admin->accp_file_redirect_init();

		/**
		 * Flush the rewrite rules if the
		 * accp_plugin_activation option is
		 * still set.
		 */
		if ( 'just-activated' === get_option( 'accp_plugin_activation' ) ) {
			flush_rewrite_rules();
		}
	}
} // END ARS_Constellation_Client_Portal_Activator
