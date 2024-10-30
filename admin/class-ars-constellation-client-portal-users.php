<?php
/**
 * ARS_Constellation_Client_Portal_Users Class
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
 * ARS_Constellation_Client_Portal_Users Class
 */
class ARS_Constellation_Client_Portal_Users {

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
	 * Add user profile fields.
	 *
	 * @param object $user - The user object.
	 *
	 * @hooked show_user_profile.
	 * @hooked edit_user_profile.
	 * @hooked user_new_form.
	 */
	public function add_core_user_profile_fields( $user ) {

		if ( ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $user || ! is_object( $user ) ) {
			return;
		}

		$user_id = $user->ID;

		if ( ! $user_id ) {
			return;
		}

		/**
		 * Define the allowed html for wp_kses.
		 */
		$accp_settings = new ARS_Constellation_Client_Portal_Settings();
		$allowed_html  = $accp_settings->get_customized_allowed_html_for_wp_kses();

		echo wp_kses( $this->get_core_user_fields_html( $user, $user_id ), $allowed_html );
	}


	/**
	 * Get the core user profile fields html.
	 *
	 * @param object $user - The user object.
	 * @param int    $user_id - The user ID.
	 */
	public function get_core_user_fields_html( $user, $user_id ) {

		if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		}

		if ( ! $user || ! $user_id ) {
			return;
		}

		$wp_post_statuses = get_post_statuses() ? array_keys( (array) get_post_statuses() ) : array();

		$html = '';

		$html .= '<h2>Constellation Client Information</h2>';

		$html .= '<table class="form-table">';

		/**
		 * User edit nonce field.
		 */
		$html .= $this->get_user_edit_nonce_field_html();

		/**
		 * Primary Company section.
		 */
		$html .= $this->get_user_primary_company_html( $user, $user_id );

		/**
		 * Client Status section.
		 */
		$html .= $this->get_user_client_status_html( $user, $user_id );

		/**
		 * Additional Assigned Companies section.
		 */
		$html .= $this->get_user_additional_companies_html( $user, $user_id );

		/**
		 * Allow content to be added after the core
		 * user content.
		 *
		 * Buffer the output of the hook so that it
		 * appears in the expected position.
		 *
		 * Hook - accp_after_user_profile_settings.
		 */
		if ( is_admin() && is_user_logged_in() && current_user_can( 'manage_options' ) ) {

			ob_start();

			do_action( 'accp_after_user_profile_settings', $user_id );

			$html .= ob_get_contents();

			ob_end_clean();

		}

		$html .= '</table>';

