<?php
/**
 * Public-facing functionality.
 *
 * @package    ARS_CONSTELLATION_CLIENT_PORTAL
 * @subpackage ARS_Constellation_Client_Portal/public
 * @author     Adrian Rodriguez Studios <dev@adrianrodriguezstudios.com>
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public-facing functionality.
 */
class ARS_Constellation_Client_Portal_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register public-facing css.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ars-constellation-client-portal-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register public-facing scripts.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ars-constellation-client-portal-public.js', array( 'jquery' ), $this->version, false );

		/**
		 * Localize script for AJAX functionality.
		 */
		wp_localize_script( $this->plugin_name, 'accpfrontajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}


	/**
	 * Set up Client Home Page Link shortcode.
	 * This will only check the user's primary
	 * assigned company, and base the home page
	 * on that.
	 *
	 * Shortcode: [accp_my_company_page]
	 *
	 * @param array $atts - array of atts passed in via the shortcode.
	 *
	 * @return string $html - shortcode result.
	 */
	public function accp_client_home_link( $atts ) {

		/**
		 * Exit if this is WP Admin.
		 */
		if ( is_admin() ) {
			return;
		}

		global $post;

		$user    = wp_get_current_user();
		$user_id = get_current_user_id();

		/**
		 * Set up the Client Home Page Link shortcode attributes.
		 */
		$atts = shortcode_atts(
			array(
				'link_text'       => '', // Sets the link text.
				'link_class'      => '', // Adds a class or classes to the link - separate multiple classes with a space.
				'hide_logged_out' => '', // Hide the link/button for logged out users - default = true.
			),
			$atts
		);

		ob_start();

		$html = '';

		/**
		 * Link Text att.
		 */
		$link_text = 'My Home';

		if ( null !== $atts['link_text'] ) {

			$link_text = $atts['link_text'];

		}

		/**
		 * Link Class att.
		 */
		$link_class = null;

		if ( null !== $atts['link_class'] ) {

			$link_class = $atts['link_class'];

		}

		/**
		 * Hide if Logged Out att.
		 */
		$hide_logged_out = true;

		if ( null !== $atts['hide_logged_out'] ) {

			$hide_logged_out = strtolower( $atts['hide_logged_out'] );

		}

		/**
		 * Get the primary Company ID associated with the current user.
		 */
		$curr_user_company_id = get_user_meta( $user_id, 'client_company', true );

		if ( ! $curr_user_company_id ) {
			return;
		}

		/**
		 * Get the home page of the current user's company.
		 */
		$company_home_page = null;

		if ( $curr_user_company_id && ! empty( $curr_user_company_id ) ) {

			$company_home_page = get_post_meta( $curr_user_company_id, 'accp_home_page', true );

		}

		if ( null === $company_home_page || ! $company_home_page || empty( $company_home_page ) ) {
			return;
		}

		if ( is_user_logged_in() ) {

			$home_page_link = get_the_permalink( $company_home_page );

			?>				
			<a class="accp-home-link <?php echo null !== $link_class ? esc_attr( $link_class ) : ''; ?>" href="<?php echo esc_url( $home_page_link ); ?>"><?php echo esc_html( $link_text ); ?></a>
			<?php

		} elseif ( ! is_user_logged_in() && 'false' === $hide_logged_out ) {

			?>
			<a class="accp-home-link <?php echo null !== $link_class ? esc_attr( $link_class ) : ''; ?>" href="<?php echo esc_url( wp_login_url( 'accp_homebtn_redir' ) ); ?>"><?php echo esc_html( $link_text ); ?></a>
			<?php

		}

		$html .= ob_get_clean();

		return $html;
	}
} // END ARS_Constellation_Client_Portal_Public class
