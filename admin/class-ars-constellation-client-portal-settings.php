<?php
/**
 * ACCP Core Settings
 *
 * Core settings related functionality.
 *
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACCP Core Settings
 */
class ARS_Constellation_Client_Portal_Settings {

	/**
	 * Plugin utility functions.
	 *
	 * @access   private
	 * @var      object   $utilities    Plugin utility functions class.
	 */
	private $utilities;

	/**
	 * Plugin admin functions.
	 *
	 * @access   private
	 * @var      object   $accp_admin    Plugin admin functions class.
	 */
	private $accp_admin;

	/**
	 * Plugin name.
	 *
	 * @access   private
	 * @var      string   $plugin_name    The plugin name.
	 */
	private $plugin_name;

	/**
	 * The class construct.
	 */
	public function __construct() {

		$this->utilities   = new ACCP_Utility_Functions();
		$this->accp_admin  = new ARS_Constellation_Client_Portal_Admin( ACCP_PLUGIN_NAME, ACCP_PLUGIN_VERSION );
		$this->plugin_name = ACCP_PLUGIN_NAME;

		/**
		 * Add core-only menu items to the defined page list.
		 */
		add_filter( 'accp_update_defined_settings', array( $this, 'add_core_pages_to_defined_settings_page_list' ) );
	}


	/**
	 * Register the main settings menu item.
	 */
	public function register_main_settings_menu_item() {

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( true === $is_pro ) {

			$menu_name = 'Client Portal Pro';

		} else {

			$menu_name = 'Client Portal';

		}

		add_menu_page( 'General Settings', $menu_name, 'manage_options', 'accp-settings', array( $this, 'get_general_settings_page_content' ), 'dashicons-welcome-widgets-menus', 22 );
	}


	/**
	 * Change the main submenu title to 'Settings'
	 * instead of Constellation Client Portal
	 */
	public function add_menu_accp_main_sub_title() {

		add_submenu_page(
			'accp-settings',
			__( 'General Settings', 'constellation-client-portal' ),
			__( 'Settings', 'constellation-client-portal' ),
			'manage_options',
			'accp-settings',
			array( $this, 'accp_settings_page' )
		);
	}


	/**
	 * Add the settings pages to WP Admin.
	 */
	public function add_accp_settings_pages_to_wp_admin() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$defined_settings_pages = $this->get_defined_settings_pages();

		if ( ! $defined_settings_pages || empty( $defined_settings_pages ) ) {
			return;
		}

