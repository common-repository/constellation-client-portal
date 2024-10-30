<?php
/**
 * Public-facing file and invoice list shortcode functionality.
 *
 * @package    ARS_CONSTELLATION_CLIENT_PORTAL
 * @subpackage ARS_Constellation_Client_Portal/public
 * @author     Adrian Rodriguez Studios <dev@adrianrodriguezstudios.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public-facing file and invoice list shortcode functionality.
 */
class ARS_Constellation_Client_Portal_List_Shortcodes {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */

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
	 * Plugin utitlity functions.
	 *
	 * @access   private
	 * @var      bool    $is_pro    True if this is the plugin, or false if not.
	 */
	private $is_pro;

	/**
	 * New line.
	 *
	 * @access   private
	 * @var      string  $new_line    New line.
	 */
	private $new_line;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = ACCP_PLUGIN_NAME;
		$this->version     = ACCP_PLUGIN_VERSION;
		$this->utilities   = new ACCP_Utility_Functions();
		$this->accp_admin  = new ARS_Constellation_Client_Portal_Admin( ACCP_PLUGIN_NAME, ACCP_PLUGIN_VERSION );
		$this->is_pro      = $this->utilities->is_pro_plugin( $this->plugin_name );
		$this->new_line    = "\n";

		/**
		 * Enqueue the list shortcode default CSS.
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_shortcode_style' ) );
	}


	/**
	 * Client file shortcode.
	 *
	 * Shortcode: [accp_clientfiles id="$id"]
	 *
	 * Note: $atts other than "id" are for core and legacy shortcodes.
	 * The pro plugin only requires the "id" att, and gets shortcode
	 * attributes from saved shortcode options.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 *
	 * @return string $html - the shortcode result.
	 */
	public function get_client_files_shortcode( $atts ) {

		/**
		 * Exit if the user is not logged in
		 * or if this is WP Admin.
		 */
		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		/**
		 * Check for the use of legacy shortcode params
		 * and conditionally throw a PHP warning.
		 */
		$this->check_for_legacy_params_in_list_shortcodes( $atts );

		$list_type = 'accp_clientfile';

		$html = $this->generate_the_shortcode( $atts, $list_type );

		return $html;
	}


	/**
	 * Client invoice shortcode.
	 *
	 * Shortcode: [accp_clientinvoices id="$id"]
	 *
	 * Note: $atts other than "id" are for core and legacy shortcodes.
	 * The pro plugin only requires the "id" att, and gets shortcode
	 * attributes from saved shortcode options.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 *
	 * @return string $html - the shortcode result.
	 */
	public function get_client_invoices_shortcode( $atts ) {

		/**
		 * Exit if the user is not logged in
		 * or if this is WP Admin.
		 */
		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		/**
		 * Check for the use of legacy shortcode params
		 * and conditionally throw a PHP warning.
		 */
		$this->check_for_legacy_params_in_list_shortcodes( $atts );

		$list_type = 'accp_clientinvoice';

		$html = $this->generate_the_shortcode( $atts, $list_type );

		return $html;
	}


	/**
	 * Generate the shortcode content (for non-global shortcodes).
	 *
	 * @param array  $atts - array of atts passed in via the shortcode.
	 * @param string $list_type - The list type.
	 *
	 * @return string $html - the shortcode result.
	 */
	public function generate_the_shortcode( $atts, $list_type = 'accp_clientfile' ) {

		if ( ! $atts ) {

			$atts = array();

		}

		/**
		 * Exit if the user is not logged in
		 * or if this is WP Admin.
		 */
		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		/**
		 * Post a shortcode nonce.
		 */
		$this->post_shortcode_nonce();

		global $post;

		$post_id       = get_the_ID();
		$user_id       = get_current_user_id();
		$list_instance = filter_var( $this->generate_list_instance_id(), FILTER_SANITIZE_NUMBER_INT );
		$paged_param   = 'paged' . $list_instance;

		/**
		 * Set the paged value.
		 */
		$paged = 1;

		if ( isset( $_POST['accp_shortcode_nonce'] ) ) {

			$nonce = sanitize_text_field( wp_unslash( $_POST['accp_shortcode_nonce'] ) );

			if ( wp_verify_nonce( $nonce, 'accp_shortcode_nonce' ) ) {
				$paged = isset( $_GET[ $paged_param ] ) ? (int) $_GET[ $paged_param ] : 1;
			}
		}

		$authorized_company_id = $this->get_client_page_authorized_company_id_by_user_id( $user_id, $post_id );
		$allowed_html          = $this->get_shortcode_allowed_html_for_wp_kses();

		/**
		 * Exit if the $authorized_company_id is false.
		 */
		if ( false === $authorized_company_id || ! $authorized_company_id ) {
			return;
		}

		/**
		 * Get the shortcode atts.
		 */
		if ( 'accp_clientinvoice' === $list_type ) {

			$atts = shortcode_atts( $this->get_clientinvoice_core_shortcode_atts(), $atts );

		} else {

			$atts = shortcode_atts( $this->get_clientfile_core_shortcode_atts(), $atts );

		}

		/**
		 * Exit if the user's ID or one of the user's roles
		 * is specified in the shortcodes excluded users or
		 * excluded roles lists.
		 */
		if ( true === $is_pro ) {

			$authorization           = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
			$shortcode_authorization = $authorization->check_shortcode_excluded_users_and_roles( $atts );

			if ( true !== $shortcode_authorization ) {
				return;
			}
		}

		/**
		 * Check if this is the pro plugin and a pro shortcode.
		 *
		 * Account for core and legacy pro shortcode implementations.
		 */
		$shortcode_id     = '';
		$is_pro_shortcode = $this->check_if_shortcode_is_pro( $atts );

		if ( true === $is_pro_shortcode ) {

			$shortcode_id = array_key_exists( 'id', $atts ) ? trim( $atts['id'] ) : '';

		}

		/**
		 * Get the file status var.
		 */
		$file_status = $this->get_file_or_invoice_status_var( $atts );

		/**
		 * Get the categories var.
		 */
		if ( 'accp_clientinvoice' === $list_type ) {

			$taxonomy = 'accp_invoice_categories';

		} else {

			$taxonomy = 'accp_file_categories';

		}

		$categories = $this->get_categories_var( $atts, $taxonomy );

		/**
		 * Get the number of posts var (posts per page).
		 */
		$number_of_posts = $this->get_number_of_posts_var( $atts );

		/**
		 * Get the order_by var.
		 */
		$order_by = $this->get_order_by_var( $atts );

		/**
		 * Get the order var.
		 */
		$order = $this->get_order_var( $atts );

		/**
		 * Get the display excerpt var.
		 */
		$show_excerpt = $this->get_show_excerpt_var( $atts );

		/**
		 * Get the excerpt length var.
		 */
		$excerpt_length = $this->get_excerpt_length_var( $atts );

		/**
		 * Get the thumbnail size var.
		 */
		$show_thumbnail = $this->get_show_thumbnail_var( $atts );

		/**
		 * Get the thumbnail size var.
		 */
		$thumbnail_size = $this->get_thumbnail_size_var( $atts );

		/**
		 * Get the align thumbnail var.
		 */
		$align_thumbnail = $this->get_align_thumbnail_var( $atts );

		/**
		 * Get the link title var.
		 */
		$link_title = $this->get_link_title_var( $atts );

		/**
		 * Get the container id var.
		 */
		$container_id = $this->get_container_id_var( $atts );

		/**
		 * Get the container classes var.
		 */
		$container_classes = $this->get_container_classes_var( $atts );

		/**
		 * Get the display due date var.
		 */
		$due_date = $this->get_due_date_var( $atts );

		/**
		 * Get the display post status var.
		 */
		$post_status = $this->get_post_status_var( $atts );

		/**
		 * Get the display past due notice var.
		 */
		$past_due_notice = $this->get_past_due_notice_var( $atts );

		/**
		 * Get the legacy list_id var.
		 */
		$list_id = $this->get_list_id_var( $atts );

		/**
		 * Generate the post query.
		 */
		if ( 'accp_clientinvoice' === $list_type ) {

			$args = $this->generate_invoice_query_args( $categories, $number_of_posts, $paged, $order_by, $order, $authorized_company_id, $file_status );

		} else {

			$args = $this->generate_file_query_args( $categories, $number_of_posts, $paged, $order_by, $order, $authorized_company_id, $file_status );

		}

		$query = new WP_Query( $args );

		/**
		 * Start the shortcode output.
		 */
		ob_start();

		$html = '';

		/**
		 * Hook - Allow content to be added before the file or invoice list.
		 *
		 * @param $post_id- The ID of the client page on which the shortcode is embedded.
		 */
		ob_start();

		if ( 'accp_clientinvoice' === $list_type ) {

			do_action( 'accp_before_invoice_list', $post_id );

		} else {

			do_action( 'accp_before_file_list', $post_id );

		}

		$html .= ob_get_contents();

		ob_end_clean();

		/**
		 * Get the Loop Content
		 */
		$args = array(
			'list_type'         => $list_type,
			'atts'              => $atts,
			'query'             => $query,
			'container_id'      => $container_id,
			'container_classes' => $container_classes,
			'list_instance'     => $list_instance,
			'link_title'        => $link_title,
			'due_date'          => $due_date,
			'post_status'       => $post_status,
			'past_due_notice'   => $past_due_notice,
			'allowed_html'      => $allowed_html,
			'list_id'           => $list_id,
		);

		$html .= $this->get_the_list_html( $args );

		/**
		 * Hook - Allow content to be added after
		 * the file or invoice list.
		 *
		* @param $post_id- The ID of the client page on which the shortcode is embedded.
		*/
		ob_start();

		if ( 'accp_clientinvoice' === $list_type ) {

			do_action( 'accp_after_invoice_list', $post_id );

		} else {

			do_action( 'accp_after_file_list', $post_id );

		}

		$html .= ob_get_contents();

		ob_end_clean();

		/**
		 * Get the list pagination html.
		 */
		$html .= $this->get_shortcode_pagination_html( $paged_param, $paged, $query, $list_instance, $shortcode_id, $post_id, $list_type );

		$html .= ob_get_clean();

		return $html;
	}


