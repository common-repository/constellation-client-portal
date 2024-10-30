<?php
/**
 * ARS_Constellation_Client_Portal_Admin Class
 *
 * @link       https://adrianrodriguezstudios.com
 * @since      1.0.0
 *
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ARS_Constellation_Client_Portal_Admin Class.
 */
class ARS_Constellation_Client_Portal_Admin {

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
	 * Plugin utitlity functions.
	 *
	 * @access   private
	 * @var      bool    $is_pro    True if this is the plugin, or false if not.
	 */
	private $is_pro;

	/**
	 * WP uploads directory constant.
	 *
	 * @access   private
	 * @var      string $wp_uploads_dir_const       The WP uploads directory.
	 */
	private $wp_uploads_dir_const;

	/**
	 * WP uploads base directory constant.
	 *
	 * @access   private
	 * @var      string $wp_uploads_base_dir_const      The WP uploads base directory.
	 */
	private $wp_uploads_base_dir_const;

	/**
	 * WP uploads base directory url constant.
	 *
	 * @access   private
	 * @var      string $wp_uploads_base_url_const      The WP uploads base directory url.
	 */
	private $wp_uploads_base_url_const;

	/**
	 * The class construct.
	 *
	 * @param string $plugin_name - The plugin name.
	 * @param string $version - The plugin version.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name            = $plugin_name;
		$this->version                = $version;
		$this->accp_utility_functions = new ACCP_Utility_Functions();
		$this->is_pro                 = $this->accp_utility_functions->is_pro_plugin( $this->plugin_name );

		/**
		 * Setup constants for the WP uploads dir and url
		 * to make this readily available in cases where using
		 * the wp_get_upload_dir() WP function causes problems
		 * (example: within the 'upload_dir' filter).
		 */
		$this->wp_uploads_dir_const      = wp_get_upload_dir();
		$this->wp_uploads_base_dir_const = $this->wp_uploads_dir_const['basedir'];
		$this->wp_uploads_base_url_const = $this->wp_uploads_dir_const['baseurl'];
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * Enqueue Admin Styles
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ars-constellation-client-portal-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui/1.11.1/themes/cupertino/jquery-ui.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '-selec2-css', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

		/**
		 * Conditionally enqueue Font Awesome CSS.
		 */
		if ( ! wp_style_is( 'font-awesome', 'registered' ) ) {

			wp_enqueue_style( 'accp-font-awesome-css', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/font-awesome/css/all.min.css', array(), $this->version, 'all' );

		}
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ars-constellation-client-portal-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_enqueue_script( $this->plugin_name . '-select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js', array( 'jquery' ), $this->version, false );
	}


	/**
	 * File Check Redirects
	 *
	 * Prevent direct access to clientfiles.
	 */
	public function accp_file_redirect_init() {

		/**
		 * Define the query string that coincides with the query_var.
		 */
		$page_with_query_string = 'index.php/?accp_direct_access=1';

		/**
		 * Prep the regex (Example: value should look like - '^wp-content\/uploads\/accp-clientfiles\/.*?\/.*?\..*?$').
		 */
		$regex_path = $this->accp_get_clientfile_dir_rewrite_regex_path();

		/**
		 * Add the rewrite rule for direct file access.
		 */
		add_rewrite_rule( $regex_path, $page_with_query_string, 'top' );
	}


	/**
	 * Get the accp-clientfiles dir rewrite regex path.
	 *
	 * @return string $regex_path - Example: '^wp-content\/uploads\/accp-clientfiles\/.*?\/.*?\..*?$'.
	 */
	public function accp_get_clientfile_dir_rewrite_regex_path() {

		$default_regex_path = '^wp-content\/uploads\/accp-clientfiles\/.*?\/.*?\..*?$';

		/**
		 * Don't specify the plugin directory name to account for different
		 * dir names based on the plugin tier - Basic, Pro, etc.
		 *
		 * Account for WP being installed in a sub directory, and the wp-content
		 * and/or /uploads directories being renamed.
		 */
		$wp_content_dir_name = $this->accp_utility_functions->accp_get_wp_content_dir_name();

		if ( ! $wp_content_dir_name ) {
			return $default_regex_path;
		}

		$accp_clientfiles_full_dir_path = $this->accp_utility_functions->accp_get_clientfiles_path();

		if ( ! $accp_clientfiles_full_dir_path ) {
			return $default_regex_path;
		}

		/**
		 * Constrain path to start with 'wp-content/...' as an example, but this is dynamic based on the environment.
		 */
		$accp_clientfiles_partial_dir_path         = strstr( $accp_clientfiles_full_dir_path, $wp_content_dir_name );
		$accp_clientfiles_partial_dir_path_slashed = str_replace( '/', '\/', $accp_clientfiles_partial_dir_path ?? '' );

		/**
		 * Prep the regex (Example: value should look like - '^wp-content\/uploads\/accp-clientfiles\/.*?\/.*?\..*?$').
		 */
		$regex_path = '^' . $accp_clientfiles_partial_dir_path_slashed . '\/.*?\/.*?\..*?$';

		return $regex_path;
	}



	/**
	 * Add ACCP query_vars.
	 *
	 * @param array $query_vars - The WP query vars array.
	 */
	public function accp_query_vars( $query_vars ) {

		$query_vars[] = 'accp_direct_access';

		return $query_vars;
	}


	/**
	 * Parse ACCP query_vars and handle actions.
	 *
	 * @param object $wp - The WP object.
	 */
	public function accp_parse_request( &$wp ) {

		if ( array_key_exists( 'accp_direct_access', $wp->query_vars ) ) {

			$request_uri = '';

			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				$request_uri = filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_URL );
			}

			if ( ! $request_uri || empty( $request_uri ) ) {
				return;
			}

			$core_file_checks = new ARS_Constellation_Client_Portal_Core_File_Checks( $this->plugin_name, $this->version );
			$base_checks      = $core_file_checks->accp_direct_file_access_base_checks( $request_uri );

