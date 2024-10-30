<?php
/**
 * ARS_Constellation_Client_Portal_Client_File Class
 *
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez
 * @since      1.0.0
 *
 * Client File Admin specific functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ARS_Constellation_Client_Portal_Client_File Class
 */
class ARS_Constellation_Client_Portal_Client_File {

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
	 * Register the Client File custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_accp_clientfile() {

		$labels = array(
			'name'               => _x( 'Client Files', 'constellation-client-portal' ),
			'singular_name'      => _x( 'Client File', 'constellation-client-portal' ),
			'add_new'            => _x( 'Add New', 'constellation-client-portal' ),
			'add_new_item'       => _x( 'Add New Client File', 'constellation-client-portal' ),
			'edit_item'          => _x( 'Edit Client File', 'constellation-client-portal' ),
			'new_item'           => _x( 'New Client File', 'constellation-client-portal' ),
			'view_item'          => _x( 'View Client File', 'constellation-client-portal' ),
			'search_items'       => _x( 'Search Client Files', 'constellation-client-portal' ),
			'not_found'          => _x( 'No client files found', 'constellation-client-portal' ),
			'not_found_in_trash' => _x( 'No client files found in Trash', 'constellation-client-portal' ),
			'parent_item_colon'  => _x( 'Parent Client File:', 'constellation-client-portal' ),
			'menu_name'          => _x( 'Client Files', 'constellation-client-portal' ),
		);

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_file_rewrite_slug();

		} else {

			$rewrite_slug = false;

		}

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'author', 'editor', 'excerpt', 'thumbnail' ),
			'taxonomies'          => array( 'accp_file_categories', 'accp_file_tags' ),
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

		register_post_type( 'accp_clientfile', $args );
	}


	/**
	 * Register the Categories for the Client File custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_taxonomy_file_categories() {

		$labels = array(
			'name'                       => _x( 'Client File Categories', 'constellation-client-portal' ),
			'singular_name'              => _x( 'Client File Category', 'constellation-client-portal' ),
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
			'menu_name'                  => _x( 'Client File Categories', 'constellation-client-portal' ),
		);

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_file_categories_rewrite_slug();

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

		register_taxonomy( 'accp_file_categories', array( 'accp_clientfile' ), $args );
	}


	/**
	 * Register the Tags for the Client File custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_taxonomy_file_tags() {

		$labels = array(
			'name'                       => _x( 'Client File Tags', 'constellation-client-portal' ),
			'singular_name'              => _x( 'Client File Tags', 'constellation-client-portal' ),
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
			'menu_name'                  => _x( 'Client File Tags', 'constellation-client-portal' ),
		);

		/**
		 * Allow custom rewrite slug.
		 */
		if ( true === $this->utilities->is_pro_plugin( $this->plugin_name ) ) {

			$rewrite_slug = $this->pro_admin->get_client_file_tags_rewrite_slug();

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

		register_taxonomy( 'accp_file_tags', array( 'accp_clientfile' ), $args );
	}


	/**
	 * Display the Company column content in the Client File WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientfile_column_display_company_name( $column_name, $post_id ) {

		$this->admin->get_company_column_content_for_post_lists( $column_name, $post_id );
	}


	/**
	 * Display the Status column content in the Client File WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function accp_clientfile_column_display_file_status( $column_name, $post_id ) {

		if ( 'status' !== $column_name ) {
			return;
		}

		$file_status = get_post_meta( $post_id, 'file_status', true );

		if ( $file_status ) {
			echo esc_html( ucfirst( $file_status ) );
		}
	}


	/**
	 * Display the Document ID column content - Client File WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientfile_column_display_wp_id( $column_name, $post_id ) {

		// Document ID.
		if ( 'doc_id' !== $column_name ) {
			return;
		}

		$doc_id = $post_id;

		echo esc_html( $doc_id );
	}


	/**
	 * Display the Category column content in the Client File WP List Table.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientfile_column_display_category( $column_name, $post_id ) {

		$this->admin->get_category_column_content_for_post_lists( $column_name, $post_id );
	}


	/**
	 * Register the column as sortable - Client File WP List Table.
	 *
	 * @param array $columns - The columns array.
	 */
	public function clientfile_column_register_sortable( $columns ) {

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
	public function clientfile_column_orderby( $vars ) {

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
	 * Add WP List Table Filter Fields.
	 *
	 * @param string $post_type - The post type.
	 */
	public function accp_add_core_file_list_filter_fields( $post_type ) {

		if ( 'accp_clientfile' !== $post_type ) {
			return;
		}

		/**
		 * Company Filter
		 */
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
		<select class="accp_admin_list_filter" name="accp_file_company_filter">

			<option value=""><?php esc_html_e( 'Filter By Company ', 'constellation-client-portal' ); ?></option>
			<?php
				$current_value = filter_input( INPUT_GET, 'accp_file_company_filter', FILTER_SANITIZE_NUMBER_INT ) ? filter_input( INPUT_GET, 'accp_file_company_filter', FILTER_SANITIZE_NUMBER_INT ) : '';

			foreach ( $values as $value ) {

				$label = get_the_title( $value );

				?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php echo (int) $value === (int) $current_value ? ' selected="selected"' : ''; ?> ><?php echo esc_html( $label ); ?></option>
					<?php

			}
			?>

		</select>
		<?php
	}


	/**
	 * Update the file WP list filter query based on
	 * additional filters.
	 *
	 * @param array $query - The post list query.
	 */
	public function accp_core_file_additional_filters( $query ) {

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

		if ( 'accp_clientfile' !== $type ) {
			return;
		}

		if ( ! is_admin() ) {
			return;
		}

		/**
		 * Company Filter
		 */
		if ( 'edit.php' === $pagenow && isset( $_GET['accp_file_company_filter'] ) && '' !== $_GET['accp_file_company_filter'] && $query->is_main_query() ) {

			$query->query_vars['meta_key']   = 'accp_user'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$query->query_vars['meta_value'] = filter_input( INPUT_GET, 'accp_file_company_filter', FILTER_SANITIZE_NUMBER_INT ); // phpcs:ignore WordPress.DB.SlowDBQuery

		}
	}


	/**
	 * Define File Statuses
	 *
	 * Returns array of defined file statuses.
	 */
	public function accp_get_defined_file_statuses() {

		$default_statuses = array(
			'na'          => 'NA',
			'incomplete'  => 'Incomplete',
			'in-progress' => 'In Progress',
			'completed'   => 'Completed',
		);

		/**
		 * Hook - Allow file statuses to be edited.
		 *
		 * @param array $default_statuses - an array of default file statuses.
		 */
		$defined_statuses = apply_filters( 'accp_define_file_statuses', $default_statuses );

		/**
		 * Restore the 'completed' status if needed.
		 * 'na', 'incomplete', and 'in-progress' are the only default
		 * statuses that can be removed.
		 */
		if ( ! array_key_exists( 'completed', $defined_statuses ) ) {
			$defined_statuses['completed'] = 'Completed';
		}

		$statuses = array();

		foreach ( $defined_statuses as $key => $status ) {
			$statuses[ sanitize_text_field( $key ) ] = sanitize_text_field( $status );
		}

		return $statuses;
	}


	/**
	 * Create metaboxes for the Client File post page
	 */
	public function rerender_file_status_meta_options() {

		$screens = array( 'accp_clientfile' );

		foreach ( $screens as $screen ) {
			add_meta_box( 'file-status', 'File Status', array( $this, 'display_file_status_meta_options' ), $screen, 'side' );
		}
	}


	/**
	 * Client File Status Metabox Display
	 */
	public function display_file_status_meta_options() {

		global $post;

		$saved_status     = get_post_meta( $post->ID, 'file_status', true );
		$defined_statuses = $this->accp_get_defined_file_statuses();

		?>
		<select name="file_status">

			<option value=""></option>

			<?php
			foreach ( $defined_statuses as $value => $label ) {
				?>

				<option id="<?php echo esc_attr( $value ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $saved_status === $value ? 'selected="selected"' : ''; ?> ><?php echo esc_html( $label ); ?></option>
			
				<?php
			}
			?>

		</select>
		<?php
	}


	/**
	 * Client File Status Metabox Save Functionality
	 *
	 * @param int $post_id - The post ID.
	 */
	public function save_file_status_meta_options( $post_id ) {

		global $post;

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

		if ( current_user_can( 'edit_posts' ) && is_admin() && function_exists( 'get_current_screen' ) ) {

			$current_screen = get_current_screen();

			if ( 'accp_clientfile' === $current_screen->id ) {

				if ( $post && ! empty( $_POST['file_status'] ) ) {

					$post_status = sanitize_text_field( wp_unslash( $_POST['file_status'] ) );

					update_post_meta( $post_id, 'file_status', $post_status );

				}

				if ( $post && empty( $_POST['file_status'] ) ) {
					delete_post_meta( $post_id, 'file_status' );
				}
			}
		}
	}


	/**
	 * Add File fields to the WP Admin Quick Edit form.
	 *
	 * @param string $column_name - The column name.
	 * @param string $post_type - The post type.
	 */
	public function accp_add_file_quick_edit_fields( $column_name, $post_type ) {

		global $post;

		if ( ! is_admin() || ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( 'accp_clientfile' !== $post_type ) {
			return;
		}

		if ( 'status' === $column_name ) {

			$defined_statuses = $this->accp_get_defined_file_statuses();

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

					<h4 class="accp-admin-file-data-quickedit">File Data</h4>

						<label inline-edit-status alignleft>
							
							<span class="title accp-quickedit-file-status-label">File Status</span>
						
							<select name="file_status_quickedit">

								<option value="">— No Change —</option>

								<?php
								foreach ( $defined_statuses as $value => $label ) {
									?>

									<option id="<?php echo esc_attr( $value ); ?>" value="<?php echo esc_attr( $value ); ?>" ><?php echo esc_html( $label ); ?></option>
								
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
	 * Add File fields to the WP Admin Bulk Edit form.
	 *
	 * @param string $column_name - The column name.
	 * @param string $post_type - The post type.
	 */
	public function accp_add_file_bulk_edit_fields( $column_name, $post_type ) {

		global $post;

		if ( 'accp_clientfile' !== $post_type ) {
			return;
		}

		$nonce = wp_create_nonce( 'accp_bulk_edit_file_nonce' );

		if ( 'status' === $column_name ) {

			$defined_statuses = $this->accp_get_defined_file_statuses();

			?>
			<fieldset id="accp-file-bulk-edit-section" class="inline-edit-col-left" data-nonce="<?php echo esc_attr( $nonce ); ?>">

				<div class="inline-edit-col">

					<h4 class="accp-admin-file-data-quickedit">File Data</h4>

						<label inline-edit-status alignleft>
							
							<span class="title accp-quickedit-file-status-label">File Status</span>
						
							<select id="file-status-bulk-edit-select" name="file_status_quickedit">

								<option value="-1">— No Change —</option>

								<?php
								foreach ( $defined_statuses as $value => $label ) {
									?>

									<option value="<?php echo esc_attr( $value ); ?>" ><?php echo esc_html( $label ); ?></option>
								
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
	 * Save File quick edit fields.
	 *
	 * @param int $post_id - The post ID.
	 */
	public function accp_save_file_quick_edit_fields( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
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

		if ( isset( $_POST['file_status_quickedit'] ) && ! empty( $_POST['file_status_quickedit'] ) ) {

			$post_status = sanitize_text_field( wp_unslash( $_POST['file_status_quickedit'] ) );

			update_post_meta( $post_id, 'file_status', $post_status );

		}
	}


	/**
	 * Save File bulk edit fields AJAX function.
	 */
	public function accp_save_file_bulk_edit() {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			wp_die();
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'accp_bulk_edit_file_nonce' ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['post_ids'] ) || empty( $_POST['post_ids'] ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['file_status'] ) || '-1' === strval( sanitize_text_field( wp_unslash( $_POST['file_status'] ) ) ) ) {
			wp_die();
		}

		$post_ids    = array_map( 'intval', $_POST['post_ids'] );
		$file_status = sanitize_text_field( wp_unslash( $_POST['file_status'] ) );

		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {

			foreach ( $post_ids as $post_id ) {

				update_post_meta( $post_id, 'file_status', $file_status );

			}
		}

		wp_die();
	}
} // END ARS_Constellation_Client_Portal_Client_File Class