	/**
	 * Get the core atts for the clientfile shortcode.
	 *
	 * @return array $atts - array of defined shortcode atts.
	 */
	public function get_clientfile_core_shortcode_atts() {

		$atts = array(
			'id'              => '', // This is reserved and is for internal use only.
			'css_id'          => '', // Optional ID for the accp_documents_filelist list div.  Defaults to null.
			'class'           => '', // Optional additional class(es) for the accp_documents_filelist list div.  Defaults to null.
			'categories'      => '', // Category atts can be entered as the Slug or ID in the shortcode.
			'file_status'     => '', // File status to show - file status slug or null. Defaults to null.
			'display_number'  => '', // This is the number of posts per page.
			'order_by'        => '', // This allows ordering by post title - default is post date - options "title" or "date".
			'order'           => '', // This can be set as ASC or DESC - default is DESC.
			'show_excerpt'    => '', // Displays truncated text from the post content field.
			'show_thumbnail'  => '', // Displays the posts featured image in the loop.
			'excerpt_length'  => '', // Accepts the number of words for the excerpt.
			'thumbnail_size'  => '', // Enter the thumbnail slug defined in the theme.
			'align_thumbnail' => '', // center, left, right, float-left, float-right.
			'show_post_date'  => '', // Display the post date - default is false.
			'list_id'         => '', // Integer - Useful when there are multiple shortcode lists on the same page - default is null.
			'link_title'      => '', // nolink or empty.  Used to disable post link titles - default is null.
		);

		return apply_filters( 'accp_update_file_atts_array', $atts );
	}


	/**
	 * Get the core atts for the clientinvoice shortcode.
	 *
	 * @return array $atts - array of defined shortcode atts.
	 */
	public function get_clientinvoice_core_shortcode_atts() {

		$atts = array(
			'id'              => '', // This is reserved and is for internal use only.
			'css_id'          => '', // Optional ID for the accp_documents_filelist list div.  Defaults to null.
			'class'           => '', // Optional additional class(es) for the accp_documents_filelist list div.  Defaults to null.
			'categories'      => '', // Category atts can be entered as the Slug or ID in the shortcode.
			'invoice_status'  => '', // Invoice status to show - 'paid' or 'unpaid'. Defaults to 'unpaid'.
			'display_number'  => '', // This is the number of posts per page.
			'order_by'        => '', // This allows ordering by post title - default is post date - options "title" or "date".
			'order'           => '', // This can be set as ASC or DESC - default is DESC.
			'show_excerpt'    => '', // Displays truncated text from the post content field.
			'show_thumbnail'  => '', // Displays the posts featured image in the loop.
			'excerpt_length'  => '', // Accepts the number of words for the excerpt.
			'thumbnail_size'  => '', // Enter the thumbnail slug defined in the theme.
			'align_thumbnail' => '', // center, left, right, float-left, float-right.
			'show_post_date'  => '', // Display the post date - default is false.
			'list_id'         => '', // Integer - Useful when there are multiple shortcode lists on the same page - default is null.
			'link_title'      => '', // nolink or empty.  Used to disable post link titles - default is null.
		);

		return apply_filters( 'accp_update_invoice_atts_array', $atts );
	}


	/**
	 * Get the core atts for the global_file shortcode.
	 *
	 * @return array $atts - array of defined shortcode atts.
	 */
	public function get_global_file_core_shortcode_atts() {

		$atts = array(
			'id'              => '', // This is reserved and is for internal use only.
			'css_id'          => '', // Optional ID for the accp_documents_filelist list div.  Defaults to null.
			'class'           => '', // Optional additional class(es) for the accp_documents_filelist list div.  Defaults to null.
			'categories'      => '', // Category atts can be entered as the Slug or ID in the shortcode.
			'file_status'     => '', // File status to show - file status slug or null. Defaults to null.
			'display_number'  => '', // This is the number of posts per page.
			'order_by'        => '', // This allows ordering by post title - default is post date - options "title" or "date".
			'order'           => '', // This can be set as ASC or DESC - default is DESC.
			'show_excerpt'    => '', // Displays truncated text from the post content field.
			'show_thumbnail'  => '', // Displays the posts featured image in the loop.
			'excerpt_length'  => '', // Accepts the number of words for the excerpt.
			'thumbnail_size'  => '', // Enter the thumbnail slug defined in the theme.
			'align_thumbnail' => '', // center, left, right, float-left, float-right.
			'show_post_date'  => '', // Display the post date - default is false.
			'list_id'         => '', // Integer - Useful when there are multiple shortcode lists on the same page - default is null.
			'link_title'      => '', // nolink or empty.  Used to disable post link titles - default is null.
		);

		return apply_filters( 'accp_update_global_file_atts_array', $atts );
	}


	/**
	 * Check if a given shortcode is a core/legacy shortcode
	 * or a new pro shortcode.
	 *
	 * @param array $atts - The shortcode atts.
	 *
	 * @return bool $is_pro_shortcode - True if the shortcode is a pro shortcode.
	 */
	public function check_if_shortcode_is_pro( $atts ) {

		if ( ! $atts ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $atts ) ) {
			return false;
		}

