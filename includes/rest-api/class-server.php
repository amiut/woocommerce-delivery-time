<?php
/**
 * REST API Server
 *
 * @package WooDeliveryTime\RestApi
 */

namespace Dornaweb\WooDeliveryTime\Rest_API;
use Dornaweb\WooDeliveryTime\Rest_API\Utils\Singleton_Trait;

defined( 'ABSPATH' ) || exit;


/**
 * Class responsible for loading the REST API and all REST API namespaces.
 */
class Server {
	use Singleton_Trait;

	/**
	 * REST API namespaces and endpoints.
	 *
	 * @var array
	 */
	protected $controllers = array();

	/**
	 * Hook into WordPress ready to init the REST API as needed.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		foreach ( $this->get_rest_namespaces() as $namespace => $controllers ) {
			foreach ( $controllers as $controller_name => $controller_class ) {
				$this->controllers[ $namespace ][ $controller_name ] = new $controller_class();
				$this->controllers[ $namespace ][ $controller_name ]->register_routes();
			}
		}
	}

	/**
	 * Get API namespaces - new namespaces should be registered here.
	 *
	 * @return array List of Namespaces and Main controller classes.
	 */
	protected function get_rest_namespaces() {
		return apply_filters(
			'dweb_woodeliverytime_api_controllers',
			array(
				'wc/v1' => $this->get_v1_controllers(),
			)
		);
	}

	/**
	 * List of controllers in the wc/v1 namespace.
	 *
	 * @return array
	 */
	protected function get_v1_controllers() {
		return array(
		);
	}

	/**
	 * Return the path to the package.
	 *
	 * @return string
	 */
	public static function get_path() {
		return dirname( __DIR__ );
	}
}
