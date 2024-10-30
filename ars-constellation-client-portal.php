<?php
/**
 * Constellation Client Portal WordPress plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Constellation Client Portal
 * Plugin URI:        https://adrianrodriguezstudios.com/constellation-client-portal/
 * Description:       Create private pages for each of your clients, post private files, and protect your client files from unauthorized users and search engines.  <strong>Important:</strong> All Site-level File Protection features will cease to function if the plugin is disabled or uninstalled.
 * Version:           1.9.0
 * Author:            ARS
 * Author URI:        https://adrianrodriguezstudios.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       constellation-client-portal
 * Domain Path:       /languages
 *
 * Copyright: (c) 2020-2023, Adrian Rodriguez Studios LLC (info@adrianrodriguezstudios.com)
 *
 * @package    ARS_Constellation_Client_Portal
 * @author     Adrian Rodriguez Studios LLC
 * @link       https://adrianrodriguezstudios.com
 * @since      1.0.0
 * @copyright  Copyright (c) 2020-2023, Adrian Rodriguez Studios LLC
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/gpl-3.0.html>.
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Stop and display an admin message
 * if another version of the plugin is active
 * when attempting to activate this plugin.
 */
if ( function_exists( 'activate_ars_constellation_client_portal' ) ) {

	$notice = '<p style="color: #3c434a; font-size: 13px; font-family: sans-serif; line-height: 1.5;"><strong>Duplicate Plugins:</strong> It looks like you already have another version of Constellation Client Portal active.  Please deactivate that plugin before attempting to activate this one.</p>';

	exit( wp_kses_post( $notice ) );

} else {

	/**
	 * Current plugin name and version.
	 */
	define('ACCP_PLUGIN_NAME', 'ARS_CONSTELLATION_CLIENT_PORTAL');
	define('ACCP_PLUGIN_VERSION', '1.9.0'); // Change the version in the header as well.
	define( ACCP_PLUGIN_NAME, ACCP_PLUGIN_VERSION );
	define( 'ACCP_PLUGIN_FILE_NAME', __FILE__ );
	define( 'ACCP_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
	define( 'ACCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );


	/**
	 * Proceed with the install or uninstall
	 * if another version of the plugin is not
	 * already active.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/ars-constellation-client-portal-install-uninstall.php';
}
