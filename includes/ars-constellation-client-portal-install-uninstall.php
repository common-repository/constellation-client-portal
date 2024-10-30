<?php
/**
 * Fired during plugin activation.
 *
 * Install and uninstall related functionality.
 *
 * @since      1.0.0
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Activation.
 * includes/class-ars-constellation-client-portal-activator.php
 */
function activate_ars_constellation_client_portal() {

	require_once plugin_dir_path( __FILE__ ) . 'class-ars-constellation-client-portal-activator.php';
	ARS_Constellation_Client_Portal_Activator::activate();
}

register_activation_hook( ACCP_PLUGIN_FILE_NAME, 'activate_ars_constellation_client_portal' );


/**
 * Plugin deactivation.
 * includes/class-ars-constellation-client-portal-deactivator.php
 */
function deactivate_ars_constellation_client_portal() {

	require_once plugin_dir_path( __FILE__ ) . 'class-ars-constellation-client-portal-deactivator.php';
	ARS_Constellation_Client_Portal_Deactivator::deactivate();
}

register_deactivation_hook( ACCP_PLUGIN_FILE_NAME, 'deactivate_ars_constellation_client_portal' );


/**
 * Core class
 */
require plugin_dir_path( __FILE__ ) . 'class-ars-constellation-client-portal.php';


/**
 * Add default Company Status options on plugin activation -
 * leave the options/statuses in place on plugin deactivation
 * in case the plugin is reactivated.
 */
function add_default_company_statuses() {

	$company_status_rows = get_option( 'accp_company_status_repeater' );
	$new                 = array();

	/**
	 * If there are no existing options from a previous activation just
	 * add the three default statuses.
	*/
	if ( empty( $company_status_rows ) ) {

		$new[] = array(
			'label'         => sanitize_text_field( 'Active' ),
			'value'         => sanitize_text_field( 'active' ),
			'status_action' => sanitize_text_field( 'vis_no_action' ),
		);

		$new[] = array(
			'label'         => sanitize_text_field( 'Pending' ),
			'value'         => sanitize_text_field( 'pending' ),
			'status_action' => sanitize_text_field( 'vis_no_action' ),
		);

		$new[] = array(
			'label'         => sanitize_text_field( 'Inactive' ),
			'value'         => sanitize_text_field( 'inactive' ),
			'status_action' => sanitize_text_field( 'vis_no_action' ),
		);

		update_option( 'accp_company_status_repeater', $new );

		return;

	}

	/**
	 * If there are existing statuses from a previous activation
	 * check for the existence of each default value in the saved options.
	 */
	if ( is_array( $company_status_rows ) && ! empty( $company_status_rows ) && null !== $company_status_rows ) {

		$existing_statuses = array();

		foreach ( $company_status_rows as $company_status_row ) {

			if ( 'active' === $company_status_row['value'] ) {

				$existing_statuses[] = 'active';

			}

			if ( 'inactive' === $company_status_row['value'] ) {

				$existing_statuses[] = 'inactive';

			}

			if ( 'pending' === $company_status_row['value'] ) {

				$existing_statuses[] = 'pending';

			}
		}

		/**
		 * Add the default 'active' status if not already present.
		 */
		if ( ! in_array( 'active', $existing_statuses, true ) ) {

			$new[] = array(
				'label'         => sanitize_text_field( 'Active' ),
				'value'         => sanitize_text_field( 'active' ),
				'status_action' => sanitize_text_field( 'vis_no_action' ),
			);

		}

		/**
		 * Add the default 'pending' status if not already present.
		 */
		if ( ! in_array( 'pending', $existing_statuses, true ) ) {

			$new[] = array(
				'label'                                => sanitize_text_field( 'Pending' ),
				'value'                                => 'pending',
				sanitize_text_field( 'status_action' ) => sanitize_text_field( 'vis_no_action' ),
			);

		}

		/**
		 * Add the default 'inactive' status if not already present.
		 */
		if ( ! in_array( 'inactive', $existing_statuses, true ) ) {

			$new[] = array(
				'label'         => sanitize_text_field( 'Inactive' ),
				'value'         => sanitize_text_field( 'inactive' ),
				'status_action' => sanitize_text_field( 'vis_no_action' ),
			);

		}

		/**
		 *  Combine the existing array values with the default array values.
		 */
		if ( ! empty( $new ) ) {

			$combined_array = array_merge( $new, $company_status_rows );

		} else {

			$combined_array = $company_status_rows;

		}

		/**
		 * Update the status list if the default($new) status array
		 * is not empty - not empty means that there are missing default
		 * statuses that need to be added.
		 */
		if ( ! empty( $new ) ) {

			update_option( 'accp_company_status_repeater', $combined_array );

		}
	}
}
register_activation_hook( ACCP_PLUGIN_FILE_NAME, 'add_default_company_statuses' );


/**
 * Start the plugin
 *
 * @since    1.0.0
 */
function run_ars_constellation_client_portal() {

	$plugin = new ARS_Constellation_Client_Portal();
	$plugin->run();
}
run_ars_constellation_client_portal();
