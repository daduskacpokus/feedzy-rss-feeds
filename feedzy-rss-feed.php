<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://themeisle.com
 * @since             3.0.0
 * @package           feedzy-rss-feeds
 *
 * @wordpress-plugin
 * Plugin Name:       Feedzy RSS Feeds Pro
 * Plugin URI:        https://themeisle.com/plugins/feedzy-rss-feeds/
 * Description:       A small and lightweight RSS aggregator plugin. Fast and very easy to use, it allows you to aggregate multiple RSS feeds into your WordPress site through fully customizable shortcodes & widgets.
 * Version:           4.4.9
 * Author:            Themeisle
 * Author URI:        http://themeisle.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       feedzy-rss-feeds
 * Domain Path:       /languages
 * WordPress Available:  yes
 * Pro Slug:    feedzy-rss-feeds-pro
 * Requires License:    no
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/feedzy-rss-feeds-activator.php
 */
function activate_feedzy_rss_feeds() {
	Feedzy_Rss_Feeds_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is  documented in includes/feedzy-rss-feeds-deactivator.php
 *
 * @since    3.0.0
 */
function deactivate_feedzy_rss_feeds() {
	Feedzy_Rss_Feeds_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_feedzy_rss_feeds' );
register_deactivation_hook( __FILE__, 'deactivate_feedzy_rss_feeds' );
/**
 * The function thast will handle the queue for autoloader.
 *
 * @since    3.0.0
 */
function feedzy_rss_feeds_autoload( $class ) {
	$namespaces = array( 'Feedzy_Rss_Feeds' );
	foreach ( $namespaces as $namespace ) {
		if ( substr( $class, 0, strlen( $namespace ) ) === $namespace ) {
			$filename = plugin_dir_path( __FILE__ ) . 'includes/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/abstract/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/admin/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/gutenberg/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/util/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/elementor/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
		}
	}
	if ( is_readable( plugin_dir_path( __FILE__ ) . 'includes/admin/feedzy-wp-widget.php' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/feedzy-wp-widget.php';

		return true;
	}

	return false;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.s
 *
 * @since    3.0.0.
 */
function run_feedzy_rss_feeds() {
	define( 'FEEDZY_BASEFILE', __FILE__ );
	define( 'FEEDZY_ABSURL', plugins_url( '/', __FILE__ ) );
	define( 'FEEDZY_BASENAME', plugin_basename( __FILE__ ) );
	define( 'FEEDZY_ABSPATH', dirname( __FILE__ ) );
	define( 'FEEDZY_DIRNAME', basename( FEEDZY_ABSPATH ) );
	define( 'FEEDZY_UPSELL_LINK', 'https://themeisle.com/plugins/feedzy-rss-feeds/upgrade/' );
	define( 'FEEDZY_SUBSCRIBE_API', 'https://api.themeisle.com/tracking/subscribe' );
	define( 'FEEDZY_NAME', 'Feedzy RSS Feeds' );
	define( 'FEEDZY_USER_AGENT', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36' );
	define( 'FEEDZY_ALLOW_HTTPS', true );
	define( 'FEEDZY_REST_VERSION', '1' );
	// to redirect all themeisle_log_event to error log.
	define( 'FEEDZY_LOCAL_DEBUG', false );
	define( 'FEEDZY_FEED_CUSTOM_TAG_NAMESPACE', 'http://feedzy.themeisle.com' );

	// always make this true before testing
	// also used in gutenberg.
	define( 'FEEDZY_DISABLE_CACHE_FOR_TESTING', false );

	$vendor_file = FEEDZY_ABSPATH . '/vendor/autoload.php';
	if ( is_readable( $vendor_file ) ) {
		require_once $vendor_file;
	}
	$feedzy = Feedzy_Rss_Feeds::instance();
	$feedzy->run();
	add_filter( 'themeisle_sdk_products', 'feedzy_register_sdk', 10, 1 );
	add_filter( 'pirate_parrot_log', 'feedzy_register_parrot', 10, 1 );
	add_filter(
		'themeisle_sdk_compatibilities/' . FEEDZY_DIRNAME, function ( $compatibilities ) {
			$compatibilities['FeedzyPRO'] = array(
				'basefile'  => defined( 'FEEDZY_PRO_BASEFILE' ) ? FEEDZY_PRO_BASEFILE : '',
				'required'  => '1.7',
				'tested_up' => '2.4',
			);
			return $compatibilities;
		}
	);
	add_filter(
		'feedzy_rss_feeds_about_us_metadata',
		function() {
			return array(
				'logo'             => FEEDZY_ABSURL . 'img/feedzy.svg',
				'location'         => 'feedzy-admin-menu',
				'has_upgrade_menu' => ! feedzy_is_pro(),
				'upgrade_text'     => esc_html__( 'Upgrade to Pro', 'feedzy-rss-feeds' ),
				'upgrade_link'     => tsdk_utmify( FEEDZY_UPSELL_LINK, 'aboutUsPage' ),
			);
		}
	);
	define( 'FEEDZY_SURVEY_PRO', 'https://forms.gle/FZXhL3D48KJUhb7q9' );
	define( 'FEEDZY_SURVEY_FREE', 'https://forms.gle/yQUGSrKEa7XJTGLx8' );

}

/**
 * Registers with the SDK
 *
 * @since    1.0.0
 */
function feedzy_register_sdk( $products ) {
	$products[] = FEEDZY_BASEFILE;
	return $products;
}

/**
 * Registers with the parrot plugin
 *
 * @since    1.0.0
 */
function feedzy_register_parrot( $plugins ) {
	$plugins[] = FEEDZY_NAME;
	return $plugins;
}

spl_autoload_register( 'feedzy_rss_feeds_autoload' );
run_feedzy_rss_feeds();


if ( FEEDZY_LOCAL_DEBUG ) {
	add_action( 'themeisle_log_event', 'feedzy_themeisle_log_event', 10, 5 );

	/**
	 * Redirect themeisle_log_event to error log.
	 */
	function feedzy_themeisle_log_event( $name, $msg, $type, $file, $line ) {
		if ( $name === FEEDZY_NAME ) {
			error_log( sprintf( '%s (%s:%d): %s', $type, $file, $line, $msg ) );
		}
	}
}

add_filter( 'themeisle_sdk_enable_telemetry', '__return_true' );

add_filter(
	'feedzy_rss_feeds_float_widget_metadata', function () {
		return array(
			'nice_name'          => 'Feedzy',
			'logo'               => FEEDZY_ABSURL . 'img/feedzy.svg',
			'primary_color'      => '#4268CF',
			'pages'              => array( 'feedzy_imports', 'edit-feedzy_imports', 'edit-feedzy_categories', 'feedzy_page_feedzy-settings', 'feedzy_page_feedzy-support' ),
			'has_upgrade_menu'   => ! feedzy_is_pro(),
			'upgrade_link'       => tsdk_utmify( FEEDZY_UPSELL_LINK, 'floatWidget' ),
			'documentation_link' => tsdk_utmify( 'https://docs.themeisle.com/collection/1569-feedzy-rss-feeds', 'floatWidget' ),
			'wizard_link'        => ! feedzy_is_pro() && ! empty( get_option( 'feedzy_fresh_install', false ) ) ? admin_url( 'admin.php?page=feedzy-setup-wizard&tab#step-1' ) : '',
		);
	}
);
