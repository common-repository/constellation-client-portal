<?php
/**
 * ARS_Constellation_Client_Portal_Client_Invoice Class
 *
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez
 * @since      1.0.0
 *
 * Client Invoice admin specific functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ARS_Constellation_Client_Portal_Client_Invoice Class
 */
class ARS_Constellation_Client_Portal_Client_Invoice {

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
	 * The class construct.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
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
	 * Register the Client Invoice custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_accp_clientinvoice() {

		$labels = array(
			'name'               => _x( 'Client Invoices', 'constellation-client-portal' ),
			'singular_name'      => _x( 'Client Invoice', 'constellation-client-portal' ),
			'add_new'            => _x( 'Add New', 'constellation-client-portal' ),
			'add_new_item'       => _x( 'Add New Client Invoice', 'constellation-client-portal' ),
			'edit_item'          => _x( 'Edit Client Invoice', 'constellation-client-portal' ),
			'new_item'           => _x( 'New Client Invoice', 'constellation-client-portal' ),
			'view_item'          => _x( 'View Client Invoice', 'constellation-client-portal' ),
			'search_items'       => _x( 'Search Client Invoices', 'constellation-client-portal' ),
			'not_found'          => _x( 'No client invoices found', 'constellation-client-portal' ),
			'not_found_in_trash' => _x( 'No client invoices found in Trash', 'constellation-client-portal' ),
			'parent_item_colon'  => _x( 'Parent Client Invoice:', 'constellation-client-portal' ),
			'menu_name'          => _x( 'Client Invoices', 'constellation-client-portal' ),
		);

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_invoice_rewrite_slug();

		} else {

			$rewrite_slug = false;

		}

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'author', 'editor', 'excerpt', 'thumbnail' ),
			'taxonomies'          => array( 'accp_invoice_categories', 'accp_invoice_tags' ),
			'show_in_rest'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false !== $rewrite_slug ? array( 'slug' => $rewrite_slug ) : false,
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

		register_post_type( 'accp_clientinvoice', $args );
	}


	/**
	 * Register the Categories for the Client Invoice custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_taxonomy_invoice_categories() {

		$labels = array(
			'name'                       => _x( 'Client Invoice Categories', 'constellation-client-portal' ),
			'singular_name'              => _x( 'Client Invoice Category', 'constellation-client-portal' ),
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
			'menu_name'                  => _x( 'Invoice Categories', 'constellation-client-portal' ),
		);

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_invoice_categories_rewrite_slug();

		} else {

			$rewrite_slug = false;

		}

		$args = array(
			'labels'            => $labels,
			'public'            => true,
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

		register_taxonomy( 'accp_invoice_categories', array( 'accp_clientinvoice' ), $args );
	}


	/**
	 * Register the Tags for the Client Invoice custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_taxonomy_invoice_tags() {

		$labels = array(
			'name'                       => _x( 'Client Invoice Tags', 'constellation-client-portal' ),
			'singular_name'              => _x( 'Client Invoice Tags', 'constellation-client-portal' ),
			'search_items'               => _x( 'Search Tags', 'constellation-client-portal' ),
			'popular_items'              => _x( 'Popular Tags', 'constellation-client-portal' ),
			'all_items'                  => _x( 'All Categories', 'constellation-client-portal' ),
			'parent_item'                => _x( 'Parent Category', 'constellation-client-portal' ),
			'parent_item_colon'          => _x( 'Parent Tag:', 'constellation-client-portal' ),
			'edit_item'                  => _x( 'Edit Tag', 'constellation-client-portal' ),
			'update_item'                => _x( 'Update Tag', 'constellation-client-portal' ),
			'add_new_item'               => _x( 'Add New Tag', 'constellation-client-portal' ),
			'new_item_name'              => _x( 'New Tag', 'constellation-client-portal' ),
			'separate_items_with_commas' => _x( 'Separate tags with commas', 'constellation-client-portal' ),
			'add_or_remove_items'        => _x( 'Add or remove tags', 'constellation-client-portal' ),
			'choose_from_most_used'      => _x( 'Choose from the most used tags', 'constellation-client-portal' ),
			'menu_name'                  => _x( 'Invoice Tags', 'constellation-client-portal' ),
		);

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_invoice_tags_rewrite_slug();

		} else {

			$rewrite_slug = false;

		}

		$args = array(
			'labels'            => $labels,
			'public'            => true,
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

		register_taxonomy( 'accp_invoice_tags', array( 'accp_clientinvoice' ), $args );
	}


	/**
	 * Display the Company column content in the Client Invoice WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function accp_clientinvoice_column_display_company_name( $column_name, $post_id ) {

		$this->admin->get_company_column_content_for_post_lists( $column_name, $post_id );
	}


	/**
	 * Display the Status column content in the Client Invoice WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function accp_clientinvoice_column_display_invoice_status( $column_name, $post_id ) {

		if ( 'status' !== $column_name ) {
			return;
		}

		$invoice_status = get_post_meta( $post_id, 'invoice_status', true );

		if ( $invoice_status ) {
			echo esc_html( ucfirst( $invoice_status ) );
		}
	}


	/**
	 * Display the Document ID column content - Client Invoice WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function accp_clientinvoice_column_display_wp_document_id( $column_name, $post_id ) {

		// Document ID.
		if ( 'doc_id' !== $column_name ) {
			return;
		}

		echo esc_html( $post_id );
	}


	/**
	 * Display the Category column content in the Client Ivnoice WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function accp_clientinvoice_column_display_categories( $column_name, $post_id ) {

		$this->admin->get_category_column_content_for_post_lists( $column_name, $post_id );
	}


	/**
	 * Register the column as sortable - Client File WP List Table.
	 *
	 * @param array $columns - The columns array.
	 */
	public function accp_clientinvoice_column_register_sortable( $columns ) {

		$columns['user']    = 'user';
		$columns['company'] = 'company';
		$columns['doc_id']  = 'doc_id';

		return $columns;
	}


	/**
	 * Sort orderby.
	 *
	 * @param array $vars - The vars array.
	 */
	public function accp_clientinvoice_column_orderby( $vars ) {

		/**
		 * Sort the Title column.
		 */
		if ( isset( $vars['orderby'] ) && 'user' === $vars['orderby'] ) {

			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'clientfile', // phpcs:ignore WordPress.DB.SlowDBQuery
					'orderby'  => 'meta_value',
				)
			);

		}

		/**
		 * Sort the Company column - sorts by the accp_user id.
		 */
		if ( isset( $vars['orderby'] ) && 'company' === $vars['orderby'] ) {

			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'accp_user', // phpcs:ignore WordPress.DB.SlowDBQuery
					'orderby'  => 'meta_value',
				)
			);

		}

		/**
		 * Sort the Document ID column.
		 */
		if ( isset( $vars['orderby'] ) && 'doc_id' === $vars['orderby'] ) {

			$vars = array_merge(
				$vars,
				array(
					'orderby' => 'doc_id',
				)
			);

		}

		return $vars;
	}


	/**
	 * Create metaboxes for the Client Invoice post page
	 */
	public function rerender_invoice_status_meta_options() {

		$screens = array( 'accp_clientinvoice' );

		foreach ( $screens as $screen ) {
			add_meta_box( 'invoice-status', 'Invoice Status', array( $this, 'display_invoice_status_meta_options' ), $screen, 'side' );
		}
	}

	/**
	 * Client Invoice Status Metabox Display
	 */
	public function display_invoice_status_meta_options() {

		global $post;

		$saved_status = get_post_meta( $post->ID, 'invoice_status', true );

		/**
		 * If there is no saved status, set the invoice
		 * status to the default - 'unpaid.'
		 */
		if ( is_object( $post ) && ! $saved_status ) {
			update_post_meta( $post->ID, 'invoice_status', 'unpaid' );
		}

		if ( ! $saved_status ) {
			$saved_status = 'unpaid';
		}

		$defined_statuses = $this->accp_get_defined_invoice_statuses();

		?>
		<select name="invoice_status">

			<?php
			foreach ( $defined_statuses as $value => $label ) {
				?>

				<option id="<?php echo esc_attr( $value ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo ! $saved_status || $saved_status === $value ? 'selected="selected"' : ''; ?> >

					<?php echo esc_html( $label ); ?>

				</option>
			
				<?php
			}
			?>

		</select>
		<?php
	}


	/**
	 * Define Invoice Statuses
	 *
	 * Returns array of defined invoice statuses.
	 */
	public function accp_get_defined_invoice_statuses() {

		$default_statuses = array(
			'unpaid'    => 'Unpaid',
			'paid'      => 'Paid',
			'voided'    => 'Voided',
			'refunded'  => 'Refunded',
			'write-off' => 'Write Off',
		);

		/**
		 * Hook - Allow invoice statuses to be edited.
		 *
		 * @param $default_statuses array - an array of default invoice statuses.
		 */
		$defined_statuses = apply_filters( 'accp_define_invoice_statuses', $default_statuses );

		/**
		 * Restore the 'paid' and 'unpaid' statuses if needed.
		 * 'voided', 'refunded', and 'write-off' are the only default
		 * statuses that can be removed.
		 */
		if ( ! array_key_exists( 'unpaid', $defined_statuses ) ) {
			$defined_statuses['unpaid'] = 'Unpaid';
		}

		if ( ! array_key_exists( 'paid', $defined_statuses ) ) {
			$defined_statuses['paid'] = 'Paid';
		}

		$statuses = array();

		foreach ( $defined_statuses as $key => $status ) {
			$statuses[ sanitize_text_field( $key ) ] = sanitize_text_field( $status );
		}

		return $statuses;
	}


	/**
	 * Client Invoice Status Metabox Save Functionality
	 *
	 * @param int $post_id - The post ID.
	 */
	public function save_invoice_status_meta_options( $post_id ) {

		if ( current_user_can( 'edit_posts' ) && is_admin() && function_exists( 'get_current_screen' ) ) {

			$nonce_field_name = $this->admin->get_accp_post_edit_nonce_field_name();
			$nonce_name       = $this->admin->get_accp_post_edit_nonce_name();

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

			$current_screen = get_current_screen();

			if ( 'accp_clientinvoice' === $current_screen->id ) {

				global $post;

				if ( $post && ! empty( $_POST['invoice_status'] ) ) {

					$post_status = sanitize_text_field( wp_unslash( $_POST['invoice_status'] ) );

					update_post_meta( $post_id, 'invoice_status', $post_status );

				}

				if ( $post && empty( $_POST['invoice_status'] ) ) {
					delete_post_meta( $post_id, 'invoice_status' );
				}
			}
		}
	}




	/**
	 * Add WP List Table Filter Fields
	 *
	 * @param string $post_type - The post type.
	 */
	public function accp_add_core_invoice_list_filter_fields( $post_type ) {

		if ( 'accp_clientinvoice' !== $post_type ) {
			return;
		}

		// Company Filter.
		$args = array(
			'post_type'   => 'accp_clientcompany',
			'numberposts' => -1,
			'fields'      => 'ids',
		);

		$values = get_posts( $args );

		/**
		 * Add a nonce field.
		 */
		$wp_list_filter_nonce_field = $this->admin->get_accp_list_table_filter_nonce_field_html();
		$core_settings              = new ARS_Constellation_Client_Portal_Settings();
		$allowed_html               = $core_settings->get_customized_allowed_html_for_wp_kses();

		echo wp_kses( $wp_list_filter_nonce_field, $allowed_html );

		?>
		<select class="accp_admin_list_filter" name="accp_invoice_company_filter">
		<option value=""><?php esc_html_e( 'Filter By Company ', 'constellation-client-portal' ); ?></option>
		<?php
			$current_value = filter_input( INPUT_GET, 'accp_invoice_company_filter', FILTER_SANITIZE_NUMBER_INT ) ? filter_input( INPUT_GET, 'accp_invoice_company_filter', FILTER_SANITIZE_NUMBER_INT ) : '';

		foreach ( $values as $value ) {

			$label = get_the_title( $value );

			?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php echo (int) $value === (int) $current_value ? ' selected="selected"' : ''; ?> >

				<?php echo esc_html( $label ); ?>

				</option>
				<?php

		}
		?>
		</select>
		<?php
	}


	/**
	 * Update the invoice WP list filter query based on
	 * additional filters.
	 *
	 * @param array $query - The query array.
	 */
	public function accp_core_invoice_additional_filters( $query ) {

		global $pagenow;
		global $post;

		$nonce_field_name = $this->admin->get_accp_list_table_filter_nonce_field_name();
		$nonce_name       = $this->admin->get_accp_list_table_filter_nonce_name();

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

		if ( ! isset( $_GET['post_type'] ) ) {
			return;
		}

		$type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );

		if ( 'accp_clientinvoice' !== $type ) {
			return;
		}
		if ( ! is_admin() ) {
			return;
		}

		// Company Filter.
		if ( 'edit.php' === $pagenow && isset( $_GET['accp_invoice_company_filter'] ) && '' !== $_GET['accp_invoice_company_filter'] && $query->is_main_query() ) {

			$query->query_vars['meta_key']   = 'accp_user'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$query->query_vars['meta_value'] = filter_input( INPUT_GET, 'accp_invoice_company_filter', FILTER_SANITIZE_NUMBER_INT ); // phpcs:ignore WordPress.DB.SlowDBQuery

		}
	}



	/**
	 * Add Invoice fields to the WP Admin Quick Edit form.
	 *
	 * @param string $column_name - The column name.
	 * @param string $post_type - The post type.
	 */
	public function accp_add_invoice_quick_edit_fields( $column_name, $post_type ) {

		global $post;

		if ( ! is_admin() || ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( 'accp_clientinvoice' !== $post_type ) {
			return;
		}

		if ( 'status' === $column_name ) {

			$defined_statuses = $this->accp_get_defined_invoice_statuses();

			?>
			<fieldset class="inline-edit-col-left">

				<?php
				/**
				 * Output the quick edit field nonce.
				 */
				$quick_edit_field_nonce = $this->admin->get_accp_post_quick_edit_nonce_field_html();
				$core_settings          = new ARS_Constellation_Client_Portal_Settings();
				$allowed_html           = $core_settings->get_customized_allowed_html_for_wp_kses();

				echo wp_kses( $quick_edit_field_nonce, $allowed_html );
				?>

				<div class="inline-edit-col">

					<h4 class="accp-admin-invoice-data-quickedit">Invoice Data</h4>

						<label inline-edit-status alignleft>
							
							<span class="title accp-quickedit-invoice-status-label">Invoice Status</span>
						
							<select name="invoice_status_quickedit">

								<option value="">— No Change —</option>

								<?php
								foreach ( $defined_statuses as $value => $label ) {
									?>

									<option id="<?php echo esc_attr( $value ); ?>" value="<?php echo esc_attr( $value ); ?>" >
										
										<?php echo esc_html( $label ); ?>
									
									</option>
								
									<?php
								}
								?>

							</select>

						</label>

					</span>
				
			</fieldset>
			<?php

		}
	}

	/**
	 * Add Invoice fields to the WP Admin Bulk Edit form.
	 *
	 * @param string $column_name - The column name.
	 * @param string $post_type - The post type.
	 */
	public function accp_add_invoice_bulk_edit_fields( $column_name, $post_type ) {

		global $post;

		if ( 'accp_clientinvoice' !== $post_type ) {
			return;
		}

		$nonce = wp_create_nonce( 'accp_bulk_edit_nonce' );

		if ( 'status' === $column_name ) {

			$defined_statuses = $this->accp_get_defined_invoice_statuses();

			?>
			<fieldset id="accp-invoice-bulk-edit-section" class="inline-edit-col-left" data-nonce="<?php echo esc_attr( $nonce ); ?>">

				<div class="inline-edit-col">

					<h4 class="accp-admin-invoice-data-quickedit">Invoice Data</h4>

						<label inline-edit-status alignleft>
							
							<span class="title accp-quickedit-invoice-status-label">Invoice Status</span>
						
							<select id="invoice-status-bulk-edit-select" name="invoice_status_quickedit">

								<option value="-1">— No Change —</option>

								<?php
								foreach ( $defined_statuses as $value => $label ) {
									?>

									<option value="<?php echo esc_attr( $value ); ?>" >
									
										<?php echo esc_html( $label ); ?>
									
									</option>
								
									<?php
								}
								?>

							</select>

						</label>

					</span>
				
			</fieldset>
			<?php

		}
	}


	/**
	 * Save Invoice quick edit fields.
	 *
	 * @param int $post_id - The post ID.
	 */
	public function accp_save_invoice_quick_edit_fields( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/**
		 * Nonce verification.
		 */
		$nonce_field_name = $this->admin->get_accp_post_quick_edit_nonce_field_name();
		$nonce_name       = $this->admin->get_accp_post_quick_edit_nonce_name();

		if ( ! isset( $_REQUEST[ $nonce_field_name ] ) ) {
			return $post_id;
		}

		$nonce = sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_field_name ] ) );

		if ( function_exists( 'wp_verify_nonce' ) ) {

			if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
				return $post_id;
			}
		}

		$current_screen = get_current_screen();

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['invoice_status_quickedit'] ) && ! empty( $_POST['invoice_status_quickedit'] ) ) {

			$post_status = sanitize_text_field( wp_unslash( $_POST['invoice_status_quickedit'] ) );

			update_post_meta( $post_id, 'invoice_status', $post_status );

		}
	}


	/**
	 * Save Invoice bulk edit fields.
	 */
	public function accp_save_invoice_bulk_edit() {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			wp_die();
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'accp_bulk_edit_nonce' ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['post_ids'] ) || empty( $_POST['post_ids'] ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['invoice_status'] ) || '-1' === strval( sanitize_text_field( wp_unslash( $_POST['invoice_status'] ) ) ) ) {
			wp_die();
		}

		$post_ids       = array_map( 'intval', $_POST['post_ids'] );
		$invoice_status = sanitize_text_field( wp_unslash( $_POST['invoice_status'] ) );

		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {

			foreach ( $post_ids as $post_id ) {

				update_post_meta( $post_id, 'invoice_status', $invoice_status );

			}
		}

		wp_die();
	}
} // END ARS_Constellation_Client_Portal_Client_Invoice Class
