<?php
/**
 * ARS_Constellation_Client_Portal_Company Class
 *
 * @package    ARS_Constellation_Client_Portal
 * @subpackage ARS_Constellation_Client_Portal/admin
 * @author     Adrian Rodriguez
 * @since      1.0.0
 *
 * Company (accp_clientcompany) post type admin specific functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ARS_Constellation_Client_Portal_Company Class.
 */
class ARS_Constellation_Client_Portal_Company {

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
	 * Plugin admin functions.
	 *
	 * @access   private
	 * @var      object   $admin    Plugin admin functions class.
	 */
	private $admin;

	/**
	 * The class contruct.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name            = $plugin_name;
		$this->version                = $version;
		$this->accp_utility_functions = new ACCP_Utility_Functions();
		$this->is_pro                 = $this->accp_utility_functions->is_pro_plugin( $this->plugin_name );
		$this->admin                  = new ARS_Constellation_Client_Portal_Admin( $this->plugin_name, $this->version );
	}

	/**
	 * Register the Client Company custom post type.
	 *
	 * @since    1.0.0
	 */
	public function accp_register_clientcompany() {

		$labels = array(
			'name'               => _x( 'Client Company', 'constellation-client-portal' ),
			'singular_name'      => _x( 'Client Company', 'constellation-client-portal' ),
			'add_new'            => _x( 'Add New', 'constellation-client-portal' ),
			'add_new_item'       => _x( 'Add New Client Company', 'constellation-client-portal' ),
			'edit_item'          => _x( 'Edit Client Company', 'constellation-client-portal' ),
			'new_item'           => _x( 'New Client Company', 'constellation-client-portal' ),
			'view_item'          => _x( 'View Client Company', 'constellation-client-portal' ),
			'search_items'       => _x( 'Search Client Companies', 'constellation-client-portal' ),
			'not_found'          => _x( 'No client Companies found', 'constellation-client-portal' ),
			'not_found_in_trash' => _x( 'No client companiess found in Trash', 'constellation-client-portal' ),
			'parent_item_colon'  => _x( 'Parent Client Company:', 'constellation-client-portal' ),
			'menu_name'          => _x( 'Client Companies', 'constellation-client-portal' ),
		);

		/**
		 * Only show the post type in rest if the user has sufficient
		 * capabilities.  We want to enable show_in_rest to allow Gutenberg support,
		 * without allowing public access to the post type via the WP REST API.
		 */
		$show_in_rest = current_user_can( 'manage_options' ) ? true : false;

		$show_in_menu = false;

		$is_pro = $this->accp_utility_functions->is_pro_plugin( $this->plugin_name );

		if ( true === $is_pro ) {

			$company_customize_view = get_option( 'accp_company_add_to_main_menu' );

			$show_in_menu = $company_customize_view && 'show_in_main_menu' === $company_customize_view ? true : false;

		}

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => true,
			'supports'            => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt' ),
			'taxonomies'          => array( 'accp_client_company_categories' ),
			'show_in_rest'        => $show_in_rest,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => $show_in_menu,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
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

		register_post_type( 'accp_clientcompany', $args );
	}