		/**
		 * Return false if this is not the pro version.
		 */
		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( false === $is_pro ) {
			return false;
		}

		/**
		 * Return false if the id att is not a number.
		 */
		$id = $atts['id'];

		if ( false === ctype_digit( $id ) && false === is_int( $id ) ) {
			return false;
		}

		/**
		 * Pro shortcodes should only have 1 att ("id")
		 * so return false if there is more than one att.
		 */
		$atts_count = count( $atts );

		if ( $atts_count > 1 ) {
			return false;
		}

		/**
		 * If the ID is a number, this should be a new pro
		 * shortcode, so return true here.
		 */
		if ( true === ctype_digit( $id ) || true === is_int( $id ) ) {

			return true;

		}

		return false;
	}


	/**
	 * Set up the $file_status var.
	 *
	 * @param array  $atts - array of atts passed in via the shortcode.
	 * @param string $shortcode_id - The pro shortcode ID (if any).
	 *
	 * DEV Note: The $shortcode_id param is required by the pro plugin.
	 * Ignore PHPCS warnings of this being an unused param.
	 */
	public function get_file_or_invoice_status_var( $atts, $shortcode_id = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		$file_status = array();

		if ( is_array( $atts ) && array_key_exists( 'file_status', $atts ) && ! empty( $atts['file_status'] ) ) {

			$file_status = (array) trim( strtolower( $atts['file_status'] ) );

		}

		if ( is_array( $atts ) && array_key_exists( 'invoice_status', $atts ) && ! empty( $atts['invoice_status'] ) ) {

			$file_status = (array) trim( strtolower( $atts['invoice_status'] ) );

		}

		return apply_filters( 'accp_update_file_status_shortcode_param', $file_status, $atts );
	}


	/**
	 * Set up the $invoice_status var.
	 *
	 * @param array  $atts - array of atts passed in via the shortcode.
	 * @param string $shortcode_id - The pro shortcode ID (if any).
	 *
	 * DEV Note: The $shortcode_id param is required by the pro plugin.
	 * Ignore PHPCS warnings of it being an unused param.
	 */
	public function get_invoice_status_var( $atts, $shortcode_id = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		$invoice_status = array();

		if ( is_array( $atts ) && array_key_exists( 'invoice_status', $atts ) && ! empty( $atts['invoice_status'] ) ) {

			$invoice_status = (array) trim( strtolower( $atts['invoice_status'] ) );

		}

		return apply_filters( 'accp_update_invoice_status_shortcode_param', $invoice_status, $atts );
	}


	/**
	 * Set up the $categories var.
	 *
	 * @param array  $atts - array of atts passed in via the shortcode.
	 * @param string $taxonomy - either 'accp_file_categories' or 'accp_invoice_categories'.
	 * @param string $shortcode_id - The pro shortcode ID (if any).
	 *
	 * @return array $categories - array of category ID's.
	 *
	 * DEV Note: The $shortcode_id param is required by the pro plugin.
	 * Ignore PHPCS warnings of it being an unused param.
	 */
	public function get_categories_var( $atts, $taxonomy, $shortcode_id = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		$categories = array();

		if ( array_key_exists( 'categories', $atts ) && ! empty( $atts['categories'] ) ) {

			$categories_list = explode( ',', trim( $atts['categories'] ) );
			$categories      = array();

			foreach ( $categories_list as $category_item ) {

				$category_item = sanitize_text_field( trim( $category_item ) );

				// We accept either a category ID or slug, so let's try
				// to determine what has been entered.
				if ( ! ctype_digit( $category_item ) ) {

					// We assume this is a slug, get the term by the slug.
					$category = get_term_by( 'slug', $category_item, $taxonomy );

					if ( $category ) {

						$categories[] = $category->term_id;

					}
				} else {

					$category = get_term_by( 'ID', (int) $category_item, $taxonomy );

					if ( $category ) {

						$categories[] = $category_item;

					} else {

						// Account for a category name/slug that is all numerical characters.
						$category = get_term_by( 'slug', $category_item, $taxonomy );

						if ( $category ) {

							$categories[] = $category->term_id;

						}
					}
				}
			}
		}

		return apply_filters( 'accp_update_categories_shortcode_var', $categories, $atts );
	}


	/**
	 * Generate a list instance ID by posting
	 * and incrementing a number that can be assigned
	 * to lists that are added to the page via file and
	 * invoice shortcodes.
	 *
	 * This allows for accurate pagination functionality
	 * when multiple lists are added to the same page, and
	 * can allow for better css and/or script targetting.
	 *
	 * @return int $list_instance_id - a unique number to be used
	 * for a file or invoice list.
	 */
	public function generate_list_instance_id() {

		$nonce_name = 'accp_shortcode_nonce';

		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) );

		if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
			return;
		}

		/**
		 * If $_POST['accp_list_count'] is not set,
		 * this should be the first (or only) list on the page,
		 * so let's set $_POST['accp_list_count'] to "1".
		 */
		if ( ! isset( $_POST['accp_list_count'] ) ) {

			$_POST['accp_list_count'] = 1;

		} else {

			/**
			 * If we've gotten this far and $_POST['accp_list_count']
			 * is already set, There's more than one list on the page.
			 * So, let's increment $_POST['accp_list_count'] by 1 and
			 * use that for the current list's ID (and repeat until
			 * all lists on the page have a unique list ID).             *
			 */
			$_POST['accp_list_count'] = (int) $_POST['accp_list_count'] + 1;

		}

		$list_instance_id = filter_var( wp_unslash( $_POST['accp_list_count'] ), FILTER_SANITIZE_NUMBER_INT );

		return $list_instance_id;
	}


	/**
	 * Post a shortcode nonce.
	 */
	public function post_shortcode_nonce() {

		$nonce_name = 'accp_shortcode_nonce';
		$nonce      = wp_create_nonce( $nonce_name );

		$_POST[ $nonce_name ] = $nonce;
	}


	/**
	 * Get the authorized company ID for a given user.
	 *
	 * This ID is used for generating file and invoice
	 * post loops for the specified company(ID).
	 *
	 * @param int $user_id - The user ID of the respective user.
	 * @param int $post_id - The ID of the current Client Page.
	 *
	 * @return int|bool $authorized_company_id|false - The post ID of the company that the user has
	 * access to.
	 */
	public function get_client_page_authorized_company_id_by_user_id( $user_id, $post_id ) {

		if ( ! $user_id || ! $post_id ) {
			return false;
		}

		/**
		 * Get the primary Company ID associated with the current user.
		 */
		$user_primary_company_id = get_user_meta( $user_id, 'client_company', true );

		/**
		 * Get additonal companies assigned to the user (if any).
		 */
		$additional_assigned_companies = get_user_meta( $user_id, 'client_additional_company', true ) ? get_user_meta( $user_id, 'client_additional_company', true ) : array();

		if ( $additional_assigned_companies && ! empty( $additional_assigned_companies ) ) {

			$additional_assigned_companies = array_map( 'intval', $additional_assigned_companies );
		}

		/**
		 * Check if this is a Client page set to global
		 * access before checking for a saved company.
		 *
		 * Only valid for Client Page post types,
		 * and not Client Files, Client Invoices, or
		 * direct file access.
		 */
		$core_authorization = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );
		$is_global_page     = $core_authorization->is_global_company_page( $post_id );

		if ( true === $is_global_page ) {

			/**
			 * If this is a global page, verify that the current user
			 * only has a single company assigned, otherwise return false.
			 *
			 * Global pages are only suitable for users that have a single
			 * company assigned.
			 */
			if ( $user_primary_company_id && $additional_assigned_companies && ! empty( $additional_assigned_companies ) ) {
				return false;
			}

			if ( ! $user_primary_company_id && $additional_assigned_companies && count( $additional_assigned_companies ) > 1 ) {
				return false;
			}

			/**
			 * Otherwise set the $authorized_company_id to the
			 * user primary company ID, or a single additonal company
			 * if the primary company is not set, and only a single
			 * additional company is set.
			 */
			if ( $user_primary_company_id ) {

				$authorized_company_id = (int) $user_primary_company_id;

				return $authorized_company_id;

			}

			if ( ! empty( $additional_assigned_companies ) && is_array( $additional_assigned_companies ) && count( $additional_assigned_companies ) === 1 ) {

				$authorized_company_id = (int) $additional_assigned_companies[0];

				return $authorized_company_id;

			}
		}

		/**
		 * Get the Company id of the current page if
		 * this is not a global page.
		 */
		$page_company_id = get_post_meta( $post_id, 'accp_user', true ) ? (int) get_post_meta( $post_id, 'accp_user', true ) : '';

		if ( ! $page_company_id || empty( $page_company_id ) ) {
			return false;
		}

		if ( $additional_assigned_companies && ! empty( $additional_assigned_companies ) && in_array( $page_company_id, $additional_assigned_companies, true ) ) {

			$additional_company_id = $page_company_id;

		} else {

			$additional_company_id         = '';
			$additional_assigned_companies = array();

		}

		if ( $additional_company_id && ! empty( $additional_company_id ) ) {

			$authorized_company_id = (int) $additional_company_id;

		} else {

			$authorized_company_id = $user_primary_company_id ? (int) $user_primary_company_id : false;

		}

		/**
		 * Admins have access to all companies, so just
		 * return the current Client Page ID as the authorized
		 * company ID if the current user is an admin.
		 */
		if ( current_user_can( 'manage_options' ) ) {

			$authorized_company_id         = $page_company_id;
			$additional_assigned_companies = array();

			return $authorized_company_id;

		}

		return $authorized_company_id;
	}


	/**
	 * Set up $number_of_posts shortcode var.
	 *
	 * @param array $atts - Array of atts passed in via the shortcode.
	 * @return int $number_of_posts - Integer indicating the number of posts per page.
	 */
	public function get_number_of_posts_var( $atts ) {

		if ( array_key_exists( 'display_number', $atts ) && ! empty( $atts['display_number'] ) ) {

			$att_input       = preg_replace( '/[^0-9]/', '', $atts['display_number'] );
			$number_of_posts = (int) $att_input;

		} else {

			$number_of_posts = 20;

		}

		return apply_filters( 'accp_update_posts_per_page_shortcode_var', $number_of_posts, $atts );
	}


	/**
	 * Set up $order_by shortcode var.
	 *
	 * @param array $atts - Array of atts passed in via the shortcode.
	 * @return string $order_by - The order_by value (ex. "date").
	 */
	public function get_order_by_var( $atts ) {

		if ( array_key_exists( 'order_by', $atts ) && ! empty( $atts['order_by'] ) ) {

			$order_by = sanitize_text_field( trim( $atts['order_by'] ) );

		} else {

			$order_by = 'date';

		}

		return apply_filters( 'accp_update_order_by_shortcode_var', $order_by, $atts );
	}


	/**
	 * Set up $order shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string $order - the order value (ASC or DESC).
	 */
	public function get_order_var( $atts ) {

		if ( array_key_exists( 'order', $atts ) && ! empty( $atts['order'] ) ) {

			$order = sanitize_text_field( trim( $atts['order'] ) );

		} else {

			$order = 'DESC';

		}

		return apply_filters( 'accp_update_order_shortcode_var', $order, $atts );
	}


	/**
	 * Set up $excerpt_length shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return int $excerpt_length - integer.
	 */
	public function get_excerpt_length_var( $atts ) {

		if ( array_key_exists( 'excerpt_length', $atts ) && ! empty( $atts['excerpt_length'] ) ) {

			$att_input = preg_replace( '/[^0-9]/', '', $atts['excerpt_length'] );

			if ( ! empty( $att_input ) ) {

				$excerpt_length = (int) $atts['excerpt_length'];

			} else {

				$excerpt_length = null;

			}
		} else {

			$excerpt_length = null;

		}

		return apply_filters( 'accp_update_excerpt_length_shortcode_var', $excerpt_length, $atts );
	}


	/**
	 * Set up $show_excerpt shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return bool|string $show_excerpt - true|enable to show the excerpt.
	 */
	public function get_show_excerpt_var( $atts ) {

		if ( array_key_exists( 'show_excerpt', $atts ) && ! empty( $atts['show_excerpt'] ) ) {

			$show_excerpt = sanitize_text_field( trim( $atts['show_excerpt'] ) );

		} else {

			$show_excerpt = false;

		}

		return apply_filters( 'accp_update_show_excerpt_shortcode_var', $show_excerpt, $atts );
	}


	/**
	 * Set up $show_thumbnail shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return bool|string $show_thumbnail - true|enable to show the thumbnail.
	 */
	public function get_show_thumbnail_var( $atts ) {

		if ( array_key_exists( 'show_thumbnail', $atts ) && ! empty( $atts['show_thumbnail'] ) ) {

			$show_thumbnail = sanitize_text_field( trim( $atts['show_thumbnail'] ) );

		} else {

			$show_thumbnail = false;

		}

		return apply_filters( 'accp_update_show_thumbnail_shortcode_var', $show_thumbnail, $atts );
	}


	/**
	 * Set up $thumbnail_size shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string $thumbnail_size - the thumbnail slug.
	 */
	public function get_thumbnail_size_var( $atts ) {

		if ( array_key_exists( 'thumbnail_size', $atts ) && ! empty( $atts['thumbnail_size'] ) ) {

			$thumbnail_size = sanitize_text_field( trim( $atts['thumbnail_size'] ) );

		} else {

			$thumbnail_size = null;

		}

		return apply_filters( 'accp_update_thumbnail_size_shortcode_var', $thumbnail_size, $atts );
	}


	/**
	 * Set up $align_thumbnail shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string|false $align_thumbnail - the thumbnail CSS class suffix.
	 */
	public function get_align_thumbnail_var( $atts ) {

		if ( array_key_exists( 'align_thumbnail', $atts ) && ! empty( $atts['align_thumbnail'] ) ) {

			$att_input = trim( strtolower( $atts['align_thumbnail'] ) );

		} else {

			$att_input = '';

		}

		switch ( $att_input ) {

			case 'left':
				$align_thumbnail = '-left';
				break;

			case 'right':
				$align_thumbnail = '-right';
				break;

			case 'center':
				$align_thumbnail = '-cetner';
				break;

			case 'float-left':
				$align_thumbnail = '-float-left';
				break;

			case 'float-right':
				$align_thumbnail = '-float-right';
				break;

			default:
				$align_thumbnail = '';

		}

		return apply_filters( 'accp_update_align_thumbnail_shortcode_var', $align_thumbnail, $atts );
	}


	/**
	 * Set up $link_title shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string $link_title - The link title value.
	 */
	public function get_link_title_var( $atts ) {

		if ( array_key_exists( 'link_title', $atts ) && ! empty( $atts['link_title'] ) ) {

			$link_title = sanitize_text_field( trim( $atts['link_title'] ) );

		} else {

			$link_title = null;

		}

		return apply_filters( 'accp_update_link_title_shortcode_var', $link_title, $atts );
	}



	/**
	 * Set up $show_post_date shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string $show_post_date - The link title value.
	 */
	public function get_show_post_date_var( $atts ) {

		if ( array_key_exists( 'show_post_date', $atts ) && ! empty( $atts['show_post_date'] ) ) {

			$show_post_date = sanitize_text_field( trim( $atts['show_post_date'] ) );

		} else {

			$show_post_date = false;

		}

		return apply_filters( 'accp_update_show_post_date_shortcode_var', $show_post_date, $atts );
	}


	/**
	 * Set up $container_id shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string $container_id - The container ID value.
	 */
	public function get_container_id_var( $atts ) {

		if ( array_key_exists( 'css_id', $atts ) && ! empty( $atts['css_id'] ) ) {

			$container_id = sanitize_text_field( trim( $atts['css_id'] ) );

		} else {

			$container_id = null;

		}

		return sanitize_text_field( apply_filters( 'accp_update_container_id_shortcode_var', $container_id, $atts ) );
	}


	/**
	 * Set up $container_classes shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string $container_classes - The container class value.
	 */
	public function get_container_classes_var( $atts ) {

		if ( array_key_exists( 'class', $atts ) && ! empty( $atts['class'] ) ) {

			$container_classes = sanitize_text_field( trim( $atts['class'] ) );

		} else {

			$container_classes = null;

		}

		return sanitize_text_field( apply_filters( 'accp_update_container_classes_shortcode_var', $container_classes, $atts ) );
	}


	/**
	 * Set up $due_date shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return bool $due_date - The due date value.
	 */
	public function get_due_date_var( $atts ) {

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( ! $is_pro || false === $is_pro ) {
			return false;
		}

		$default_due_date_val = false;
		$due_date             = $default_due_date_val;

		if ( true === $is_pro ) {

			$due_date = sanitize_text_field( apply_filters( 'accp_update_due_date_shortcode_var', $default_due_date_val, $atts ) );

		}

		return $due_date;
	}


	/**
	 * Set up $post_status shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return string|bool $post_status - The post_status value or false.
	 */
	public function get_post_status_var( $atts ) {

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( ! $is_pro || false === $is_pro ) {
			return false;
		}

		$default_post_status_val = false;
		$post_status             = $default_post_status_val;

		if ( true === $is_pro ) {

			$post_status = sanitize_text_field( apply_filters( 'accp_update_post_status_shortcode_var', $default_post_status_val, $atts ) );

		}

		return $post_status;
	}


	/**
	 * Set up $past_due_notice shortcode var.
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 * @return bool $past_due_notice - The past due notice value.
	 */
	public function get_past_due_notice_var( $atts ) {

		$is_pro = $this->utilities->is_pro_plugin( $this->plugin_name );

		if ( ! $is_pro || false === $is_pro ) {
			return false;
		}

		$default_past_due_notice_val = false;
		$past_due_notice             = $default_past_due_notice_val;

		if ( true === $is_pro ) {

			$past_due_notice = sanitize_text_field( apply_filters( 'accp_update_past_due_notice_shortcode_var', $default_past_due_notice_val, $atts ) );

		}

		return $past_due_notice;
	}


	/**
	 * Get the shortcode pagination html.
	 *
	 * @param string $paged_param - The paged_param (ex. "paged1").
	 * @param int    $paged - The paged number/integer.
	 * @param object $query - The post query.
	 * @param int    $list_instance - The auto-generated shortcode instance ID.
	 * @param int    $shortcode_id - The saved shortcode ID (Pro only).
	 * @param int    $post_id - The id of the client page on which the shortcode is embedded.
	 * @param string $list_type - The list type (accp_clientinvoice or accp_clientfile).
	 *
	 * @return string $html - The pagination section html.
	 */
	public function get_shortcode_pagination_html( $paged_param, $paged, $query, $list_instance, $shortcode_id, $post_id, $list_type ) {

		if ( ! $paged_param || ! $paged || ! $query || ! $list_instance ) {
			return;
		}

		$page_args = array(
			'format'  => '?' . $paged_param . '=%#%',
			'current' => $paged,
			'total'   => $query->max_num_pages,
		);

		$html = '';

		if ( paginate_links( $page_args ) ) {

			$html .= '<div class="accp-page-nav-container" data-accp-rel-list="' . esc_attr( $list_instance ) . '>';

			$html .= paginate_links( $page_args );

			$html .= '</div>';

			/**
			 * Hook - Allow content to be added after the list pagination element.
			 *
			 * This is added whether the the pagination element exists or not.
			 *
			 * @param int $post_id - The ID of the page that contains the shortcode.
			 * @param int $shortcode_id - The ID of the saved shortcode (Pro only).
			 * @param int $list_instance - The ID of of the shortcode on the page
			 */
			ob_start();

			if ( 'accp_clientinvoice' === $list_type ) {

				do_action( 'accp_after_invoice_list_pagination', $post_id, $shortcode_id, $list_instance );

			}

			if ( 'accp_clientfile' === $list_type ) {

				do_action( 'accp_after_file_list_pagination', $post_id, $shortcode_id, $list_instance );

			}

			if ( 'accp_global_file' === $list_type ) {

				do_action( 'accp_after_global_file_list_pagination', $post_id, $shortcode_id, $list_instance );

			}

			$html .= ob_get_contents();

			ob_end_clean();

		}

		return wp_kses_post( $html );
	}


	/**
	 * Generate file query args.
	 *
	 * @param array  $categories - array of category ID's.
	 * @param int    $number_of_posts - number of posts to show in the shortcode loop.
	 * @param int    $paged - the query args paged value.
	 * @param string $order_by - the query args order_by value.
	 * @param string $order - the query args order value.
	 * @param int    $authorized_company_id - post ID for the authorized company.
	 * @param string $file_status - file status value.
	 * @return array $args - array of args for the file query.
	 */
	public function generate_file_query_args( $categories, $number_of_posts, $paged, $order_by, $order, $authorized_company_id, $file_status ) {

		/**
		 * Add a tax_query if 'categories' are specified.
		 */
		$tax_query = array();

		if ( ! empty( $categories ) ) {

			$sanitized_category_ids = array();

			foreach ( $categories as $id ) {

				$id = (int) $id;

				if ( $id && ! empty( $id ) ) {

					$sanitized_category_ids[] = $id;

				}
			}

			$tax_query[] = array(
				'taxonomy' => 'accp_file_categories',
				'field'    => 'id',
				'terms'    => $sanitized_category_ids,
			);

		}

		$meta_query = array();

		/**
		 * Add the file status to the meta_query
		 * if it's not empty.
		 */
		if ( ! empty( $file_status ) ) {

			$sanitized_file_statuses = array();

			foreach ( $file_status as $status ) {

				$sanitized_file_statuses[] = sanitize_text_field( $status );

			}

			$meta_query['relation'] = 'AND';

			$meta_query[] = array(
				'key'     => 'file_status',
				'value'   => $sanitized_file_statuses,
				'compare' => 'IN',
			);

		}

		$meta_query[] = array(
			'key'   => 'accp_user',
			'value' => (int) $authorized_company_id,
		);

		$args = array(
			'post_type'      => 'accp_clientfile',
			'posts_per_page' => (int) $number_of_posts,
			'paged'          => sanitize_text_field( $paged ),
			'orderby'        => sanitize_text_field( $order_by ),
			'order'          => sanitize_text_field( $order ),
			'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery
			'tax_query'      => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery
		);

		return $args;
	}


	/**
	 * Generate invoice query args.
	 *
	 * @param array  $categories - array of category ID's.
	 * @param int    $number_of_posts - number of posts to show in the shortcode loop.
	 * @param int    $paged - the query args paged value.
	 * @param string $order_by - the query args order_by value.
	 * @param string $order - the query args order value.
	 * @param int    $authorized_company_id - post ID for the authorized company.
	 * @param string $invoice_status - invoice status value.
	 * @return array $args - array of args for the invoice query.
	 */
	public function generate_invoice_query_args( $categories, $number_of_posts, $paged, $order_by, $order, $authorized_company_id, $invoice_status ) {

		/**
		 * Add a tax_query if 'categories' are specified.
		 */
		$tax_query = array();

		if ( ! empty( $categories ) ) {

			$sanitized_category_ids = array();

			foreach ( $categories as $id ) {

				$id = (int) $id;

				if ( $id && ! empty( $id ) ) {

					$sanitized_category_ids[] = $id;

				}
			}

			$tax_query[] = array(
				'taxonomy' => 'accp_invoice_categories',
				'field'    => 'id',
				'terms'    => $sanitized_category_ids,
			);

		}

		$sanitized_invoice_statuses = array();

		if ( ! empty( $invoice_status ) ) {

			foreach ( $invoice_status as $status ) {

				$sanitized_invoice_statuses[] = sanitize_text_field( $status );

			}
		}

		$args = array(
			'post_type'      => 'accp_clientinvoice',
			'posts_per_page' => (int) $number_of_posts,
			'paged'          => sanitize_text_field( $paged ),
			'orderby'        => sanitize_text_field( $order_by ),
			'order'          => sanitize_text_field( $order ),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'AND',
				array(
					'key'   => 'accp_user',
					'value' => (int) $authorized_company_id,
				),
				array(
					'key'     => 'invoice_status',
					'value'   => $sanitized_invoice_statuses,
					'compare' => 'IN',
				),
			),
			'tax_query'      => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery
		);

		return $args;
	}


	/**
	 * Get the file list html for the file shortcode.
	 *
	 * @param array $args - List of args needed to generate the list html.
	 * $args = array(
	 *  'list_type' => $list_type,
	 *  'atts' => $atts,
	 *  'query' => $query,
	 *  'container_id' => $container_id,
	 *  'container_classes' => $container_classes,
	 *  'list_instance' => $list_instance,
	 *  'link_title' => $link_title,
	 *  'due_date' => $due_date,
	 *  'post_status' => $post_status,
	 *  'past_due_notice' => $past_due_notice,
	 *  'allowed_html' => $allowed_html,
	 *  'list_id' => $list_id,
	 * ).
	 *
	 * @return string $html - The file list html that is output via the file shortcode.
	 */
	public function get_the_list_html( $args ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $args || empty( $args ) ) {
			return;
		}

		$query = array_key_exists( 'query', $args ) && ! empty( $args['query'] ) ? $args['query'] : '';

		if ( ! $query || empty( $query ) ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( ! $user_id || 0 === $user_id ) {
			return;
		}

		$is_pro            = $this->utilities->is_pro_plugin( $this->plugin_name );
		$list_type         = array_key_exists( 'list_type', $args ) && ! empty( $args['list_type'] ) ? $args['list_type'] : 'accp_clientfile';
		$atts              = array_key_exists( 'atts', $args ) && ! empty( $args['atts'] ) ? $args['atts'] : '';
		$container_id      = array_key_exists( 'container_id', $args ) && ! empty( $args['container_id'] ) ? $args['container_id'] : '';
		$container_id_attr = $container_id && ! empty( $container_id ) ? 'id="' . esc_attr( $container_id ) . '"' : '';
		$container_classes = array_key_exists( 'container_classes', $args ) && ! empty( $args['container_classes'] ) ? $args['container_classes'] : '';
		$list_instance     = array_key_exists( 'list_instance', $args ) && ! empty( $args['list_instance'] ) ? $args['list_instance'] : '';
		$list_id           = array_key_exists( 'list_id', $args ) && ! empty( $args['list_id'] ) ? $args['list_id'] : '';
		$link_title        = array_key_exists( 'link_title', $args ) && ! empty( $args['link_title'] ) ? $args['link_title'] : '';
		$due_date          = array_key_exists( 'due_date', $args ) && ! empty( $args['due_date'] ) ? $args['due_date'] : '';
		$past_due_notice   = array_key_exists( 'past_due_notice', $args ) && ! empty( $args['past_due_notice'] ) ? $args['past_due_notice'] : '';
		$allowed_html      = array_key_exists( 'allowed_html', $args ) && ! empty( $args['allowed_html'] ) ? $args['allowed_html'] : '';

		/**
		 * Conditionally add a data-pro-shortcode-id property
		 * to the list container.
		 */
		$pro_shortcode_id = '';

		if ( true === $is_pro ) {

			$pro_shortcode_id = apply_filters( 'accp_update_pro_shortcode_id', $pro_shortcode_id, $atts );

			if ( ! empty( $pro_shortcode_id ) ) {

				$shorcode_id_prop = 'data-pro-shortcode-id=' . (int) $pro_shortcode_id;

			} else {

				$shorcode_id_prop = '';

			}
		} else {

			$shorcode_id_prop = '';

		}

		$html = '';

		$html .= '<div ' . $container_id_attr . ' class="accp_documents_filelist ' . esc_attr( $container_classes ) . '" data-accp-rel-list="' . esc_attr( $list_id ) . '" data-list-id="' . esc_attr( $list_instance ) . '" ' . esc_attr( $shorcode_id_prop ) . '>';

		/**
		 * Post Loop
		 */
		if ( $query->have_posts() ) :

			$post_ids = array();

			while ( $query->have_posts() ) :

				$query->the_post();

				$post_id       = get_the_ID();
				$post_ids[]    = $post_id;
				$attached_file = get_post_meta( $post_id, 'accp_file', true );

				/**
				 * Get the Company ID associated with the file.
				 */
				$company_id = get_post_meta( $post_id, 'accp_user', true );

				/**
				 * Check if pro access checks need to be instantiated.
				 */
				if ( true === $is_pro ) {

					$authorization       = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
					$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

				} else {

					/**
					 * Use the default file check if this is the Core version
					 * to determine if access should be granted.
					 */
					$authorization       = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );
					$check_authorization = $authorization->verify_page_or_post_authorization( $post_id, $user_id );

				}

				if ( true === $check_authorization ) :

					/**
					 * Hook - Allow content to be added before the file or invoice list item.
					 *
					 * Buffer the do_action output to work as excpected in the loop.
					 *
					 * @param $post_id - ID of the file or invoice post in the loop.
					 */
					ob_start();

					if ( 'accp_clientinvoice' === $list_type ) {

						do_action( 'accp_before_invoice_list_item', $post_id );

					} else {

						do_action( 'accp_before_file_list_item', $post_id );

					}

					$html .= ob_get_contents();

					ob_end_clean();

					/**
					 * Start the item.
					 */
					$html .= '<div class="accp-item-container clearfix">';

					/**
					 * Hook - Allow content to be added to
					 * the top of the file or invoice list item.
					 *
					 * @param $post_id - The ID of the post in the loop.
					 */
					ob_start();

					if ( 'accp_clientinvoice' === $list_type ) {

						do_action( 'accp_invoice_list_item_top_inside', $post_id );

					} else {

						do_action( 'accp_file_list_item_top_inside', $post_id );

					}

					$html .= ob_get_contents();

					ob_end_clean();

					/**
					 * Post Title
					 */
					$html .= $this->get_list_item_title_html( $post_id, $user_id, $link_title );

					/**
					 * Post Meta
					 */
					$html .= $this->get_list_item_meta_html( $post_id, $user_id, $atts, $due_date, $past_due_notice );

					/**
					 * Post Thumbnail
					 */
					$html .= $this->get_list_item_thumbnail_html( $post_id, $user_id, $atts );

					/**
					 * Post Excerpt
					 */
					$html .= $this->get_list_item_excerpt_html( $post_id, $user_id, $atts );

					/**
					 * File View and Download
					 */
					$html .= $this->get_list_item_download_section_html( $post_id, $user_id, $attached_file );

					/**
					 * Pro Invoice Elements
					 *
					 * @param $post_id - ID of the file or invoice post in the loop.
					 */
					if ( 'accp_clientinvoice' === $list_type ) {

						ob_start();

						do_action( 'accp_add_invoice_item_elements', $post_id, $atts );

						$html .= ob_get_contents();

						ob_end_clean();

					}

					/**
					 * Hook - Allow content to be added to the bottom of the file or invoice list item.
					 *
					 * Buffer the do_action output to work as excpected in the loop.
					 *
					 * @param $post_id - ID of the file or invoice post in the loop.
					 */
					ob_start();

					if ( 'accp_clientinvoice' === $list_type ) {

						do_action( 'accp_invoice_list_item_bottom_inside', $post_id );

					} else {

						do_action( 'accp_file_list_item_bottom_inside', $post_id );

					}

					$html .= ob_get_contents();

					ob_end_clean();

					/**
					 * Close the loop item container.
					 */
					$html .= '</div>'; // END .accp-item-container.

					/**
					 * Hook - Allow content to be added after the file or invoice list item.
					 *
					 * Buffer the do_action output to work as excpected in the loop.
					 *
					 * @param $post_id - ID of the file or invoice post in the loop.
					 */
					ob_start();

					if ( 'accp_clientinvoice' === $list_type ) {

						do_action( 'accp_after_invoice_list_item', $post_id );

					} else {

						do_action( 'accp_after_file_list_item', $post_id );

					}

					$html .= ob_get_contents();

					ob_end_clean();

				endif;

			endwhile;

			wp_reset_postdata();

			/**
			 * Pro Invoice Elements
			 *
			 * @param array $post_ids - Array of file or invoice post IDs in the loop.
			 * @param array $args - The list html args.
			 */
			if ( 'accp_clientinvoice' === $list_type ) {

				ob_start();

				do_action( 'accp_add_invoice_list_elements', $post_ids, $args );

				$html .= ob_get_contents();

				ob_end_clean();

			}

		endif;

		$html .= '</div>'; // END .accp_documents_filelist.

		return wp_kses( $html, $allowed_html );
	}


	/**
	 * Get the list item title html.
	 *
	 * @param int    $post_id - The ID of the post in the loop.
	 * @param int    $user_id - The ID of the current user.
	 * @param string $link_title - Indication of whether to disable the post title link.
	 *
	 * @return string $html - The section html.
	 */
	public function get_list_item_title_html( $post_id, $user_id, $link_title ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $post_id || ! $user_id ) {
			return;
		}

		/**
		 * Verify Authorization.
		 */
		$core_authorization = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );

		if ( true === $this->is_pro ) {

			$authorization  = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
			$is_global_file = $core_authorization->is_global_file_post( $post_id );

			if ( true === $is_global_file ) {

				$check_authorization = $authorization->verify_global_file_post_authorization_pro( $post_id, $user_id );

			} else {

				$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

			}
		} else {

			/**
			 * Use the default file check if this is the Core version
			 * to determine if access should be granted.
			 */
			$check_authorization = $core_authorization->verify_page_or_post_authorization( $post_id, $user_id );

		}

		if ( true !== $check_authorization ) {
			return;
		}

		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );

		$html = '';

		if ( 'nolink' !== $link_title && 'disable' !== $link_title ) {

			$html .= '<span class="accp-file-list-item-title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></span>';

		} else {

			$html .= '<span class="accp-file-list-item-title">' . esc_html( $title ) . '</span>';

		}

		return wp_kses_post( $html );
	}


	/**
	 * Get the list item meta html.
	 *
	 * @param int         $post_id - The ID of the post in the loop.
	 * @param int         $user_id - The ID of the current user.
	 * @param string      $atts - The shortcode atts.
	 * @param bool|string $due_date - Pro - Whether to display the due date or not.
	 * @param bool|string $past_due_notice - Pro - Whether to display the past due notice or not.
	 *
	 * @return string $html - The section html.
	 */
	public function get_list_item_meta_html( $post_id, $user_id, $atts, $due_date, $past_due_notice ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $post_id || ! $user_id ) {
			return;
		}

		/**
		 * Verify Authorization.
		 */
		$core_authorization = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );

		if ( true === $this->is_pro ) {

			$authorization  = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
			$is_global_file = $core_authorization->is_global_file_post( $post_id );

			if ( true === $is_global_file ) {

				$check_authorization = $authorization->verify_global_file_post_authorization_pro( $post_id, $user_id );

			} else {

				$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

			}
		} else {

			/**
			 * Use the default file check if this is the Core version
			 * to determine if access should be granted.
			 */
			$check_authorization = $core_authorization->verify_page_or_post_authorization( $post_id, $user_id );

		}

		if ( true !== $check_authorization ) {
			return;
		}

		$show_post_date = $this->get_show_post_date_var( $atts );
		$post_date      = get_the_date( '', $post_id );

		$html = '';

		$html .= '<div class="accp-list-item-meta">';

		$content = '';

		/**
		 * Post Date
		 */
		if ( true === $show_post_date || 'true' === $show_post_date || 'enable' === strtolower( $show_post_date ) ) {

			$content .= '<div class="accp-file-post-date">' . esc_html( $post_date ) . '</div>';

		}

		/**
		 * Hook - Filter the section content.
		 */
		$html .= apply_filters( 'accp_file_list_meta_section_content', $content, $post_id, $due_date, $past_due_notice, $atts );

		$html .= '</div>';

		return wp_kses_post( $html );
	}


	/**
	 * Get the list item thumbnail html.
	 *
	 * @param int    $post_id - The ID of the post in the loop.
	 * @param int    $user_id - The ID of the current user.
	 * @param string $atts - The shortcode atts.
	 *
	 * @return string $html - The section html.
	 */
	public function get_list_item_thumbnail_html( $post_id, $user_id, $atts ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $post_id || ! $user_id ) {
			return;
		}

		/**
		 * Verify Authorization.
		 */
		$core_authorization = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );

		if ( true === $this->is_pro ) {

			$authorization  = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
			$is_global_file = $core_authorization->is_global_file_post( $post_id );

			if ( true === $is_global_file ) {

				$check_authorization = $authorization->verify_global_file_post_authorization_pro( $post_id, $user_id );

			} else {

				$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

			}
		} else {

			/**
			 * Use the default file check if this is the Core version
			 * to determine if access should be granted.
			 */
			$check_authorization = $core_authorization->verify_page_or_post_authorization( $post_id, $user_id );

		}

		if ( true !== $check_authorization ) {
			return;
		}

		$show_thumbnail = $this->get_show_thumbnail_var( $atts );

		if ( true !== $show_thumbnail && 'true' !== $show_thumbnail && 'enable' !== $show_thumbnail ) {
			return;
		}

		$thumbnail_size  = $this->get_thumbnail_size_var( $atts );
		$align_thumbnail = $this->get_align_thumbnail_var( $atts );
		$thumbnail       = $thumbnail_size && ! empty( $thumbnail_size ) ? get_the_post_thumbnail( $post_id, $thumbnail_size ) : get_the_post_thumbnail( $post_id );

		$html = '';

		$html .= '<div class="accp-list-item-thumbnail-container file-loop-thumbnail file-thumb-align' . esc_attr( $align_thumbnail ) . '">';

		$html .= $thumbnail;

		$html .= '</div>';

		return wp_kses_post( $html );
	}


	/**
	 * Get the list item excerpt html.
	 *
	 * @param int    $post_id - The ID of the post in the loop.
	 * @param int    $user_id - The ID of the current user.
	 * @param string $atts - The shortcode atts.
	 *
	 * @return string $html - The section html.
	 */
	public function get_list_item_excerpt_html( $post_id, $user_id, $atts ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $post_id || ! $user_id ) {
			return;
		}

		/**
		 * Verify Authorization.
		 */
		$core_authorization = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );

		if ( true === $this->is_pro ) {

			$authorization  = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
			$is_global_file = $core_authorization->is_global_file_post( $post_id );

			if ( true === $is_global_file ) {

				$check_authorization = $authorization->verify_global_file_post_authorization_pro( $post_id, $user_id );

			} else {

				$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

			}
		} else {

			/**
			 * Use the default file check if this is the Core version
			 * to determine if access should be granted.
			 */
			$check_authorization = $core_authorization->verify_page_or_post_authorization( $post_id, $user_id );

		}

		if ( true !== $check_authorization ) {
			return;
		}

		$show_excerpt   = $this->get_show_excerpt_var( $atts );
		$excerpt_length = $this->get_excerpt_length_var( $atts );
		$excerpt        = get_the_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';

		if ( ! $excerpt || empty( $excerpt ) ) {
			return;
		}

		if ( true !== $show_excerpt && 'true' !== $show_excerpt && 'enable' !== $show_excerpt ) {
			return;
		}

		$html = '';

		$html .= '<div class="accp-item-excerpt-container file-loop-description">';

		$html .= wp_trim_words( $excerpt, $excerpt_length, $more = '...' );

		$html .= '</div>';

		return wp_kses_post( $html );
	}


	/**
	 * Get the list item file view and download section html.
	 *
	 * @param int   $post_id - The ID of the post in the loop.
	 * @param int   $user_id - The ID of the current user.
	 * @param array $attached_file - The file attached to the loop item post (if any).
	 *
	 * @return string $html - The section html.
	 */
	public function get_list_item_download_section_html( $post_id, $user_id, $attached_file ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $post_id || ! $user_id || ! $attached_file || empty( $attached_file ) || ! is_array( $attached_file ) ) {
			return;
		}

		/**
		 * Verify Authorization.
		 */
		$core_authorization = new ARS_Constellation_Client_Portal_Core_Authorization( $this->plugin_name, $this->version );

		if ( true === $this->is_pro ) {

			$authorization  = new ARS_Constellation_Client_Portal_Pro_Authorization( $this->plugin_name, $this->version );
			$is_global_file = $core_authorization->is_global_file_post( $post_id );

			if ( true === $is_global_file ) {

				$check_authorization = $authorization->verify_global_file_post_authorization_pro( $post_id, $user_id );

			} else {

				$check_authorization = $authorization->verify_page_or_post_authorization_pro( $post_id, $user_id );

			}
		} else {

			/**
			 * Use the default file check if this is the Core version
			 * to determine if access should be granted.
			 */
			$check_authorization = $core_authorization->verify_page_or_post_authorization( $post_id, $user_id );

		}

		if ( true !== $check_authorization ) {
			return;
		}

		$nonce      = wp_create_nonce( 'accp_file_download_nonce' );
		$url_params = get_site_url() . '/?accp-dl-id=' . $post_id . '&nonce=' . $nonce;

		$html = '';

		$html .= '<div class="accp-view-dl-link-container">';

		if ( isset( $attached_file['url'] ) ) {

			$html .= '<a href="' . esc_url( $attached_file['url'] ) . '" class="view-print accp-file-view-print" target="_blank">View and Print</a>';

		}

		$html .= '<span class="accp-view-download-separator"> | </span>';

		$html .= '<a href="' . esc_url( $url_params ) . '" class="download accp-file-download" data-file-id="' . esc_attr( $post_id ) . '" data-nonce="' . esc_attr( $nonce ) . '" target="_blank">Download</a>';

		$html .= '</div>';

		return wp_kses_post( $html );
	}


	/**
	 * Set up the $list_id var.
	 *
	 * This is a legacy var and is not used in the new
	 * Pro shortcode framework.
	 *
	 * @param array $atts - Array of atts passed in via the shortcode.
	 * @return int $list_id - Integer.
	 */
	public function get_list_id_var( $atts ) {

		if ( ! $atts ) {
			return;
		}

		if ( array_key_exists( 'list_id', $atts ) && ! empty( $atts['list_id'] ) ) {

			$list_id_input = (int) preg_replace( '/[^0-9]/', '', $atts['list_id'] );

			$list_id = filter_var( $list_id_input, FILTER_SANITIZE_NUMBER_INT );

		} else {

			$list_id = '';
		}

		return $list_id;
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
	public function get_shortcode_allowed_html_for_wp_kses( $additional_tags = array() ) {

		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_atts = $this->get_default_wp_kses_atts_for_custom_tag();

		$allowed_html['input'] = $allowed_atts;

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

		if ( is_admin() || ! is_user_logged_in() ) {
			return;
		}

		$default_atts = array(
			'align'       => array(),
			'class'       => array(),
			'type'        => array(),
			'id'          => array(),
			'dir'         => array(),
			'lang'        => array(),
			'style'       => array(),
			'readonly'    => array(),
			'src'         => array(),
			'alt'         => array(),
			'href'        => array(),
			'rel'         => array(),
			'rev'         => array(),
			'target'      => array(),
			'novalidate'  => array(),
			'value'       => array(),
			'name'        => array(),
			'tabindex'    => array(),
			'action'      => array(),
			'method'      => array(),
			'for'         => array(),
			'width'       => array(),
			'height'      => array(),
			'data'        => array(),
			'title'       => array(),
			'checked'     => array(),
			'enctype'     => array(),
			'selected'    => array(),
			'placeholder' => array(),
			'multiple'    => array(),
			'required'    => array(),
			'disabled'    => array(),
			'size'        => array(),
		);

		return $default_atts;
	}


	/**
	 * Check for legacy use of the "id" shortcode
	 * param, and throw a warning if it is being
	 * used incorrectly.
	 *
	 * @param array $atts - The array of shortcode atts.
	 *
	 * @return php warning|null - A PHP warning if the "id" param is being used incorrectly.
	 */
	public function check_for_legacy_params_in_list_shortcodes( $atts ) {

		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( ! $atts || empty( $atts ) ) {
			return;
		}

		if ( ! array_key_exists( 'id', $atts ) || empty( $atts['id'] ) ) {
			return;
		}

		$id = (int) $atts['id'];

		/**
		 * Throw a warning if the $id is
		 * false or 0.
		 */
		if ( false === $id || 0 === $id ) {

			$message = 'The "id" shortcode parameter is reserved for Constellation pro shortcodes.  To add a CSS container ID use the "css_id" shortcode parameter.';

			/**
			 * DEV Note: This warning is useful for site admins to
			 * prompt them to change instances of the legacy core
			 * shortcode "id" param that is now a reserved pro
			 * shortcode parameter (and should not be used in
			 * the core plugin).
			 */
			trigger_error( esc_html( $message ), E_USER_WARNING ); // phpcs:ignore.

		}
	}


	/**
	 * Enqueue the list shortcode stylesheet.
	 */
	public function enqueue_shortcode_style() {

		$handle = $this->get_style_handle();

		wp_enqueue_style( $handle, plugin_dir_url( __DIR__ ) . '/css/ars-constellation-client-portal-list-shortcode-styles.css', $this->version, 'all' );
	}


	/**
	 * Get the shortcode stylesheet handle
	 * for enqueuing and targeting.
	 *
	 * @return string $handle - The stylesheet handle.
	 */
	public function get_style_handle() {

		return 'accp-list-shortcode-style';
	}
}
