<?php
/**
 * Osom Login Customizer
 */

namespace osom\Osom_Login;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter( 'plugin_action_links_osom-login-page-customizer/osom-login-customizer.php', __NAMESPACE__ . '\osom_settings_link' );
/**
 * Add Plugin Settings link to plugin page.
 */
function osom_settings_link( $links ) {
	$url = esc_url(
		add_query_arg(
			'page',
			'osom_lc_main_menu',
			get_admin_url() . 'admin.php'
		)
	);

	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	array_push(
		$links,
		$settings_link
	);
	return $links;
};

// Create new item in the admin menu
if ( ! function_exists( __NAMESPACE__ . '\osom_lc_parent_page' ) ) {

	function osom_lc_parent_page() {

		if ( ! osom_lc_menu_exists( 'osom' ) ) {
			add_menu_page(
				'Osom',
				'OsomPress',
				'manage_options',
				'osom',
				__NAMESPACE__ . '\osom_lc_parent_page',
				'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 227 230" > <path fill="#063B54" stroke="#063B54" strokeWidth="8" d="M18.584 169.3l.013.023.013.022c9.893 16.985 23.258 30.631 40.054 40.895 16.856 10.301 35.16 15.465 54.836 15.465 19.819 0 38.112-4.965 54.797-14.9l.011-.007c16.816-10.09 30.108-23.661 39.826-40.666C218.045 152.925 223 134.203 223 114.041c0-19.637-4.964-37.914-14.877-54.757-9.714-16.992-22.913-30.473-39.556-40.384C151.883 8.965 133.59 4 113.771 4 94.115 4 75.826 9.063 58.977 19.17 42.16 29.075 28.782 42.55 18.886 59.54 8.97 76.38 4 94.572 4 114.041c0 19.63 4.87 38.071 14.584 55.259zM159.84 32.786l.016.009.015.01c14.19 8.478 25.426 19.969 33.745 34.528 8.307 14.536 12.458 30.262 12.458 47.249 0 17.159-4.237 33.061-12.72 47.777l-.004.006-.004.007c-8.321 14.562-19.562 26.148-33.762 34.809-14.16 8.458-29.495 12.679-46.083 12.679-16.588 0-31.923-4.221-46.083-12.679-14.2-8.661-25.534-20.249-34.036-34.816-8.304-14.713-12.454-30.618-12.454-47.783 0-16.98 4.148-32.701 12.449-47.233C41.877 52.78 53.208 41.285 67.4 32.804l.016-.009.015-.01c14.154-8.63 29.573-12.94 46.34-12.94 16.577 0 31.907 4.306 46.068 12.94z" ></path> <path fill="#AABDCF" d="M114.312 105.385c7.47 0 13.525 6.055 13.525 13.525 0 7.47-6.055 13.526-13.525 13.526-7.47 0-13.526-6.056-13.526-13.526s6.056-13.525 13.526-13.525zM114.312 150.019c7.47 0 13.525 6.056 13.525 13.526s-6.055 13.526-13.525 13.526c-7.47 0-13.526-6.056-13.526-13.526s6.056-13.526 13.526-13.526zM114.312 60.75c7.47 0 13.525 6.056 13.525 13.526S121.782 87.8 114.312 87.8c-7.47 0-13.526-6.055-13.526-13.525 0-7.47 6.056-13.526 13.526-13.526z" ></path></svg>' ),
				81
			);
		}

	}
	add_action( 'admin_menu', __NAMESPACE__ . '\osom_lc_parent_page' );
}
if ( ! function_exists( __NAMESPACE__ . '\osom_lc_add_submenu' ) ) {
	function osom_lc_add_submenu() {
		add_submenu_page(
			'osom',
			esc_html__( 'Osom Login Customizer', 'osom-lc' ),
			esc_html__( 'Osom Login Customizer', 'osom-lc' ),
			'manage_options',
			'osom_lc_main_menu',
			__NAMESPACE__ . '\osom_lc_main_menu_settings'
		);

		remove_submenu_page( 'osom', 'osom' );
	}
	add_action( 'admin_menu', __NAMESPACE__ . '\osom_lc_add_submenu' );
}

