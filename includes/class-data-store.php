<?php
/**
 * WooDeliveryTime Data Store.
 *
 * @package WooDeliveryTime\Classes
 * @since   1.0.0
 * @version 1.0.0
 */
namespace Dornaweb\WooDeliveryTime;

defined( 'ABSPATH' ) || exit;

/**
 * Data store class.
 */
class Data_Store {

	/**
	 * Contains an instance of the data store class that we are working with.
	 *
	 * @var Data_Store
	 */
	private $instance = null;

	/**
	 * Contains an array of default WC supported data stores.
	 * Format of object name => class name.
	 * You can also pass something like product_<type> for product stores and
	 * that type will be used first when available, if a store is requested like
	 * this and doesn't exist, then the store would fall back to 'product'.
	 * Ran through `dweb_deliverytime_data_stores`.
	 *
	 * @var array
	 */
	private $stores = array(
		'bookmark'                => '\\Dornaweb\\WooDeliveryTime\\Data_Stores\\Bookmark_Data_Store',
		'bookmark_list'                => '\\Dornaweb\\WooDeliveryTime\\Data_Stores\\Bookmark_List_Data_Store',
	);

	/**
	 * Contains the name of the current data store's class name.
	 *
	 * @var string
	 */
	private $current_class_name = '';

	/**
	 * The object type this store works with.
	 *
	 * @var string
	 */
	private $object_type = '';


	/**
	 * Tells Data_Store which object (coupon, product, order, etc)
	 * store we want to work with.
	 *
	 * @throws \Exception When validation fails.
	 * @param string $object_type Name of object.
	 */
	public function __construct( $object_type ) {
		$this->object_type = $object_type;
		$this->stores      = apply_filters( 'dweb_deliverytime_data_stores', $this->stores );

		// If this object type can't be found, check to see if we can load one
		// level up (so if product-type isn't found, we try product).
		if ( ! array_key_exists( $object_type, $this->stores ) ) {
			$pieces      = explode( '-', $object_type );
			$object_type = $pieces[0];
		}

		if ( array_key_exists( $object_type, $this->stores ) ) {
			$store = apply_filters( 'dweb_deliverytime_' . $object_type . '_data_store', $this->stores[ $object_type ] );
			if ( is_object( $store ) ) {
				if ( ! $store instanceof \Dornaweb\WooDeliveryTime\Interfaces\Object_Data_Store_Interface ) {
					throw new \Exception( __( 'Invalid data store.', 'dweb_deliverytime' ) );
				}
				$this->current_class_name = get_class( $store );
				$this->instance           = $store;
			} else {
				if ( ! class_exists( $store ) ) {
					throw new \Exception( __( 'Invalid data store.', 'dweb_deliverytime' ) );
				}
				$this->current_class_name = $store;
				$this->instance           = new $store();
			}
		} else {
			throw new \Exception( __( 'Invalid data store.', 'dweb_deliverytime' ) );
		}
	}

	/**
	 * Only store the object type to avoid serializing the data store instance.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'object_type' );
	}

	/**
	 * Re-run the constructor with the object type.
	 *
	 * @throws \Exception When validation fails.
	 */
	public function __wakeup() {
		$this->__construct( $this->object_type );
	}

	/**
	 * Loads a data store.
	 *
	 * @param string $object_type Name of object.
	 *
	 * @since 1.0.0
	 * @throws \Exception When validation fails.
	 * @return Data_Store
	 */
	public static function load( $object_type ) {
		return new Data_Store( $object_type );
	}

	/**
	 * Returns the class name of the current data store.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_current_class_name() {
		return $this->current_class_name;
	}

	/**
	 * Reads an object from the data store.
	 *
	 * @since 1.0.0
	 * @param DATA $data dweb_deliverytime data instance.
	 */
	public function read( &$data ) {
		$this->instance->read( $data );
	}

	/**
	 * Create an object in the data store.
	 *
	 * @since 1.0.0
	 * @param DATA $data dweb_deliverytime data instance.
	 */
	public function create( &$data ) {
		$this->instance->create( $data );
	}

	/**
	 * Update an object in the data store.
	 *
	 * @since 1.0.0
	 * @param DATA $data dweb_deliverytime data instance.
	 */
	public function update( &$data ) {
		$this->instance->update( $data );
	}

	/**
	 * Delete an object from the data store.
	 *
	 * @since 1.0.0
	 * @param DATA $data dweb_deliverytime data instance.
	 * @param array   $args Array of args to pass to the delete method.
	 */
	public function delete( &$data, $args = array() ) {
		$this->instance->delete( $data, $args );
	}

	/**
	 * Data stores can define additional functions (for example, coupons have
	 * some helper methods for increasing or decreasing usage). This passes
	 * through to the instance if that function exists.
	 *
	 * @since 1.0.0
	 * @param string $method     Method.
	 * @param mixed  $parameters Parameters.
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		if ( is_callable( array( $this->instance, $method ) ) ) {
			$object     = array_shift( $parameters );
			$parameters = array_merge( array( &$object ), $parameters );
			return $this->instance->$method( ...$parameters );
		}
	}
}