			/**
			 * If this is the Basic tier, serve the file here if the base checks passed.
			 */
			if ( true !== $this->is_pro ) {

				if ( true === $base_checks ) {

					$checks_passed = true;

					/**
					 * Serve the requested file.
					 */
					$this->accp_load_requested_file( $request_uri, $checks_passed );
					exit;

				} else {

					/**
					 * Redirect to the home page.
					 */
					wp_safe_redirect( '/' );
					exit;

				}
			} else {

				/**
				 * Proceed through Pro checks.
				 */

				/**
				 * Exit if the pro check class does not exist.
				 */
				if ( ! class_exists( 'ARS_Constellation_Client_Portal_Pro_File_Checks' ) ) {

					wp_safe_redirect( '/' );
					exit;

				}

				$pro_admin           = new ARS_Constellation_Client_Portal_Pro_Admin( $this->plugin_name, $this->version );
				$is_global_files_dir = $pro_admin->check_if_uri_is_global_files_dir( $request_uri );
				$pro_check_class     = new ARS_Constellation_Client_Portal_Pro_File_Checks();
				$pro_checks          = $pro_check_class->accp_direct_file_access_pro_checks( $request_uri );

				if ( true === $is_global_files_dir ) {

					/**
					 * Global file checks.
					 */
					$global_file_authorized = $pro_check_class->accp_direct_global_file_access_pro_checks( $request_uri );

					if ( true === $global_file_authorized ) {

						$checks_passed = true;

						/**
						 * Serve the requested file.
						 */
						$this->accp_load_requested_file( $request_uri, $checks_passed );
						exit();

					} else {

						/**
						 * Redirect to the home page.
						 */
						wp_safe_redirect( '/' );
						exit;

					}
				} else {

					/**
					 * Standard client file checks.
					 */
					if ( true === $base_checks && true === $pro_checks ) {

						$checks_passed = true;

						/**
						 * Serve the requested file.
						 */
						$this->accp_load_requested_file( $request_uri, $checks_passed );
						exit();

					}

					/**
					 * Redirect to the home page.
					 */
					wp_safe_redirect( '/' );
					exit;

				}
			}
		}
	}

	/**
	 * Get the WP home path.
	 *
	 * Reconstruct the WP get_home_path()
	 * function for use here.
	 */
	public function accp_get_wp_home_path() {

		$home    = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {

			$server_script_filename = '';

			if ( isset( $_SERVER['SCRIPT_FILENAME'] ) ) {

				$server_script_filename = filter_input( INPUT_SERVER, 'SCRIPT_FILENAME', FILTER_SANITIZE_URL );

			}

			if ( $server_script_filename && ! empty( $server_script_filename ) ) {

				$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl );
				$pos                 = strripos( str_replace( '\\', '/', $server_script_filename ?? '' ), trailingslashit( $wp_path_rel_to_home ) );
				$home_path           = substr( $server_script_filename, 0, $pos );
				$home_path           = trailingslashit( $home_path );

			} else {

				$home_path = ABSPATH;

			}
		} else {

			$home_path = ABSPATH;

		}

		return str_replace( '\\', '/', $home_path ?? '' );
	}


	/**
	 * Load requested file.
	 *
	 * Used in conjunction with the direct access file check.
	 * Serves the requested file after checks have passed.
	 *
	 * @param string $request_uri - The file request URI.
	 * @param bool   $checks_passed - The result of the authorization check.
	 */
	private function accp_load_requested_file( $request_uri, $checks_passed = false ) {

		if ( true !== $checks_passed ) {
			return;
		}

		/**
		 * Set up the actual file path for readfile to work properly.
		 */
		$parse_url_array = wp_parse_url( esc_url_raw( $request_uri ) );

		if ( ! isset( $parse_url_array['path'] ) ) {
			return;
		}

		$file_path = $parse_url_array['path'];

		/**
		 * Account for wp-content being renamed in installer's environment.
		 */
		$home_path           = $this->accp_get_wp_home_path();
		$wp_content_dir_name = $this->accp_utility_functions->accp_get_wp_content_dir_name();
		$file                = $home_path . strstr( $file_path, $wp_content_dir_name );

		/**
		 * Set up the mime info.
		 */
		$mime = wp_check_filetype( $file );

		if ( false === $mime['type'] && function_exists( 'mime_content_type' ) ) {

			$mime['type'] = mime_content_type( $file );

		}

		if ( $mime['type'] ) {

			$mimetype = $mime['type'];

		} else {

			$mimetype = 'image/' . substr( $file, strrpos( $file, '.' ) + 1 );

		}

		/**
		 * Set mime type header.
		 */
		header( 'Content-Type: ' . $mimetype );

		/**
		 * Set file check header.
		 */
		header( 'File-Check: validated' );

		/**
		 * Serve the file.
		 *
		 * Dev Note: The $wp_filesystem get_contents
		 * method does not work as expected in this case.
		 * Use readfile to ensure proper functionality.
		 */
		readfile( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}


	/**
	 * Run plugin initialization functions.
	 */
	public function accp_plugin_initialize() {

		if ( is_admin() && 'just-activated' === get_option( 'accp_plugin_activation' ) ) {

			/**
			 * Check for the "client" role and add it
			 * if it does not already exist.
			 */
			$this->add_roles_on_plugin_activation();

			/**
			 * Flush the rewrite rules.
			 */
			flush_rewrite_rules();

			/**
			 * Clear the 'accp_plugin_activation' option,
			 * which should only exist temporarily, immediately
			 * after plugin activation.
			 */
			delete_option( 'accp_plugin_activation' );

		}
	}


	/**
	 * Add Client role when the plugin is activated.
	 */
	public function add_roles_on_plugin_activation() {

		global $wp_roles;

		$client_role = wp_roles()->is_role( 'client' );

		if ( true !== $client_role || ! $client_role ) {

			add_role( 'client', 'Client', get_role( 'subscriber' )->capabilities );

		}
	}


	/**
	 * Flush rewrite rules on plugin upgrade.
	 *
	 * @param object $upgrader_object - The upgrade object.
	 * @param array  $options - Array of bulk item update data.
	 */
	public function accp_upgrade_completed( $upgrader_object, $options ) {

		$accp_plugin_basename = dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/ars-constelation-client-portal.php';

		if ( 'update' === $options['action'] && 'plugin' === $options['type'] && isset( $options['plugins'] ) ) {

			foreach ( $options['plugins'] as $plugin ) {

				if ( $accp_plugin_basename === $plugin ) {

					flush_rewrite_rules();

				}
			}
		}
	}


	/**
	 * Add items to the plugin row meta.
	 *
	 * @param array  $links - Array of plugin links.
	 * @param string $file - Plugin file path.
	 *
	 * @return array $links - Array of plugin links.
	 *
	 * @hooked plugin_row_meta.
	 */
	public function accp_add_plugin_row_meta( $links, $file ) {

		if ( strpos( $file ?? '', plugin_basename( dirname( __DIR__ ) ) ) !== false ) {

			$new_links = array(
				'<a href="https://adrianrodriguezstudios.com/documentation-constellation-client-portal/" target="_blank">Documentation</a>',
			);

			$links = array_merge( $links, $new_links );

		}

		return $links;
	}


	/**
	 * Add additional menu items to the plugin action menu.
	 *
	 * @param array $actions - Array of plugin action links.
	 *
	 * @return array $actions - Array of plugin action links.
	 *
	 * @hooked plugin_action_links_{$plugin_file}.
	 *
	 * Dev Note: Phpcs flagged the use of unused params.  These are related to a WP hook,
	 * and the presence of these params is acceptable.
	 */
	public function accp_add_links_to_plugin_row_actions_menu( $actions ) {

		/**
		 * Add a Settings menu item.
		 */
		$admin_url                    = admin_url( 'admin.php?page=accp-settings' );
		$new_actions['accp_settings'] = '<a href="' . esc_url( $admin_url ) . '">Settings</a>';

		/**
		 * Add a Quick Start menu item.
		 */
		$quickstart_url                  = 'https://adrianrodriguezstudios.com/documentation-constellation-client-portal/#quick-start';
		$new_actions['accp_quick_start'] = '<a href="' . esc_url( $quickstart_url ) . '" target="_blank">Quick Start</a>';

		return array_merge( $new_actions, $actions );
	}


	/**
	 * Add an Upgrade menu item to the plugin action menu - core plugin only.
	 *
	 * @param array $actions - Array of plugin action links.
	 *
	 * @return array $actions - Array of plugin action links.
	 *
	 * @hooked plugin_action_links_{$plugin_file}.
	 *
	 * Dev Note: Phpcs flagged the use of unused params.  These are related to a WP hook,
	 * and the presence of these params is acceptable.
	 */
	public function accp_add_upgrade_link_to_plugin_row_actions_menu( $actions ) {

		/**
		 * Add a Upgrade menu item.
		 */
		$upgrade_url                 = 'https://adrianrodriguezstudios.com/constellation-client-portal/?utm_source=accp-upgrade-link';
		$new_actions['accp_upgrade'] = '<a href="' . esc_url( $upgrade_url ) . '" target="_blank" style="font-weight: bold;">Upgrade to Pro</a>';

		return array_merge( $new_actions, $actions );
	}


	/**
	 * AJAX - Delete the file associated with the clientfile post
	 * when the delete permanently button is clicked.
	 *
	 * If there is no file associated with the post the default
	 * WP functionality will handle the post deletion.
	 */
	public function accp_delete_file_on_post_delete() {

		/**
		 * Verify the nonce.
		 */
		if ( ! isset( $_POST['file_del_nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['file_del_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'clientfile_admin_nonce' ) ) {
			die();
		}

		if ( ! isset( $_POST['file_post_id'] ) ) {
			die();
		}

		$raw_post_id = (int) $_POST['file_post_id'];
		$post_id     = $this->accp_utility_functions->accp_sanitize_integers( $raw_post_id );

		if ( ! $post_id || empty( $post_id ) ) {
			die();
		}

		$post                = get_post( $post_id );
		$post_type           = $post->post_type;
		$accp_file           = get_post_meta( $post_id, 'accp_file', true );
		$included_post_types = array( 'accp_clientinvoice', 'accp_clientfile', 'accp_global_file' );

		if ( in_array( $post_type, $included_post_types, true ) ) {

			/**
			 * Check if the post has a file.
			 */
			if ( ! empty( $accp_file ) ) {

				$file_parse_array    = wp_parse_url( $accp_file['url'] );
				$accp_file_path      = isset( $file_parse_array['path'] ) ? $file_parse_array['path'] : '';
				$accp_file_full_path = untrailingslashit( get_home_path() ) . $accp_file_path;

				if ( $accp_file_path && ! empty( $accp_file_path ) && file_exists( $accp_file_full_path ) ) {

					/**
					 * If there is a file, delete it.
					 */
					wp_delete_file( $accp_file_full_path );

					/**
					 * Also delete the associated post.
					 */
					wp_delete_post( $post_id, true );

					echo 'The file and post were successfully deleted.';

				} else {

					/**
					 * If the file no longer exists,
					 * just delete the post.
					 */
					wp_delete_post( $post_id, true );

					echo 'No file associated with this post. The post was successfully deleted.';

				}
			} else {

				/**
				 * If the post doesn't have a file just delete the post.
				 */
				wp_delete_post( $post_id, true );

				echo 'The post was successfully deleted.';

			}
		}

		/**
		 * Remove any associated post notes if this is the Pro version.
		 */
		$this->accp_delete_post_notes_on_post_delete( $post_id );

		wp_die();
	}


	/**
	 * Pro - Delete associated post notes on post delete.
	 *
	 * @param int $post_id - The post ID.
	 */
	public function accp_delete_post_notes_on_post_delete( $post_id ) {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'ars-constellation-client-portal-pro' !== $this->plugin_name ) {
			return;
		}

		/**
		 * Query for related post notes.
		 *
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			'post_type'  => 'accp_post_note',
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query' => array(
				array(
					'key'   => 'related_post_id',
					'value' => $post_id,
				),

			),
		);

		$note_query = new WP_Query( $args );

		if ( $note_query->have_posts() ) :

			while ( $note_query->have_posts() ) :

				$note_query->the_post();
				$note_id = get_the_ID();

				/**
				 * Delete the note post.
				 */
				wp_delete_post( $note_id, true );

			endwhile;

			wp_reset_postdata();

		endif;
	}



	/**
	 * AJAX - Bulk delete the files associated with the clientfile
	 * post when the delete permanently button is clicked.
	 *
	 * If there is no file associated with the post, the default
	 * WP functionality will handle the post deletion.
	 */
	public function accp_bulk_delete_file_on_post_delete() {

		/**
		 * Verify the nonce.
		 */
		if ( ! isset( $_POST['bulk_delete_nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['bulk_delete_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'clientfile_admin_nonce' ) ) {
			die();
		}

		if ( ! isset( $_POST['del_file_post_id_json'] ) ) {
			die();
		}

		$file_ids            = json_decode( sanitize_text_field( wp_unslash( $_POST['del_file_post_id_json'] ) ) );
		$included_post_types = array( 'accp_clientinvoice', 'accp_clientfile', 'accp_global_file' );

		if ( $file_ids && ! empty( $file_ids ) ) {

			foreach ( $file_ids as $post_id ) {

				$post_id   = $this->accp_utility_functions->accp_sanitize_integers( $post_id );
				$post      = get_post( $post_id );
				$post_type = $post->post_type;
				$accp_file = get_post_meta( $post_id, 'accp_file', true );

				if ( in_array( $post_type, $included_post_types, true ) ) {

					/**
					 * Check if the post has file.
					 */
					if ( ! empty( $accp_file ) ) {

						$file_parse_array    = wp_parse_url( $accp_file['url'] );
						$accp_file_path      = isset( $file_parse_array['path'] ) ? $file_parse_array['path'] : '';
						$accp_file_full_path = untrailingslashit( get_home_path() ) . $accp_file_path;

						if ( $accp_file_path && ! empty( $accp_file_path ) && file_exists( $accp_file_full_path ) ) {

							/**
							 * If there is a file, delete it.
							 */
							wp_delete_file( $accp_file_full_path );

							/**
							 * Also delete the associated post.
							 */
							wp_delete_post( $post_id, true );

							echo 'The file and post were successfully deleted.';

						} else {

							/**
							 * If the file no longer exists,
							 * just delete the post.
							 */
							wp_delete_post( $post_id, true );

							echo 'The file was successfully deleted.';

						}
					} else {

						/**
						 * If the post doesn't have a file just delete the post.
						 */
						wp_delete_post( $post_id, true );

						echo 'The file was successfully deleted.';

					}
				}

				/**
				 * Pro - Delete any associated post notes.
				 */
				$this->accp_delete_post_notes_on_post_delete( $post_id );

			}
		}

		die();
	}


	/**
	 * AJAX - Bulk delete the files associated with the clientfile
	 * post when the empty trash button is clicked.
	 *
	 * If there is no file associated with the post the default
	 * WP functionality will handle the post deletion
	 */
	public function accp_bulk_delete_file_on_empty_trash() {

		/**
		 * Verify the nonce.
		 */
		if ( ! isset( $_POST['empty_trash_nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['empty_trash_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'clientfile_admin_nonce' ) ) {
			die();
		}

		/**
		 * Set up a query of the clienfile or accp_clientinvoice posts
		 * with a status of 'trash' so that we can quickly
		 * get all posts in trash.
		 */
		if ( ! isset( $_POST['post_type'] ) ) {
			die();
		}

		$post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'trash',
			'posts_per_page' => -1,
		);

		$trash_query = new WP_Query( $args );

		if ( $trash_query->have_posts() ) :

			while ( $trash_query->have_posts() ) :

				$trash_query->the_post();

				$post_id   = get_the_id();
				$post      = get_post( $post_id );
				$post_type = $post->post_type;
				$accp_file = get_post_meta( $post_id, 'accp_file', true );

				if ( 'accp_clientcompany' !== $post_type ) {

					/**
					 * Check if the post has a file.
					 */
					if ( ! empty( $accp_file ) ) {

						$file_parse_array    = wp_parse_url( $accp_file['url'] );
						$accp_file_path      = isset( $file_parse_array['path'] ) ? $file_parse_array['path'] : '';
						$accp_file_full_path = untrailingslashit( get_home_path() ) . $accp_file_path;

						if ( $accp_file_path && ! empty( $accp_file_path ) && file_exists( $accp_file_full_path ) ) {

							/**
							 * If there is a file, delete it.
							 */
							wp_delete_file( $accp_file_full_path );

							/**
							 * Also delete the associated post.
							 */
							wp_delete_post( $post_id, true );

							echo 'The file and post were successfully deleted.';

						} else {

							/**
							 * If the file no longer exists,
							 * just delete the post.
							 */
							wp_delete_post( $post_id, true );

							echo 'The file was successfully deleted.';

						}
					} else {

						/**
						 * If the post doesn't have a file just delete the post.
						 */
						wp_delete_post( $post_id, true );

						echo 'The file was successfully deleted.';

					}

					/**
					 * Pro - Delete any associated post notes.
					 */
					$this->accp_delete_post_notes_on_post_delete( $post_id );

				}

			endwhile;

			wp_reset_postdata();

		endif;

		wp_die();
	}


	/**
	 * Restrict access to Client Pages, Client File, Client Invoice,
	 * and Global File posts on the front-end.
	 */
	public function accp_restrict_client_page_access() {

		if ( is_admin() ) {
			return;
		}

		$post_id = get_the_id();

		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type();

		if ( ! $post_type ) {
			return;
		}

		/**
		 * Exit if this is not a Client Page, Client File, Global File
		 * or Client Invoice post type.
		 */
		if ( 'accp_client_pages' !== $post_type &&
			'accp_clientfile' !== $post_type &&
			'accp_clientinvoice' !== $post_type &&
			'accp_global_file' !== $post_type
		) {
			return;
		}

		$user = wp_get_current_user();

		/**
		 * Redirect to the home page if no user was returned.
		 */
		if ( ! $user ) {

			wp_safe_redirect( '/' );
			exit;

		}

		/**
		 * Redirect to the home page if there is no user ID
		 * or the user ID equals 0.
		 */
		$user_id = $user->ID ? $user->ID : false;

		if ( ! $user_id || 0 === $user_id ) {

			wp_safe_redirect( '/' );
			exit;

		}

		/**
		 * Check if pro access checks need to be instantiated.
		 */
		if ( 'ars-constellation-client-portal-pro' === $this->plugin_name ) {

			$authorization = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );

			if ( 'accp_global_file' !== $post_type ) {

				$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

			} else {

				$check_authorization = $authorization->verify_global_file_post_authorization_pro( $post_id, $user_id );

			}
		} else {

			/**
			 * Use the default file check if this is the Core version
			 * to determine if access should be granted.
			 */
			$authorization       = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );
			$check_authorization = $authorization->verify_page_or_post_authorization( $post_id, $user_id );

		}

		/**
		 * Redirect to the home page if authorization failed.
		 */
		if ( false === $check_authorization ) {

			wp_safe_redirect( '/' );
			exit;

		}
	}


	/**
	 * Restrict access to Client Company Posts on front-end.
	 */
	public function accp_restrict_company_page_access() {

		if ( is_admin() ) {
			return;
		}

		global $post;

		$post_id         = get_the_id();
		$post_type       = get_post_type();
		$current_user    = wp_get_current_user();
		$current_user_id = (int) $current_user->ID;

		/**
		 * Get a list of users that are assigned
		 * to the current Company.
		 *
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'client_company',
					'value'   => $post_id,
					'compare' => 'IN',
				),
				array(
					'key'     => 'client_additional_company',
					'value'   => $post_id,
					'compare' => 'LIKE',
				),
			),
		);

		$user_query    = new WP_User_Query( $args );
		$users         = (array) $user_query->results;
		$user_id_array = array();

		/**
		 * Set up an array of assigned user ID's.
		 */
		foreach ( $users as $user ) {
			$user_id_array[] = (int) $user->ID;
		}

		if ( 'accp_clientcompany' === $post_type ) {

			/**
			 * Exit early and grant access if this is a WP admin.
			 */
			if ( current_user_can( 'manage_options' ) ) {
				return;
			}

			/**
			 * Check if the current user is logged in and that
			 * they are assigned to a company that has access to the page.
			 */
			if ( ! is_user_logged_in() || 0 === $current_user_id || ! in_array( $current_user_id, $user_id_array, true ) ) {

				wp_safe_redirect( '/' );
				exit;

			}
		}
	}


	/**
	 * Get the Company upload directory name.
	 *
	 * @param int|string $company_id - The post ID of the company.
	 *
	 * @return string $dir - The directory name.
	 */
	public function accp_get_company_dir( $company_id ) {

		if ( ! isset( $company_id ) ) {
			return false;
		}

		$dir = get_post_meta( $company_id, 'accp_dir', true );

		if ( empty( $dir ) ) {

			$dir = uniqid( $company_id . '_' );
			add_post_meta( $company_id, 'accp_dir', $dir );

		}

		return $dir;
	}


	/**
	 * Update the the post edit form tag for
	 * client file, client invoice, and global
	 * file post types.
	 *
	 * Add 'enctype="multipart/form-data"' to allow
	 * for file upload support.
	 *
	 * @hooked post_edit_form_tag.
	 */
	public function accp_update_post_edit_form_tag() {

		global $post;

		if ( ! $post ) {
			return;
		}

		$post_type = get_post_type( $post->ID );

		if ( 'accp_clientfile' !== $post_type && 'accp_clientinvoice' !== $post_type && 'accp_global_file' !== $post_type ) {
			return;
		}

		$enctype      = ' enctype=multipart/form-data'; // No need for double quotes.
		$autocomplete = ' autocomplete=off'; // No need for double quotes.

		echo esc_attr( $enctype ) . esc_attr( $autocomplete );
	}


	/**
	 * Add meta fields to client file, client invoice,
	 * and global file post types.
	 *
	 * @hooked admin_menu.
	 */
	public function accp_client_file_meta_box() {

		$screens = array( 'accp_clientfile', 'accp_clientinvoice' );
		$is_pro  = $this->accp_utility_functions->is_pro_plugin( $this->plugin_name );

		if ( true === $is_pro ) {

			$screens[] = 'accp_global_file';

		}

		foreach ( $screens as $screen ) {

			add_meta_box( 'clientfile', __( 'Client File', 'constellation-client-portal' ), array( $this, 'accp_client_file_meta_fields' ), $screen, 'normal', 'high' );

		}
	}


	/**
	 * Client file attachment meta box content.
	 *
	 * Adds the file upload and current file UI.
	 */
	public function accp_client_file_meta_fields() {

		global $post;

		$file_save_nonce                = wp_create_nonce( 'accp_file_save_nonce' );
		$post_id                        = $post->ID;
		$post_type                      = get_post_type( $post_id );
		$accp_file                      = get_post_meta( $post_id, 'accp_file', true );
		$post_company                   = get_post_meta( $post_id, 'accp_user', true ) ? (int) get_post_meta( $post_id, 'accp_user', true ) : '';
		$accp_clientfiles_full_dir_path = $this->accp_utility_functions->accp_get_clientfiles_path();

		?>
		<input type="hidden" name="wp_accp_nonce" value="<?php echo esc_attr( $file_save_nonce ); ?>">		
		<?php

		if ( $accp_file && ! empty( $accp_file ) ) :

			$company_dir      = $post_company ? $this->accp_get_company_dir( $post_company ) : '';
			$file_parse_array = wp_parse_url( $accp_file['url'] );
			$accp_file_path   = isset( $file_parse_array['path'] ) ? $file_parse_array['path'] : '';

			if ( 'accp_clientinvoice' === $post_type || 'accp_clientfile' === $post_type ) {

				$accp_file_full_path = $accp_clientfiles_full_dir_path . '/' . $company_dir . '/' . basename( $accp_file_path );

			} else {

				$accp_file_full_path = $accp_clientfiles_full_dir_path . '/global-files/' . basename( $accp_file_path );

			}

			/**
			 * Verify that the actual file (not post) still exists.
			 */
			clearstatcache();

			if ( $accp_file_path && ! empty( $accp_file_path ) && file_exists( $accp_file_full_path ) ) :
				?>
				
				<div id="curr_file_container" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-file-path="<?php echo esc_attr( $accp_file_full_path ); ?>" data-assigned-company="<?php echo $post_company ? esc_attr( $post_company ) : 'global'; ?>">

					<p>Current File</p>

					<p>
						<a href="<?php echo esc_url( $accp_file['url'] ); ?>" target="_blank"><?php echo esc_html( basename( $accp_file['file'] ) ); ?></a>
					</p>

					<div id="replace-file-toggle" class="button">Replace File</div>		
					
					<?php
						$file_exists = true;
					?>

				</div>

			<?php else : ?>

				<p>Current File</p>

				<p>The associated file (<?php echo esc_html( basename( $accp_file['file'] ) ); ?>) no longer exists in the directory.  Please re-upload the file to restore it.</p>

				<?php
					$file_exists = false;
				?>

			<?php endif; ?>

			<?php // Replace file section. ?>

			<?php
			$replace_section_class = isset( $file_exists ) && true === $file_exists ? 'accp-file-replace-hide' : '';
			?>

			<div class="<?php echo esc_attr( $replace_section_class ); ?>">
				
				<p class="label"><label for="accp_file">Upload a File</label></p>	
				<p><input type="file" name="accp_file" id="accp_file" class="accp-upload-btn button" /></p>

			</div>

		<?php else : ?>

				<?php
					$accp_file_path        = '';
					$accp_file_full_path   = '';
					$file_exists           = false;
					$replace_section_class = isset( $file_exists ) && true === $file_exists ? 'accp-file-replace-hide' : '';
				?>

				<div class="<?php echo esc_attr( $replace_section_class ); ?>">
					
					<p class="label"><label for="accp_file">Upload a File</label></p>	
					<p><input id="accp_file" type="file" name="accp_file" class="accp-upload-btn button file-not-present" /></p>

				</div>		

		<?php endif; ?>	
		
		<?php // Assigned company section. ?>
		
		<?php
		/**
		 * Exit if this is not a client invoice or
		 * client file post type.
		 */
		if ( 'accp_clientinvoice' !== $post_type && 'accp_clientfile' !== $post_type ) {
			return;
		}

		if ( ! empty( $post_company ) ) {

			echo '<p>Assigned Company</p>';
			echo '<p>' . esc_html( get_the_title( $post_company ) ) . '</p>';

			/**
			 * Only display the Reassign button if the file exists.
			 */
			if ( ! empty( $accp_file ) ) {

				if ( file_exists( $accp_file_full_path ) ) {

					?>

					<div id="reassign-toggle" class="button">Reassign</div>
		   
					<?php
				}
			}
		}
		?>
				   
		<div class="company-assign-container <?php echo ( ! empty( $post_company ) ) ? 'accp-hide-assigment' : ''; ?>">

			<p class="label"><label for="accp_user">*Assign Company</label></p>

			<?php
			wp_dropdown_pages(
				array(
					'post_type'        => 'accp_clientcompany',
					'id'               => 'company-select',
					'value_field'      => 'ID',
					'echo'             => true,
					'show_option_none' => 'Select a company...',
					'name'             => 'accp_user',
					'selected'         => esc_html( $post_company ),
					'required'         => 'required',
				)
			);

			?>
		</div>
		  
		<?php
		$reassign_nonce = wp_create_nonce( 'accp_reassign_nonce' );

		/**
		 * Reassign post/file to another company.
		 * This deletes the accp_user and accp_file post meta.
		 */
		?>
		<div id="reassign-form-container" style="display: none;">				

			<input type="hidden" name="accp_reassign" id="accp_reassign" value="1" />
			
			<?php if ( ! empty( $accp_file ) ) { ?>

				<p class="label">
					<input type="checkbox" name="accp_leave_prev_file" value="1"> 
					<label for="accp_leave_prev_file"><?php esc_html_e( 'Leave Copy of File in Previous Company Directory', 'constellation-client-portal' ); ?></label>
				</p>

				<p class="accp-field-instructions">
					Selecting this option will leave a copy of <?php echo '<span class="blue-text"> ' . esc_html( basename( $accp_file['file'] ) ) . ' </span>'; ?> in the <?php echo '<span class="blue-text"> ' . esc_url( dirname( $accp_file_path ) ) . ' </span>'; ?> directory.  Leave this option unchecked if you would like to simply move the file from the previous directory to the new directory.
				</p>

			<?php } ?>

			<?php
			$data_current_company_id = $post_company && ! empty( $post_company ) ? $post_company : '';
			?>
			
			<p>
				<input type="button" class="button button-primary" data-nonce="<?php echo esc_attr( $reassign_nonce ); ?>" name="accp_reassign_btn" id="accp_reassign_btn" value="Assign New Company" data-current-company-id="<?php echo esc_attr( $data_current_company_id ); ?>" />
			</p>			

		</div>		
		<?php
	}


	/**
	 * Reassign file to another company - AJAX function.
	 */
	public function accp_reassign_file_1() {

		if ( ! isset( $_POST['reassign_nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['reassign_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'accp_reassign_nonce' ) ) {
			die();
		}

		if ( ! isset( $_POST['post_id'] ) ) {
			die();
		}

		if ( ! isset( $_POST['selected_company'] ) ) {
			die();
		}

		$raw_post_id          = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		$post_id              = null !== (int) $raw_post_id ? (int) $raw_post_id : '';
		$post_type            = get_post_type( $post_id );
		$raw_selected_company = sanitize_text_field( wp_unslash( $_POST['selected_company'] ) );
		$selected_company     = null !== (int) $raw_selected_company ? (int) $raw_selected_company : '';
		$raw_leave_copy       = isset( $_POST['leave_copy'] ) ? sanitize_text_field( wp_unslash( $_POST['leave_copy'] ) ) : '';
		$leave_copy           = null !== (int) $raw_leave_copy ? (int) $raw_leave_copy : '';
		$current_company_id   = get_post_meta( $post_id, 'accp_user', true );
		$accp_uploads_dir     = $this->accp_utility_functions->accp_get_clientfiles_path();
		$current_company_dir  = $this->accp_get_company_dir( $current_company_id );
		$current_file         = get_post_meta( $post_id, 'accp_file', true );
		$file_parse_array     = wp_parse_url( $current_file['url'] );
		$accp_file_path       = isset( $file_parse_array['path'] ) ? $file_parse_array['path'] : '';
		$accp_file_name       = basename( $accp_file_path );
		$accp_file_full_path  = $accp_uploads_dir . '/' . $current_company_dir . '/' . $accp_file_name;
		$file_type            = $current_file['type'];
		$new_company_dir      = get_post_meta( $selected_company, 'accp_dir', true );
		$note                 = '<p>Company Reassigned:</p>';

		/**
		 * Exit early if the current company ID is
		 * equal to the new company ID.
		 */
		if ( (int) $current_company_id === $selected_company ) {

			echo 'The old an new companies are the same.  The post was not reassigned.';

			wp_die();

		}

		/**
		 * Create the new company dir if it does not exist.
		 */
		if ( empty( $new_company_dir ) ) {

			$new_company_dir  = uniqid( $selected_company . '_' );
			$new_company_path = $accp_uploads_dir . DIRECTORY_SEPARATOR . $new_company_dir;

			wp_mkdir_p( $new_company_path );
			update_post_meta( $selected_company, 'accp_dir', $new_company_dir );

			/**
			 * Append to notes.
			 */
			$note .= 'A new directory (' . $new_company_path . ') was created.\n';

		}

		$accp_clientfiles_dir_url = $this->accp_utility_functions->accp_get_clientfiles_upload_dir_url();
		$new_company_path         = $accp_uploads_dir . DIRECTORY_SEPARATOR . $new_company_dir;
		$new_full_path            = $new_company_path . DIRECTORY_SEPARATOR . basename( $accp_file_full_path );
		$new_file_name            = 's/' . $new_company_dir . '/' . basename( $new_full_path );
		$new_file_url             = $accp_clientfiles_dir_url . '/' . $new_company_dir . '/' . basename( $new_full_path );

		/**
		 * Verify that the actual file (not post) still exists.
		 */
		clearstatcache();

		/**
		 * Check if the file name already exists in the new dir.
		 */
		if ( ! file_exists( $new_full_path ) ) {

			/**
			 * If the file does not already exist copy it to the new dir.
			 */
			$copy = copy( $accp_file_full_path, $new_full_path );

			/**
			 * Append to notes.
			 */
			if ( true === $copy ) {

				$note .= '<p>The file was successfully copied from ' . esc_html( $accp_file_full_path ) . ' to ' . esc_html( $new_full_path ) . '.</p>';

			} else {

				$note .= '<p>The file could not be copied from ' . esc_html( $accp_file_full_path ) . ' to ' . esc_html( $new_full_path ) . '.  Please check your server configuration.</p>';

			}

			/**
			 * Prep file meta data to store in the file post.
			 */
			$file_array = array(
				'file' => $new_file_name,
				'url'  => $new_file_url,
				'type' => $file_type,
			);

		} else {

			/**
			 * Otherwise loop through and add a number to the file name as needed,
			 * to ensure that the filename is unique.
			 */
			$new_full_path = $this->check_for_existing_duplicate_file_and_update_name( $new_full_path );

			/**
			 * Copy the file with the incremented file name to the new company dir.
			 */
			$copy = copy( $accp_file_full_path, $new_full_path );

			/**
			 * Append to notes.
			 */
			if ( true === $copy ) {

				$note .= '<p>The file was successfully copied from ' . esc_html( $accp_file_full_path ) . ' to ' . esc_html( $new_full_path ) . '.</p>';

			} else {

				$note .= '<p>The file could not be copied from ' . esc_html( $accp_file_full_path ) . ' to ' . esc_html( $new_full_path ) . '.  Please check your site configuration.</p>';

			}

			/**
			 * Update the $new_file_url var and meta info so that the correct
			 * file name and url are stored in the post meta.
			 */
			$new_file_url  = $accp_clientfiles_dir_url . '/' . $new_company_dir . '/' . basename( $new_full_path );
			$new_file_name = 's/' . $new_company_dir . '/' . basename( $new_full_path );

			$file_array = array(
				'file' => $new_file_name,
				'url'  => $new_file_url,
				'type' => $file_type,
			);

		}

		/**
		 * Delete the old file if the 'leave copy' option is unchecked.
		 */
		if ( 1 === $leave_copy || true === $leave_copy || '1' === $leave_copy ) {

			clearstatcache();

			wp_delete_file( $accp_file_full_path );

			/**
			 * Append to notes.
			 */
			$note .= '<p>The file was successfully deleted from ' . esc_html( $accp_file_full_path ) . '.</p>';

		}

		/**
		 * Update the file meta info (assigned company and file data).
		 */
		update_post_meta( $post_id, 'accp_file', $file_array ); // File info.
		update_post_meta( $post_id, 'accp_user', $selected_company ); // Newly assigned company.

		/**
		 * Append to notes.
		 */
		$old_company_name = get_the_title( $current_company_id ) ? get_the_title( $current_company_id ) : '';
		$new_company_name = get_the_title( $selected_company ) ? get_the_title( $selected_company ) : '';

		$note .= '<p>The post has been successfully reassigned from ' . esc_html( $old_company_name ) . ' to ' . esc_html( $new_company_name ) . '.</p>';

		/**
		 * Add a note to the post.
		 */
		if ( $note && ! empty( $note ) ) {

			$this->accp_add_directory_update_note( $post_id, $note, $post_type );

		}

		echo esc_url( $new_full_path );

		echo 'The file has been successfully reassigned.';

		wp_die();
	}


	/**
	 * Check for duplicate file in a directory, and
	 * update the file name (append a number to the name)
	 * if a duplicate file was found in the directory.
	 *
	 * @param string $destination_file - The full path of the file.
	 *
	 * @return string $destination_file - The existing file path if a duplicate was not found, or the new file
	 * path with a new file name if a duplicate file was found in the directory.
	 */
	public function check_for_existing_duplicate_file_and_update_name( $destination_file ) {

		if ( ! file_exists( $destination_file ) ) {
			return $destination_file;
		}

		$i = 2;

		while ( file_exists( $destination_file ) ) {

			$path_parts = explode( '.', $destination_file );

			/**
			 * Remove any numbers in brackets in the file name.
			 */
			$path_parts[0]  = preg_replace( '/\(([0-9]*)\)$/', '', $path_parts[0] );
			$path_parts[0] .= '-' . $i;

			$new_fname = implode( '.', $path_parts );

			if ( ! file_exists( $new_fname ) ) {
				$destination_file = $new_fname;
				break;
			}

			++$i;

		}

		return $destination_file;
	}


	/**
	 * Generate a company directory - AJAX.
	 */
	public function accp_generate_company_dir() {

		if ( ! isset( $_POST['generate_nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['generate_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'accp_generate_nonce' ) ) {
			die();
		}

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! isset( $_POST['post_id'] ) ) {
			die();
		}

		$company_id = filter_var( wp_unslash( $_POST['post_id'] ), FILTER_SANITIZE_NUMBER_INT );

		$move_files   = '';
		$update_links = '';

		if ( isset( $_POST['move_files'] ) && 'move' === $_POST['move_files'] ) {

			$move_files = 'move';

		}

		if ( isset( $_POST['update_links'] ) && 'update' === $_POST['update_links'] ) {

			$update_links = 'update';

		}

		$new_company_dir                = get_post_meta( $company_id, 'accp_dir', true );
		$accp_clientfiles_full_dir_path = $this->accp_utility_functions->accp_get_clientfiles_path();

		/**
		 * Create the new company dir if it does not exist.
		 */
		if ( empty( $new_company_dir ) ) {

			$new_company_dir     = uniqid( $company_id . '_' );
			$new_company_path    = $accp_clientfiles_full_dir_path . DIRECTORY_SEPARATOR . $new_company_dir;
			$wp_content_dir_name = $this->accp_utility_functions->accp_get_wp_content_dir_name();
			$new_truncated_path  = '/' . strstr( $new_company_path, $wp_content_dir_name );

			/**
			 * Create the new directory.
			 */
			wp_mkdir_p( $new_company_path );

			/**
			 * Add the new directory name post meta.
			 */
			add_post_meta( $company_id, 'accp_dir', $new_company_dir );

			/**
			 * Add a note indicating that the new dir was created - Pro feature.
			 */
			$note_content = 'The new upload directory (' . $new_company_dir . ') was created.';

			if ( ! file_exists( $new_company_path ) ) {

				$note_content = 'The new directory could not be created.  Please verify your server settings and try again.';

			}

			$this->accp_add_directory_update_note( $company_id, $note_content );

			echo esc_html( $new_truncated_path );

		} else {

			$new_company_dir     = uniqid( $company_id . '_' );
			$new_company_path    = $accp_clientfiles_full_dir_path . DIRECTORY_SEPARATOR . $new_company_dir;
			$wp_content_dir_name = $this->accp_utility_functions->accp_get_wp_content_dir_name();
			$new_truncated_path  = '/' . strstr( $new_company_path, $wp_content_dir_name );

			/**
			 * Create the new directory.
			 */
			wp_mkdir_p( $new_company_path );

			$new_dir_name = $new_company_dir;

			/**
			 * Add a note indicating that the new dir was created - Pro feature.
			 */
			$note_content = 'The new upload directory (' . $new_company_dir . ') was created.';

			if ( ! file_exists( $new_company_path ) ) {

				$note_content = 'The new directory could not be created.  Please verify your server settings and try again.';

			}

			$this->accp_add_directory_update_note( $company_id, $note_content );

			/**
			 * Move files from the old directory to the new directory
			 * if that option was selected.
			 */
			if ( 'move' === $move_files ) {

				$old_dir_name = get_post_meta( $company_id, 'accp_dir', true );

				$process_file_moves = $this->accp_move_files_to_another_directory( $old_dir_name, $new_dir_name );

				if ( is_array( $process_file_moves ) && array_key_exists( 'errors', $process_file_moves ) && ! empty( $process_file_moves['errors'] ) ) {

					$note_content = implode( ' ', $process_file_moves['errors'] );

				} elseif ( false === $process_file_moves ) {

					$note_content = 'No files were moved from the ' . $old_dir_name . ' directory.';

				} else {

					/**
					 * Add a note with file move details - Pro feature.
					 */
					$note_content = 'All files were moved from the ' . $old_dir_name . ' directory to the ' . $new_dir_name . ' directory.';
				}

				$this->accp_add_directory_update_note( $company_id, $note_content );

			}

			/**
			 * Update attachment links for existing
			 * File and Invoice posts is that option was
			 * selected.
			 */
			if ( 'update' === $update_links ) {

				$process_link_updates = $this->accp_update_dir_name_in_file_attachments( $company_id, $new_dir_name, array( 'accp_clientinvoice', 'accp_clientfile' ) );

				/**
				 * Add a note with attachment link update details - Pro feature.
				 */
				$note_content = 'No file attachments were found, so no attachment links were updated.';

				if ( is_array( $process_link_updates ) && ! empty( $process_link_updates ) ) {

					$edited_post_list = filter_var_array( $process_link_updates, FILTER_SANITIZE_NUMBER_INT );

					$note_content = 'Attachments were found in the following posts (' . esc_html( implode( ', ', $edited_post_list ) ) . ') and the associated links were updated for those attachments.';

				}

				$this->accp_add_directory_update_note( $company_id, $note_content );

			}

			/**
			 * Add the new directory name post meta.
			 */
			update_post_meta( $company_id, 'accp_dir', $new_company_dir );

			echo esc_html( $new_truncated_path );

		}

		wp_die();
	}


	/**
	 * Maybe insert note - Pro feature.
	 *
	 * @param int|string $company_id - The post ID of the company or file, invoice, global file post.
	 * @param string     $note_content - The note content.
	 * @param string     $post_type - The post type slug.  Defaults to "accp_clientcompany".
	 */
	public function accp_add_directory_update_note( $company_id, $note_content, $post_type = 'accp_clientcompany' ) {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( class_exists( 'ARS_Constellation_Client_Portal_Pro_Notes' ) ) {

			$accp_notes    = new ARS_Constellation_Client_Portal_Pro_Notes( $this->version, $this->plugin_name );
			$note_audience = 'admin';
			$author_id     = get_current_user_id() ? (int) get_current_user_id() : '';
			$date_now      = gmdate( 'F j, Y, g:i a' );
			$post_title    = 'Note for post ' . $company_id . ' - ' . $date_now;

			$accp_notes->accp_insert_post_note(
				(int) $company_id,
				(int) $author_id,
				sanitize_text_field( $post_title ),
				wp_kses_post( $note_content ),
				sanitize_text_field( $note_audience ),
				sanitize_text_field( $post_type )
			);

		}
	}


	/**
	 * Move files from one company upload directory
	 * to another company upload directory.
	 *
	 * @param string $old_dir_name - The old directory name (moving away from).
	 * @param string $new_dir_name - The new directory name (moving to).
	 * @param bool   $overwrite - Whether to overwite duplicate files in the destination dir.  Default = false.
	 *
	 * @return bool|array true|false|$errors - True if all files were moved (or false if none). Error if there issues with specific files.
	 */
	protected function accp_move_files_to_another_directory( $old_dir_name, $new_dir_name, $overwrite = false ) {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		global $wp_filesystem;

		WP_Filesystem();

		$errors = array();

		/**
		 * Only allow alphanumeric, hyhpen, and underscore.
		 */
		$old_dir_name = sanitize_text_field( preg_replace( '/[^-\w,]/', '', $old_dir_name ) );
		$new_dir_name = sanitize_text_field( preg_replace( '/[^-\w,]/', '', $new_dir_name ) );

		if ( ! $old_dir_name || ! $new_dir_name ) {
			return false;
		}

		if ( $old_dir_name === $new_dir_name ) {
			return false;
		}

		$accp_clientfiles_full_dir_path = $this->accp_utility_functions->accp_get_clientfiles_path();

		$files = glob( $accp_clientfiles_full_dir_path . '/' . $old_dir_name . '/*.*' );

		if ( ! $files ) {
			return false;
		}

		foreach ( $files as $file ) {

			if ( $file ) {

				$new_file_path = str_replace( $old_dir_name, $new_dir_name, $file ?? '' );
				$move_files    = $wp_filesystem->move( $file, $new_file_path, $overwrite );

				if ( false === $move_files ) {

					$error_note = 'The file ' . esc_html( $file ) . ' could not be moved. Verify that the file name does not already exist in the destination directory.';

					$errors['errors'][] = $error_note;
				}
			}
		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}

		return true;
	}


	/**
	 * Update directory name in File and Invoice
	 * file attachments if there is a file attachment.
	 *
	 * This updates the directory name within the
	 * 'file' and 'url' values in the 'accp_file' post meta
	 * array.
	 *
	 * @param string $company_id - the ID of the target company.
	 * @param string $new_dir_name - the new directory name to insert in the file attachment
	 * path and url.
	 * @param array  $post_types - default array('accp_clientinvoice', 'accp_clientfile').
	 *
	 * @return $edited_post_list - array of post ID's that were edited,
	 * or false if no post ID's were returned in the query.
	 */
	private function accp_update_dir_name_in_file_attachments( $company_id, $new_dir_name, $post_types = array( 'accp_clientinvoice', 'accp_clientfile' ) ) {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! in_array( 'accp_clientinvoice', $post_types, true ) && ! in_array( 'accp_clientfiles', $post_types, true ) ) {
			return false;
		}

		/**
		 * Query for File and Invoice posts for the
		 * specificed company that have a file attached.
		 *
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			'post_type'  => $post_types,
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query' => array(
				array(
					'key'     => 'accp_file',
					'compare' => 'EXISTS',
				),
				array(
					'key'   => 'accp_user',
					'value' => $company_id,
				),
			),
		);

		$query = new WP_Query( $args );

		$post_count = $query->found_posts;

		/**
		 * Return false if no posts were returned.
		 */
		if ( $post_count < 1 ) {
			return false;
		}

		$edited_post_list = array();

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {

				$query->the_post();

				$post_id = get_the_ID();

				$accp_file = get_post_meta( $post_id, 'accp_file', true );

				if ( $accp_file ) {

					/**
					 * Get the old dir name from the file post instead
					 * of the company post to account for the company upload
					 * dir being previously changed without updating the associated
					 * post file links.
					 */
					$old_dir_name = sanitize_text_field( basename( dirname( $accp_file['file'] ) ) );

					if ( $old_dir_name !== $new_dir_name ) {

						$new_file = array(
							'file' => str_replace( $old_dir_name, sanitize_text_field( $new_dir_name ), $accp_file['file'] ?? '' ),
							'url'  => str_replace( $old_dir_name, sanitize_text_field( $new_dir_name ), $accp_file['url'] ?? '' ),
							'type' => sanitize_text_field( $accp_file['type'] ),
						);

						update_post_meta( (int) $post_id, 'accp_file', $new_file );

						$edited_post_list[] = (int) $post_id;

					}
				}
			}

			wp_reset_postdata();

		}

		return $edited_post_list;
	}


	/**
	 * Specify a company directory - AJAX.
	 */
	public function accp_specify_company_dir() {

		if ( ! isset( $_POST['generate_nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['generate_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'accp_generate_nonce' ) ) {
			die();
		}

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! isset( $_POST['dir_name'] ) ) {
			die();
		}

		if ( ! isset( $_POST['post_id'] ) ) {
			die();
		}

		$company_id           = filter_var( wp_unslash( $_POST['post_id'] ), FILTER_SANITIZE_NUMBER_INT );
		$move_files           = '';
		$overwrite_duplicates = '';
		$update_links         = '';

		if ( isset( $_POST['move_files'] ) && 'move' === $_POST['move_files'] ) {

			$move_files = 'move';

		}

		if ( isset( $_POST['overwrite_duplicates'] ) && 'overwrite' === $_POST['overwrite_duplicates'] ) {

			$overwrite_duplicates = 'overwrite';
			$overwrite            = true;
		} else {

			$overwrite = false;
		}

		if ( isset( $_POST['update_links'] ) && 'update' === $_POST['update_links'] ) {

			$update_links = 'update';

		}

		$accp_clientfiles_full_dir_path = $this->accp_utility_functions->accp_get_clientfiles_path();

		$dir_name        = sanitize_text_field( wp_unslash( $_POST['dir_name'] ) );
		$new_company_dir = preg_replace( '/[^-\w,]/', '', $dir_name );

		if ( empty( $new_company_dir ) ) {
			die();
		}

		/**
		 * Check for use of reserved "global-file" dir name.
		 */
		if ( 'global-files' === strtolower( $new_company_dir ) ||
			'global_files' === strtolower( $new_company_dir ) ||
			'globalfiles' === strtolower( $new_company_dir ) ||
			'global.files' === strtolower( $new_company_dir )
		) {

			$note_content = 'The ' . esc_html( strtolower( $new_company_dir ) ) . ' directory name is reserved and cannot be used.  Please enter a different name.';

			$this->accp_add_directory_update_note( $company_id, $note_content );

			die();

		}

		$new_company_path    = $accp_clientfiles_full_dir_path . DIRECTORY_SEPARATOR . $new_company_dir;
		$wp_content_dir_name = $this->accp_utility_functions->accp_get_wp_content_dir_name();
		$new_truncated_path  = '/' . strstr( $new_company_path, $wp_content_dir_name );

		/**
		 * Create the new directory if it does not exist.
		 */
		if ( ! file_exists( $new_company_path ) ) {

			wp_mkdir_p( $new_company_path );

		}

		$new_dir_name = $new_company_dir;

		/**
		 * Add a note indicating that the new dir was created - Pro feature.
		 */
		$note_content = 'The upload directory (' . $new_company_dir . ') was assigned.';

		if ( ! file_exists( $new_company_path ) ) {

			$note_content = 'The new directory could not be created.  Please verify your server settings and try again.';

		}

		$this->accp_add_directory_update_note( $company_id, $note_content );

		/**
		 * Move files from the old directory to the new directory
		 * if that option was selected.
		 */
		if ( 'move' === $move_files ) {

			$old_dir_name = get_post_meta( $company_id, 'accp_dir', true );

			$process_file_moves = $this->accp_move_files_to_another_directory( $old_dir_name, $new_dir_name, $overwrite );

			if ( is_array( $process_file_moves ) && array_key_exists( 'errors', $process_file_moves ) && ! empty( $process_file_moves['errors'] ) ) {

				$note_content = implode( ' ', $process_file_moves['errors'] );

			} elseif ( false === $process_file_moves ) {

				$note_content = 'No files were moved from the ' . $old_dir_name . ' directory.';

			} else {

				/**
				 * Add a note with file move details - Pro feature.
				 */
				$note_content = 'All files were moved from the ' . $old_dir_name . ' directory to the ' . $new_dir_name . ' directory.';
			}

			$this->accp_add_directory_update_note( $company_id, $note_content );

		}

		/**
		 * Update attachment links for existing
		 * File and Invoice posts is that option was
		 * selected.
		 */
		if ( 'update' === $update_links ) {

			$process_link_updates = $this->accp_update_dir_name_in_file_attachments( $company_id, $new_dir_name, array( 'accp_clientinvoice', 'accp_clientfile' ) );

			/**
			 * Add a note with attachment link update details - Pro feature.
			 */
			$note_content = 'No file attachments were found, so no attachment links were updated.';

			if ( is_array( $process_link_updates ) && ! empty( $process_link_updates ) ) {

				$edited_post_list = filter_var_array( $process_link_updates, FILTER_SANITIZE_NUMBER_INT );

				$note_content = 'Attachments were found in the following posts (' . esc_html( implode( ', ', $edited_post_list ) ) . ') and the associated links were updated for those attachments.';

			}

			$this->accp_add_directory_update_note( $company_id, $note_content );

		}

		/**
		 * Add the new directory name post meta.
		 */
		update_post_meta( $company_id, 'accp_dir', $new_company_dir );

		echo esc_html( $new_truncated_path );

		wp_die();
	}


	/**
	 * Save file to company directory.
	 *
	 * @param int         $post_id - The post ID.
	 * @param object|null $post - The post object.
	 *
	 * @hooked save_post.
	 */
	public function accp_save_post( $post_id, $post = null ) {

		if ( ! is_user_logged_in() ) {
			return wp_die( 'Invalid command.' );
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( isset( $_POST['wp_accp_nonce'] ) ) {

			$nonce = sanitize_text_field( wp_unslash( $_POST['wp_accp_nonce'] ) );

			if ( ! wp_verify_nonce( $nonce, 'accp_file_save_nonce' ) ) {
				return $post_id;
			}
		} else {

			return $post_id;

		}

		$post      = get_post( $post_id );
		$post_type = $post->post_type;

		if ( 'accp_clientfile' !== $post_type && 'accp_clientinvoice' !== $post_type && 'accp_global_file' !== $post_type ) {
			return $post_id;
		}

		/**
		 * Add the company ID to the file post meta.
		 */
		if ( 'accp_clientfile' === $post_type || 'accp_clientinvoice' === $post_type ) {

			if ( ! isset( $_POST['accp_user'] ) ) {
				return $post_id;
			}

			$company_post_data = filter_var( wp_unslash( $_POST['accp_user'] ), FILTER_SANITIZE_NUMBER_INT );
			$company           = get_post( $company_post_data );
			$company_id        = $company->ID;

			update_post_meta( $post_id, 'accp_user', $company_id );

		}

		/**
		 * Upload the file attachment.
		 */
		if ( isset( $_FILES['accp_file'] ) ) {

			/**
			 * Dev Note: We presanitize the $_FILES['accp_file']
			 * array here for phpcs validation.
			 */
			$files = array_map( 'sanitize_text_field', $_FILES['accp_file'] );

			$this->accp_upload_file( $files, $post_id );

		}
	}


	/**
	 * Upload client file attachment.
	 *
	 * @param array $files - The $_FILES['accp_file'] array.
	 * @param int   $post_id - The post ID that the file is associated with.
	 */
	public function accp_upload_file( $files, $post_id ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 *  Verify that the file field is not empty.
		 *
		 * 'wp_check_filetype' and 'wp_handle_upload'
		 * are used to verify and handle the upload.
		 */
		if ( ! empty( $files['name'] ) ) {

			/**
			 * Get list of enabled mime types.
			 */
			$enabled_option_list = $this->accp_get_enabled_mime_type_option_values();

			/**
			 * Set up the Supported Mime Type var.
			 */
			if ( ! empty( $enabled_option_list ) ) {

				$supported_types = $enabled_option_list;

			} else {

				/**
				 * If no mime types were selected in the settings,
				 * add 'application/pdf' as the default mime type.
				 */
				$supported_types = array( 'application/pdf' );

			}

			/**
			 * Get the file type of the upload.
			 */
			$file_type_array    = wp_check_filetype( basename( $files['name'] ) );
			$uploaded_file_type = $file_type_array['type'];

			/**
			 * Verify that the mime type is supported.
			 */
			if ( in_array( $uploaded_file_type, $supported_types, true ) ) {

				/**
				 * Upload the file.
				 */
				if ( ! empty( $file_type_array['type'] ) ) {

					$upload = wp_handle_upload( $files, array( 'test_form' => false ) );

				}

				if ( isset( $upload['error'] ) && 0 !== $upload['error'] && false !== $upload['error'] ) {

					wp_die( 'There was an error uploading your file. ' . esc_html( $upload['error'] ) );

				} else {

					/**
					 * Save uploaded file info to post meta.
					 */
					update_post_meta( $post_id, 'accp_file', $upload );

					/**
					 * Add index.php (silence is golden) to the respective
					 * company directory if it doesn't already exist.
					 */
					$this->copy_index_file_to_company_dir( $post_id );

					/**
					 * Copy the detault index.php file to the main
					 * client portal uploads dir if needed.
					 */
					$this->copy_index_file_to_uploads_dir( $post_id );

				}
			} else {

				wp_die( 'The file type being uploaded is not supported.  Allowed file types can be updated on the <a href="' . esc_url( get_admin_url() ) . 'admin.php?page=admin.php%3Fpage%3Daccp-settings.php">settings</a> page.' );

			}
		}
	}


	/**
	 * Copy index.php file to client portal
	 * uploads dir.
	 *
	 * @param int $post_id - The post ID of the post initiating the action.
	 */
	private function copy_index_file_to_uploads_dir( $post_id ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'accp_clientinvoice' !== $post_type &&
			'accp_clientfile' !== $post_type &&
			'accp_global_file' !== $post_type
		) {

			return;

		}

		$source_file   = plugin_dir_path( __DIR__ ) . 'public/assets/index.php';
		$main_file_dir = wp_get_upload_dir()['basedir'] . '/index.php';

		/**
		 * Add index.php (silence is golden) to the main
		 * accp-clientfiles upload dir if it doesn't exist
		 * NOTE: Consider adding this to plugin activation -
		 * Need to create the /accp-clientfiles dir on activation if so.
		 */
		if ( ! file_exists( $main_file_dir ) ) {

			copy( $source_file, $main_file_dir );

		}
	}


	/**
	 * Copy index.php file to company and/or
	 * global-files upload dir.
	 *
	 * @param int $post_id - The post ID of the post initiating the action.
	 */
	private function copy_index_file_to_company_dir( $post_id ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'accp_clientinvoice' !== $post_type &&
			'accp_clientfile' !== $post_type &&
			'accp_global_file' !== $post_type
		) {

			return;

		}

		$source_file = plugin_dir_path( __DIR__ ) . 'public/assets/index.php';

		$dir_name = '';

		if ( 'accp_global_file' !== $post_type ) {

			$company_id = get_post_meta( $post_id, 'accp_user', true ) ? (int) get_post_meta( $post_id, 'accp_user', true ) : '';

			if ( ! $company_id ) {
				return;
			}

			$company_dir_name = $this->accp_get_company_dir( $company_id );
			$dir_name         = $company_dir_name;

		} else {

			$dir_name = 'global-files';

		}

		if ( ! $dir_name || empty( $dir_name ) ) {
			return;
		}

		/**
		 * The upload dir is updated for client portal post types, so
		 * wp_get_upload_dir()['basedir'] returns 'fullpath/uploads/accp-clientfiles.
		 */
		$directory = wp_get_upload_dir()['basedir'] . '/' . $dir_name . '/index.php';

		if ( ! file_exists( $directory ) ) {

			copy( $source_file, $directory );

		}
	}


	/**
	 * Configure custom upload directory for the
	 * 'accp_clientfile' and 'accp_clientinvoice' post types.
	 *
	 * This sets the company-specific upload dir, and not
	 * the global file upload dir.
	 *
	 * @param array $default_dir - Default uploads directory array.
	 *
	 * @return array $custom_dir|$default_dir - Custom dir if customized, else $default_dir.
	 *
	 * @hooked upload_dir.
	 */
	public function accp_set_upload_dir( $default_dir ) {

		$nonce_field_name = $this->get_accp_post_edit_nonce_field_name();
		$nonce_name       = $this->get_accp_post_edit_nonce_name();

		if ( ! isset( $_REQUEST[ $nonce_field_name ] ) ) {
			return $default_dir;
		}

		$nonce = sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_field_name ] ) );

		/**
		 * Dev Note: The nonce check is here for phpcs validation.
		 */
		if ( function_exists( 'wp_verify_nonce' ) ) {

			if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
				return $default_dir;
			}
		}

		if ( ! isset( $_REQUEST['post_ID'] ) || $_REQUEST['post_ID'] < 0 ) {
			return $default_dir;
		}

		if ( ! isset( $_REQUEST['accp_user'] ) ) {
			return $default_dir;
		}

		if ( ! isset( $_REQUEST['post_type'] ) ) {
			return $default_dir;
		}

		if ( 'accp_clientfile' !== $_REQUEST['post_type'] && 'accp_clientinvoice' !== $_REQUEST['post_type'] ) {
			return $default_dir;
		}

		if ( ! isset( $_REQUEST['accp_user'] ) ) {
			return $default_dir;
		}

		$company_id = filter_var( wp_unslash( $_REQUEST['accp_user'] ), FILTER_SANITIZE_NUMBER_INT );
		$custom_dir = $this->get_custom_upload_dir( $company_id );

		if ( ! $custom_dir ) {
			return $default_dir;
		}

		return $custom_dir;
	}

	/**
	 * Get the accp post edit nonce field name.
	 *
	 * Gets the nonce field name that is added to
	 * all accp post type edit forms.
	 */
	public function get_accp_post_edit_nonce_field_name() {

		$nonce_field_name = 'accp_post_edit_form_nonce_field';

		return $nonce_field_name;
	}

	/**
	 * Get the accp post edit nonce name.
	 *
	 * Gets the nonce name that is associated with
	 * the nonce field that is added to all accp post
	 * type edit forms.
	 */
	public function get_accp_post_edit_nonce_name() {

		$nonce_name = 'accp_post_edit_form_nonce';

		return $nonce_name;
	}

	/**
	 * Get the accp post quick edit nonce field name.
	 *
	 * Gets the nonce field name that is added to
	 * all accp post quick type edit forms.
	 */
	public function get_accp_post_quick_edit_nonce_field_name() {

		$nonce_field_name = 'accp_post_quick_edit_form_nonce_field';

		return $nonce_field_name;
	}

	/**
	 * Get the accp post quick edit nonce name.
	 *
	 * Gets the nonce name that is associated with
	 * the nonce field that is added to all accp post
	 * type quick edit forms.
	 */
	public function get_accp_post_quick_edit_nonce_name() {

		$nonce_name = 'accp_post_quick_edit_form_nonce';

		return $nonce_name;
	}


	/**
	 * Get the accp post quick edit field html.
	 *
	 * @return string $html - The field html.
	 */
	public function get_accp_post_quick_edit_nonce_field_html() {

		$nonce_name       = $this->get_accp_post_quick_edit_nonce_name();
		$nonce_field_name = $this->get_accp_post_quick_edit_nonce_field_name();
		$nonce            = wp_create_nonce( $nonce_name );

		$html = '<input type="hidden" name="' . esc_attr( $nonce_field_name ) . '" value="' . esc_attr( $nonce ) . '">';

		return $html;
	}




	/**
	 * Get the accp WP list table filter nonce field name.
	 *
	 * Gets the nonce field name that is added to
	 * all accp WP list table filter forms.
	 */
	public function get_accp_list_table_filter_nonce_field_name() {

		$nonce_field_name = 'accp_list_table_filter_nonce_field';

		return $nonce_field_name;
	}

	/**
	 * Get the accp WP list table filter nonce name.
	 *
	 * Gets the nonce name that is associated with
	 * the nonce field that is added to all accp WP list
	 * table filter forms.
	 */
	public function get_accp_list_table_filter_nonce_name() {

		$nonce_name = 'accp_list_table_filter_nonce';

		return $nonce_name;
	}


	/**
	 * Get the accp WP list table filter field html.
	 *
	 * @return string $html - The field html.
	 */
	public function get_accp_list_table_filter_nonce_field_html() {

		$nonce_name       = $this->get_accp_list_table_filter_nonce_name();
		$nonce_field_name = $this->get_accp_list_table_filter_nonce_field_name();
		$nonce            = wp_create_nonce( $nonce_name );

		$html = '<input type="hidden" name="' . esc_attr( $nonce_field_name ) . '" value="' . esc_attr( $nonce ) . '">';

		return $html;
	}





	/**
	 * Get custom upload directory for
	 * accp_clientfile and accp_clientinvoice
	 * post types.
	 *
	 * @param int $company_id - The post ID of the company.
	 *
	 * @return array $custom_dir - The upload directory array.
	 */
	public function get_custom_upload_dir( $company_id ) {

		if ( ! $company_id ) {
			return;
		}

		$base_dir = $this->wp_uploads_base_dir_const . '/accp-clientfiles';
		$base_url = $this->wp_uploads_base_url_const . '/accp-clientfiles';

		$subdir      = '/' . $this->accp_get_company_dir( $company_id );
		$company_dir = $base_dir . $subdir;
		$company_url = $base_url . $subdir;

		$custom_dir = array(
			'path'    => $company_dir,
			'url'     => $company_url,
			'subdir'  => $subdir,
			'basedir' => $base_dir,
			'baseurl' => $base_url,
			'error'   => false,
		);

		return $custom_dir;
	}


	/**
	 * File attachment front-end download functionality.
	 */
	public function accp_get_download() {

		$current_user = wp_get_current_user();

		if ( ! isset( $_GET['accp-dl-id'] ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) );

		/**
		 * Verify the nonce.
		 */
		if ( function_exists( 'wp_verify_nonce' ) ) {

			if ( ! wp_verify_nonce( $nonce, 'accp_file_download_nonce' ) ) {
				return;
			}
		}

		if ( ! is_user_logged_in() ) {

			if ( isset( $_SERVER['REQUEST_URI'] ) ) {

				$url = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

			} else {

				$url = '/';

			}

			wp_safe_redirect( wp_login_url( $url ) );
			exit;

		}

		$file_id        = filter_var( wp_unslash( $_GET['accp-dl-id'] ), FILTER_SANITIZE_NUMBER_INT );
		$post_type      = get_post_type( $file_id );
		$is_global_file = 'accp_global_file' === $post_type ? true : false;

		if ( false === $is_global_file ) {

			if ( ! get_post_meta( $file_id, 'accp_user', true ) ) {
				return;
			}
		}

		if ( ! $current_user->ID || 0 === $current_user->ID ) {
			return;
		}

		$file_company_id = '';

		if ( false === $is_global_file ) {

			$file_company_id = (int) get_post_meta( $file_id, 'accp_user', true );

		}

		if ( ! current_user_can( 'manage_options' ) ) {

			$current_user_id               = $current_user->ID;
			$user_main_company             = (int) get_user_meta( $current_user_id, 'client_company', true );
			$additional_assigned_companies = get_user_meta( $current_user_id, 'client_additional_company', true ) ? get_user_meta( $current_user_id, 'client_additional_company', true ) : array();

			if ( $file_company_id !== $user_main_company && false === $is_global_file ) {

				if ( ! $additional_assigned_companies || empty( $additional_assigned_companies ) ) {
					return;
				}

				if ( is_array( $additional_assigned_companies ) ) {

					$additional_assigned_companies = array_map( 'intval', $additional_assigned_companies );

					if ( ! in_array( $file_company_id, $additional_assigned_companies, true ) ) {
						return;
					}
				}
			}
		}

		if ( false === $is_global_file ) {

			$company_dir_name = get_post_meta( $file_company_id, 'accp_dir', true );

		} else {

			$company_dir_name = 'global-files';

		}

		$accp_file             = get_post_meta( $file_id, 'accp_file', true );
		$accp_dir_and_filename = $company_dir_name . '/' . basename( $accp_file['file'] );
		$accp_file_path        = $this->wp_uploads_base_dir_const . '/accp-clientfiles/' . $accp_dir_and_filename;
		$accp_file_name        = basename( $accp_dir_and_filename );

		set_time_limit( 0 );

		$action = 'download';
		$this->accp_output_file( $accp_file_path, $accp_file_name, $accp_file['type'], $action );
	}


	/**
	 * File attachment output functionality.
	 *
	 * @param string $file - The file path.
	 * @param string $name - The file name.
	 * @param string $mime_type - The mime type.
	 * @param string $action - The action. Defaults to "download".
	 */
	private function accp_output_file( $file, $name, $mime_type = '', $action = 'download' ) {

		if ( ! is_readable( $file ) ) {
			return;
		}

		/**
		 * Prep WP_Filesystem.
		 */
		global $wp_filesystem;

		/**
		 * Include WP File since this is related
		 * to a download on the front-end.
		 */
		require_once ABSPATH . '/wp-admin/includes/file.php';

		WP_Filesystem();

		/**
		 * Define mime type.
		 * TODO: Add a filter to allow developers to filter the output mime_types list.
		 */
		$defined_mime_types = $this->accp_defined_file_mime_types();
		$file_extension     = strtolower( substr( strrchr( $file, '.' ), 1 ) );

		/**
		 * Set jpeg extension to jpg.
		 */
		if ( 'jpeg' === $file_extension ) {

			$file_extension = 'jpg';

		}

		$mime_type = '';

		if ( ! empty( $defined_mime_types ) ) {

			foreach ( $defined_mime_types as $key => $value ) {

				if ( strtolower( $file_extension ) === strtolower( $value['file_extension'] ) ) {

					$mime_type = $value['file_extension'];
					break;

				}
			}
		}

		if ( '' === $mime_type || null === $mime_type || 'undefined' === $mime_type ) {

			$mime_type = 'application/force-download';

		}

		$name = rawurldecode( $name );

		/**
		 * DEV Note: using this method instead
		 * of @ob_end_clean() to clear phpcs error
		 * supression warnings.
		 */
		if ( ob_get_level() ) {
			ob_end_clean();
		}

		$this->set_base_file_headers( $mime_type, $action, $name );

		$size = filesize( $file );

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {

			$http_range = sanitize_text_field( wp_unslash( $_SERVER['HTTP_RANGE'] ) );

			list($a, $range)         = explode( '=', $http_range, 2 );
			list($range)             = explode( ',', $range, 2 );
			list($range, $range_end) = explode( '-', $range );
			$range                   = intval( $range );

			if ( ! $range_end ) {

				$range_end = $size - 1;

			} else {

				$range_end = intval( $range_end );

			}

			$updated_length = $range_end - $range + 1;

			header( 'HTTP/1.1 206 Partial Content' );
			header( 'Content-Length: ' . esc_html( $updated_length ) );
			header( 'Content-Range: bytes ' . esc_html( $range - $range_end / $size ) );

		} else {

			$updated_length = $size;
			header( 'Content-Length: ' . esc_html( $size ) );

		}

		$output = $wp_filesystem->get_contents( $file );

		if ( false !== $output ) {

			/**
			 * Dev Note: Set phpcs to ignore until valid
			 * methods for escaping files (i.e. images, PDF, Excel,
			 * CSV, Word, etc) exist.
			 *
			 * The files output here are uploaded by admins
			 * of the user's site, and are loaded from
			 * the user's site (not loaded from external sources).
			 */
			print( $wp_filesystem->get_contents( $file ) ); // phpcs:ignore WordPress.Security.EscapeOutput

			wp_die();

		} else {

			wp_die( 'Error - unable to open file.' );

		}

		wp_die();
	}


	/**
	 * Set base file headers.
	 *
	 * @param string $mime_type - The file mime type.
	 * @param string $action - The action. Defaults to "download".
	 * @param string $name - The file name for the file to output.
	 */
	private function set_base_file_headers( $mime_type, $action, $name ) {

		header( 'Content-Type: ' . $mime_type );

		if ( 'download' === $action ) {
			header( 'Content-Disposition: attachment; filename="' . esc_html( $name ) . '"' );
		} else {
			header( 'Content-Disposition: inline; filename="' . esc_html( $name ) . '"' );
		}

		header( 'Content-Transfer-Encoding: binary' );
		header( 'Accept-Ranges: bytes' );
		header( 'Cache-control: private' );
		header( 'Pragma: private' );
		header( 'Expires: Sat, 01 Jan 2000 08:00:00 GMT' );
	}

	/**
	 * Disable zlib.output_compression.
	 *
	 * DEPRECATED.
	 */
	private function maybe_disable_zlib_output_compression() {

		if ( ini_get( 'zlib.output_compression' ) ) {

			ini_set( 'zlib.output_compression', 'Off' ); // phpcs:ignore

		}
	}


	/**
	 * Company Select Metabox
	 * Display on Client Page post type.
	 */
	public function rerender_company_select_meta_options() {

		$screen = 'accp_client_pages';

		add_meta_box( 'client-meta-2', 'Assign Company*', array( $this, 'display_meta_company_select_options' ), $screen, 'normal', 'high' );
	}


	/**
	 * Display company select meta box on the Client Pages.
	 */
	public function display_meta_company_select_options() {

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return;
		}

		$accp_user           = (int) get_post_meta( $post_id, 'accp_user', true );
		$global_page_enabled = get_post_meta( $post_id, 'accp_make_page_global', true );

		if ( $accp_user || ( $global_page_enabled && 'global' === $global_page_enabled ) ) {

			if ( $global_page_enabled && 'global' === $global_page_enabled ) {

				$assigned_company_name = 'Global Page';

			} else {

				$assigned_company_name = $accp_user ? get_the_title( $accp_user ) : '';

			}

			?>
			<div class="accp-boxed-container current-assigned-company-container">

				<p><strong>Current Assigned Company:</strong> <?php echo esc_html( $assigned_company_name ); ?></p>

			</div>
			<?php

		} else {

			?>
			<div class="accp-boxed-container current-assigned-company-container">

				<p>Unassigned: Please select an option below to assign this page to a company or set this as a global page.</p>

			</div>
			<?php

		}
		?>

		<?php
		/**
		 * Specify unique company section.
		 */
		?>
		<div class="accp-boxed-container accp-specify-company-field-container">

			<p class="label"><label for="accp_user">Assign Company</label></p>
			
			<p class="accp-field-note">This field is required if this is not a global page.</p>
			<?php

			/**
			 * Assign Company select field.
			 */
			wp_dropdown_pages(
				array(
					'post_type'        => 'accp_clientcompany',
					'id'               => 'company-select',
					'value_field'      => 'ID',
					'echo'             => true,
					'show_option_none' => 'Select a company...',
					'name'             => 'accp_user',
					'selected'         => esc_html( $accp_user ),
					'class'            => 'select2-container',
				)
			);

			?>
		</div> <?php // END .accp-specify-company-field-container. ?>

		<?php
		if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) {

			do_action( 'accp_after_client_page_company_select_section', $post_id );

		}
	}


	/**
	 * Save Client Page meta fields.
	 *
	 * @param int    $post_id - The post ID.
	 * @param object $post - The post object.
	 */
	public function save_client_company_meta_options( $post_id, $post ) {

		if ( current_user_can( 'manage_options' ) && is_admin() && function_exists( 'get_current_screen' ) ) {

			$current_screen = get_current_screen();

			if ( ! $current_screen || empty( $current_screen ) ) {
				return;
			}

			if ( 'accp_client_pages' === $current_screen->id ) {

				if ( ! $post ) {
					return;
				}

				if ( ! $post_id ) {
					return;
				}

				$nonce_field_name = $this->get_accp_post_edit_nonce_field_name();
				$nonce_name       = $this->get_accp_post_edit_nonce_name();

				if ( ! isset( $_REQUEST[ $nonce_field_name ] ) ) {
					return;
				}

				$nonce = sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_field_name ] ) );

				/**
				 * Dev Note: The nonce check is here for phpcs validation.
				 */
				if ( function_exists( 'wp_verify_nonce' ) ) {

					if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
						return;
					}
				}

				/**
				 * Save the Assign Company select field on the Client Pages.
				 */
				if ( $post && ! empty( $_POST['accp_user'] ) ) {

					$company_id = (int) $_POST['accp_user'];
					update_post_meta( $post->ID, 'accp_user', $company_id );

				}

				if ( $post && empty( $_POST['accp_user'] ) ) {
					delete_post_meta( $post->ID, 'accp_user' );
				}

				/**
				 * Save the Enable Global Comany Access check box
				 * on Company Pages.
				 */
				if ( $post && ! empty( $_POST['accp_make_page_global'] ) ) {

					$company_id = sanitize_text_field( wp_unslash( $_POST['accp_make_page_global'] ) );
					update_post_meta( $post->ID, 'accp_make_page_global', $company_id );

					/**
					 * Delete the assigned company if it exists,
					 * since this is now a global page.
					 */
					delete_post_meta( $post->ID, 'accp_user' );

				}

				if ( $post && empty( $_POST['accp_make_page_global'] ) ) {
					delete_post_meta( $post->ID, 'accp_make_page_global' );
				}
			}
		}
	}

	/**
	 * Add a nonce field to all ACCP post type edit forms.
	 */
	public function add_nonce_field_to_post_edit_forms() {

		$screen = array(
			'accp_clientinvoice',
			'accp_clientfile',
			'accp_global_file',
			'accp_client_pages',
			'accp_clientcompany',
		);

		add_meta_box( 'accp-post-edit-nonce', 'ACCP Nonce', array( $this, 'get_accp_post_edit_nonce_field' ), $screen, 'normal', 'high' );
	}

	/**
	 * Get the ACCP post edit nonce field.
	 */
	public function get_accp_post_edit_nonce_field() {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_nonce_field( 'accp_post_edit_form_nonce', 'accp_post_edit_form_nonce_field' );
	}

	/**
	 * Hide ACCP metaboxes.
	 *
	 * @param array  $hidden - Array of hidden metaboxes.
	 * @param object $screen - WP_Screen.
	 *
	 * @return array $hidden - Array of hidden metaboxes.
	 */
	public function hide_accp_metaboxes( $hidden, $screen ) {

		$screens = array(
			'accp_clientinvoice',
			'accp_clientfile',
			'accp_global_file',
			'accp_client_pages',
			'accp_clientcompany',
		);

		if ( ! in_array( $screen->id, $screens, true ) ) {
			return $hidden;
		}

		/**
		 * Hide the post edit nonce metabox.
		 */
		$hidden[] = 'accp-post-edit-nonce';

		return $hidden;
	}

	/**
	 * Add content above the clienfile list table.
	 */
	public function accp_add_content_before_file_list_table() {

		$screen = get_current_screen();

		if (
			'edit.php?post_type=accp_clientfile' === $screen->parent_file ||
			'edit.php?post_type=accp_clientinvoice' === $screen->parent_file ||
			'edit.php?post_type=accp_global_file' === $screen->parent_file
		) {

			if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
				return;
			}

			$url = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

			/**
			 * Add content to the clientfile trash page.
			 */
			if ( strpos( $url ?? '', 'post_status=trash' ) !== false ) {

				/**
				 * Add a nonce to the accp_clientfile trash header that can be used for ajax calls.
				 */
				$clientfile_admin_nonce = wp_create_nonce( 'clientfile_admin_nonce' );

				echo wp_kses_post( '<span id="clientfile-admin-nonce" data-nonce="' . esc_attr( $clientfile_admin_nonce ) . '" class="hidden"> </span>' );

				/**
				 * Add an admin notice on the trash page.
				 */
				$permanent_delete_notice = 'Deleting posts on this screen will also delete any file attached to the post.';
				$permanent_delete_notice = apply_filters( 'accp_update_permanent_post_delete_notice', $permanent_delete_notice );

				echo wp_kses_post( '<div class="notice notice-error"><p>' . esc_html( $permanent_delete_notice ) . '</p></div>' );

			}
		}
	}


	/**
	 * Define MIME Types.
	 *
	 * @return array $mime_types - Array of defined mime types (enabled and disabled).
	 */
	public function accp_defined_file_mime_types() {

		$mime_types = array();

		// PDF.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_pdf',
			'value'          => 'application/pdf',
			'label'          => 'PDF - application/pdf',
			'file_extension' => 'pdf',
		);

		// .doc application/msword.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_doc',
			'value'          => 'application/msword',
			'label'          => 'DOC (Microsoft Word) - application/msword',
			'file_extension' => 'doc',
		);

		// .docx application/vnd.openxmlformats-officedocument.wordprocessingml.document.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_docx',
			'value'          => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'label'          => 'DOCX (Microsoft Word - OpenXML) - application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'file_extension' => 'docx',
		);

		// .xls application/vnd.ms-excel.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_xls',
			'value'          => 'application/vnd.ms-excel',
			'label'          => 'XLS (Microsoft Excel) - application/vnd.ms-excel',
			'file_extension' => 'xls',
		);

		// .xlsx application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_xlsx',
			'value'          => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'label'          => 'XLSX (Microsoft Excel - OpenXML) - application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'file_extension' => 'xlsx',
		);

		// .csv text/csv.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_csv',
			'value'          => 'text/csv',
			'label'          => 'CSV - text/csv',
			'file_extension' => 'csv',
		);

		// .ppt application/vnd.ms-powerpoint.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_ppt',
			'value'          => 'application/vnd.ms-powerpoint',
			'label'          => 'PPT (Microsoft PowerPoint) - application/vnd.ms-powerpoint',
			'file_extension' => 'ppt',
		);

		// .pptx application/vnd.openxmlformats-officedocument.presentationml.presentation.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_pptx',
			'value'          => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'label'          => 'PPTX (Microsoft PowerPoint - OpenXML) - application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'file_extension' => 'pptx',
		);

		// .zip application/zip.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_zip',
			'value'          => 'application/zip',
			'label'          => 'ZIP - application/zip',
			'file_extension' => 'zip',
		);

		// .png image/png.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_png',
			'value'          => 'image/png',
			'label'          => 'PNG Image - image/png',
			'file_extension' => 'png',
		);

		// .jpg/jpeg image/jpeg.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_jpg',
			'value'          => 'image/jpeg',
			'label'          => 'JPEG/JPG Image - image/jpeg',
			'file_extension' => 'jpg',
		);

		// .gif image/gif.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_gif',
			'value'          => 'image/gif',
			'label'          => 'GIF Image - image/gif',
			'file_extension' => 'gif',
		);

		// .tif/.tiff image/tiff.
		$mime_types[] = array(
			'option_name'    => 'accp_file_types_tiff',
			'value'          => 'image/tiff',
			'label'          => 'TIF/TIFF Image - image/tiff',
			'file_extension' => 'tiff',
		);

		// TODO: add a filter to allow developers to add additional mime types.

		return $mime_types;
	}


	/**
	 * Get Enabled ACCP Mime Type Options
	 *
	 * @return array $enabled_mime_types - Array of enabled (only) mime type options.
	 *
	 * Option value = $enabled_mime_types['value'].
	 */
	public function accp_get_enabled_mime_type_options() {

		$defined_mime_types = $this->accp_defined_file_mime_types();

		$enabled_mime_types = array();

		foreach ( $defined_mime_types as $key => $value ) {

			if ( get_option( $value['option_name'] ) ) {

				$enabled_mime_types[] = $value;

			}
		}

		return $enabled_mime_types;
	}


	/**
	 * Get Enabled TCP Mime Type Option Values
	 *
	 * @return array $enabled_mime_type_vals - Array of enabled (only) mime type option values.
	 */
	public function accp_get_enabled_mime_type_option_values() {

		$enabled_mime_types = $this->accp_get_enabled_mime_type_options();

		$enabled_mime_type_vals = array();

		foreach ( $enabled_mime_types as $key => $value ) {

			$enabled_mime_type_vals[] = $value['value'];

		}

		return $enabled_mime_type_vals;
	}


	/**
	 * Add Client Portal sub menu items
	 * in the WP Admin sidebar.
	 */
	public function add_menu_accp_add_sub_menu_items_to_main_menu_item() {

		/**
		 * Company menu page.
		 */
		add_submenu_page( 'accp-settings', 'Companies', 'Companies', 'manage_options', 'edit.php?post_type=accp_clientcompany' );
	}


	/**
	 * Generate user password - AJAX.
	 */
	public function accp_generate_user_password() {

		/**
		 * Verify the nonce.
		 */
		if ( ! isset( $_POST['nonce'] ) ) {
			die();
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'accp_generate_new_user' ) ) {
			die();
		}

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		$password = wp_generate_password( 15, true, false );

		echo esc_html( $password );

		wp_die();
	}


	/**
	 * Get the company directory name from a URL.
	 *
	 * @param string $request_uri - the URL to parse.
	 * @return string|bool $company_dir|false - the company dir name.
	 */
	public function get_company_dir_name_from_url( $request_uri ) {

		if ( ! $request_uri ) {
			return false;
		}

		if ( strpos( $request_uri, '/accp-clientfiles/' ) !== false ) {

			$file_str        = substr( $request_uri, strpos( $request_uri ?? '', '/accp-clientfiles/' ) + 1 );
			$file_path_array = explode( '/', $file_str );
			$company_dir     = $file_path_array[1];

			return $company_dir;

		}

		return false;
	}


	/**
	 * Get the company ID by the company directory name.
	 *
	 * @param string $company_dir - the company dir name (not path).
	 * @return int $company_id - the company ID associated with the dir.
	 */
	public function get_company_id_by_company_dir_name( $company_dir ) {

		if ( ! $company_dir ) {
			return false;
		}

		$company_dir = str_replace( '/', '', $company_dir ?? '' );
		$company_dir = str_replace( '.', '', $company_dir ?? '' );
		$company_id  = '';

		/**
		 * Query for companies that contain a matching
		 * company directory name.
		 *
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			'post_type'   => array( 'accp_clientcompany' ),
			'post_status' => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query'  => array(
				array(
					'key'   => 'accp_dir',
					'value' => $company_dir,
				),
			),
		);

		$wp_query = new WP_Query( $args );

		if ( $wp_query->have_posts() ) {

			$company_id = '';

			while ( $wp_query->have_posts() ) {

				$wp_query->the_post();

				if ( get_the_ID() ) {

					$company_id = get_the_ID();

				}

				break;

			}

			wp_reset_postdata();

			/**
			 * Return the company ID if it's set.
			 */
			if ( $company_id && ! empty( $company_id ) ) {
				return $company_id;
			}
		}

		return false;
	}


	/**
	 * Add columns to the WP user list table.
	 *
	 * @param array $columns - Array of column names.
	 *
	 * @return array $columns - Array of column names.
	 *
	 * @hooked manage_users_columns.
	 */
	public function add_wp_user_list_columns( $columns ) {

		$columns['accp_assigned_companies'] = 'Assigned Companies';

		return $columns;
	}


	/**
	 * Add WP user list table column content.
	 *
	 * @param string $output - The column content.
	 * @param string $column_name - The column name.
	 * @param string $user_id - The ID of the currently listed user.
	 *
	 * @return string $output - The column content.
	 *
	 * @hooked manage_users_custom_column.
	 */
	public function add_wp_user_list_column_content( $output, $column_name, $user_id ) {

		if ( 'accp_assigned_companies' === $column_name ) {

			if ( ! $user_id ) {
				return;
			}

			$company_list = array();

			$user_primary_company = get_user_meta( $user_id, 'client_company', true );

			if ( $user_primary_company ) {

				$post_status      = get_post_status( $user_primary_company ) ? get_post_status( $user_primary_company ) : '';
				$post_status_text = '';

				if ( $post_status && ! empty( $post_status ) ) {

					if ( 'publish' !== $post_status ) {

						$post_status_text = ' (' . $post_status . ')';

					}
				}

				$company_name   = get_the_title( $user_primary_company ) ? esc_html( get_the_title( $user_primary_company ) ) : (int) $user_primary_company;
				$company_list[] = $company_name . $post_status_text;

			}

			$additional_assigned_companies = get_user_meta( $user_id, 'client_additional_company', true );

			if ( $additional_assigned_companies && is_array( $additional_assigned_companies ) && ! empty( $additional_assigned_companies ) ) {

				foreach ( $additional_assigned_companies as $company_id ) {

					$post_status      = get_post_status( $company_id ) ? get_post_status( $company_id ) : '';
					$post_status_text = '';

					if ( $post_status && ! empty( $post_status ) ) {

						if ( 'publish' !== $post_status ) {

							$post_status_text = ' (' . $post_status . ')';

						}
					}

					$company_name   = get_the_title( $company_id ) ? esc_html( get_the_title( $company_id ) ) : (int) $company_id;
					$company_list[] = $company_name . $post_status_text;

				}
			}

			if ( ! empty( $company_list ) ) {

				$company_str = esc_html( implode( ', ', $company_list ) );

				return $company_str;

			}
		}
	}


	/**
	 * Get all assigned companies for a user.
	 *
	 * @param int    $user_id - The ID of the respective user.
	 * @param string $include_statuses - The WP post statuses to include, or empty for all
	 * (example: array('publish', 'draft')).
	 *
	 * @return array $assigned_companies - Array of companies that the user is assigned to.
	 */
	public function get_all_assigned_companies_for_user( $user_id, $include_statuses = array() ) {

		if ( ! $user_id ) {
			return array();
		}

		$company_list = array();

		$user_primary_company = get_user_meta( $user_id, 'client_company', true );

		if ( $user_primary_company ) {

			$post_status = get_post_status( $user_primary_company ) ? get_post_status( $user_primary_company ) : '';

			if ( ( $post_status && ! empty( $post_status ) ) && ( empty( $include_statuses ) || in_array( $post_status, $include_statuses, true ) ) ) {

				$company_name = get_the_title( $user_primary_company ) ? esc_html( get_the_title( $user_primary_company ) ) : (int) $user_primary_company;

				$company_list[ $user_primary_company ]['id']          = $user_primary_company;
				$company_list[ $user_primary_company ]['name']        = $company_name;
				$company_list[ $user_primary_company ]['post_status'] = $post_status;

			}
		}

		$additional_assigned_companies = get_user_meta( $user_id, 'client_additional_company', true );

		if ( $additional_assigned_companies && is_array( $additional_assigned_companies ) && ! empty( $additional_assigned_companies ) ) {

			foreach ( $additional_assigned_companies as $company_id ) {

				$post_status = get_post_status( $company_id ) ? get_post_status( $company_id ) : '';

				if ( ( $post_status && ! empty( $post_status ) ) && ( empty( $include_statuses ) || in_array( $post_status, $include_statuses, true ) ) ) {

					$company_name = get_the_title( $company_id ) ? esc_html( get_the_title( $company_id ) ) : (int) $company_id;

					$company_list[ $company_id ]['id']          = $company_id;
					$company_list[ $company_id ]['name']        = $company_name;
					$company_list[ $company_id ]['post_status'] = $post_status;

				}
			}
		}

		return $company_list;
	}


	/**
	 * Generate admin settings message and error markup.
	 *
	 * @param string $message - The message to be displayed, which can also contain markup.
	 * @param string $type - The type of message type from the
	 * list of available WP admin notice type classes (notice-error,
	 * notice-warning, notice-succes, notice-info).
	 *
	 * @return string $html - The html message to be displayed.
	 */
	public function generate_admin_settings_message( $message, $type = 'notice-info' ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $message || empty( $message ) ) {
			return;
		}

		$accepted_types = array( 'notice-error', 'notice-warning', 'notice-success', 'notice-info' );

		if ( ! in_array( $type, $accepted_types, true ) ) {

			$type = 'notice-info';

		}

		$html = '';

		$html .= '<div class="accp-settings-messages-and-errors-item notice ' . esc_attr( $type ) . '">';

		$html .= $message;

		$html .= '</div>';

		return wp_kses_post( $html );
	}


	/**
	 * Set rewrite flush needed option.
	 */
	public function maybe_set_rewrite_flush_needed_option() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 * Set the rewrite flush needed options
		 * if it's not already set.
		 */
		$saved_option = get_option( 'accp_rewrite_flush_needed_after_settings_change' );

		if ( ! $saved_option || empty( $saved_option ) ) {

			if ( 'rewrite-flush-needed' !== $saved_option ) {

				update_option( 'accp_rewrite_flush_needed_after_settings_change', 'rewrite-flush-needed' );

			}
		}
	}


	/**
	 * Flush rewrite rules if the 'accp_rewrite_flush_needed_after_settings_change'
	 * option is present and set to 'rewrite-flush-needed.'
	 *
	 * @hooked init.
	 */
	public function accp_maybe_flush_rewrite_rules() {

		$saved_option = get_option( 'accp_rewrite_flush_needed_after_settings_change' );

		if ( ! $saved_option || empty( $saved_option ) ) {
			return;
		}

		if ( 'rewrite-flush-needed' !== $saved_option ) {
			return;
		}

		if ( 'rewrite-flush-needed' === $saved_option ) {

			/**
			 * Flush the rewrite rules.
			 */
			flush_rewrite_rules();

			/**
			 * Clear the rewrite flush needed option.
			 */
			delete_option( 'accp_rewrite_flush_needed_after_settings_change' );

		}
	}


	/**
	 * Get the Company column content html.
	 *
	 * This generates the Company column content for post
	 * (file, invoice, global file) WP list tables, and
	 * not for taxonomy lists.
	 *
	 * @param string $column_name - The column name slug.
	 * @param int    $post_id - The post ID.
	 *
	 * @return string $html - The column content html.
	 */
	public function get_company_column_content_for_post_lists( $column_name, $post_id ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'company' !== $column_name ) {
			return;
		}

		$company_id = get_post_meta( $post_id, 'accp_user', true ) ? get_post_meta( $post_id, 'accp_user', true ) : '';

		if ( ! $company_id || empty( $company_id ) ) {
			return;
		}

		$company_post = get_post( $company_id );

		if ( ! $company_post || ! is_object( $company_post ) || empty( $company_post ) ) {
			return;
		}

		$company_name = get_the_title( $company_id ) ? get_the_title( $company_id ) : '';

		$html = '';

		$html .= $company_name;

		/**
		 * If a file exists, output the file path data that
		 * is used when deleting posts.
		 */
		$accp_file = get_post_meta( $post_id, 'accp_file', true );

		if ( $accp_file && ! empty( $accp_file ) && isset( $accp_file['url'] ) ) {

			$file_parse_array    = wp_parse_url( $accp_file['url'] );
			$accp_file_path      = isset( $file_parse_array['path'] ) ? $file_parse_array['path'] : '';
			$accp_file_full_path = untrailingslashit( get_home_path() ) . $accp_file_path;
			$accp_del_nonce      = wp_create_nonce( 'post_delete_file_nonce' );

			$html .= '<span class="data-path-container hidden" data-file-path="' . esc_url( $accp_file_full_path ) . '" data-nonce="' . esc_attr( $accp_del_nonce ) . '"></span>';

		}

		echo wp_kses_post( $html );
	}


	/**
	 * Get the Category column content html.
	 *
	 * This generates the Category column content for post
	 * (file, invoice, client page, global file, etc) WP list tables,
	 * and not for taxonomy lists.
	 *
	 * @param string $column_name - The column name slug.
	 * @param int    $post_id - The post ID.
	 *
	 * @return string $html - The column content html.
	 */
	public function get_category_column_content_for_post_lists( $column_name, $post_id ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'file_category' !== $column_name ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( ! $post_type ) {
			return;
		}

		$category_slug = '';

		if ( 'accp_clientinvoice' === $post_type ) {

			$category_slug = 'accp_invoice_categories';

		}

		if ( 'accp_clientfile' === $post_type ) {

			$category_slug = 'accp_file_categories';

		}

		if ( 'accp_client_pages' === $post_type ) {

			$category_slug = 'accp_client_page_categories';

		}

		if ( 'accp_global_file' === $post_type ) {

			$category_slug = 'accp_global_file_categories';

		}

		if ( empty( $category_slug ) ) {
			return;
		}

		$terms = get_the_terms( $post_id, $category_slug );

		if ( $terms && ! is_wp_error( $terms ) ) {

			$categories = array();

			foreach ( $terms as $term ) {

				$categories[] = $term->name;

			}

			$category_list = join( ', ', $categories );

			echo esc_html( $category_list );

		}
	}


	/**
	 * Add core WP list table columns.
	 *
	 * @param array $columns - Array of column heading data.
	 *
	 * @hooked manage_edit-accp_clientfile_columns.
	 * @hooked manage_edit-accp_clientinvoice_columns.
	 * @hooked manage_edit-accp_global_file_columns.
	 */
	public function add_core_wp_list_table_columns( $columns ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $current_screen;

		$post_type       = $current_screen->post_type;
		$position_column = 'title';

		$new_columns = array();

		foreach ( $columns as $column_slug => $column_name ) {

			$new_columns[ $column_slug ] = $column_name;

			if ( $position_column === $column_slug ) {

				/**
				 * Company
				 */
				if ( 'accp_global_file' !== $post_type ) {

					$new_columns['company'] = __( 'Company', 'constellation-client-portal' );

				}

				/**
				 * Category
				 */
				$new_columns['file_category'] = __( 'Category', 'constellation-client-portal' );

				/**
				 * Status
				 */
				$new_columns['status'] = __( 'Status', 'constellation-client-portal' );

				/**
				 * Document ID
				 */
				$new_columns['doc_id'] = __( 'Document ID', 'constellation-client-portal' );

				/**
				 * Due Date
				 */
				$new_columns['due_date'] = __( 'Due Date', 'constellation-client-portal' );

			}
		}

		return $new_columns;
	}

	/**
	 * Generate an admin tooltip.
	 *
	 * @param array $args - Array of args for the tooltip.
	 * $args = array(
	 *     'message' => $message, - the tooltip message - can contain HTML.
	 *     'position => $message_position - the tooltip position - left, right, center.
	 * ).
	 *
	 * @retrun string $html - The tooltip html.
	 */
	public function generate_wp_admin_tooltip( $args = array() ) {

		$message            = array_key_exists( 'message', $args ) ? $args['message'] : ' ';
		$message_position   = array_key_exists( 'position', $args ) ? $args['position'] : 'center';
		$position_class     = 'accp-wp-admin-has-tooltip-' . esc_html( strtolower( trim( $message_position ) ) );
		$additional_classes = array_key_exists( 'classes', $args ) ? ' ' . esc_html( strtolower( trim( $args['classes'] ) ) ) : '';

		$html = '';

		$html .= '<span class="accp-admin-tooltip-icon">i</span>';

		$html .= '<span class="accp-wp-admin-tooltip accp-wp-admin-tooltip-dark ' . esc_attr( $position_class ) . $additional_classes . '">' . wp_kses_post( $message ) . '</span>';

		return wp_kses_post( $html );
	}
} //End ARS_Constellation_Client_Portal_Admin Class