		foreach ( $defined_settings_pages as $slug => $settings_page ) {

			$label = $settings_page[ $slug ]['label'];

			add_submenu_page(
				' ', // Set to empty (not NULL) to hide in the WP Admin menu.
				$label,
				$label,
				'manage_options',
				$slug,
				array( $this, $settings_page[ $slug ]['callback'] )
			);

		}
	}


	/**
	 * Generate the settings page html for all settings pages.
	 *
	 * @param string $settings_content - The html of all sections to be included in the settings page.
	 * @param bool   $form_wrap - Whether to wrap the settings in a form tag.  Defaults to true.
	 * @param bool   $submit_button - Whether to include the form submit button on the settings page. Defaults to true.
	 * @param string $form_action - "options.php" or empty.  Defaults to empty.
	 * @param string $instatiate_wp_settings - Include the settings name to run settings_fields() and do_settings_sections().  Defaults to empty.
	 */
	public function generate_settings_page_html( $settings_content, $form_wrap = true, $submit_button = true, $form_action = '', $instatiate_wp_settings = '' ) {

		$html = '';

		/**
		 * Display notices, if any.
		 */
		$html .= $this->display_notices();

		$html .= '<div class="accp-settings-page-container wrap">';

		/**
		 * Settings Sidebar
		 */
		$html .= $this->generate_settings_sidebar_html();

		/**
		 * Main settings content container.
		 */
		$html .= '<div id="accp-admin-main-content">';

		/**
		 * Main content header.
		 */
		$html .= '<div class="accp-admin-main-content-header">';

		$html .= '<h1>' . esc_html( $this->get_settings_page_title() ) . '</h1>';

		$html .= '</div>';

		/**
		 * Conditionally output the opening form tag.
		 */
		if ( true === $form_wrap ) {

			$action_destination = $form_action && ! empty( $form_action ) ? $form_action : '';

			$html .= '<form action="' . esc_attr( $action_destination ) . '" method="post" enctype="multipart/form-data">';

			/**
			 * Output the settings form nonce field.
			 */
			$html .= $this->get_settings_form_nonce_field_html();

			if ( $instatiate_wp_settings && ! empty( $instatiate_wp_settings ) ) {

				$html .= $this->instantiate_settings_group( $instatiate_wp_settings );

			}
		}

		/**
		 * Output the settings sections.
		 */
		$html .= $settings_content;

		/**
		 * Conditionally output the submit button element.
		 */
		if ( true === $submit_button ) {

			$html .= $this->get_settings_submit_button_html();

		}

		/**
		 * Conditionally output the closing form tag.
		 */
		if ( true === $form_wrap ) {

			$html .= '</form>';

		}

		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}


	/**
	 * Get the settings nonce name.
	 */
	public function get_settings_form_nonce_name() {

		return 'accp_settings_form_nonce';
	}


	/**
	 * Get the settings nonce field name.
	 */
	public function get_settings_form_nonce_field_name() {

		return 'accp_settings_form_nonce';
	}


	/**
	 * Get the settings nonce field html.
	 */
	public function get_settings_form_nonce_field_html() {

		$nonce_name       = $this->get_settings_form_nonce_name();
		$nonce_field_name = $this->get_settings_form_nonce_field_name();
		$nonce            = wp_create_nonce( $nonce_name );

		$html = '';

		$html .= '<input type="hidden" name="' . esc_attr( $nonce_field_name ) . '" value="' . esc_attr( $nonce ) . '">';

		return $html;
	}


	/**
	 * Generate the settings sidebar html.
	 *
	 * @return string $html - The sidebar html.
	 */
	public function generate_settings_sidebar_html() {

		$plugin_dir_url = ACCP_PLUGIN_DIR_URL;
		$logo_url       = $plugin_dir_url . 'assets/img/accp-full-logo.png';

		$html = '<div id="accp-settings-sidebar-container">';

		$html .= '<span class="accp-setting-logo-container">';

		$html .= '<img src="' . esc_url( $logo_url ) . '">';

		$html .= '</span>';

		$html .= $this->generate_settings_nav_html();

		$html .= '</div>';

		return $html;
	}


	/**
	 * Get the settings nav html.
	 *
	 * @return string $html - The html of the settings nav.
	 */
	public function generate_settings_nav_html() {

		$settings_pages = $this->get_defined_settings_pages();

		$html = '<ul>';

		foreach ( $settings_pages as $slug => $settings_page ) {

			$label           = $settings_page[ $slug ]['label'];
			$url_param_title = str_replace( ' ', '_', $label ?? '' );
			$url             = $settings_page[ $slug ]['url'] . '&title=' . $url_param_title;

			$html .= '<li>';

			$html .= '<a href="' . esc_url( $url ) . '">' . $label . '</a>';

			$html .= '</li>';

		}

		$html .= '<ul>';

		return $html;
	}


	/**
	 * Get the settings page title from the "title"
	 * URL param.
	 *
	 * @return string $page_title - The title of the settings page.
	 */
	public function get_settings_page_title() {

		/**
		 * Check for the global $title first.
		 */
		global $title;

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( 'Settings' === $title ) {
			$page_title = 'General Settings';
		} else {
			$page_title = 'Settings';
		}

		/**
		 * Check for the "title" url param.
		 */
		$url_title_raw = filter_input( INPUT_GET, 'title' );

		if ( ! isset( $url_title_raw ) ) {
			return $page_title;
		}

		$url_title = str_replace( '_', ' ', sanitize_text_field( wp_unslash( $url_title_raw ) ) ?? '' );

		if ( ! $url_title || empty( $url_title ) ) {
			return $page_title;
		}

		$page_title = $url_title;

		if ( $title && ! empty( $title ) && ! $page_title ) {
			return $title;
		}

		return $page_title;
	}


	/**
	 * General Settings page content.
	 */
	public function get_general_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Output the Allowed File Type section.
		 */
		$html .= $this->get_allowed_file_type_section_html();

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_general_settings_page_section_html', $html );

		}

		$form_action            = 'options.php';
		$instatiate_wp_settings = 'ars-constellation-client-portal-settings-group';

		echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );
	}


	/**
	 * Invoice settings page content.
	 */
	public function get_invoice_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_invoice_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			$form_action            = 'options.php';
			$instatiate_wp_settings = 'accp-invoice-settings-group';

			echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );

		}
	}


	/**
	 * File settings page content.
	 */
	public function get_file_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_file_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			$form_action            = 'options.php';
			$instatiate_wp_settings = 'accp-file-settings-group';

			echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );

		}
	}


	/**
	 * Global File settings page content.
	 */
	public function get_global_file_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_global_file_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			$form_action            = 'options.php';
			$instatiate_wp_settings = 'accp-global-file-settings-group';

			echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );

		}
	}


	/**
	 * Client Page settings page content.
	 */
	public function get_client_page_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_client_page_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			$form_action            = 'options.php';
			$instatiate_wp_settings = 'accp-client-page-settings-group';

			echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );

		}
	}

	/**
	 * Company settings page content.
	 */
	public function get_company_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_company_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			$form_action            = 'options.php';
			$instatiate_wp_settings = 'accp-company-settings-group';

			echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );

		}
	}


	/**
	 * Get the email settings page content.
	 */
	public function get_email_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_email_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			$form_action            = 'options.php';
			$instatiate_wp_settings = 'accp-email-settings-group';

			echo wp_kses( $this->generate_settings_page_html( $html, true, true, $form_action, $instatiate_wp_settings ), $allowed_html );

		}
	}


	/**
	 * Get the list/shortcode settings page content.
	 */
	public function get_list_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_list_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			echo wp_kses( $this->generate_settings_page_html( $html, false, false, false, false ), $allowed_html );

		}
	}


	/**
	 * Get the documentation settings page content.
	 */
	public function get_documentation_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Add the core content.
		 */
		$html .= $this->get_core_conent_for_documentation_settings_page();

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_documentation_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			echo wp_kses( $this->generate_settings_page_html( $html, false, false ), $allowed_html );

		}
	}


	/**
	 * Get the site info settings page content.
	 */
	public function get_site_info_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Add the core content.
		 */
		$html .= $this->get_core_conent_for_site_info_settings_page();

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_site_info_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			echo wp_kses( $this->generate_settings_page_html( $html, false, false ), $allowed_html );

		}
	}


	/**
	 * Get the license settings page content.
	 */
	public function get_license_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		/**
		 * Section html filter.
		 */
		if ( true === $is_pro ) {

			$html = apply_filters( 'accp_license_settings_page_section_html', $html );

		}

		if ( ! empty( $html ) ) {

			echo wp_kses( $this->generate_settings_page_html( $html, false, false ), $allowed_html );

		}
	}


	/**
	 * Get the upgrade settings page content.
	 */
	public function get_upgrade_settings_page_content() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$allowed_html = $this->get_customized_allowed_html_for_wp_kses();
		$is_pro       = $this->utilities->is_pro_plugin( $this->plugin_name );
		$html         = '';

		$html .= $this->get_core_conent_for_upgrade_settings_page();

		if ( ! empty( $html ) ) {

			echo wp_kses( $this->generate_settings_page_html( $html, false, false ), $allowed_html );

		}
	}


	/**
	 * Get the content for the Upgrade settings page.
	 *
	 * @return string $html - The html content for the settings page.
	 */
	public function get_core_conent_for_upgrade_settings_page() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( $is_pro ) {
			return '';
		}

		$html = '';

		$html .= '<div>';

		$html .= '<div class="accp-upgrade-tab-banner">';

		$html .= '<h2>Upgrade to Pro</h2>';

		$html .= '<p>Upgrade to Constellation Client Portal Pro today and unlock a wealth of new functionality for your site.</p>';

		$html .= '<a href="https://adrianrodriguezstudios.com/constellation-client-portal/?utm_source=accp-upgrade-tab" target="_blank">Learn More</a>';

		$html .= '</div>';

		$html .= '<a href="https://adrianrodriguezstudios.com/constellation-client-portal/?utm_source=accp-upgrade-tab" target="_blank" class="accp-upgrade-button" title="Get Pro Now">Get Pro</a>';

		$html .= '<h2 class="accp-upgrade-feature-list-heading">Pro Features</h2>';

		$html .= wp_kses_post( $this->accp_generate_upgrade_feature_list_html() );

		$html .= '<div style="clear: both;"></div>';

		$html .= '</div>';

		return $html;
	}


	/**
	 * Get the content for the Documentation settings page.
	 *
	 * @return string $html - The html content for the settings page.
	 */
	public function get_core_conent_for_documentation_settings_page() {

		$is_pro            = $this->utilities->is_pro_plugin( $this->plugin_name );
		$list_settings_url = get_admin_url() . 'admin.php?page=accp-list-settings&title=Lists';

		$html = '';

		$html .= '<div class="accp-settings-section">';

		$html .= '<h2>Quick Start</h2>';

		$html .= '<p>View the <a href="https://adrianrodriguezstudios.com/documentation-constellation-client-portal/#quick-start" target="_blank">Quick Start</a> guide to quickly set up the initial foundation for your client portal.</p>';

		$html .= '</div>';

		$html .= '<div class="accp-settings-section">';

		$html .= '<h2>Documentation</h2>';

		$html .= '<p>View the <a href="https://adrianrodriguezstudios.com/documentation-constellation-client-portal" target="_blank">Documentation</a> for helpful information about the plugin.</p>';

		$html .= '</div>';

		$html .= '<div class="accp-settings-section">';

		$html .= '<h2>Shortcodes</h2>';

		if ( ! $is_pro ) :

			$html .= '<p>View the <a href="https://adrianrodriguezstudios.com/documentation-constellation-client-portal/#shortcodes" target="_blank">Shortcode Documentation</a> for detailed information about all available shortcode parameters, or upgrade to the <a href="https://adrianrodriguezstudios.com/constellation-client-portal/?utm_source=plugin_documentation_upgrade" target="_blank">Pro version</a> for easier list generation and implementation.</p>';

			$html .= '<div class="accp-shortcode-container">';

			$html .= '<h3>File List Shortcodes</h3>';

			$html .= '<div class="shortcode-markup">';

			$html .= '<p>[accp_clientfiles categories="category1-slug, category2-slug" display_number="20" order_by="title" order="ASC" show_excerpt="true" excerpt_length="50" show_thumbnail="true" thumbnail_size="portfolio" align_thumbnail="center" show_post_date="true" due_date="true" past_due_notice="true"]</p>';

			$html .= '</div>';

			$html .= '<div class="shortcode-description">';

			$html .= '<p>The accp_clientfiles shortcode displays a list of files that are assigned to the respective company (of the logged in user).  The list can be filtered by category, as well as other filtering options, and multiple lists can be placed on the same page.</p>';

			$html .= '</div>';

			$html .= '</div>';

			$html .= '<div class="accp-shortcode-container">';

			$html .= '<h3>Invoice List Shortcodes</h3>';

			$html .= '<div class="shortcode-markup">';

			$html .= '<p>[accp_clientinvoices invoice_status="unpaid" display_number="20" order_by="title" order="ASC" show_excerpt="true" excerpt_length="50" show_thumbnail="true" thumbnail_size="portfolio" align_thumbnail="center" show_post_date="true" due_date="true" past_due_notice="true" display_pay_btn="true"]</p>';

			$html .= '</div>';

			$html .= '<div class="shortcode-markup">';

			$html .= '<p>[accp_clientinvoices invoice_status="paid" display_number="20" order_by="title" order="ASC" show_excerpt="true" excerpt_length="50" show_thumbnail="true" thumbnail_size="portfolio" align_thumbnail="center" show_post_date="true"]</p>';

			$html .= '</div>';

			$html .= '<div class="shortcode-description">';

			$html .= '<p>The accp_clientinvoices shortcode displays a list of invoices that are assigned to the respective company (of the logged in user).  Separate lists can be created for paid and unpaid invoices, and each list can be further filtered by category (as well as by other filtering options).  Multiple lists can also be placed on the same page (example: an "unpaid" invoice shortcode can be placed at the top of a page to display the current invoice, and a "paid" invoice shortcode can be placed below that to display a list of past/paid invoices).</p>';

			$html .= '</div>';

		else :

			$html .= '<div class="shortcode-description">';

			$html .= '<p>File and invoice shortcodes can be created in the <a href="' . esc_url( $list_settings_url ) . '">List Settings</a>.  The shortcodes can then be pasted into client pages to display file and invoice lists on the front-end.</p>';

			$html .= '</div>';

		endif;

		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}


	/**
	 * Get the content for the Site Info settings page.
	 *
	 * @return string $html - The html content for the settings page.
	 */
	public function get_core_conent_for_site_info_settings_page() {

		/**
		 * Instantiate WP_Filesystem.
		 */
		global $wp_filesystem;

		WP_Filesystem();

		$accp_file_path  = $this->utilities->accp_get_clientfiles_path();
		$dir_is_writable = $wp_filesystem->is_writable( $accp_file_path );
		$thumbnails      = get_intermediate_image_sizes();

		$html = '';

		$html .= '<div class="accp-settings-section">';

		$html .= '<h2>Your Client File Directory</h2>';

		$html .= '<span>Your client files are stored in "' . esc_url( $accp_file_path ) . '".</span>';

		if ( true === $dir_is_writable ) {

			$p_class              = 'accp-functioning-correctly';
			$dir_writable_message = 'Your file directory is writable.';

		} else {

			$p_class              = 'accp-function-error';
			$dir_writable_message = 'Error: Your file directory is not writable.  Your file uploads will not work until this issue is resolved.  If you have not yet created a File or Invoice post and uploaded a file, this may be the problem.   Create a test File post and attach a file to it.  This will create the accp-clientfiles upload directory. If this notice is still visable after you have done that, please contact your administrator for more information.';

		}

		$html .= '<p class="' . esc_attr( $p_class ) . '">' . esc_html( $dir_writable_message ) . '</p>';

		$html .= '</div>';

		$html .= '<div class="accp-settings-section floated-container">';

		$html .= '<h2>Your Theme\'s Thumbnail Names</h2>';

		$html .= '<span>Below is a list of your theme&apos;s thumbnail names that can be pasted into shortcodes if you choose to enable thumbnails in a given list.</span>';

		$html .= '<ul class="theme-thumb-list">';

		foreach ( $thumbnails as $key => $thumbnail ) {
			$html .= '<li>' . esc_html( $thumbnail ) . '</li>';
		}

		$html .= '</ul>';

		$html .= '</div>';

		$html .= '<div style="clear: both;"></div>';

		return $html;
	}


	/**
	 * Instantiate settings group.
	 *
	 * @param string $settings_name - The name of the settings group.
	 *
	 * @return string $html - The html output of settings_fields() and
	 * do_settings_sections().  Allows the output to be placed with better
	 * precision on the page.
	 */
	public function instantiate_settings_group( $settings_name ) {

		if ( ! $settings_name ) {
			return '';
		}

		ob_start();

		settings_fields( $settings_name );
		do_settings_sections( $settings_name );

		return ob_get_clean();
	}


	/**
	 * Add core-only settings pages to the
	 * defined settings page list.
	 *
	 * @param array $page_list - The defined page list.
	 *
	 * @return array $new_page_list - The updated defined page list.
	 */
	public function add_core_pages_to_defined_settings_page_list( $page_list ) {

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( $is_pro ) {
			return $page_list;
		}

		if ( ! $page_list || ! is_array( $page_list ) ) {

			$page_list = array();

		}

		/**
		 * Add new items to the page list array.
		 */
		$page_list['accp-upgrade-settings'] = 'Upgrade';

		return $page_list;
	}


	/**
	 * Define the settings nav links
	 * and labels.
	 *
	 * @return array $settings_pages - Array of settings pages.
	 */
	public function get_defined_settings_pages() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		$page_list = array(
			'accp-general-settings' => 'General Settings',
		);

		/**
		 * Add pro menu items.
		 *
		 * Dev note: adjust this as needed if core settings
		 * are added to these pages in the future.
		 */
		if ( true === $is_pro ) {

			$page_list['accp-invoice-settings']     = 'Invoices';
			$page_list['accp-file-settings']        = 'Files';
			$page_list['accp-global-file-settings'] = 'Global Files';
			$page_list['accp-client-page-settings'] = 'Client Pages';
			$page_list['accp-company-settings']     = 'Companies';
			$page_list['accp-email-settings']       = 'Email';
			$page_list['accp-list-settings']        = 'Lists';

		}

		$page_list['accp-documentation-settings'] = 'Documentation';
		$page_list['accp-site-info-settings']     = 'Site Info';

		/**
		 * Add the remaning Core menu items.
		 */

		$page_list = apply_filters( 'accp_update_defined_settings', $page_list );

		/**
		 * Generate an array containing the page URL
		 * using the simple page_list array.
		 */
		$settings_pages = array();

		foreach ( $page_list as $slug => $label ) {

			if ( $slug && $label && ! empty( $slug ) && ! empty( $slug ) ) {

				$settings_pages[ $slug ] = $this->genarate_settings_page_item_array( $slug, $label );

			}
		}

		return $settings_pages;
	}


	/**
	 * Generate settings page item.
	 *
	 * @param string $slug - The slug of the settings page.
	 * @param string $label - The label of the settings page.
	 *
	 * @return array $settings_page_array - Array of settings page data.
	 */
	public function genarate_settings_page_item_array( $slug, $label ) {

		if ( ! $slug || ! $label ) {
			return;
		}

		$wp_admin_url             = untrailingslashit( get_admin_url() );
		$settings_pages           = array();
		$slug_accp_prefix_removed = str_replace( 'accp-', '', $slug ?? '' );
		$callback                 = 'get_' . str_replace( '-', '_', $slug_accp_prefix_removed ?? '' ) . '_page_content';

		$settings_pages[ $slug ] = array(
			'slug'     => $slug,
			'label'    => $label,
			'url_slug' => 'admin.php?page=' . $slug,
			'url'      => sanitize_url( $wp_admin_url ) . '/admin.php?page=' . $slug,
			'callback' => $callback,
		);

		return $settings_pages;
	}


	/**
	 * Get Allowed File Types settings section html.
	 *
	 * @return string $html - The settings section html.
	 */
	public function get_allowed_file_type_section_html() {

		$html = '';

		$html .= '<div class="accp-settings-section">';

		$html .= '<h3>Allowed Upload File Types</h3>';

		$html .= '<span>Select the upload file types that you would like to enable for client files and invoices.  At least one file type must be enabled.</span>';

		/**
		 * Generate nonce to delete mime type option data.
		 */
		$delete_mime_nonce = wp_create_nonce( 'delete_mime_nonce' );

		/**
		 * Get list of available mime types.
		 */
		$mime_checkboxes = $this->accp_admin->accp_defined_file_mime_types();

		$html .= '<ul>';

		/**
		 * Outpot Checkboxes
		 */
		foreach ( $mime_checkboxes as $mime_checkbox ) {

			$html .= '<li>';

			/**
			 * Update the $_POST data.
			 */
			$option_name  = $mime_checkbox['option_name'];
			$option_value = $mime_checkbox['value'];
			$option_label = $mime_checkbox['label'];
			$checked      = get_option( $option_name ) && get_option( $option_name ) === $option_value ? 'checked' : '';

			/**
			 * Verify settings form nonce.
			 */
			$nonce_name       = $this->get_settings_form_nonce_name();
			$nonce_field_name = $this->get_settings_form_nonce_field_name();

			if ( isset( $_POST[ $nonce_field_name ] ) ) {

				$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_field_name ] ) );

				if ( wp_verify_nonce( $nonce, $nonce_name ) ) {

					if ( isset( $_POST[ $option_name ] ) ) {

						update_option( $option_name, sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) ) );

					} else {

						delete_option( $option_name );

					}
				}
			}

			/**
			 * Note: the 'name' attribute for the input needs to be the same as the WP option name
			 * as this is used in the ajax function below to clear option data when the box is unchecked.
			 */

			$html .= '<input class="accp_mime_checkbox" data-nonce="' . esc_attr( $delete_mime_nonce ) . '" type="checkbox" name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $option_value ) . '" ' . $checked . '>';

			$html .= esc_html( $option_label );

			$html .= '</li>';

		}

		$html .= '</ul>';

		$html .= '</div>';

		return $html;
	}


	/**
	 * Get the settings submit element html.
	 *
	 * @return string $html - The submit element html.
	 */
	public function get_settings_submit_button_html() {

		$html = '<span id="accp-settings-submit">';

		$html .= get_submit_button();

		$html .= '</span>';

		return $html;
	}


	/**
	 * Get allowed html for wp_kses.
	 *
	 * Example usage "return wp_kses($html, $allowed_html)"
	 *
	 * @param array $additional_tags - Optionally add addtional allowed html tags.
	 *
	 * @return array $allowed_html - Array of allowed html.
	 */
	public function get_customized_allowed_html_for_wp_kses( $additional_tags = array() ) {

		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_atts = $this->get_default_wp_kses_atts_for_custom_tag();

		$allowed_html['input']    = $allowed_atts;
		$allowed_html['select']   = $allowed_atts;
		$allowed_html['option']   = $allowed_atts;
		$allowed_html['form']     = $allowed_atts;
		$allowed_html['textarea'] = $allowed_atts;

		/**
		 * Add additional tags if any are specified.
		 */
		if ( ! empty( $additional_tags ) ) {

			foreach ( $additional_tags as $tag ) {

				if ( ! array_key_exists( $tag, $allowed_html ) ) {

					$allowed_html[ $tag ] = $allowed_atts;

				}
			}
		}

		return $allowed_html;
	}


	/**
	 * Get default wp_kses atts.
	 *
	 * @return array $default_atts - Array of default atts for custom allows html tags for wp_kses.
	 */
	public function get_default_wp_kses_atts_for_custom_tag() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$default_atts = array(
			'align'          => array(),
			'class'          => array(),
			'type'           => array(),
			'id'             => array(),
			'dir'            => array(),
			'lang'           => array(),
			'style'          => array(),
			'readonly'       => array(),
			'src'            => array(),
			'alt'            => array(),
			'href'           => array(),
			'rel'            => array(),
			'rev'            => array(),
			'target'         => array(),
			'novalidate'     => array(),
			'value'          => array(),
			'name'           => array(),
			'tabindex'       => array(),
			'action'         => array(),
			'method'         => array(),
			'for'            => array(),
			'width'          => array(),
			'height'         => array(),
			'data'           => array(),
			'title'          => array(),
			'checked'        => array(),
			'enctype'        => array(),
			'selected'       => array(),
			'placeholder'    => array(),
			'multiple'       => array(),
			'required'       => array(),
			'disabled'       => array(),
			'size'           => array(),
			'min'            => array(),
			'max'            => array(),
			'data-term-slug' => array(),
			'step'           => array(),
			'maxlength'      => array(),
			'data-post-type' => array(),
			'media'          => array(),
		);

		return $default_atts;
	}


	/**
	 * Generate the Pro feature list for the core upgrade tab.
	 */
	public function accp_generate_upgrade_feature_list_html() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$feature_list = array(
			array(
				'title'   => 'Accept Payments for Invoices',
				'content' => 'Integrate with WooCommerce and accept payments for invoices.',
			),
			array(
				'title'   => 'Send Automated Email Notifications',
				'content' => 'Automatically send new post and reminder notification emails to clients.',
			),
			array(
				'title'   => 'Speed Up File and Invoice Creation',
				'content' => 'Bulk create file and invoice posts by importing CSV files.',
			),
			array(
				'title'   => 'Improve Reporting',
				'content' => 'Export file and invoice lists to CSV for improved reporting and integration with other processes in your operation, such as accounting and bookkeeping.',
			),
			array(
				'title'   => 'Improve Tracking and Communication',
				'content' => 'Add internal notes to file and invoice posts for better issue tracking and communication.',
			),
			array(
				'title'   => 'Improve Access Restrictions',
				'content' => 'Fine tune access restrictions by user, role, and category for more granular control your of content.',
			),
		);

		$html = '';

		$html .= '<div class="accp-upgrade-pro-feature-short-list">';

		foreach ( $feature_list as $feature ) {

			$title   = $feature['title'];
			$content = $feature['content'];

			$html .= '<div class="accp-upgrade-pro-feature-item-container">';

			$html .= '<a href="https://adrianrodriguezstudios.com/constellation-client-portal/?utm_source=accp-upgrade-tab" target="_blank">';

			$html .= '<span class="accp-upgrade-pro-feature-item-title">' . wp_kses_post( $title ) . '</span>';

			$html .= '<div class="accp-upgrade-pro-feature-content-container">';

			$html .= '<p>' . wp_kses_post( $content ) . '</p>';

			$html .= '<span class="accp-hover-upgrade-button">Get Pro</span>';

			$html .= '</div>';

			$html .= '</a>';

			$html .= '</div>';

		}

		$html .= '</div>';

		return $html;
	}


	/**
	 * Register Constellation setting.
	 *
	 * Adds the setting option value to a master Constellation
	 * option that should list all plugin options that
	 * have been created.  This can then be used to clear
	 * plugin option data on plugin deletion.
	 *
	 * @param string $option_name - The name of the option to add to the master list.
	 */
	public function register_constellation_setting( $option_name ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $option_name || empty( sanitize_text_field( $option_name ) ) ) {
			return;
		}

		$master_option_list = get_option( 'accp_plugin_option_list' );

		/**
		 * Get the current value of the accp_plugin_option_list option.
		 *
		 * The value of accp_plugin_option_list should always be an array.
		 */
		if ( $master_option_list && ! empty( $master_option_list ) ) {

			/**
			 * Account for the option value being stored
			 * as a string rather than an array, and correct
			 * the value if this is the case.
			 */
			if ( ! is_array( $master_option_list ) ) {

				$updated_master_option = array();

				/**
				 * Add the option name if it does not already exist
				 * in the list.
				 */
				if ( $master_option_list !== $option_name ) {

					$updated_master_option[] = sanitize_text_field( $master_option_list );
					$updated_master_option[] = sanitize_text_field( $option_name );

					update_option( 'accp_plugin_option_list', $updated_master_option );

				}
			} elseif ( ! in_array( $option_name, $master_option_list, true ) ) {

				/**
				 * Add the option name if it does not already exist
				 * in the list.
				 */
				$master_option_list[] = sanitize_text_field( $option_name );

				update_option( 'accp_plugin_option_list', $master_option_list );

			}
		} else {

			$master_option_list = array( sanitize_text_field( $option_name ) );

			update_option( 'accp_plugin_option_list', $master_option_list );

		}
	}


	/**
	 * Register ACCP Settings
	 */
	public function register_client_portal_settings() {

		/**
		 * File Types
		 *
		 * Note: Mime type options must use the 'accp_file_types_' prefix as these are
		 * located via that prefix in the 'accp_save_post' function (ex. accp_file_types_tiff).
		 */

		$defined_mime_types = $this->accp_admin->accp_defined_file_mime_types();

		foreach ( $defined_mime_types as $key => $value ) {
			register_setting( 'ars-constellation-client-portal-settings-group', $value['option_name'] );
			$this->register_constellation_setting( $value['option_name'] );
		}

		/**
		 * Register the license key setting
		 */
		register_setting( 'arscp-license-key-settings-group', 'arscp_license_key' );
		$this->register_constellation_setting( 'arscp_license_key' );
	}


	/**
	 * Display notices on the settings page.
	 *
	 * Notice types: notice-error, notice-warning, notice-succes, notice-info.
	 *
	 * @return string $html - The notice html.
	 */
	public function display_notices() {

		$html = '';

		/**
		 * Check for Constellation notices and errors in $_POST data.
		 */
		if ( isset( $_POST['accp_notices_and_errors'] ) ) {

			$nonce_name       = $this->get_settings_form_nonce_name();
			$nonce_field_name = $this->get_settings_form_nonce_field_name();

			if ( isset( $_POST[ $nonce_field_name ] ) ) {

				$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_field_name ] ) );

				if ( wp_verify_nonce( $nonce, $nonce_name ) ) {

					if ( is_array( $_POST['accp_notices_and_errors'] ) ) {

						$message     = array_key_exists( 'message', $_POST['accp_notices_and_errors'] ) ? sanitize_text_field( wp_unslash( $_POST['accp_notices_and_errors']['message'] ) ) : '';
						$notice_type = array_key_exists( 'notice-type', $_POST['accp_notices_and_errors'] ) ? sanitize_text_field( wp_unslash( $_POST['accp_notices_and_errors']['notice-type'] ) ) : '';

					} else {

						$message     = wp_kses_post( wp_unslash( $_POST['accp_notices_and_errors'] ) );
						$notice_type = 'notice-error';

					}

					$html .= '<div class="inline accp-admin-notice notice ' . esc_attr( $notice_type ) . '">';

					$html .= '<p>' . esc_html( $message ) . '</p>';

					$html .= '</div>';

					return $html;

				}
			}
		}

		/**
		 * Check for Constellation notices and errors in the 'accp_notices_and_errors' option.
		 */
		if ( get_option( 'accp_notices_and_errors' ) && ! empty( get_option( 'accp_notices_and_errors' ) ) ) {

			$message     = get_option( 'accp_notices_and_errors' );
			$notice_type = 'notice-error';

			if ( is_array( get_option( 'accp_notices_and_errors' ) ) ) {

				$option      = get_option( 'accp_notices_and_errors' );
				$message     = array_key_exists( 'message', $option ) ? $option['message'] : '';
				$notice_type = array_key_exists( 'notice-type', $option ) ? $option['notice-type'] : '';

			}

			$html .= '<div class="inline accp-admin-notice notice ' . esc_attr( $notice_type ) . '">';

			$html .= '<p>' . esc_html( $message ) . '</p>';

			$html .= '</div>';

			/**
			 * Clear the option.
			 */
			delete_option( 'accp_notices_and_errors' );

			return $html;

		}

		return $html;
	}
} // END ARS_Constellation_Client_Portal_Settings