// Render settings form
function osom_lc_main_menu_settings() {

	$plugin_url = plugin_dir_url( __DIR__ );

	?>
	<div class="wrap">
	<h2>
	<?php esc_html_e( 'Osom Login Customizer', 'osom-lc' ); ?>
	</h2>
	<img src="<?php echo esc_url( $plugin_url ); ?>assets/img/icon.png" width="100" class="logo-osom">
	<form action="options.php" method="post" class="osom-form">

	<?php
	settings_fields( 'osom_lc_settings_group' );
	do_settings_sections( 'osom_lc_main_menu' );
	submit_button();

}

add_action( 'admin_init', __NAMESPACE__ . '\osom_lc_settings_init' );
function osom_lc_settings_init() {

	// Register the setting
	register_setting( 'osom_lc_settings_group', 'osom_lc_settings', 'osom_lc_sanitize_validate_settings' );

	add_settings_section( 'osom_lc_section', '', __NAMESPACE__ . '\osom_lc_section_callback', 'osom_lc_main_menu' );

	$settings = get_option( 'osom_lc_settings' );

	if ( ! isset( $settings['lostpassword'] ) ) {
		$settings['lostpassword'] = 0;
	}
	if ( ! isset( $settings['remember'] ) ) {
		$settings['remember'] = 0;
	}
	if ( ! isset( $settings['lostpassword'] ) ) {
		$settings['lostpassword'] = 0;
	}
	if ( ! isset( $settings['backtoblog'] ) ) {
		$settings['backtoblog'] = '';
	}
	if ( ! isset( $settings['langswitcher'] ) ) {
		$settings['langswitcher'] = '';
	}
	if ( ! isset( $settings['backgroundcolor'] ) ) {
		$settings['backgroundcolor'] = '';
	}
	if ( ! isset( $settings['formcolor'] ) ) {
		$settings['formcolor'] = '';
	}
	if ( ! isset( $settings['bordercolor'] ) ) {
		$settings['bordercolor'] = '';
	}
	if ( ! isset( $settings['buttoncolor'] ) ) {
		$settings['buttoncolor'] = '';
	}
	if ( ! isset( $settings['loginurl'] ) ) {
		$settings['loginurl'] = '';
	}

	global $wp_version;

	add_settings_field(
		'osom_lc_field_remember',
		esc_html__( 'Display Remember option', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'  => 'osom_lc_settings[remember]',
			'value' => $settings['remember'],
			'type'  => 'checkbox',
		)
	);

	add_settings_field(
		'osom_lc_field_showpass',
		esc_html__( 'Display Lost Password option', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'  => 'osom_lc_settings[lostpassword]',
			'value' => $settings['lostpassword'],
			'type'  => 'checkbox',
		)
	);

	add_settings_field(
		'osom_lc_field_backtoblog',
		esc_html__( 'Display Back to Blog link', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'  => 'osom_lc_settings[backtoblog]',
			'value' => $settings['backtoblog'],
			'type'  => 'checkbox',
		)
	);

	if ( $wp_version >= 5.9) {
		add_settings_field(
			'osom_lc_field_lang_switcher',
			esc_html__( 'Display Language Switcher', 'osom-lc' ),
			__NAMESPACE__ . '\osom_lc_fields_callback',
			'osom_lc_main_menu',
			'osom_lc_section',
			array(
				'name'  => 'osom_lc_settings[langswitcher]',
				'value' => $settings['langswitcher'],
				'type'  => 'checkbox',
			)
		);
	}

	add_settings_field(
		'osom_lc_field_color',
		esc_html__( 'Background Color', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'        => 'osom_lc_settings[backgroundcolor]',
			'value'       => $settings['backgroundcolor'],
			'type'        => 'color',
			'idcolor'     => 1,
			'default'     => '#FFFFFF',
			'description' => '',
		)
	);

	add_settings_field(
		'osom_lc_login_form_color_field',
		esc_html__( 'Login Form Color', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'        => 'osom_lc_settings[formcolor]',
			'value'       => $settings['formcolor'],
			'type'        => 'color',
			'idcolor'     => 2,
			'default'     => '#21759B',
			'description' => '',
		)
	);

	add_settings_field(
		'osom_lc_login_form_border_color_field',
		esc_html__( 'Login Form Border Color', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'        => 'osom_lc_settings[bordercolor]',
			'value'       => $settings['bordercolor'],
			'type'        => 'color',
			'idcolor'     => 3,
			'default'     => '#000000',
			'description' => '',
		)
	);

	add_settings_field(
		'osom_lc_button_color_field',
		esc_html__( 'Login Button Color', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'        => 'osom_lc_settings[buttoncolor]',
			'value'       => $settings['buttoncolor'],
			'type'        => 'color',
			'idcolor'     => 4,
			'default'     => '#FFFFFF',
			'description' => '',
		)
	);

	add_settings_field(
		'osom_lc_field_loginurl',
		esc_html__( 'Login URL redirection', 'osom-lc' ),
		__NAMESPACE__ . '\osom_lc_fields_callback',
		'osom_lc_main_menu',
		'osom_lc_section',
		array(
			'name'        => 'osom_lc_settings[loginurl]',
			'value'       => $settings['loginurl'],
			'type'        => 'textbox',
			'description' => esc_html__( 'URL after login' ),
		)
	);

}

