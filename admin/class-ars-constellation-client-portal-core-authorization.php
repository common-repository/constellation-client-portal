<?php
/**
 * Core authorization functionality.
 *
 * @package    ARS_CONSTELLATION_CLIENT_PORTAL
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez Studios <dev@adrianrodriguezstudios.com>
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core authorization class.
 */
class ARS_Constellation_Client_Portal_Core_Authorization {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin utility functions.
	 *
	 * @access   private
	 * @var      object   $accp_utility_functions    Plugin utility functions class.
	 */
	private $accp_utility_functions;

	/**
	 * The class construct.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name            = $plugin_name;
		$this->version                = $version;
		$this->accp_utility_functions = new ACCP_Utility_Functions();
	}


	/**
	 * Core verification of page and post authorization.
	 *
	 * @param int $post_id - the post ID of the page or post to verify.
	 * @param int $user_id - the user ID of the current user.
	 * @return bool $authorized true|false - true if authorized, and false if not authorized.
	 */
	public function verify_page_or_post_authorization( $post_id, $user_id ) {

		$authorized = false;

		if ( ! $post_id || ! $user_id ) {
			return $authorized;
		}

		/**
		 * Return false if the user is not logged in.
		 */
		if ( ! is_user_logged_in() ) {
			return $authorized;
		}

		/**
		 * Return true if the user is an admin.
		 */
		if ( current_user_can( 'manage_options' ) ) {

			$authorized = true;

			return $authorized;

		}

		/**
		 * Check the user status (client_status user meta) and return
		 * false if the status is "inactive" or "pending."
		 */
		$user_status = $this->check_authorization_based_on_user_status( $user_id );

		if ( ! $user_status || false === $user_status ) {

			$authorized = false;

			return $authorized;

		}

		/**
		 * Check if this is a Global Access page.
		 *
		 * Only valid for Client Page post types,
		 * and not Client Files, Client Invoices, or
		 * direct file access.
		 */
		$is_global_page = $this->is_global_company_page( $post_id );

		if ( true === $is_global_page ) {

			$authorized = true;

			return $authorized;

		}

		/**
		 * Check the companies assigned to the user against
		 * the company assigned to the post.
		 */
		if ( get_post_meta( $post_id, 'accp_user', true ) ) {

			$post_assigned_company = (int) get_post_meta( $post_id, 'accp_user', true );

			/**
			 * Check the main company that the
			 * user is assigned to.
			 */
			$user_company_id = $this->get_user_assigned_company( $user_id );

			if ( $user_company_id ) {

				/**
				 * Return true if the user main company ID
				 * equals the post company ID.
				 */
				if ( $post_assigned_company === $user_company_id ) {

					$authorized = true;

					return $authorized;

				}
			}

			/**
			 * Check if the user is assigned to additional companies.
			 */
			$additional_user_companies = $this->get_additional_user_assigned_companies( $user_id );

			if ( $additional_user_companies && ! empty( $additional_user_companies ) ) {

				/**
				 * Return true if the post company ID is in the array
				 * of additional companies that the user is assigned to.
				 */
				if ( in_array( $post_assigned_company, $additional_user_companies, true ) ) {

					$authorized = true;

					return $authorized;

				}
			}
		}

		return $authorized;
	}


	/**
	 * Core verification of direct file access (by URL) authorization.
	 *
	 * @param int $company_id - the post ID of the company to verify.
	 * @param int $user_id - the user ID of the current user.
	 * @return bool $authorized true|false - true if authorized, and false if not authorized.
	 */
	public function verify_direct_file_authorization( $company_id, $user_id ) {

		$authorized = false;

		if ( ! $company_id || ! $user_id ) {
			return $authorized;
		}

		/**
		 * Return false if the user is not logged in.
		 */
		if ( ! is_user_logged_in() ) {
			return $authorized;
		}

		/**
		 * Return true if the user is an admin.
		 */
		if ( current_user_can( 'manage_options' ) ) {

			$authorized = true;

			return $authorized;

		}

		/**
		 * Check the user status (client_status user meta) and return
		 * false if the status is "inactive" or "pending."
		 */
		$user_status = $this->check_authorization_based_on_user_status( $user_id );

		if ( ! $user_status || false === $user_status ) {

			$authorized = false;

			return $authorized;

		}

		/**
		 * Check the main company that the
		 * user is assigned to.
		 */
		$user_company_id = $this->get_user_assigned_company( $user_id );

		if ( $user_company_id ) {

			/**
			 * Return true if the user main company ID
			 * equals the company ID parsed from the URI.
			 */
			if ( $company_id === $user_company_id ) {

				$authorized = true;

				return $authorized;

			}
		}

		/**
		 * Check if the user is assigned to additional companies.
		 */
		$additional_user_companies = $this->get_additional_user_assigned_companies( $user_id );

		if ( $additional_user_companies && ! empty( $additional_user_companies ) ) {

			/**
			 * Return true if the post company ID is in the array
			 * of additional companies that the user is assigned to.
			 */
			if ( in_array( (int) $company_id, $additional_user_companies, true ) ) {

				$authorized = true;

				return $authorized;

			}
		}

		return $authorized;
	}


