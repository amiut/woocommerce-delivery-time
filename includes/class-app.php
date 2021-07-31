<?php
/**
 * WooDeliveryTime main class
 *
 * @package WooDeliveryTime
 * @since   1.0
 */

namespace Dornaweb\WooDeliveryTime;

defined('ABSPATH') || exit;

/**
 * WooDeliveryTime main class
 */
final class App
{
	/**
	 * Plugin version.
	 *
	 * @var string
	 */
    public $version = '1.0';

    /**
     * Plugin instance.
     *
     * @since 1.0
     * @var null|WooDeliveryTime\App
     */
    public static $instance = null;

    /**
     * Plugin API.
     *
     * @since 1.0
     * @var WooDeliveryTime\API\API
     */
    public $api = '';

    /**
     * Return the plugin instance.
     *
     * @return Dornaweb_Pack
     */
    public static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Dornaweb_Pack constructor.
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init();
        $this->init_hooks();

    }

    public function init_hooks() {
        add_action( 'init', array( $this, 'load_rest_api' ) );
    }

    /**
     * Make Translatable
     *
     */
    public function i18n() {
        load_plugin_textdomain( 'deliverytime', false, dirname( plugin_basename( DWEB_DELIVERY_TIME_PLUGIN_FILE ) ) . "/languages" );
    }

    /**
     * Include required files
     *
     */
    public function includes() {
        include DWEB_DELIVERY_TIME_ABSPATH . 'includes/helpers/helper-functions.php';
    }

    /**
     * Define constant if not already set.
     *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
    }

    /**
     * Define constants
     */
    public function define_constants() {
		$this->define('DWEB_DELIVERY_TIME_ABSPATH', dirname(DWEB_DELIVERY_TIME_PLUGIN_FILE) . '/');
		$this->define('DWEB_DELIVERY_TIME_PLUGIN_BASENAME', plugin_basename(DWEB_DELIVERY_TIME_PLUGIN_FILE));
		$this->define('DWEB_DELIVERY_TIME_PLUGIN_VERSION', $this->version);
		$this->define('DWEB_DELIVERY_TIME_PLUGIN_URL', $this->plugin_url());
		$this->define('DWEB_DELIVERY_TIME_API_TEST_MODE', true);
    }

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit(plugins_url('/', DWEB_DELIVERY_TIME_PLUGIN_FILE));
    }

    /**
     * Do initial stuff
     */
    public function init() {
        register_activation_hook(DWEB_DELIVERY_TIME_PLUGIN_FILE, ['\\Dornaweb\\WooDeliveryTime\\Install', 'install']);

        // Admin stuffs
        // Admin\Admin::init();

        // Add scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'public_dependencies']);

        // Initiate Required classes
        // $this->api = new API\REST_Controller;
    }

    /**
     * Register scripts and styles for public area
     */
    public function public_dependencies() {
    }

    /**
     * Load REST api
     */
    public function load_rest_api() {
        \Dornaweb\WooDeliveryTime\Rest_API\Server::instance()->init();
    }
}