	/**
	 * Client Company Column Section
	 *
	 * Register the columns - Status, Number of Clients, and Company Home Page.
	 *
	 * @param array $columns - The columns array.
	 */
	public function clientcompany_column_register( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'title' === $column_name ) {

				$new_columns['company_id'] = __( 'Company ID', 'accp_company' );

				if ( true === $this->is_pro ) {

					$new_columns['company_status'] = __( 'Status', 'accp_company' );

				}

				$new_columns['company_primary_user'] = __( 'Primary User', 'accp_company' );
				$new_columns['number_of_clients']    = __( 'Number of Clients', 'accp_company' );
				$new_columns['company_home_page']    = __( 'Home Page', 'accp_company' );

			}
		}

		return $new_columns;
	}


	/**
	 * Display the Company ID column content - Client Company WP List Table - Company.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientcompany_column_display_company_id( $column_name, $post_id ) {

		if ( 'company_id' !== $column_name ) {
			return;
		}

		$company_id = $post_id;

		echo (int) $company_id;
	}

	/**
	 * Display the Primary User column content - Client Company WP List Table - Primary User.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientcompany_column_display_primary_user( $column_name, $post_id ) {

		if ( 'company_primary_user' !== $column_name ) {
			return;
		}

		$primary_user_id = get_post_meta( $post_id, 'accp_company_primary_user', true );

		if ( ! empty( $primary_user_id ) ) {

			$user = get_user_by( 'id', (int) $primary_user_id );

			if ( $user ) {

				echo esc_html( $user->user_login ) . ' (ID: ' . (int) $primary_user_id . ')';

			}
		}
	}


	/**
	 * Display the Number of Clients column content - Client Company WP List Table
	 * Number of Assigned Users.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientcompany_column_display_assigned_user_count( $column_name, $post_id ) {

		if ( 'number_of_clients' !== $column_name ) {
			return;
		}

		/**
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

		$user_query     = new WP_User_Query( $args );
		$users          = (array) $user_query->results;
		$user_name_list = array();

		if ( ! empty( $users ) ) {

			foreach ( $users as $user ) {

				$user_id  = $user->ID;
				$user_url = get_edit_user_link( $user->ID );

				$user_name_list[] = '<a href="' . esc_url( $user_url ) . '">' . esc_html( $user->user_login ) . '</a>';

			}

			echo esc_html( count( $user_name_list ) );

		} else {

			echo '0';

		}
	}

	/**
	 * Display the Home Page column content - Client Company WP List Table - Company Home Page.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientcompany_column_display_home_page( $column_name, $post_id ) {

		if ( 'company_home_page' !== $column_name ) {
			return;
		}

		$accp_home_page = get_post_meta( $post_id, 'accp_home_page', true );

		if ( ! empty( $accp_home_page ) ) {

			$view_page_link = get_the_permalink( $accp_home_page );

			echo '<span class="hov-nav-link">' . esc_url( $view_page_link ) . '</span>';
			echo '<ul class="home-page-hov-nav row-actions"><li><a href="post.php?post=' . esc_attr( $accp_home_page ) . '&action=edit">Edit Page</a></li><li><a href="' . esc_url( $view_page_link ) . '" target="_blank">View Page</a></li></ul>';

		}
	}


	/**
	 * Display the Home Page column content - Client Company WP List Table - Company Status.
	 *
	 * @param string $column_name - The column name.
	 * @param int    $post_id - The post ID.
	 */
	public function clientcompany_column_display_status( $column_name, $post_id ) {

		if ( ! is_user_logged_in() || ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'company_status' !== $column_name ) {
			return;
		}

		$company_statuses = get_option( 'accp_company_status_repeater' ) ? get_option( 'accp_company_status_repeater' ) : array();
		$saved_status     = get_post_meta( $post_id, 'accp_company_status', true );

		if ( ! $company_statuses || empty( $company_statuses ) ) {
			echo esc_html( $saved_status );
		}

		if ( ! empty( $saved_status ) ) {

			foreach ( $company_statuses as $field ) {

				if ( $field['value'] === $saved_status ) {

					echo esc_html( $field['label'] );
					return;

				}
			}
		}
	}


	/**
	 * Create metaboxes for the Client Company post page
	 */
	public function display_clientcompany_meta_options() {

		$screens = array( 'accp_clientcompany' );

		foreach ( $screens as $screen ) {

			add_meta_box( 'company-home-page', 'Company Home Page', array( $this, 'display_home_page_meta_options' ), $screen, 'side' );

			add_meta_box( 'company-upload-dir', 'Company Upload Directory', array( $this, 'display_company_upload_meta_options' ), $screen, 'normal', 'high' );

			add_meta_box( 'company-primary-user', 'Company Primary User', array( $this, 'display_company_primary_user_meta_options' ), $screen, 'normal', 'high' );

			add_meta_box( 'company-users', 'Company Users', array( $this, 'display_company_user_meta_options' ), $screen, 'normal', 'high' );

		}
	}


	/**
	 * Client Company Metaboxes Save field data.
	 *
	 * @param int    $post_id - The post ID.
	 * @param object $post - The post object.
	 */
	public function save_clientcompany_meta_options( $post_id, $post ) {

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

			if ( 'accp_clientcompany' === $current_screen->id ) {

				/**
				 * Exit if post does not yet exist.
				 */
				if ( ! is_object( $post ) ) {
					return;
				}

				/**
				 * Save Home Page
				 */
				if ( $post && isset( $_POST['accp_home_page'] ) ) {

					$home_page_id = filter_var( wp_unslash( $_POST['accp_home_page'] ), FILTER_SANITIZE_NUMBER_INT );

					update_post_meta( $post_id, 'accp_home_page', $home_page_id );

				}

				if ( $post && ! isset( $_POST['accp_home_page'] ) ) {

					delete_post_meta( $post_id, 'accp_home_page' );

				}

				/**
				 * Save Primary User
				 */
				if ( $post && isset( $_POST['accp_company_primary_user'] ) ) {

					$primary_user = filter_var( wp_unslash( $_POST['accp_company_primary_user'] ), FILTER_SANITIZE_NUMBER_INT );

					update_post_meta( $post_id, 'accp_company_primary_user', $primary_user );

				}

				if ( $post && ! isset( $_POST['accp_company_primary_user'] ) ) {

					delete_post_meta( $post_id, 'accp_company_primary_user' );

				}
			}
		}
	}


	/**
	 * Display meta box and custom fields - Company Home Page metabox.
	 */
	public function display_home_page_meta_options() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$company_id     = get_the_ID();
		$accp_home_page = get_post_meta( $company_id, 'accp_home_page', true );

		?>
		<p class="accp-field-instructions">Select a company home page.</p>		
		<?php

		/**
		 * Define get_pages args.
		 */
		$args = array(
			'post_type' => 'accp_client_pages',
		);

		/**
		 * Include all defined WP post statuses,
		 * not just "publish" in the get_pages args.
		 */
		$wp_post_statuses = get_post_statuses() ? array_keys( (array) get_post_statuses() ) : array();

		if ( ! empty( $wp_post_statuses ) ) {

			$args['post_status'] = $wp_post_statuses;

		}

		/**
		 * Get client pages.
		 */
		$client_page_list = get_pages( $args );

		/**
		 * Client page select field.
		 */
		if ( $client_page_list ) :

			?>
			<select name="accp_home_page" id="accp_home_page">

				<option value="">Select a page...</option>
				<?php

				foreach ( $client_page_list as $key => $post ) {

					$post_id           = $post->ID;
					$post_name         = $post->post_title;
					$selected          = $accp_home_page && (int) $accp_home_page === (int) $post_id ? 'selected' : '';
					$post_status_class = '';

					if ( 'publish' !== $post->post_status ) {

						$post_name         = $post_name . ' (' . $post->post_status . ')';
						$post_status_class = 'accp-non-published-post-option';

					}

					?>
					<option class="level-0 <?php echo esc_attr( $post_status_class ); ?>" value="<?php echo esc_attr( $post_id ); ?>" <?php echo esc_attr( $selected ); ?> ><?php echo esc_html( $post_name ); ?></option>
					<?php

				}

				?>
			</select>
			<?php

		endif;

		/**
		 * Create new page form.
		 */
		$create_page_nonce = wp_create_nonce( 'create_home_page' );

		?>
				<div class="accp-create-page-container">
		
			<p>Or, create and assign a new blank page.</p>

			<span class="button button-primary accp-show-new-page-form">Create New Page</span>

			<div class="accp-generate-page-form">

				<p class="accp-create-new-page-instructions">
					This process will create a new blank Client Page and assign it as the home page for the current Company.
				</p>

				<span class="accp-generate-new-page-field-container">

					<label for="accp_new_page_title">Client Page Title</label>
					<input type="text" class="accp-new-page-title" name="accp_new_page_title">

				</span>
				
				<?php
				if ( is_user_logged_in() && current_user_can( 'manage_options' ) && is_admin() ) {
					do_action( 'accp_after_generate_client_page_title_field' );
				}
				?>

				<span class="button button-primary accp-generate-new-page" data-nonce="<?php echo esc_attr( $create_page_nonce ); ?>" data-post-id="<?php echo esc_attr( $company_id ); ?>">Generate Page</span>

				<span class="accp-generate-page-message"></span>

			</div>

		</div>
		<?php
	}


	/**
	 * Generate new Client Page AJAX function.
	 */
	public function accp_generate_new_client_page() {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			die();
		}

		/**
		 * Verify the nonce.
		 */
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'create_home_page' ) ) {
			wp_die();
		}

		$post_title      = isset( $_POST['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) : '';
		$company_post_id = isset( $_POST['company_post_id'] ) ? (int) $_POST['company_post_id'] : '';
		$is_global       = isset( $_POST['is_global'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['is_global'] ) ) ? true : false;

		/**
		 * Exit if this is a new post that has not yet been saved.
		 */
		if ( ! get_post( $company_post_id ) ) {

			echo 'Please save the post before attempting to assign a home page';

			wp_die();

		}

		$args = array(
			'post_type'   => 'accp_client_pages',
			'post_title'  => esc_html( $post_title ),
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $args );

		if ( $post_id ) {

			/**
			 * Assign the Company to the new Client Page if this
			 * is not a global page.
			 */
			if ( true !== $is_global ) {
				update_post_meta( $post_id, 'accp_user', $company_post_id );
			}

			/**
			 * Make the page global if the global option was checked.
			 */
			if ( true === $is_global ) {
				update_post_meta( $post_id, 'accp_make_page_global', 'global' );
			}

			/**
			 * Assign the home page to the Company.
			 */
			update_post_meta( $company_post_id, 'accp_home_page', $post_id );

			echo (int) $post_id;

		} else {

			echo 'accp add post error';

		}

		wp_die();
	}


	/**
	 * Display meta box and custom fields - Company Users metabox.
	 */
	public function display_company_user_meta_options() {

		global $post;

		$id = get_the_ID();

		/**
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'client_company',
					'value'   => $id,
					'compare' => 'IN',
				),
				array(
					'key'     => 'client_additional_company',
					'value'   => $id,
					'compare' => 'LIKE',
				),
			),
		);

		$user_query = new WP_User_Query( $args );
		$users      = (array) $user_query->results;

		if ( ! empty( $users ) ) {

			echo '<p>There are currently ' . esc_html( count( $users ) ) . ' users assigned to this company.</p>';

		} else {

			echo '<p>There are currently no users assigned to this company.</p>';

		}
		?>

		<p class="accp-field-instructions">To add or remove users to the company, please navigate to the respective <a href="users.php">user acount</a>, and select a company (or remove the company), in the user's profile.</p>

		<?php
		if ( ! empty( $users ) ) {
			?>
			<table class="wp-list-table widefat fixed striped posts">
				<tr>
					<th>Username</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email</th>
				</tr>

				<?php
				foreach ( $users as $user ) {

					$user_id  = $user->ID;
					$user_url = get_edit_user_link( $user->ID );

					?>
					<tr>			 
						<td><a href="<?php echo esc_url( $user_url ); ?>"><?php echo esc_html( $user->user_login ); ?></a></td>
						<td><?php echo esc_html( $user->user_firstname ); ?></td>
						<td><?php echo esc_html( $user->user_lastname ); ?></td>
						<td><?php echo esc_html( $user->user_email ); ?></td>
					</tr>
					<?php

				}
				?>

			</table>
			<?php
		}
	}

	/**
	 * Company primary user meta box.
	 */
	public function display_company_primary_user_meta_options() {

		?>
		<p class="accp-primary-user-section-message">Assign a primary user for this company.  This user will receive file and invoice email notifications if that functionality is enabled.</p>
		<?php

		$company_id = get_the_ID();

		/**
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'client_company',
					'value'   => $company_id,
					'compare' => 'IN',
				),
				array(
					'key'     => 'client_additional_company',
					'value'   => $company_id,
					'compare' => 'LIKE',
				),
			),
		);

		$user_query = new WP_User_Query( $args );
		$users      = (array) $user_query->results;

		if ( ! empty( $users ) ) {

			$saved_primary_user = (int) get_post_meta( $company_id, 'accp_company_primary_user', true );

			?>
			<p>
				<strong>Current User:</strong>
			<?php

			if ( $saved_primary_user ) {

				$user = get_user_by( 'ID', $saved_primary_user );

				if ( $user ) {

					echo ' ' . esc_html( $user->user_login );

				} else {

					echo ' Unassigned.  Select a user and save the post to assign the primary user.';

				}
			} else {

				echo ' Unassigned.  Select a user and save the post to assign the primary user.';

			}

			?>
			</p>
			<select name="accp_company_primary_user">
				
				<option value="">Select user</option>
				<?php

				foreach ( $users as $user ) {

					$user_id         = $user->ID;
					$user_name       = $user->user_login;
					$user_first_name = $user->first_name;
					$user_last_name  = $user->last_name;
					$selected        = $saved_primary_user && $saved_primary_user === $user_id ? 'selected' : '';

					?>
					<option value="<?php echo esc_attr( $user_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $user_name ); ?> <?php echo $user_first_name ? ' - ' . esc_html( $user_first_name ) : ''; ?> <?php echo esc_html( $user_last_name ) ? ' ' . esc_html( $user_last_name ) : ''; ?></option>
					<?php

				}

				?>
			</select>			
			<?php

			$args = array(
				'message'  => 'Not seeing a user? Assign the user to this company in their WP user profile to display the user here.',
				'position' => 'right',
			);
			$user_select_tooltip = $this->admin->generate_wp_admin_tooltip( $args );

			echo wp_kses_post( $user_select_tooltip );

		} else {

			$post_id = get_the_ID();

			if ( $post_id && 0 !== $post_id ) :

				?>

				<p class="accp-no-users-assigned-message">There are currently no users assigned to this company.</p>

				<div id="accp-assign-primary-user-container">

				<input type="hidden" name="accp_company_primary_user" value="">

				<span class="accp-assign-primary-user-heading">Assign Existing User</span>

					<p>Assign an existing user to this company and make them the primary user.</p>

					<?php
					/**
					 * Assign an existing WP user as the primary user.
					 */
					?>
					
					<?php
					$args = array(
						'name'            => 'accp_assign_new_primary_user',
						'id'              => 'accp_assign_new_primary_user_select',
						'show_option_all' => 'Select user',
						'show'            => 'display_name_with_login',
					);

					wp_dropdown_users( $args );

					$assign_user_nonce = wp_create_nonce( 'accp_assign_primary_user' );
					?>
					
					<span id="accp-assign-existing-primary-user-btn" class="button" data-nonce="<?php echo esc_attr( $assign_user_nonce ); ?>" data-post-id="<?php echo esc_attr( $company_id ); ?>">Assign Primary User</span>

					<?php
					/**
					 * Create a new WP user and assign as the primary user.
					 */

					$new_user_nonce = wp_create_nonce( 'accp_generate_new_user' );
					?>
					
					<span class="accp-assign-primary-user-heading">Create and Assign New User</span>

					<p>Or, create a new user, assign them to this company, and make them the primary user.</p>
					
					<span class="accp-create-new-primary-user-btn button">
						<span class="accp-create-user-button-text">Create New Primary User</span>
						<span class="accp-create-user-cancel-button-text">Cancel</span>
					</span>

					<div id="accp-create-user-container">

						<span class="accp-new-user-field">

							<label class="accp-set-width-label-85">Username<abbr>*</abbr></label>
							<input id="accp-new-user-username" class="accp-create-user-text-field" name="accp_new_user_username" type="text" value="">
						
						</span>

						<span class="accp-new-user-field">

							<label class="accp-set-width-label-85">Role<abbr>*</abbr></label>
							<select id="accp-new-user-role" name="accp_new_user_role">
								<?php wp_dropdown_roles( 'subscriber' ); ?>
							</select>

						</span>

						<span class="accp-new-user-field">

							<label  class="accp-set-width-label-85">Email Addres<abbr>*</abbr></label>
							<input id="accp-new-user-email" class="accp-create-user-text-field" name="accp_new_user_email" type="email" value="">

						</span>

						<span class="accp-new-user-field">

							<label  class="accp-set-width-label-85">Password<abbr>*</abbr></label>
							<input id="accp-new-user-password" class="accp-create-user-text-field" name="accp_new_user_password" type="text" value=""><span id="accp-autogenerate-password" class="button" data-nonce="<?php echo esc_attr( $new_user_nonce ); ?>">Generate Password</span>

						</span>

						<span class="accp-new-user-field">

							<label  class="accp-set-width-label-85">First Name</label>
							<input id="accp-new-user-firstname" class="accp-create-user-text-field" name="accp_new_user_firstname" type="text" value="">

						</span>

						<span class="accp-new-user-field">

							<label  class="accp-set-width-label-85">Last Name</label>
							<input id="accp-new-user-lastname" class="accp-create-user-text-field" name="accp_new_user_lastname" type="text" value="">

						</span>

						<span class="accp-new-user-field">

							<label><input type="checkbox" id="accp-send-user-notification" value="send"> Send email to new user with a password reset link.</label>

						</span>

						<span class="accp-generate-user-message"></span>

						<span id="accp-generate-new-user-btn" class="button" data-nonce="<?php echo esc_attr( $new_user_nonce ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">Generate User</span>

					</div>

				</div>

				<?php
			else :
				?>

				<p>Please save the post before assigning a primary user.</p>

				<?php
			endif;
		}
	}

	/**
	 * Display Company upload dir metabox.
	 */
	public function display_company_upload_meta_options() {

		global $post;

		?>
		<p class="accp-company-directory-label">Upload Directory:</p> 		
		<?php

		$id                             = get_the_id();
		$upload_dir                     = get_post_meta( $id, 'accp_dir', true );
		$accp_clientfiles_full_dir_path = $this->accp_utility_functions->accp_get_clientfiles_path();
		$wp_content_dir_name            = $this->accp_utility_functions->accp_get_wp_content_dir_name();
		$truncated_path                 = strstr( $accp_clientfiles_full_dir_path, $wp_content_dir_name );

		/**
		 * If this directory is assigned to more than one company
		 * output a notice.
		 */
		$duplicate_dir_assignment   = $this->accp_check_company_dir_for_multiple_assignments( $upload_dir );
		$directory_notice_dismissed = get_post_meta( $id, 'accp_duplicate_dir_notice' ) ? get_post_meta( $id, 'accp_duplicate_dir_notice', true ) : '';

		if ( ! empty( $duplicate_dir_assignment ) && count( $duplicate_dir_assignment ) > 1 ) {

			$notice_nonce = wp_create_nonce( 'accp_dismiss_notice' );

			$company_edit_link_list = array();

			foreach ( $duplicate_dir_assignment as $id ) {

				$post_edit_link = get_edit_post_link( $id ) ? get_edit_post_link( $id ) : '#';

				$company_edit_link_list[] = '<a href="' . $post_edit_link . '">' . $id . '</a>';

			}

			if ( 'dismissed' !== $directory_notice_dismissed ) :
				?>

				<div class="accp-duplicate-dir-assignment-notice">

					<h3>Directory Assigned to Multiple Companies</h3>

					<p>It looks like this upload directory (<strong><?php echo esc_html( $upload_dir ); ?></strong>) is assigned to more than one company (company ID's: <?php echo wp_kses_post( implode( ', ', $company_edit_link_list ) ); ?>).  This can happen if you have duplicated a company post, or if you have migrated Client Portal data to a site with an existing Client Portal instance (as examples).</p>

					<p>Any files that you upload to each of these companies will be accessible by all of the companies that have this upload directory assignment.</p>

					<p>To resolve this issue, locate the companies that have the incorrect upload directory name, then generate a new directory (or assign an exising directory) for each of those companies.</p>

					<p>If you have intentionally assigned the same directory to multiple companies, you can choose to dismiss this notification.</p>

					<span class="accp-dismiss-dir-assigment-msg button" data-nonce="<?php echo esc_attr( $notice_nonce ); ?>" data-post-id="<?php echo esc_attr( $id ); ?>">Dismiss</span>

				</div>

				<?php
			else :
				?>

				<p>This directory (<?php echo esc_html( $upload_dir ); ?>) is assigned to the following companies: <?php echo wp_kses_post( implode( ', ', $company_edit_link_list ) ); ?>.</p>

				<?php
			endif;

		}

		if ( $upload_dir ) {

			$company_dir = $truncated_path . '/' . $upload_dir;

			?>
			<div id="upload-directory"><?php echo esc_html( $company_dir ); ?></div>
			<?php

		}

		$post_status    = get_post_status( $id );
		$generate_nonce = wp_create_nonce( 'accp_generate_nonce' );

		/**
		 * Only display this section if it is not a new unsaved post.
		 */
		if ( 'auto-draft' !== $post_status ) {

			if ( ! $upload_dir ) {

				$generate_dir_text = 'Uploading a new file and selecting this company will also automatically generate a company directory.  So creating a directory here is <strong>not required</strong>.';

				$specify_dir_text = 'If you have already manually created a directory for this company on the server, you can specify the directory name here. Uploading a new file and selecting this company will also automatically generate a company directory.  So specifying a directory here is <strong>not required</strong>.';

				$dir_assigned_class = '';
				?>

				<p>There is currently no directory assigned to this company.</p>					

				<?php
			} else {

				$generate_dir_text = 'Regenerating the upload directory will disassociate the current directory from this company, and create a new empty directory for this company.  Any files in the previous directory will be inaccessible to members of this company.';

				$specify_dir_text = 'Specifying a directory name will disassociate the current directory from this company, and associate the new directory name with this company.  Any files in the previous directory will be inaccessible to members of this company.';

				$dir_assigned_class = 'accp-directory-assigned';

				?>
				<span class="accp-reassign-directory-button button">
					<span class="accp-reassign-btn-initial">Update Directory</span>
					<span class="accp-reassign-btn-cancel">Cancel</span>
				</span>
				<?php

			}
			?>
			
			<div class="accp-generate-or-assign-dir-container <?php echo esc_attr( $dir_assigned_class ); ?>">

				<?php
				if ( $upload_dir ) {
					?>
					
					<p class="accp-dir-update-notice">Updating the directory name is a big change, and if this company already has files uploaded to the existing directory, you should assess the ramifications (within your operation) of changing the directory name.</p> 

					<?php
				}
				?>

				<div class="accp-generate-dir-container accp-dir-reassign-option">
					
					<p><strong>Generate a new directory (Advanced)</strong></p>					

					<?php
					if ( $upload_dir ) {
						?>

						<p>Generating and assigning a company directory for a company that already has an existing directory assigned is an an advanced feature, and should only be used if you understand the ramifications of this update.</p>

						<p>

							<label class="accp-dir-update-check-label"><input type="checkbox" id="new-dir-move-files" class="accp-new-dir-checkbox" name="new_dir_move_files" value="move"> Move files from the old directory (<?php echo esc_html( $upload_dir ); ?>) to the new directory.</label>							

							<label class="accp-dir-update-check-label"><input type="checkbox" id="new-dir-update-links" class="accp-new-dir-checkbox" name="new_dir_update_links" value="update"> Update attachment links with the new directory name for existing File and Invoice posts, if they exist.</label>
						
						</p>
					
						<?php
					}
					?>
					
					<input class="button" data-nonce="<?php echo esc_attr( $generate_nonce ); ?>" data-post-id="<?php echo esc_attr( $id ); ?>" type="submit" value="Generate New Directory" id="accp-generate-dir-btn" />
					
					<p class="accp-field-instructions"><?php echo wp_kses_post( $generate_dir_text ); ?></p>

				</div>

				<div class="accp-specify-dir-container accp-dir-reassign-option">

					<p><strong>Specify an existing directory name (Advanced)</strong></p>

					<p>Assigning a company directory for a company that already has an existing directory assigned is an an advanced feature, and should only be used if you understand the ramifications of this update.</p>
					
					<p><strong>Important:</strong> Any directory specified here should not be assigned to any other company.  The directory assignment must be unique to this company.</p>

					<?php
					if ( $upload_dir ) {
						?>

						<p>
							<strong>Important:</strong> If a file in the source directory has the same name as a file in the destination directory, the file will <strong>NOT</strong> be moved or copied to the destination directory.  To guard against conflicts, be sure to inspect the files in your source directory and destination directory to ensure that there are no files with the same name in the source and destination directories.
						</p>

						<p>

							<label class="accp-dir-update-check-label"><input type="checkbox" id="specify-dir-move-files" class="accp-specify-dir-checkbox" name="specify_dir_move_files" value="move"> Move files from the old directory (<?php echo esc_html( $upload_dir ); ?>) to the new directory.</label>

							<label class="accp-dir-update-check-label"><input type="checkbox" id="specify-dir-overwrite-duplicate-files" class="accp-specify-dir-checkbox" name="specify_dir_overwrite_duplicate_files" value="overwrite"> Overwrite duplicate files in destination directory. <strong>This is not reversible.</strong></label>

							<label class="accp-dir-update-check-label"><input type="checkbox" id="specify-dir-update-links" class="accp-specify-dir-checkbox" name="specify_dir_update_links" value="update"> Update attachment links with the new directory name for existing File and Invoice posts, if they exist.</label>
						
						</p>
					
						<?php
					}
					?>

					<?php echo esc_html( $truncated_path . '/' ); ?><input type="text" id="accp-specify-dir-name" name="accp_specify_dir_name" value=""> <span class="accp-field-note">Letters, numbers, underscores, or hyphens only.  The "global-files" name is reserved and cannot be used.</span>

					<input class="button" data-nonce="<?php echo esc_attr( $generate_nonce ); ?>" data-post-id="<?php echo esc_attr( $id ); ?>" type="submit" value="Assign Directory" id="accp-assign-dir-btn" />

					<p class="accp-specify-dir-field-instructions"><?php echo wp_kses_post( $specify_dir_text ); ?></p>

				</div>

			</div>			
			<?php

		}
	}


	/**
	 * Check for company directories assigned to more than one company.
	 *
	 * @param string $directory_name - the name of the company directory.
	 * @return array - list of company ID's if any, or empty array.
	 */
	public function accp_check_company_dir_for_multiple_assignments( $directory_name ) {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$company_id_list = array();

		/**
		 * Dev Note: Phpcs flagged the use of
		 * meta_query.  This is acceptable use.
		 */
		$args = array(
			'post_type'  => 'accp_clientcompany',
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query' => array(
				array(
					'key'   => 'accp_dir',
					'value' => sanitize_text_field( $directory_name ),
				),
			),
		);

		$company_query = new WP_Query( $args );

		$count = $company_query->found_posts;

		/**
		 * Return an empty array if 1 or fewer posts are returned.
		 */
		if ( $count <= 1 ) {
			return $company_id_list;
		}

		if ( $company_query->have_posts() ) {

			while ( $company_query->have_posts() ) {

				$company_query->the_post();

				$company_id_list[] = get_the_ID();

			}

			wp_reset_postdata();

		}

		return $company_id_list;
	}

	/**
	 * Dismiss duplicate upload directory
	 * assignment notice - AJAX.
	 */
	public function accp_dismiss_duplicate_dir_assignment_notice() {

		if ( ! isset( $_POST['nonce'] ) ) {
			die();
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'accp_dismiss_notice' ) ) {
			die();
		}

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! isset( $_POST['post_id'] ) ) {
			die();
		}

		$post_id = (int) $_POST['post_id'];

		update_post_meta( $post_id, 'accp_duplicate_dir_notice', 'dismissed' );

		wp_die();
	}


	/**
	 * Assign existing primary user AJAX function.
	 */
	public function accp_assign_existing_primary_user() {

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! is_admin() ) {
			die();
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			die();
		}

		/**
		 * Verify the nonce.
		 */
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'accp_assign_primary_user' ) ) {
			die();
		}

		if ( ! isset( $_POST['company_id'] ) || ! isset( $_POST['user_id'] ) ) {
			die();
		}

		$company_id = (int) $_POST['company_id'];
		$user_id    = (int) $_POST['user_id'];

		/**
		 * Check the user's main and additional company
		 * assignments and update the user's assigned
		 * companies accordingly.
		 */
		if ( ! get_user_meta( $user_id, 'client_company', true ) || empty( get_user_meta( $user_id, 'client_company', true ) ) ) {

			update_user_meta( $user_id, 'client_company', $company_id );

		} else {

			/**
			* If there is already a main company assignment,
			* assign to the additional companies list.
			*/
			$additional_companies = filter_var_array( (array) get_user_meta( $user_id, 'client_additional_company', true ), FILTER_SANITIZE_NUMBER_INT );

			if ( ! empty( $additional_companies ) ) {

				$additional_companies = array_map( 'intval', $additional_companies );
			}

			if ( (int) get_user_meta( $user_id, 'client_company', true ) !== $company_id && ! in_array( $company_id, $additional_companies, true ) ) {

				$additional_companies[] = (int) $company_id;

				update_user_meta( $user_id, 'client_additional_company', $additional_companies );

			}
		}

		/**
		 * Assign the primary user to the company.
		 */
		update_post_meta( $company_id, 'accp_company_primary_user', (int) $user_id );

		$user = get_user_by( 'ID', $user_id );

		$response = array(
			'status'   => 'success',
			'user_id'  => $user_id,
			'username' => $user->user_login,
		);

		echo wp_json_encode( $response );

		wp_die();
	}


	/**
	 * Create and assign new primary user AJAX function.
	 */
	public function accp_create_and_assign_primary_user() {

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! is_admin() ) {
			die();
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			die();
		}

		/**
		 * Verify the nonce.
		 */
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'accp_generate_new_user' ) ) {
			die();
		}

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! is_user_logged_in() ) {
			die();
		}

		if ( ! isset( $_POST['username'] ) || ! isset( $_POST['email'] ) || ! isset( $_POST['password'] ) || ! isset( $_POST['company_id'] ) || ! isset( $_POST['role'] ) ) {
			die();
		}

		/**
		 * Enforce password complexity.
		 */
		$password = sanitize_text_field( wp_unslash( $_POST['password'] ) );

		if ( ! preg_match( '/(?=^.{8,30}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $password ) ) {

			echo 'Please enter a password between 8-30 characters long, that contains at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.';

			wp_die();

		}

		$username   = sanitize_user( wp_unslash( $_POST['username'] ) );
		$role       = sanitize_text_field( wp_unslash( $_POST['role'] ) );
		$email      = filter_var( wp_unslash( $_POST['email'] ), FILTER_SANITIZE_EMAIL );
		$firstname  = isset( $_POST['firstname'] ) ? sanitize_text_field( wp_unslash( $_POST['firstname'] ) ) : '';
		$lastname   = isset( $_POST['lastname'] ) ? sanitize_text_field( wp_unslash( $_POST['lastname'] ) ) : '';
		$send_email = isset( $_POST['send_email'] ) ? sanitize_text_field( wp_unslash( $_POST['send_email'] ) ) : '';
		$company_id = filter_var( wp_unslash( $_POST['company_id'] ), FILTER_SANITIZE_NUMBER_INT );

		if ( ! $username || empty( $username ) ) {

			echo 'Please enter a valid username.';

			wp_die();

		}

		if ( ! $role || empty( $role ) ) {

			echo 'You must select a role for the new user.';

			wp_die();

		}

		if ( ! $company_id || empty( $company_id ) ) {

			echo 'Please save the post and try again.';

			wp_die();

		}

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {

			echo 'Please enter a valid email address.';

			wp_die();

		}

		$post = get_post( $company_id );

		if ( 'accp_clientcompany' !== $post->post_type ) {
			die();
		}

		/**
		 * Add the new user.
		 */
		$user_data = array(
			'user_login' => $username,
			'user_pass'  => $password,
			'user_email' => $email,
			'role'       => $role,
		);

		/**
		 * Add the firt and last name to the
		 * user data if the values are set.
		 */
		if ( $firstname && ! empty( $firstname ) ) {
			$user_data['first_name'] = $firstname;
		}

		if ( $lastname && ! empty( $lastname ) ) {
			$user_data['last_name'] = $lastname;
		}

		$new_user_id = wp_insert_user( $user_data );

		/**
		 * Errors - if there are errors,
		 * output the errors and exit.
		 */
		if ( is_object( $new_user_id ) ) {

			if ( $new_user_id->errors ) {

				$errors = $new_user_id->errors;

				foreach ( $errors as $error ) {

					if ( isset( $error[0] ) ) {

						echo esc_html( str_replace( '!', '.', $error[0] ?? '' ) );
					}
				}

				wp_die();

			}
		}

		/**
		 * Assign the company to the new user.
		 */
		update_user_meta( $new_user_id, 'client_company', (int) $company_id );

		/**
		 * Set user status to Active.
		 */
		update_user_meta( $new_user_id, 'client_status', 'active' );

		/**
		 * Assign the new user as the primary user
		 * for this company.
		 */
		update_post_meta( $company_id, 'accp_company_primary_user', (int) $new_user_id );

		/**
		 * Send notification email to new user.
		 */
		if ( 'send' === $send_email ) {

			$this->accp_send_new_account_notification_to_user( $new_user_id );

		}

		/**
		 * Output new user info.
		 */
		$user = get_user_by( 'ID', $new_user_id );

		$response = array(
			'status'   => 'success',
			'user_id'  => $new_user_id,
			'username' => $user->user_login,
		);

		echo wp_json_encode( $response );

		wp_die();
	}


	/**
	 * Send new user account email notification to
	 * the user with a password reset link.
	 *
	 * This is similar to the wp_new_user_notification
	 * WP function.
	 *
	 * @param int $new_user_id - The new user ID.
	 */
	protected function accp_send_new_account_notification_to_user( $new_user_id ) {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $new_user_id ) {
			return;
		}

		$user = get_user_by( 'ID', (int) $new_user_id );

		if ( ! $user ) {
			return;
		}

		$email        = $user->user_email;
		$username     = $user->user_login;
		$site_name    = get_option( 'blogname' );
		$login_url    = wp_login_url();
		$reset_key    = get_password_reset_key( $user );
		$pw_reset_url = $login_url . '?action=rp&key=' . $reset_key . '&login=' . $username;

		/**
		 * Email subject.
		 */
		$subject = '[' . $site_name . '] New Account Details';

		/**
		 * Email message.
		 */
		$message  = "Hello, \r\n";
		$message .= 'Your new ' . esc_html( $site_name ) . " account has been configured, and is ready for use. \r\n\r\n";
		$message .= 'Username: ' . esc_html( $username ) . "\r\n\r\n";
		$message .= "To set your password, please visit the following address: \r\n";
		$message .= rawurldecode( $pw_reset_url ) . "\r\n\r\n";
		$message .= "Thank you,\r\n";
		$message .= $site_name;

		/**
		 * Email headers.
		 */
		$headers   = array();
		$headers[] = 'From: "' . htmlspecialchars_decode( esc_html( get_bloginfo( 'name' ) ), ENT_QUOTES ) . '" <' . filter_var( get_option( 'admin_email' ), FILTER_SANITIZE_EMAIL ) . '>';
		$headers[] = 'Content-Type: text/plain; charset=UTF-8';

		/**
		 * Send the email.
		 */
		wp_mail( filter_var( $email, FILTER_SANITIZE_EMAIL ), esc_html( $subject ), wp_kses_post( $message ), $headers );
	}
} //End ARS_Constellation_Client_Portal_Company Class
