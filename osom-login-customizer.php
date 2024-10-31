<?php
/**
 * Osom Login Page Customizer
 *
 * Plugin Name:       Osom Login Page Customizer
 * Plugin URI:        https://osompress.com
 * Description:       Osom Login Page Customizer lets you to easily customize the layout of the WordPress login page.
 * Version:           1.1.4
 * Author:            OsomPress
 * Author URI:        https://osompress.com/plugins/osom-login-page-customizer
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       osom-lc
 * Domain Path:       /languages
 */

namespace osom\Osom_Login;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//  Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OSOM_LC_VERSION', '1.1.4' );

add_action( 'init', __NAMESPACE__ . '\osom_lc_init' );
/**
 * Load required files.
 */
function osom_lc_init() {
	require dirname( __FILE__ ) . '/inc/osom-admin.php';
}

add_action( 'init', __NAMESPACE__ . '\osom_lc_load_textdomain' );
function osom_lc_load_textdomain() {
	load_plugin_textdomain( 'osom-lc', false, basename( __DIR__ ) . '/languages' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\osom_lc_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\osom_lc_styles' );
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\osom_lc_styles' );

function osom_lc_styles() {
	$plugin_url = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'osom-lc-style', $plugin_url . '/assets/css/osom-login-customizer.css', OSOM_LC_VERSION, true );
	wp_enqueue_style( 'dashicons' );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\osom_lc_load_color_picker' );
function osom_lc_load_color_picker() {
	$plugin_url = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );
	wp_enqueue_script( 'iris-init', $plugin_url . '/assets/js/iris-init.js' );
}

// Redirect to plugin settings after activation
register_activation_hook( __FILE__, __NAMESPACE__ . '\osom_lc_activate' );
add_action( 'admin_init', __NAMESPACE__ . '\osom_lc_redirect' );

function osom_lc_activate() {
	add_option( 'osom_lc_do_activation_redirect', true );
}

function osom_lc_redirect() {
	if ( get_option( 'osom_lc_do_activation_redirect', false ) ) {
		delete_option( 'osom_lc_do_activation_redirect' );
		if ( ! isset( $_GET['activate-multi'] ) ) {
			wp_redirect( home_url() . '/wp-admin/admin.php?page=osom_lc_main_menu' );
		}
	}
}

add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\osom_login_customize_output' );
/**
 * Output settings.
 */
function osom_login_customize_output() {

	$settings = get_option( 'osom_lc_settings' );

	if ( ! isset( $settings['remember'] ) ) {
		$settings['remember'] = 0;
	}
	if ( ! isset( $settings['lostpassword'] ) ) {
		$settings['lostpassword'] = 0;
	}
	if ( ! isset( $settings['backtoblog'] ) ) {
		$settings['backtoblog'] = 0;
	}
	if ( ! isset( $settings['langswitcher'] ) ) {
		$settings['langswitcher'] = 0;
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

	global $wp_version;

	$remember                = $settings['remember'];
	$lostpassword            = $settings['lostpassword'];
	$back_to_blog            = $settings['backtoblog'];
	$language_switcher       = $settings['langswitcher'];
	$background_color        = $settings['backgroundcolor'];
	$login_form_color        = $settings['formcolor'];
	$login_form_border_color = $settings['bordercolor'];
	$login_button_color      = $settings['buttoncolor'];

	?>

	<?php
	if ( ! empty( $background_color ) ) {
		?>
	<style type="text/css">
		body.login {
			background-color: <?php echo esc_attr( $background_color ); ?>;
		}
	</style>
		<?php
	}

	if ( ! empty( $login_form_color ) ) {
		?>
<style type="text/css">
	body.login div#login form#loginform {
		background-color: <?php echo esc_attr( $login_form_color ); ?>;
	}
</style>
		<?php
	}

	if ( ! empty( $login_form_border_color ) ) {
		?>
<style type="text/css">
	body.login div#login form#loginform {
		border: 2px solid <?php echo esc_attr( $login_form_border_color ); ?>;
	}
</style>
		<?php
	}

	if ( ! empty( $login_form_color ) ) {
		?>
<style type="text/css">
	body.login div#login form p.submit input#wp-submit {
		background-color: <?php echo esc_attr( $login_button_color ); ?>;
	}
</style>
		<?php
	}

	if ( 0 === $remember ) {
		?>
	<style type="text/css">
		.login .forgetmenot {
			display: none;
		}
	</style>
		<?php
	}

	if ( $lostpassword == 0 ) {
		?>
	<style type="text/css">
		.login #nav {
			display: none;
		}
	</style>
		<?php
	}

	if ( $back_to_blog == 0 ) {
		?>
	<style type="text/css">
		.login #backtoblog {
			display: none;
		}
	</style>
		<?php
	}

	if ( $wp_version >= 5.9 && 0 === $language_switcher ) {
		add_filter( 'login_display_language_dropdown', '__return_false' );
	}

}

add_action( 'login_head', __NAMESPACE__ . '\osom_lc_login_logo' );
/**
 * Use site logo instead of WordPress logo.
 */
function osom_lc_login_logo() {
	if ( ! empty( ( get_theme_mod( 'custom_logo' ) ) ) ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		$logo_image_url = $image[0];
	}
	?>
	<style type="text/css">
	.login h1 a {
		background-image: url('<?php echo esc_url( $logo_image_url ) ?>');
		background-size: contain;
		background-repeat: no-repeat;
		display: block;
		overflow: hidden;
		text-indent: -9999em;
		width: 100%;
		height: 100px;
	}
	</style>
	<?php
}

add_filter( 'login_headerurl', __NAMESPACE__ . '\osom_lc_login_header_url' );
/**
 * Redirect logo URL to home.
 */
function osom_lc_login_header_url( $url ) {
	return esc_url( home_url() );
}

/**
 * Add login_redirect filter only if OML is not activated
 */

if ( ! in_array( 'osom-modal-login/osom-modal-login.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_filter( 'login_redirect', __NAMESPACE__ . '\osom_lc_login_url_redirection' );
}

/**
 * Manage URL redirections after login.
 */
function osom_lc_login_url_redirection() {

	$settings  = get_option( 'osom_lc_settings' );
	$login_url = ! empty( $settings['loginurl'] );

	if ( '' === $login_url ) {
		$login_url = home_url();
	}

	if ( current_user_can( 'manage_options' ) ) {

		$login_url = home_url() . '/wp-admin/';

	} elseif ( ! empty( $settings['loginurl'] ) ) {

		$login_url = $settings['loginurl'];

	} else {

		$login_url = esc_url( home_url() );

	}

	return $login_url;

}

register_uninstall_hook( __FILE__, 'osom_lc_uninstall_plugin' );
/**
 * Uninstall plugin.
 */
function osom_lc_uninstall_plugin() {

	$settings = get_option( 'osom_lc_settings' );
	delete_option( 'osom_lc_settings' );

}