// Sanitize and validate settings
function osom_lc_sanitize_validate_settings( $input ) {

	$output = get_option( 'osom_lc_settings' );

	$output['remember']        = absint( $input['remember'] );
	$output['lostpassword']    = absint( $input['lostpassword'] );
	$output['backtoblog']      = absint( $input['backtoblog'] );
	$output['langswitcher']    = absint( $input['langswitcher'] );
	$output['backgroundcolor'] = sanitize_hex_color( $input['backgroundcolor'] );
	$output['formcolor']       = sanitize_hex_color( $input['formcolor'] );
	$output['bordercolor']     = sanitize_hex_color( $input['bordercolor'] );
	$output['buttoncolor']     = sanitize_hex_color( $input['buttoncolor'] );
	$output['loginurl']        = sanitize_text_field( $input['loginurl'] );

	return $output;
}

function osom_lc_section_callback() {
	echo nl2br( esc_html__( 'The plugin allows you to customize the WordPress login page', 'osom-lc' ) );
	echo '<br>';
	echo nl2br( esc_html__( 'On this page you can set the options for the login page.', 'osom-lc' ) );
}

function osom_lc_fields_callback( $args ) {

	if ( ! isset( $args['placeholder'] ) ) {
		$args['placeholder'] = '';
	}

	switch ( $args['type'] ) :

		case 'checkbox':
			?>
			<input type="checkbox" name="<?php echo esc_attr( $args['name'] ); ?>" <?php checked( $args['value'], 1 ); ?> value="1">
			<?php
			break;

		case 'color':
			?>
			<input type="text" class="color-picker" name="<?php echo esc_attr( $args['name'] ); ?>" id='color-picker-<?php echo esc_attr( $args['idcolor'] ); ?>' value="<?php echo esc_attr( $args['value'] ); ?>" />
			<?php
			break;

		default:
			echo '<input type="text" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" size="60">';
			break;
	endswitch;

}

if ( ! function_exists( 'osom_lc_menu_exists' ) ) {
	function osom_lc_menu_exists( $handle, $sub = false ) {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return false;
		}
		global $menu, $submenu;
		$check_menu = $sub ? $submenu : $menu;
		if ( empty( $check_menu ) ) {
			return false;
		}
		foreach ( $check_menu as $k => $item ) {
			if ( $sub ) {
				foreach ( $item as $sm ) {
					if ( $handle === $sm[2] ) {
						return true;
					}
				}
			} else {
				if ( $handle === $item[2] ) {
					return true;
				}
			}
		}
		return false;
	}
}

?>
