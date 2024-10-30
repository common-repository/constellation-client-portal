<?php
/**
 * ACCP Pro Direct File Access Checks
 *
 * This class is used to verify authorization when files in the
 * client files directory are accessed directly via URL.
 *
 * This file contains the core checks only.  If this is the pro
 * version, additional pro checks are handled by the
 * ARS_Constellation_Client_Portal_Core_File_Checks class.
 *
 * @package    ARS_CONSTELLATION_CLIENT_PORTAL
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez Studios <dev@adrianrodriguezstudios.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACCP Pro Direct File Access Checks
 */
class ARS_Constellation_Client_Portal_Core_File_Checks {

	/**
	 * Plugin ID
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Plugin Version
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The class construct.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/**
		 * Check for Pro version.
		 */
		if ( defined( 'ARS_CONSTELLATION_CLIENT_PORTAL_PRO' ) ) {

			$this->version     = ARS_CONSTELLATION_CLIENT_PORTAL_PRO;
			$this->plugin_name = 'ars-constellation-client-portal-pro';

			/**
			 * Check for Core version.
			 */
		} elseif ( defined( 'ARS_CONSTELLATION_CLIENT_PORTAL' ) ) {

			$this->version     = ARS_CONSTELLATION_CLIENT_PORTAL;
			$this->plugin_name = 'ars-constellation-client-portal';

			/**
			 * Fall back to a generic plugin name and version.
			 */
		} else {

			$this->version     = '1.0.0';
			$this->plugin_name = 'ars-constellation-client-portal';

		}
	}


	/**
	 * Direct file access base checks.
	 *
	 * @param string $request_uri - The file URL.
	 */
	public function accp_direct_file_access_base_checks( $request_uri ) {

		if ( ! $request_uri ) {
			return false;
		}

		$user    = wp_get_current_user();
		$user_id = $user->ID;

		$admin             = new ARS_Constellation_Client_Portal_Admin( $this->plugin_name, $this->version );
		$dir_name_from_uri = $admin->get_company_dir_name_from_url( $request_uri );

		if ( false !== $dir_name_from_uri && ! empty( $dir_name_from_uri ) ) {

			$company_id_from_uri = $admin->get_company_id_by_company_dir_name( $dir_name_from_uri );

			if ( ! $company_id_from_uri || empty( $company_id_from_uri ) ) {
				return false;
			}

			$authorization       = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );
			$check_authorization = $authorization->verify_direct_file_authorization( $company_id_from_uri, $user_id );

			/**
			 * Return true if the core direct file authorization is true.
			 */
			if ( true === $check_authorization ) {
				return true;
			}
		}

		return false;
	}
} // END ARS_Constellation_Client_Portal_Core_File_Checks
