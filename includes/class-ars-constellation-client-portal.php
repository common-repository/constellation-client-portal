<?php
/**
 * Core class. Define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/includes
 * @author     Adrian Rodriguez <adrian@adrianrodriguezstudios.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core class. Define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
class ARS_Constellation_Client_Portal {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ARS_Constellation_Client_Portal_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Layout core functionality
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'ARS_CONSTELLATION_CLIENT_PORTAL_PRO' ) ) {

			$this->version     = ARS_CONSTELLATION_CLIENT_PORTAL_PRO;
			$this->plugin_name = 'ars-constellation-client-portal-pro';

		} elseif ( defined( 'ARS_CONSTELLATION_CLIENT_PORTAL' ) ) {

			$this->version     = ARS_CONSTELLATION_CLIENT_PORTAL;
			$this->plugin_name = 'ars-constellation-client-portal';

		} else {

			$this->version     = '1.0.0';
			$this->plugin_name = 'ars-constellation-client-portal';

		}

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * Include the following files:
	 *
	 * - ARS_Constellation_Client_Portal_Loader. Orchestrates the hooks of the plugin.
	 * - ARS_Constellation_Client_Portal_i18n. Defines internationalization functionality.
	 * - ARS_Constellation_Client_Portal_Admin. Defines all hooks for the admin area.
	 * - ARS_Constellation_Client_Portal_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader used to register the hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ars-constellation-client-portal-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ars-constellation-client-portal-i18n.php';

		/**
		 * Core (Basic and Pro Tiers) Admin functions.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-admin.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-company.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-client-pages.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-file.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-invoice.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-core-authorization.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-core-file-checks.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-users.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ars-constellation-client-portal-settings.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ars-constellation-client-portal-utility-functions.php';
		require_once plugin_dir_path( __DIR__ ) . 'public/shortcodes/class-ars-constellation-client-portal-list-shortcodes.php';

		/**
		 * Pro tier Admin functions.
		 */
		if ( 'ars-constellation-client-portal-pro' === $this->plugin_name ) {

			// Include the Pro functions.
			require_once dirname( __DIR__ ) . '/pro/admin/ars-constellation-client-portal-pro-admin-functions.php';

			// Pro Email Class.
			require_once dirname( __DIR__ ) . '/pro/admin/class-ars-constellation-client-portal-pro-emails.php';

			// Pro Authorization Class.
			require_once dirname( __DIR__ ) . '/pro/admin/class-ars-constellation-client-portal-pro-authorization.php';

		}

		/**
		 * Pro tier Public functions.
		 */
		if ( 'ars-constellation-client-portal-pro' === $this->plugin_name ) {

			// Include the Pro functions.
			require_once dirname( __DIR__ ) . '/pro/public/class-ars-constellation-client-portal-pro-public.php';

		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-ars-constellation-client-portal-public.php';

		$this->loader = new ARS_Constellation_Client_Portal_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ARS_Constellation_Client_Portal_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ARS_Constellation_Client_Portal_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register admin hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin          = new ARS_Constellation_Client_Portal_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_company        = new ARS_Constellation_Client_Portal_Company( $this->get_plugin_name(), $this->get_version() );
		$plugin_client_pages   = new ARS_Constellation_Client_Portal_Client_Pages( $this->get_plugin_name(), $this->get_version() );
		$plugin_client_file    = new ARS_Constellation_Client_Portal_Client_File( $this->get_plugin_name(), $this->get_version() );
		$plugin_client_invoice = new ARS_Constellation_Client_Portal_Client_Invoice( $this->get_plugin_name(), $this->get_version() );
		$plugin_authorization  = new ARS_Constellation_Client_Portal_Core_Authorization( $this->get_plugin_name(), $this->get_version() );
		$plugin_users          = new ARS_Constellation_Client_Portal_Users( $this->get_plugin_name(), $this->get_version() );
		$core_settings         = new ARS_Constellation_Client_Portal_Settings();

		/**
		 * Enqueue core styles and scripts.
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * WP user list.
		 */
		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'add_wp_user_list_columns' );
		$this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'add_wp_user_list_column_content', 10, 3 );

		/**
		 * Remove company from user profile when company is permanently deleted in WP.
		 */
		$this->loader->add_action( 'after_delete_post', $plugin_users, 'remove_company_from_user_on_company_permanent_delete', 10, 2 );

		/**
		 *
		 * Direct file access redirects.
		 */

		/**
		 * Prevent direct access to accp-clientfiles.
		 */
		$this->loader->add_action( 'init', $plugin_admin, 'accp_file_redirect_init' );

		/**
		 * Add accp query_vars.
		 */
		$this->loader->add_filter( 'query_vars', $plugin_admin, 'accp_query_vars' );

		/**
		 * Parse accp query_vars.
		 */
		$this->loader->add_action( 'parse_request', $plugin_admin, 'accp_parse_request' );

		/**
		 * Plugin upgrade complete actions.
		 */
		$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'accp_upgrade_completed', 10, 2 );

		/**
		 * Run plugin initialization functions.
		 */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'accp_plugin_initialize' );

		/**
		 * Maybe flush rewrite rules.
		 */
		$this->loader->add_action( 'init', $plugin_admin, 'accp_maybe_flush_rewrite_rules' );

		/**
		 * Client File
		 *
		 * Requires the $plugin_client_file class.
		 */

		/**
		 * Client File - Register the 'accp_clientfile' post type.
		 */
		$this->loader->add_action( 'init', $plugin_client_file, 'accp_register_accp_clientfile' );

		/**
		 * Client File - Register taxonomies for the 'accp_clientfile' post type.
		 */
		$this->loader->add_action( 'init', $plugin_client_file, 'accp_register_taxonomy_file_categories' );
		$this->loader->add_action( 'init', $plugin_client_file, 'accp_register_taxonomy_file_tags' );

		/**
		 * Client File - WP list table columns.
		 */
		$this->loader->add_filter( 'manage_edit-accp_clientfile_columns', $plugin_admin, 'add_core_wp_list_table_columns' );
		$this->loader->add_action( 'manage_accp_clientfile_posts_custom_column', $plugin_client_file, 'clientfile_column_display_company_name', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientfile_posts_custom_column', $plugin_client_file, 'accp_clientfile_column_display_file_status', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientfile_posts_custom_column', $plugin_client_file, 'clientfile_column_display_wp_id', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientfile_posts_custom_column', $plugin_client_file, 'clientfile_column_display_category', 10, 2 );
		$this->loader->add_filter( 'manage_edit-accp_clientfile_sortable_columns', $plugin_client_file, 'clientfile_column_register_sortable' );
		$this->loader->add_action( 'request', $plugin_client_file, 'clientfile_column_orderby' );

		/**
		 * Client File - Add metaboxes to client files.
		 */
		$this->loader->add_action( 'admin_init', $plugin_client_file, 'rerender_file_status_meta_options' );
		$this->loader->add_action( 'save_post', $plugin_client_file, 'save_file_status_meta_options', 10, 1 );

		/**
		 * Client File - Add Quick Edit fields.
		 */
		$this->loader->add_action( 'quick_edit_custom_box', $plugin_client_file, 'accp_add_file_quick_edit_fields', 10, 2 );

		/**
		 * Client File - Add Bulk Edit fields.
		 */
		$this->loader->add_action( 'bulk_edit_custom_box', $plugin_client_file, 'accp_add_file_bulk_edit_fields', 10, 2 );

		/**
		 * Cliet File - Save File Quick Edit fields.
		 */
		$this->loader->add_action( 'save_post_accp_clientfile', $plugin_client_file, 'accp_save_file_quick_edit_fields', 10, 1 );

		/**
		 * Client File - save bulk edit fields ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_save_file_bulk_edit', $plugin_client_file, 'accp_save_file_bulk_edit' );

		/**
		 * Client File - Add filter fields.
		 */
		$this->loader->add_action( 'restrict_manage_posts', $plugin_client_file, 'accp_add_core_file_list_filter_fields', 10, 2 );
		$this->loader->add_filter( 'parse_query', $plugin_client_file, 'accp_core_file_additional_filters', 10, 1 );

		/**
		 * Client Invoice
		 *
		 * Requires the $plugin_client_invoice class.
		 */

		/**
		 * Client Invoice - Register the 'clientinvoice' post type.
		 */
		$this->loader->add_action( 'init', $plugin_client_invoice, 'accp_register_accp_clientinvoice' );

		/**
		 * Client Invoice - Register taxonomies for the 'clientinvoice' post type.
		 */
		$this->loader->add_action( 'init', $plugin_client_invoice, 'accp_register_taxonomy_invoice_categories' );
		$this->loader->add_action( 'init', $plugin_client_invoice, 'accp_register_taxonomy_invoice_tags' );

		/**
		 * Client Invoice - WP list table columns.
		 */
		$this->loader->add_filter( 'manage_edit-accp_clientinvoice_columns', $plugin_admin, 'add_core_wp_list_table_columns' );
		$this->loader->add_action( 'manage_accp_clientinvoice_posts_custom_column', $plugin_client_invoice, 'accp_clientinvoice_column_display_company_name', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientinvoice_posts_custom_column', $plugin_client_invoice, 'accp_clientinvoice_column_display_invoice_status', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientinvoice_posts_custom_column', $plugin_client_invoice, 'accp_clientinvoice_column_display_wp_document_id', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientinvoice_posts_custom_column', $plugin_client_invoice, 'accp_clientinvoice_column_display_categories', 10, 2 );
		$this->loader->add_filter( 'manage_edit-accp_clientinvoice_sortable_columns', $plugin_client_invoice, 'accp_clientinvoice_column_register_sortable' );
		$this->loader->add_action( 'request', $plugin_client_invoice, 'accp_clientinvoice_column_orderby' );

		/**
		 * Client Invoice - Add Metaboxes to Client Invoices.
		 */
		$this->loader->add_action( 'admin_init', $plugin_client_invoice, 'rerender_invoice_status_meta_options' );
		$this->loader->add_action( 'save_post', $plugin_client_invoice, 'save_invoice_status_meta_options', 10, 1 );

		/**
		 * Client Invoice - Quick Edit fields.
		 */
		$this->loader->add_action( 'quick_edit_custom_box', $plugin_client_invoice, 'accp_add_invoice_quick_edit_fields', 10, 2 );
		$this->loader->add_action( 'bulk_edit_custom_box', $plugin_client_invoice, 'accp_add_invoice_bulk_edit_fields', 10, 2 );
		$this->loader->add_action( 'save_post_accp_clientinvoice', $plugin_client_invoice, 'accp_save_invoice_quick_edit_fields', 10, 1 );
		$this->loader->add_action( 'wp_ajax_accp_save_invoice_bulk_edit', $plugin_client_invoice, 'accp_save_invoice_bulk_edit' );

		/**
		 * Client Invoice - Add filter fields.
		 */
		$this->loader->add_action( 'restrict_manage_posts', $plugin_client_invoice, 'accp_add_core_invoice_list_filter_fields', 10, 1 );
		$this->loader->add_filter( 'parse_query', $plugin_client_invoice, 'accp_core_invoice_additional_filters', 10, 1 );

		/**
		 * Delete file attachments.
		 */
		$this->loader->add_action( 'wp_ajax_accp_delete_file_on_post_delete', $plugin_admin, 'accp_delete_file_on_post_delete' );
		$this->loader->add_action( 'wp_ajax_accp_bulk_delete_file_on_post_delete', $plugin_admin, 'accp_bulk_delete_file_on_post_delete' );
		$this->loader->add_action( 'wp_ajax_accp_bulk_delete_file_on_empty_trash', $plugin_admin, 'accp_bulk_delete_file_on_empty_trash' );

		/**
		 * Change the main submenu title to "Settings."
		 */
		$this->loader->add_action( 'admin_menu', $core_settings, 'add_menu_accp_main_sub_title' );

		/**
		 * Add Companies admin menu item/page.
		 */
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_accp_add_sub_menu_items_to_main_menu_item' );

		/**
		 * Add core settings pages.
		 */
		$this->loader->add_action( 'admin_menu', $core_settings, 'add_accp_settings_pages_to_wp_admin' );
		$this->loader->add_action( 'admin_menu', $core_settings, 'register_main_settings_menu_item' );
		$this->loader->add_action( 'admin_init', $core_settings, 'register_client_portal_settings' );

		/**
		 * Add company select field to Client Pages.
		 */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'rerender_company_select_meta_options' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_client_company_meta_options', 10, 2 );

		/**
		 * Add nonce field to all ACCP post edit forms.
		 */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_nonce_field_to_post_edit_forms' );

		/**
		 * Add user profile fields.
		 */
		$this->loader->add_action( 'show_user_profile', $plugin_users, 'add_core_user_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_users, 'add_core_user_profile_fields' );
		$this->loader->add_action( 'user_new_form', $plugin_users, 'add_core_user_profile_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_users, 'save_extra_user_profile_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_users, 'save_extra_user_profile_fields' );
		$this->loader->add_action( 'user_register', $plugin_users, 'save_extra_user_profile_fields' );

		/**
		 * Restrict page access.
		 */
		$this->loader->add_action( 'wp', $plugin_admin, 'accp_restrict_client_page_access' );
		$this->loader->add_action( 'wp', $plugin_admin, 'accp_restrict_company_page_access' );

		/**
		 * Client File upload.
		 */
		$this->loader->add_action( 'post_edit_form_tag', $plugin_admin, 'accp_update_post_edit_form_tag' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'accp_client_file_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'accp_save_post' );
		$this->loader->add_filter( 'upload_dir', $plugin_admin, 'accp_set_upload_dir' );

		/**
		 * Client Company
		 *
		 * Requires the $plugin_company class.
		 */

		/**
		 * Client Company - Register the 'accp_clientcompany' post type
		 */
		$this->loader->add_action( 'init', $plugin_company, 'accp_register_clientcompany' );

		/**
		 * Client Company - WP list table columns.
		 */
		$this->loader->add_filter( 'manage_edit-accp_clientcompany_columns', $plugin_company, 'clientcompany_column_register' );
		$this->loader->add_action( 'manage_accp_clientcompany_posts_custom_column', $plugin_company, 'clientcompany_column_display_assigned_user_count', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientcompany_posts_custom_column', $plugin_company, 'clientcompany_column_display_home_page', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientcompany_posts_custom_column', $plugin_company, 'clientcompany_column_display_status', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientcompany_posts_custom_column', $plugin_company, 'clientcompany_column_display_primary_user', 10, 2 );
		$this->loader->add_action( 'manage_accp_clientcompany_posts_custom_column', $plugin_company, 'clientcompany_column_display_company_id', 10, 2 );

		/**
		 * Client Company - Add custom fields.
		 */
		$this->loader->add_action( 'admin_init', $plugin_company, 'display_clientcompany_meta_options' );
		$this->loader->add_action( 'save_post', $plugin_company, 'save_clientcompany_meta_options', 10, 2 );

		/**
		 * Client Company - Generate new Client Page on the Company page.  AJAX function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_generate_new_client_page', $plugin_company, 'accp_generate_new_client_page' );

		/**
		 * Client Company - Check for directory names assigned to more than one company.
		 */
		$this->loader->add_action( 'wp_ajax_accp_dismiss_duplicate_dir_assignment_notice', $plugin_company, 'accp_dismiss_duplicate_dir_assignment_notice' );

		/**
		 * Create and assign new primary user ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_create_and_assign_primary_user', $plugin_company, 'accp_create_and_assign_primary_user' );

		/**
		 * Assign existing primary user ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_assign_existing_primary_user', $plugin_company, 'accp_assign_existing_primary_user' );

		/**
		 * Client Pages
		 *
		 * Requires the $plugin_client_pages class.
		 */

		/**
		 * Client Pages - Register the 'accp_client_pages' post type.
		 */
		$this->loader->add_action( 'init', $plugin_client_pages, 'accp_register_client_pages_post_type' );

		/**
		 * Client Pages - Register taxonomies for the 'accp_client_pages' post type.
		 */
		$this->loader->add_action( 'init', $plugin_client_pages, 'accp_register_taxonomy_client_page_categories' );
		$this->loader->add_action( 'init', $plugin_client_pages, 'accp_register_taxonomy_client_page_tags' );

		/**
		 * Client Pages - WP list table columns.
		 */
		$this->loader->add_filter( 'manage_accp_client_pages_posts_columns', $plugin_client_pages, 'accp_client_pages_column_register' );
		$this->loader->add_action( 'manage_accp_client_pages_posts_custom_column', $plugin_client_pages, 'accp_client_pages_column_display_company_name', 10, 2 );

		/**
		 * Reassign accp_clientfile ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_reassign_file_1', $plugin_admin, 'accp_reassign_file_1' );

		/**
		 * Generate company directory ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_generate_company_dir', $plugin_admin, 'accp_generate_company_dir' );

		/**
		 * Specify company directory ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_specify_company_dir', $plugin_admin, 'accp_specify_company_dir' );

		/**
		 * Clear Mime Type option ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_clear_mime_type_option', $plugin_admin, 'accp_clear_mime_type_option' );

		/**
		 * Generate User Password ajax function.
		 */
		$this->loader->add_action( 'wp_ajax_accp_generate_user_password', $plugin_admin, 'accp_generate_user_password' );

		/**
		 * Add content above the clienfile list table.
		 */
		$this->loader->add_action( 'all_admin_notices', $plugin_admin, 'accp_add_content_before_file_list_table', 10, 3 );

		/**
		 * File and Invoice Download AJAX function.
		 */
		$this->loader->add_action( 'init', $plugin_admin, 'accp_get_download' );

		/**
		 * Add items to the plugin row actions menu in the plugin list.
		 */
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) ) . '/ars-constellation-client-portal.php', $plugin_admin, 'accp_add_links_to_plugin_row_actions_menu', 10, 1 );

		/**
		 * Add an upgrade link to the plugin menu in the WP plugin list - base plugin only.
		 */
		if ( 'ars-constellation-client-portal-pro' !== $this->plugin_name ) {

			$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) ) . '/ars-constellation-client-portal.php', $plugin_admin, 'accp_add_upgrade_link_to_plugin_row_actions_menu', 10, 1 );

		}

		/**
		 * Add items to the plugin row meta.
		 */
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'accp_add_plugin_row_meta', 10, 4 );

		/**
		 * Pro tier hooks
		 */
		if ( 'ars-constellation-client-portal-pro' === $this->plugin_name ) {

			$plugin_pro_admin = new ARS_Constellation_Client_Portal_Pro_Admin( $this->get_plugin_name(), $this->get_version() );
			$plugin_pro_email = new ARS_Constellation_Client_Portal_Pro_Email( $this->get_plugin_name(), $this->get_version() );

			require_once dirname( __DIR__ ) . '/pro/admin/ars-constellation-client-portal-pro-admin-hooks.php';

		}
	}


	/**
	 * Register public hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public   = new ARS_Constellation_Client_Portal_Public( $this->get_plugin_name(), $this->get_version() );
		$list_shortcodes = new ARS_Constellation_Client_Portal_List_Shortcodes();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/**
		 * Client File List shortcode - [accp_clientfiles].
		 */
		$this->loader->add_shortcode( 'accp_clientfiles', $list_shortcodes, 'get_client_files_shortcode' );

		/**
		 * Client Invoice List shortcode - [accp_clientinvoices].
		 */
		$this->loader->add_shortcode( 'accp_clientinvoices', $list_shortcodes, 'get_client_invoices_shortcode' );

		/**
		 * Client Home Page Link shortcode - [accp_my_company_page].
		 */
		$this->loader->add_shortcode( 'accp_my_company_page', $plugin_public, 'accp_client_home_link' );

		/**
		 * Pro tier hooks
		 */
		if ( 'ars-constellation-client-portal-pro' === $this->plugin_name ) {

			$plugin_pro_public = new ARS_Constellation_Client_Portal_Pro_Public( $this->get_plugin_name(), $this->get_version() );
			require_once dirname( __DIR__ ) . '/pro/public/ars-constellation-client-portal-pro-public-hooks.php';

		}
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get the plugin name for WordPress and to define
	 * internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get the loader.
	 *
	 * @since     1.0.0
	 * @return    ARS_Constellation_Client_Portal_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
