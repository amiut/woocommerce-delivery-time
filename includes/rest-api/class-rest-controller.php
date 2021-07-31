<?php
/**
 * REST API Controller abstract class
 *
 * @package Dornaweb
 * @author  Am!n <dornaweb.com>
 * @version 1.0
 * @since   1.0
 */

namespace Dornaweb\WooDeliveryTime\Rest_API;

defined('ABSPATH') || exit;

abstract class REST_Controller extends \WP_REST_Controller
{
    /**
     * Namespace
     *
     * @var string
     */
    public $namespace = 'dwebdeliverytime/v1';

    /**
     * REST Route
     */
    public $path = '';

    /**
     * HTTP Methods
     *
     * @var array
     */
    public $methods = ['GET'];

    public $override = false;
    public $one_path = "(?P<id>\d+)";
    public $one_method = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->one_methods = $this->one_methods ?: ['GET'];

        add_action( 'rest_api_init', [$this, 'register_routes']);
        add_action( 'rest_api_init', [$this, 'additional_routes']);
    }

    /**
     * Register Routes
     */
    public function register_routes() {
        $args = [];
        foreach ($this->methods as $method) {
            $args[] = [
                'methods'               => $method,
                'callback'              => [$this, strtolower($method)],
                'permission_callback'   => [$this, 'permission_' . strtolower($method)]
            ];
        }

        register_rest_route($this->namespace, "/" . $this->path, $args, $this->override);

        // Register `one` methods
        $args = [];
        foreach ($this->one_methods as $method) {
            $args[] = [
                'methods'   => $method,
                'callback'              => [$this, strtolower($method) . "_one"],
                'permission_callback'   => [$this, 'permission_' . strtolower($method) . '_one'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                        }
                    ],
                ],
            ];
        }

        register_rest_route($this->namespace, "/" . $this->path . "/" . $this->one_path, $args, $this->override);
    }

    public function additional_routes() {}
}