	/**
	 * Get the main assigned company for a user.
	 *
	 * @param int $user_id - the ID of the user to check.
	 * @return int|false $company_id the ID of the assigned company if any.
	 */
	public function get_user_assigned_company( $user_id ) {

		$company_id = false;

		if ( get_user_meta( $user_id, 'client_company', true ) ) {

			$company_id = (int) get_user_meta( $user_id, 'client_company', true );

		}

		return $company_id;
	}


	/**
	 * Get the additional assigned companies for a user.
	 *
	 * @param int $user_id - The ID of the user to check.
	 * @return array $additional_companies - Array of comapny ID's that
	 * the user is assigned to, if any.
	 */
	public function get_additional_user_assigned_companies( $user_id ) {

		$additional_companies = array();

		if ( get_user_meta( $user_id, 'client_additional_company', true ) ) {

			$additional_companies = get_user_meta( $user_id, 'client_additional_company', true );

			if ( is_array( $additional_companies ) ) {

				$additional_companies = array_map( 'intval', $additional_companies );

			} else {

				/**
				 * This should not occur, but account for a string
				 * being returned.
				 */
				$additional_companies = (int) $additional_companies;

			}
		}

		return $additional_companies;
	}


	/**
	 * Check authorization based on the user status.
	 *
	 * @param int $user_id - the ID of the user to check.
	 */
	public function check_authorization_based_on_user_status( $user_id ) {

		$authorized = false;

		if ( ! $user_id ) {
			return $authorized;
		}

		$user_status = $this->get_user_status( $user_id );

		/**
		 * Return true if the user status is empty or active.
		 */
		if ( ! $user_status || 'active' === $user_status ) {

			$authorized = true;

			return $authorized;

		}

		return $authorized;
	}


	/**
	 * Get the user status.
	 *
	 * @param int $user_id - the ID of the user to check.
	 * @return string|false $user_status - status of the user.
	 */
	public function get_user_status( $user_id ) {

		if ( ! $user_id ) {
			return false;
		}

		$user_status = get_user_meta( $user_id, 'client_status', true ) ? get_user_meta( $user_id, 'client_status', true ) : '';

		return $user_status;
	}


	/**
	 * Get the company home page (if assigned)
	 * by the company ID.
	 *
	 * @param int $company_id - the post ID of the company.
	 * @return int|false $home_page_id - the post of the of the assigned company home page.
	 */
	public function get_company_home_page_by_company_id( $company_id ) {

		if ( ! $company_id ) {
			return false;
		}

		$home_page_id = get_post_meta( $company_id, 'accp_home_page', true ) ? (int) get_post_meta( $company_id, 'accp_home_page', true ) : false;

		return $home_page_id;
	}


	/**
	 * Check if this is a Global Access page
	 * (Client Pages only), and return authorization.
	 *
	 * @param int $post_id - the ID of the Client Page.
	 *
	 * @return bool $is_global_page - true if this is a global page.
	 */
	public function is_global_company_page( $post_id ) {

		if ( ! $post_id ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		$post_type = $post->post_type;

		/**
		 * Return false if the post type is not "accp_client_pages."
		 */
		if ( ! $post_type || 'accp_client_pages' !== $post_type ) {
			return false;
		}

		$is_global_page = get_post_meta( $post_id, 'accp_make_page_global', true );

		if ( $is_global_page && 'global' === $is_global_page ) {

			/**
			 * This is a global page, so return true.
			 */
			return true;

		}

		return false;
	}


	/**
	 * Check if this is a Global File post
	 * (Global File posts only), and return authorization.
	 *
	 * @param int $post_id - the ID of the Client File post.
	 *
	 * @return bool $is_global_file - true if this is a global file.
	 */
	public function is_global_file_post( $post_id ) {

		if ( ! $post_id ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		$post_type = $post->post_type;

		/**
		 * Return false if the post type is not "accp_client_pages."
		 */
		if ( ! $post_type || 'accp_global_file' !== $post_type ) {
			return false;
		}

		if ( 'accp_global_file' === $post_type ) {
			return true;
		}

		return false;
	}
} // END ARS_Constellation_Client_Portal_Core_Authorization
