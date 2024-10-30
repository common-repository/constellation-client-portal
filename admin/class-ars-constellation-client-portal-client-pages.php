<?php
/**
 * ARS_Constellation_Client_Portal_Client_Pages Class
 *
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez
 * @since      1.0.0
 *
 * Client Pages (accp_client_pages) post type admin specific functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ARS_Constellation_Client_Portal_Client_Pages Class.
 */
class ARS_Constellation_Client_Portal_Client_Pages {

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
	 * @var      object   $utilities    Plugin utility functions class.
	 */
	private $utilities;

	/**
	 * Plugin admin functions.
	 *
	 * @access   private
	 * @var      object   $admin    Plugin admin functions class.
	 */
	private $admin;

	/**
	 * Plugin pro admin functions.
	 *
	 * @access   private
	 * @var      object   $admin    Plugin pro admin functions class.
	 */
	private $pro_admin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of this plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->utilities   = new ACCP_Utility_Functions( $this->plugin_name, $this->version );
		$this->admin       = new ARS_Constellation_Client_Portal_Admin( $this->plugin_name, $this->version );

		if ( class_exists( 'ARS_Constellation_Client_Portal_Pro_Admin' ) ) {

			$this->pro_admin = new ARS_Constellation_Client_Portal_Pro_Admin( $this->plugin_name, $this->version );

		}
	}


	/**
	 * Register the Client Page custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_client_pages_post_type() {

		$labels = array(
			'name'               => _x( 'Client Pages', 'constellation-client-portal' ),
			'singular_name'      => _x( 'Client Page', 'constellation-client-portal' ),
			'add_new'            => _x( 'Add New', 'constellation-client-portal' ),
			'add_new_item'       => _x( 'Add New Client Page', 'constellation-client-portal' ),
			'edit_item'          => _x( 'Edit Client Page', 'constellation-client-portal' ),
			'new_item'           => _x( 'New Client Page', 'constellation-client-portal' ),
			'view_item'          => _x( 'View Client Page', 'constellation-client-portal' ),
			'search_items'       => _x( 'Search Client Pages', 'constellation-client-portal' ),
			'not_found'          => _x( 'No client pages found', 'constellation-client-portal' ),
			'not_found_in_trash' => _x( 'No client pages found in Trash', 'constellation-client-portal' ),
			'parent_item_colon'  => _x( 'Parent Client Page:', 'constellation-client-portal' ),
			'menu_name'          => _x( 'Client Pages', 'constellation-client-portal' ),
		);

		/**
		 * Only show the post type in rest if the user has sufficient
		 * capabilities.  We want to enable show_in_rest to allow Gutenberg support,
		 * without allowing public access to the post type via the WP REST API.
		 */
		$show_in_rest = current_user_can( 'manage_options' ) ? true : false;

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_page_rewrite_slug();

		} else {

			$rewrite_slug = 'accp-client-page';

		}

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => true,
			'supports'            => array( 'title', 'author', 'editor', 'page-attributes', 'thumbnail' ),
			'show_in_rest'        => $show_in_rest,
			'taxonomies'          => array( 'accp_client_page_categories', 'accp_client_page_tags' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array( 'slug' => $rewrite_slug ),
			'capabilities'        => array(
				'edit_post'          => 'manage_options',
				'edit_posts'         => 'manage_options',
				'edit_others_posts'  => 'manage_options',
				'publish_posts'      => 'manage_options',
				'read_post'          => 'manage_options',
				'read_private_posts' => 'manage_options',
				'delete_post'        => 'manage_options',
				'delete_posts'       => 'manage_options',
			),
		);

		register_post_type( 'accp_client_pages', $args );
	}


	/**
	 * Register the Categories for the Client Page custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_taxonomy_client_page_categories() {

		$labels = array(
			'name'                       => _x( 'Client Page Categories', 'constellation-client-portal' ),
			'singular_name'              => _x( 'Client Page Category', 'constellation-client-portal' ),
			'search_items'               => _x( 'Search Categories', 'constellation-client-portal' ),
			'popular_items'              => _x( 'Popular Categories', 'constellation-client-portal' ),
			'all_items'                  => _x( 'All Categories', 'constellation-client-portal' ),
			'parent_item'                => _x( 'Parent Category', 'constellation-client-portal' ),
			'parent_item_colon'          => _x( 'Parent Category:', 'constellation-client-portal' ),
			'edit_item'                  => _x( 'Edit Category', 'constellation-client-portal' ),
			'update_item'                => _x( 'Update Category', 'constellation-client-portal' ),
			'add_new_item'               => _x( 'Add New Category', 'constellation-client-portal' ),
			'new_item_name'              => _x( 'New Category', 'constellation-client-portal' ),
			'separate_items_with_commas' => _x( 'Separate categories with commas', 'constellation-client-portal' ),
			'add_or_remove_items'        => _x( 'Add or remove categories', 'constellation-client-portal' ),
			'choose_from_most_used'      => _x( 'Choose from the most used categories', 'constellation-client-portal' ),
			'menu_name'                  => _x( 'Client Page Categories', 'constellation-client-portal' ),
		);

		/**
		 * Only show the post type in rest if the user has sufficient
		 * capabilities.  We want to enable show_in_rest to allow Gutenberg support,
		 * without allowing public access to the post type via the WP REST API.
		 */
		$show_in_rest = current_user_can( 'manage_options' ) ? true : false;

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_page_categories_rewrite_slug();

		} else {

			$rewrite_slug = false;

		}

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_rest'      => $show_in_rest,
			'show_in_nav_menus' => false,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'hierarchical'      => true,
			'rewrite'           => false !== $rewrite_slug ? array( 'slug' => $rewrite_slug ) : false,
			'query_var'         => true,
			'capabilities'      => array(
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'manage_options',
			),
		);

		register_taxonomy( 'accp_client_page_categories', array( 'accp_client_pages' ), $args );
	}


	/**
	 * Register the Tags for the Client Page custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_taxonomy_client_page_tags() {

		$labels = array(
			'name'                       => _x( 'Client Page Tags', 'constellation-client-portal' ),
			'singular_name'              => _x( 'Client Page Tag', 'constellation-client-portal' ),
			'search_items'               => _x( 'Search Tags', 'constellation-client-portal' ),
			'popular_items'              => _x( 'Popular Tags', 'constellation-client-portal' ),
			'all_items'                  => _x( 'All Tags', 'constellation-client-portal' ),
			'parent_item'                => _x( 'Parent Tag', 'constellation-client-portal' ),
			'parent_item_colon'          => _x( 'Parent Tag:', 'constellation-client-portal' ),
			'edit_item'                  => _x( 'Edit Tag', 'constellation-client-portal' ),
			'update_item'                => _x( 'Update Tag', 'constellation-client-portal' ),
			'add_new_item'               => _x( 'Add New Tag', 'constellation-client-portal' ),
			'new_item_name'              => _x( 'New Tag', 'constellation-client-portal' ),
			'separate_items_with_commas' => _x( 'Separate tags with commas', 'constellation-client-portal' ),
			'add_or_remove_items'        => _x( 'Add or remove tags', 'constellation-client-portal' ),
			'choose_from_most_used'      => _x( 'Choose from the most used tags', 'constellation-client-portal' ),
			'menu_name'                  => _x( 'Client Page Tags', 'constellation-client-portal' ),
		);

		/**
		 * Only show the post type in rest if the user has sufficient
		 * capabilities.  We want to enable show_in_rest to allow Gutenberg support,
		 * without allowing public access to the post type via the WP REST API.
		 */
		$show_in_rest = current_user_can( 'manage_options' ) ? true : false;

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_page_tags_rewrite_slug();

		} else {

			$rewrite_slug = false;

		}

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_rest'      => $show_in_rest,
			'show_in_nav_menus' => false,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'hierarchical'      => false,
			'rewrite'           => false !== $rewrite_slug ? array( 'slug' => $rewrite_slug ) : false,
			'query_var'         => true,
			'capabilities'      => array(
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'manage_options',
			),
		);

		register_taxonomy( 'accp_client_page_tags', array( 'accp_client_pages' ), $args );
	}


	/**
	 * Register the Client Page columns - Company.
	 *
	 * @param array $columns - The columns array.
	 */
	public function accp_client_pages_column_register( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'title' === $column_name ) {
				$new_columns['company'] = __( 'Company', 'tcp' );
			}
		}

		return $new_columns;
	}


	/**
	 * Display the Company column content in the Client Page WP List Table.
	 *
	 * @param string $column_name - The slug name of the column.
	 * @param int    $post_id - The post ID of the Client Page.
	 */
	public function accp_client_pages_column_display_company_name( $column_name, $post_id ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 * Client Company
		 */
		if ( 'company' !== $column_name ) {
			return;
		}

		$company_id = get_post_meta( $post_id, 'accp_user', true ) ? (int) get_post_meta( $post_id, 'accp_user', true ) : '';

		if ( ! $company_id ) {
			return;
		}

		$company_post = get_post( $company_id );

		if ( ! $company_post || ! is_object( $company_post ) || empty( $company_post ) ) {
			return;
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

		/**
		 * Display the company name if one is assigned,
		 * and this is not a global page.
		 */
		if ( isset( $company_id ) && ! empty( $company_id ) && true !== $is_global_page ) {

			$company_name     = get_the_title( $company_id ) ? get_the_title( $company_id ) : '';
			$company_edit_url = get_edit_post_link( $company_id ) ? get_edit_post_link( $company_id ) : '';

			$html = '<a href="' . esc_url( $company_edit_url ) . '">' . esc_html( $company_name ) . '</a>';

			echo wp_kses_post( $html );

		}

		/**
		 * Display Global Page text if
		 * this is a global page.
		 */
		if ( true === $is_global_page ) {

			$hover_message = 'Global Pages are only suitable for companies comprised of users that are only assigned to one company, unless the page content is static.';

			$html = '<span class="accp-column-pointer-item" title="' . esc_attr( $hover_message ) . '" alt="' . esc_attr( $hover_message ) . '">Global Page</span>';

			echo wp_kses_post( $html );

		}
	}
} // END ARS_Constellation_Client_Portal_Client_Pages Class