		return $html;
	}


	/**
	 * Get the user edit nonce name.
	 */
	public function get_user_edit_nonce_name() {

		return 'accp_user_edit_nonce';
	}


	/**
	 * Get the user edit nonce field name.
	 */
	public function get_user_edit_nonce_field_name() {

		return 'accp_user_edit_nonce_field';
	}


	/**
	 * Get the user edit nonce field html.
	 */
	public function get_user_edit_nonce_field_html() {

		$nonce_name       = $this->get_user_edit_nonce_name();
		$nonce_field_name = $this->get_user_edit_nonce_field_name();
		$nonce            = wp_create_nonce( $nonce_name );

		$html = '';

		$html .= '<input type="hidden" name="' . esc_attr( $nonce_field_name ) . '" value="' . esc_attr( $nonce ) . '" >';

		return $html;
	}


	/**
	 * Get the user profile primary company section html.
	 *
	 * @param object $user - The user object.
	 * @param int    $user_id - The user ID.
	 *
	 * @return string $html - The section html.
	 */
	public function get_user_primary_company_html( $user, $user_id ) {

		if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		}

		if ( ! $user || ! $user_id ) {
			return;
		}

		$saved_company = is_object( $user ) ? get_user_meta( $user_id, 'client_company', true ) : '';

		$html = '';

		$html .= '<tr>';

		$html .= '<th>';

		$html .= '<label for="client_company">';

		$html .= 'Company';

		$html .= '<span class="accp-admin-tooltip-icon">i</span>';

		$html .= '<span class="accp-wp-admin-tooltip accp-wp-admin-tooltip-dark accp-wp-admin-has-tooltip-center accp-user-status-tooltip">Not seeing an assigned company? Make sure that the company post is published.</span>';

		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

		if ( current_user_can( 'manage_options' ) ) {

			$args = array(
				'post_type' => 'accp_clientcompany',
			);

			/**
			 * Include all defined WP post statuses,
			 * not just "publish."
			 */
			if ( ! empty( $wp_post_statuses ) ) {

				$args['post_status'] = $wp_post_statuses;

			}

			$company_list = get_pages( $args );

			$html .= '<select name="client_company" id="client_company">';

			$html .= '<option value="">Select a company...</option>';

			foreach ( $company_list as $key => $company ) {

				$company_id        = $company->ID;
				$company_name      = $company->post_title;
				$selected          = $saved_company && (int) $saved_company === (int) $company_id ? 'selected' : '';
				$post_status_class = '';

				if ( 'publish' !== $company->post_status ) {

					$company_name      = $company_name . ' (' . $company->post_status . ')';
					$post_status_class = 'accp-non-published-post-option';

				}

				$html .= '<option class="level-0  ' . esc_attr( $post_status_class ) . '" value="' . esc_attr( $company_id ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $company_name ) . '</option>';

			}

			$html .= '</select>';

		} elseif ( ! empty( $saved_company ) ) {

				$html .= esc_html( get_the_title( $saved_company ) );
		}

		$html .= '<br />';

		$html .= '</td>';

		$html .= '</tr>';

		return $html;
	}


	/**
	 * Get the user profile client status section html.
	 *
	 * @param object $user - The user object.
	 * @param int    $user_id - The user ID.
	 *
	 * @return string $html - The section html.
	 */
	public function get_user_client_status_html( $user, $user_id ) {

		if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		}

		if ( ! $user || ! $user_id ) {
			return;
		}

		$saved_status = is_object( $user ) ? get_user_meta( $user->ID, 'client_status', true ) : '';

		$html = '';

		$html .= '<tr>';

		$html .= '<th>';

		$html .= '<label for="client_status">';

		$html .= 'Client Status';

		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {

			$html .= '<span class="accp-admin-tooltip-icon">i</span>';

			$html .= '<span class="accp-wp-admin-tooltip accp-wp-admin-tooltip-dark accp-wp-admin-has-tooltip-center accp-user-status-tooltip">A status of <strong>Inactive</strong> or <strong>Pending</strong> will prevent the user from accessing client pages, invoices, and files.</span>';

		}

		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

		if ( current_user_can( 'manage_options' ) ) {

			$html .= '<select name="client_status" id="client-status" value="' . esc_attr( $saved_status ) . '">';

			$html .= '<option value="active" ' . ( 'active' === $saved_status ? esc_attr( 'selected' ) : '' ) . '>Active</option>';

			$html .= '<option value="inactive" ' . ( 'inactive' === $saved_status ? esc_attr( 'selected' ) : '' ) . '>Inactive</option>';

			$html .= '<option value="pending" ' . ( 'pending' === $saved_status ? esc_attr( 'selected' ) : '' ) . '>Pending</option>';

			$html .= '</select>';

		} elseif ( ! empty( $saved_status ) ) {

				$html .= esc_html( $saved_status );
		}

		$html .= '</td>';

		$html .= '</tr>';

		return $html;
	}


	/**
	 * Get the user profile additional companies section html.
	 *
	 * @param object $user - The user object.
	 * @param int    $user_id - The user ID.
	 *
	 * @return string $html - The section html.
	 */
	public function get_user_additional_companies_html( $user, $user_id ) {

		if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		}

		if ( ! $user || ! $user_id ) {
			return;
		}

		$label           = current_user_can( 'manage_options' ) ? 'Assign Additional Companies' : 'Additional Companies';
		$saved_companies = array();

		if ( is_object( $user ) ) {

			if ( get_user_meta( $user_id, 'client_additional_company', true ) && ! empty( get_user_meta( $user_id, 'client_additional_company', true ) ) ) {

				$saved_companies = (array) get_user_meta( $user_id, 'client_additional_company', true );

			}
		}

		$saved_companies = array_map( 'intval', $saved_companies );

		$html = '';

		$html .= '<tr>';

		$html .= '<th>';

		$html .= '<label for="client_company">';

		$html .= esc_html( $label );

		$html .= '<span class="accp-admin-tooltip-icon">i</span>';

		$html .= '<span class="accp-wp-admin-tooltip accp-wp-admin-tooltip-dark accp-wp-admin-has-tooltip-center accp-user-status-tooltip">Not seeing an assigned company? Make sure that the company post is published.</span>';

		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

		if ( current_user_can( 'manage_options' ) ) {

			$args = array(
				'post_type' => 'accp_clientcompany',
			);

			/**
			 * Include all defined WP post statuses,
			 * not just "publish."
			 */
			if ( ! empty( $wp_post_statuses ) ) {

				$args['post_status'] = $wp_post_statuses;

			}

			$company_list = get_pages( $args );

			$html .= '<select class="client-add-company-select" name="client_additional_company[]"  value="" multiple="multiple" >';

			foreach ( $company_list as $company ) {

				$company_id        = (int) $company->ID;
				$company_name      = $company->post_title;
				$selected          = in_array( $company_id, $saved_companies, true ) ? 'selected' : '';
				$post_status_class = '';

				if ( 'publish' !== $company->post_status ) {

					$company_name      = $company_name . ' (' . $company->post_status . ')';
					$post_status_class = 'accp-non-published-post-option';

				}

				$html .= '<option class="level-0 ' . esc_attr( $post_status_class ) . '" value="' . esc_attr( $company_id ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $company_name ) . '</option>';

			}

				$html .= '</select>';

		} elseif ( ! empty( $saved_companies ) ) {

				$html .= '<ul>';

			foreach ( $saved_companies as $saved_company ) {

				$html .= '<li>' . esc_html( get_the_title( $saved_company ) ) . '</li>';

			}

				$html .= '</ul>';
		}

		$html .= '</td>';

		$html .= '</tr>';

		return $html;
	}


	/**
	 * Check user global page suitability.  If a user
	 * is assigned to only 1 company, they are suitable
	 * for global pages, otherwise they are not.
	 *
	 * @param int $user_id - The ID of the user to check.
	 *
	 * @return bool $is_eligible - true|false.
	 */
	public function check_if_user_is_eligible_for_global_pages( $user_id ) {

		$primary_company_id     = get_user_meta( $user_id, 'client_company', true );
		$additional_company_ids = get_user_meta( $user_id, 'client_additional_company', true );

		/**
		 * Return true if a primary company is assigned and no
		 * additional companies are assigned.
		 */
		if ( $primary_company_id && ( ! $additional_company_ids || empty( $additional_company_ids ) ) ) {
			return true;
		}

		/**
		 * Return true if a primary company is not assigned and only
		 * one additional company is assigned.
		 */
		if ( ! $primary_company_id && $additional_company_ids && is_array( $additional_company_ids ) ) {

			if ( count( $additional_company_ids ) === 1 ) {
				return true;
			}
		}

		/**
		 * Return true if a primary company is not assigned and
		 * no additional companies are assigned.
		 */
		if ( ! $primary_company_id && ( ! $additional_company_ids || empty( $additional_company_ids ) ) ) {

				return true;

		}

		return false;
	}


	/**
	 * Save user profile fields.
	 *
	 * @param int $user_id - The user ID.
	 *
	 * @hooked personal_options_update.
	 * @hooked edit_user_profile_update.
	 * @hooked user_register.
	 */
	public function save_extra_user_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$nonce_name       = $this->get_user_edit_nonce_name();
		$nonce_field_name = $this->get_user_edit_nonce_field_name();

		if ( isset( $_POST[ $nonce_field_name ] ) ) {

			$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_field_name ] ) );

			if ( wp_verify_nonce( $nonce, $nonce_name ) ) {

				/**
				 * Assigned Company
				 */
				if ( isset( $_POST['client_company'] ) ) {

					$company_id = filter_var( wp_unslash( $_POST['client_company'] ), FILTER_SANITIZE_NUMBER_INT );
					update_user_meta( $user_id, 'client_company', $company_id );

				}

				/**
				 * User Client Status
				 */
				if ( isset( $_POST['client_status'] ) ) {

					$client_status = sanitize_text_field( wp_unslash( $_POST['client_status'] ) );
					update_user_meta( $user_id, 'client_status', $client_status );

				}

				/**
				 * Additional Companies
				 */
				if ( ! empty( $_POST['client_additional_company'] ) ) {

					$sanitized_company_array = array_map( 'intval', $_POST['client_additional_company'] );

					if ( $sanitized_company_array && ! empty( $sanitized_company_array ) ) {

						update_user_meta( $user_id, 'client_additional_company', $sanitized_company_array );

					}
				} else {

					delete_user_meta( $user_id, 'client_additional_company' );

				}
			}
		}
	}


	/**
	 * Remove company from user profile when a
	 * company post is permanently deleted in WP.
	 *
	 * Referenced in the "after_delete_post" hook.
	 *
	 * @param int    $post_id - The post ID of the post being deleted.
	 * @param object $post - The post object that is being deleted.
	 */
	public function remove_company_from_user_on_company_permanent_delete( $post_id, $post ) {

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			exit;
		}

		if ( ! $post_id || ! $post ) {
			return;
		}

		/**
		 * Verify that this is an accp_clientcompany post.
		 */
		if ( 'accp_clientcompany' !== $post->post_type ) {
			return;
		}

		/**
		 * Check for users that are assigned to this company.
		 */
		$users = $this->get_all_users_assigned_to_company( $post_id );

		if ( ! $users || empty( $users ) ) {
			return;
		}

		foreach ( $users as $user_id ) {

			/**
			 * Remove this company from the user's
			 * primary assigned company if it exists.
			 */
			$this->remove_primary_company_from_user( $user_id, $post_id );

			/**
			 * Remove this company from the user's
			 * additional assigned companies if it exists.
			 */
			$this->remove_additional_company_from_user( $user_id, $post_id );

		}
	}


	/**
	 * Remove a primary company from a user.  If
	 * the $company_id does not match the currently
	 * saved primary company, no action will be taken.
	 *
	 * @param int $user_id - The ID of the user.
	 * @param int $company_id - The post ID of the company.
	 */
	public function remove_primary_company_from_user( $user_id, $company_id ) {

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			exit;
		}

		if ( ! $user_id || ! $company_id ) {
			return false;
		}

		if ( ! get_user_meta( $user_id, 'client_company', true ) ) {
			return false;
		}

		$saved_company = get_user_meta( $user_id, 'client_company', true );

		if ( (int) $saved_company !== (int) $company_id ) {
			return false;
		}

		delete_user_meta( $user_id, 'client_company' );
	}


	/**
	 * Remove a company from the additional assigned
	 * company list for a user.
	 *
	 * @param int $user_id - The ID of the user.
	 * @param int $company_id - The post ID of the company.
	 */
	public function remove_additional_company_from_user( $user_id, $company_id ) {

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			exit;
		}

		if ( ! $user_id || ! $company_id ) {
			return false;
		}

		$saved_companies = (array) get_user_meta( $user_id, 'client_additional_company', true );

		if ( ! $saved_companies || empty( $saved_companies ) ) {
			return false;
		}

		$saved_companies = array_map( 'intval', $saved_companies );

		if ( ! in_array( (int) $company_id, $saved_companies, true ) ) {
			return false;
		}

		foreach ( $saved_companies as $key => $id ) {

			if ( (int) $id === (int) $company_id ) {

				unset( $saved_companies[ $key ] );

				update_user_meta( $user_id, 'client_additional_company', $saved_companies );

				break;

			}
		}
	}


	/**
	 * Get all users assigned to a given company.
	 *
	 * @param int $company_id - The post ID of the company.
	 *
	 * @return array $users - Array of user IDs assigned to a company (if any);
	 */
	public function get_all_users_assigned_to_company( $company_id ) {

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$users = array();

		if ( ! $company_id ) {
			return $users;
		}

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

		if ( empty( $user_query ) ) {
			return $users;
		}

		$query_results = (array) $user_query->results;

		if ( empty( $query_results ) ) {
			return $users;
		}

		foreach ( $query_results as $user ) {

			if ( $user->ID ) {

				$users[] = $user->ID;

			}
		}

		return $users;
	}
} // END ARS_Constellation_Client_Portal_Users
