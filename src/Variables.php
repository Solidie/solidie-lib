<?php
/**
 * The variable provider functionalities
 *
 * @package solidie/solidie-lib
 */

namespace SolidieLib;

/**
 * The class
 */
class Variables {

	/**
	 * Configs
	 *
	 * @var object
	 */
	private $configs;

	/**
	 * Variable constructor
	 *
	 * @param object $configs App configs
	 */
	public function __construct( $configs ) {
		$this->configs = $configs;
	}

	/**
	 * Get variables
	 *
	 * @return array
	 */
	public function get() {

		$nonce_action = '_solidie_' . str_replace( '-', '_', gmdate( 'Y-m-d' ) );
		$nonce        = wp_create_nonce( $nonce_action );
		$user         = wp_get_current_user();

		// Determine the react react root path
		$parsed    = wp_parse_url( get_home_url() );
		$root_site = 'http' . ( is_ssl() ? 's' : '' ) . '://' . $parsed['host'] . ( ! empty( $parsed['port'] ) ? ':' . $parsed['port'] : '' );
		$home_path = trim( $parsed['path'] ?? '', '/' );
		$page_path = is_singular() ? trim( str_replace( $root_site, '', get_permalink( get_the_ID() ) ), '/' ) : null;

		return array(
			'is_admin'     => is_admin(),
			'action_hooks' => array(),
			'filter_hooks' => array(),
			'mountpoints'  => (object) array(),
			'home_path'    => $home_path,
			'page_path'    => $page_path,
			'app_name'     => $this->configs->app_id,
			'nonce'        => $nonce,
			'nonce_action' => $nonce_action,
			'colors'       => Colors::getColors( $this->configs->color_scheme ?? null ),
			'opacities'    => Colors::getOpacities(),
			'contrast'     => Colors::CONTRAST_FACTOR,
			'text_domain'  => $this->configs->text_domain,
			'date_format'  => get_option( 'date_format' ),
			'time_format'  => get_option( 'time_format' ),
			'is_apache'    => is_admin() ? strpos( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ?? '' ) ), 'Apache' ) !== false : null,
			'bloginfo'     => array(
				'name' => get_bloginfo( 'name' ),
			),
			'user'         => array(
				'id'           => $user ? $user->ID : 0,
				'first_name'   => $user ? $user->first_name : null,
				'last_name'    => $user ? $user->last_name : null,
				'email'        => $user ? $user->user_email : null,
				'display_name' => $user ? $user->display_name : null,
				'avatar_url'   => $user ? get_avatar_url( $user->ID ) : null,
				'username'     => $user ? $user->user_login : null,
			),
			'settings'     => array(),
			'permalinks'   => array(
				'home_url' => get_home_url(),
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'logout'   => htmlspecialchars_decode( wp_logout_url( get_home_url() ) ),
			),
		);
	}
}
