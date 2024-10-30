<?php
/**
 * ACCP Core Utility Functions.
 *
 * @package    ARS_Constellation_Client_Portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACCP Core Utility Functions.
 */
class ACCP_Utility_Functions {

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
	 * Initialize the class
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'ARS_CONSTELLATION_CLIENT_PORTAL_PRO' ) ) {

			// Check for Pro version.
			$this->version     = ARS_CONSTELLATION_CLIENT_PORTAL_PRO;
			$this->plugin_name = 'ars-constellation-client-portal-pro';

		} elseif ( defined( 'ARS_CONSTELLATION_CLIENT_PORTAL' ) ) {

			// Core version.
			$this->version     = ARS_CONSTELLATION_CLIENT_PORTAL;
			$this->plugin_name = 'ars-constellation-client-portal';

		} else {

			// Fall back to defaults if nothing is defined.
			$this->version     = '1.0.0';
			$this->plugin_name = 'ars-constellation-client-portal';

		}
	}

	/**
	 * Prep integers for use.
	 *
	 * @param string $input_string - The string to sanitize.
	 */
	public function accp_sanitize_integers( $input_string ) {

		// Strip out non-numeric characters, except "-" that
		// may have found their way into the input.
		$prepped_string = preg_replace( '/[^\d-]+/', '', $input_string );

		if ( '' !== $prepped_string ) {

			// Cast the remaining characters as an int.
			$prelim_int = (int) $prepped_string;

			// Redundant sanitization.
			$integer = filter_var( $prelim_int, FILTER_SANITIZE_NUMBER_INT );

		} else {

			$integer = '';

		}

		return $integer;
	}


	/**
	 * Get the wp-content directory name.
	 *
	 * Example return: 'wp-content'.
	 */
	public function accp_get_wp_content_dir_name() {

		$wp_content_dir_name = basename( WP_CONTENT_DIR );

		if ( ! $wp_content_dir_name ) {
			return;
		}

		return $wp_content_dir_name;
	}


	/**
	 * Get the WP uploads directory name.
	 *
	 * Example return: 'uploads'.
	 */
	public function accp_get_wp_uploads_dir_name() {

		$wp_uploads_dir = wp_get_upload_dir();

		if ( ! $wp_uploads_dir || ! $wp_uploads_dir['basedir'] ) {
			return;
		}

		$wp_uploads_dir_name = basename( $wp_uploads_dir['basedir'] );

		return $wp_uploads_dir_name;
	}


	/**
	 * Get the accp-clientfiles path.
	 *
	 * Example return: '/var/www/html/wp-content/uploads/accp-clientfiles'.
	 */
	public function accp_get_clientfiles_path() {

		$clientfiles_dir_name = '/accp-clientfiles';
		$wp_uploads_dir       = wp_get_upload_dir();

		if ( ! $wp_uploads_dir || ! $wp_uploads_dir['basedir'] ) {
			return;
		}

		$wp_uploads_path = $wp_uploads_dir['basedir'];
		$clientfiles_dir = $wp_uploads_path . $clientfiles_dir_name;

		return $clientfiles_dir;
	}


	/**
	 * Get the accp-clientfiles dir url.
	 *
	 * Example return: 'https://www.example.com/wp-content/uploads/accp-clientfiles'.
	 */
	public function accp_get_clientfiles_upload_dir_url() {

		$clientfiles_dir_name = '/accp-clientfiles';
		$wp_uploads_dir       = wp_get_upload_dir();

		if ( ! $wp_uploads_dir || ! $wp_uploads_dir['baseurl'] ) {
			return;
		}

		$wp_uploads_url  = $wp_uploads_dir['baseurl'];
		$clientfiles_url = $wp_uploads_url . $clientfiles_dir_name;

		return $clientfiles_url;
	}


	/**
	 * Sanitize URL slug names.
	 *
	 * Only allow alphanumeric characters,
	 * hypens, and underscores.  Also, remove
	 * spaces.
	 *
	 * @param string $slug - The slug to sanitize.
	 *
	 * @return string $slug - The sanitized slug.
	 */
	public function sanitize_url_slug_name( $slug ) {

		if ( ! $slug ) {
			return;
		}

		/**
		 * Remove all spaces.
		 */
		$slug = trim( str_replace( ' ', '', $slug ?? '' ) );

		/**
		 * Make lowercase.
		 */
		$slug = strtolower( $slug );

		/**
		 * Remove unpermitted characters.
		 */
		$slug = preg_replace( '/[^\w-]/', '', $slug );

		return $slug;
	}


	/**
	 * Check slug for uniqueness, and
	 * verify that it is not already in use
	 * by another post type.
	 *
	 * @param string $slug - The slug to check.
	 *
	 * @return bool $is_unique - True if the slug is unique or false if not.
	 */
	public function check_if_post_type_slug_is_unique( $slug ) {

		$is_unique = true;

		if ( ! $slug ) {
			return $is_unique;
		}

		/**
		 * Get all post types.
		 */
		$post_types = get_post_types( array(), 'objects' );

		foreach ( $post_types as $post_type ) {

			/**
			 * Check the post type name.
			 */
			$name = $post_type->name;

			/**
			 * Return false if the post type name
			 * matches the slug being checked.
			 */
			if ( $name === $slug ) {

				$is_unique = false;

				return $is_unique;

			}

			/**
			 * Check the post type slug if it exists.
			 */
			if ( is_array( $post_type->rewrite ) ) {

				$post_type_slug = array_key_exists( 'slug', $post_type->rewrite ) ? $post_type->rewrite['slug'] : false;

				if ( $post_type_slug && false !== $post_type_slug ) {

					$post_type_slug = str_replace( '/', '', $post_type_slug ?? '' );

					/**
					 * Return false if the post type slug
					 * matches the slug being checked.
					 */
					if ( $post_type_slug === $slug ) {

						$is_unique = false;

						return $is_unique;

					}
				}
			}
		}

		return $is_unique;
	}


	/**
	 * Check if post type slug is a valid post type.
	 *
	 * @param string $slug - The slug to check.
	 *
	 * @return bool $is_valid_post_type - True if the slug is a valid post type or false if not.
	 */
	public function check_if_post_type_slug_is_valid_post_type( $slug ) {

		if ( ! $slug ) {
			return false;
		}

		/**
		 * Get all post types.
		 */
		$post_types = get_post_types( array(), 'objects' );

		foreach ( $post_types as $post_type ) {

			/**
			 * Check the post type name.
			 */
			$name = $post_type->name;

			/**
			 * Return false if the post type name
			 * matches the slug being checked.
			 */
			if ( sanitize_text_field( strtolower( $slug ) ) === $name ) {

				$is_valid_post_type = true;

				return $is_valid_post_type;

			}
		}

		return false;
	}


	/**
	 * Check slug for uniqueness, and
	 * verify that it is not already in use
	 * by another taxonomy.
	 *
	 * @param string $slug - The slug to check.
	 *
	 * @return bool $is_unique - True if the slug is unique or false if not.
	 */
	public function check_if_taxonomy_slug_is_unique( $slug ) {

		$is_unique = true;

		if ( ! $slug ) {
			return $is_unique;
		}

		/**
		 * Get all post types.
		 */
		$taxonomies = get_taxonomies( array(), 'objects' );

		foreach ( $taxonomies as $term ) {

			/**
			 * Check the term name.
			 */
			$name = $term->name;

			/**
			 * Return false if the term name
			 * matches the slug being checked.
			 */
			if ( $name === $slug ) {

				$is_unique = false;

				return $is_unique;

			}

			/**
			 * Check the term slug if it exists.
			 */
			if ( is_array( $term->rewrite ) ) {

				$term_slug = array_key_exists( 'slug', $term->rewrite ) ? $term->rewrite['slug'] : false;

				if ( $term_slug && false !== $term_slug ) {

					$term_slug = str_replace( '/', '', $term_slug ?? '' );

					/**
					 * Return false if the term slug
					 * matches the slug being checked.
					 */
					if ( $term_slug === $slug ) {

						$is_unique = false;

						return $is_unique;

					}
				}
			}
		}

		return $is_unique;
	}


	/**
	 * Check if this is the pro plugin.
	 *
	 * @param string $plugin_name - The name of the current plugin.
	 *
	 * @return bool $is_pro - True if this is the pro plugin, false if not.
	 */
	public function is_pro_plugin( $plugin_name ) {

		$is_pro = false;

		if ( ! $plugin_name ) {
			return $is_pro;
		}

		$plugin_name = strtolower( $plugin_name );

		if ( strpos( $plugin_name ?? '', 'pro' ) !== false ) {

			$is_pro = true;

		}

		return $is_pro;
	}
} // END ACCP_Utility_Functions
