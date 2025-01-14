<?php
/**
 * Taxonomy API: WP_Taxonomy class
 *
 * @package ClassicPress
 * @subpackage Taxonomy
 * @since WP-4.7.0
 */

/**
 * Core class used for interacting with taxonomies.
 *
 * @since WP-4.7.0
 */
final class WP_Taxonomy {
	/**
	 * Taxonomy key.
	 *
	 * @since WP-4.7.0
	 * @var string
	 */
	public $name;

	/**
	 * Name of the taxonomy shown in the menu. Usually plural.
	 *
	 * @since WP-4.7.0
	 * @var string
	 */
	public $label;

	/**
	 * An array of labels for this taxonomy.
	 *
	 * @since WP-4.7.0
	 * @var object
	 */
	public $labels = array();

	/**
	 * A short descriptive summary of what the taxonomy is for.
	 *
	 * @since WP-4.7.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $public = true;

	/**
	 * Whether the taxonomy is publicly queryable.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $publicly_queryable = true;

	/**
	 * Whether the taxonomy is hierarchical.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $hierarchical = false;

	/**
	 * Whether to generate and allow a UI for managing terms in this taxonomy in the admin.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $show_ui = true;

	/**
	 * Whether to show the taxonomy in the admin menu.
	 *
	 * If true, the taxonomy is shown as a submenu of the object type menu. If false, no menu is shown.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $show_in_menu = true;

	/**
	 * Whether the taxonomy is available for selection in navigation menus.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $show_in_nav_menus = true;

	/**
	 * Whether to list the taxonomy in the tag cloud widget controls.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $show_tagcloud = true;

	/**
	 * Whether to show the taxonomy in the quick/bulk edit panel.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $show_in_quick_edit = true;

	/**
	 * Whether to display a column for the taxonomy on its post type listing screens.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $show_admin_column = false;

	/**
	 * The callback function for the meta box display.
	 *
	 * @since WP-4.7.0
	 * @var bool|callable
	 */
	public $meta_box_cb = null;

	/**
	 * An array of object types this taxonomy is registered for.
	 *
	 * @since WP-4.7.0
	 * @var array
	 */
	public $object_type = null;

	/**
	 * Capabilities for this taxonomy.
	 *
	 * @since WP-4.7.0
	 * @var array
	 */
	public $cap;

	/**
	 * Rewrites information for this taxonomy.
	 *
	 * @since WP-4.7.0
	 * @var array|false
	 */
	public $rewrite;

	/**
	 * Query var string for this taxonomy.
	 *
	 * @since WP-4.7.0
	 * @var string|false
	 */
	public $query_var;

	/**
	 * Function that will be called when the count is updated.
	 *
	 * @since WP-4.7.0
	 * @var callable
	 */
	public $update_count_callback;

	/**
	 * Whether this taxonomy should appear in the REST API.
	 *
	 * Default false. If true, standard endpoints will be registered with
	 * respect to $rest_base and $rest_controller_class.
	 *
	 * @since WP-4.7.4
	 * @var bool $show_in_rest
	 */
	public $show_in_rest;

	/**
	 * The base path for this taxonomy's REST API endpoints.
	 *
	 * @since WP-4.7.4
	 * @var string|bool $rest_base
	 */
	public $rest_base;

	/**
	 * The controller for this taxonomy's REST API endpoints.
	 *
	 * Custom controllers must extend WP_REST_Controller.
	 *
	 * @since WP-4.7.4
	 * @var string|bool $rest_controller_class
	 */
	public $rest_controller_class;

	/**
	 * Whether it is a built-in taxonomy.
	 *
	 * @since WP-4.7.0
	 * @var bool
	 */
	public $_builtin;

	/**
	 * Constructor.
	 *
	 * @since WP-4.7.0
	 *
	 * @global WP $wp WP instance.
	 *
	 * @param string       $taxonomy    Taxonomy key, must not exceed 32 characters.
	 * @param array|string $object_type Name of the object type for the taxonomy object.
	 * @param array|string $args        Optional. Array or query string of arguments for registering a taxonomy.
	 *                                  Default empty array.
	 */
	public function __construct( $taxonomy, $object_type, $args = array() ) {
		$this->name = $taxonomy;

		$this->set_props( $object_type, $args );
	}

	/**
	 * Sets taxonomy properties.
	 *
	 * @since WP-4.7.0
	 *
	 * @param array|string $object_type Name of the object type for the taxonomy object.
	 * @param array|string $args        Array or query string of arguments for registering a taxonomy.
	 */
	public function set_props( $object_type, $args ) {
		$args = wp_parse_args( $args );

		/**
		 * Filters the arguments for registering a taxonomy.
		 *
		 * @since WP-4.4.0
		 *
		 * @param array  $args        Array of arguments for registering a taxonomy.
		 * @param string $taxonomy    Taxonomy key.
		 * @param array  $object_type Array of names of object types for the taxonomy.
		 */
		$args = apply_filters( 'register_taxonomy_args', $args, $this->name, (array) $object_type );

		$defaults = array(
			'labels'                => array(),
			'description'           => '',
			'public'                => true,
			'publicly_queryable'    => null,
			'hierarchical'          => false,
			'show_ui'               => null,
			'show_in_menu'          => null,
			'show_in_nav_menus'     => null,
			'show_tagcloud'         => null,
			'show_in_quick_edit'    => null,
			'show_admin_column'     => false,
			'meta_box_cb'           => null,
			'capabilities'          => array(),
			'rewrite'               => true,
			'query_var'             => $this->name,
			'update_count_callback' => '',
			'show_in_rest'          => false,
			'rest_base'             => false,
			'rest_controller_class' => false,
			'_builtin'              => false,
		);

		$args = array_merge( $defaults, $args );

		// If not set, default to the setting for public.
		if ( null === $args['publicly_queryable'] ) {
			$args['publicly_queryable'] = $args['public'];
		}

		if ( false !== $args['query_var'] && ( is_admin() || false !== $args['publicly_queryable'] ) ) {
			if ( true === $args['query_var'] ) {
				$args['query_var'] = $this->name;
			} else {
				$args['query_var'] = sanitize_title_with_dashes( $args['query_var'] );
			}
		} else {
			// Force query_var to false for non-public taxonomies.
			$args['query_var'] = false;
		}

		if ( false !== $args['rewrite'] && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
			$args['rewrite'] = wp_parse_args(
				$args['rewrite'],
				array(
					'with_front'   => true,
					'hierarchical' => false,
					'ep_mask'      => EP_NONE,
				)
			);

			if ( empty( $args['rewrite']['slug'] ) ) {
				$args['rewrite']['slug'] = sanitize_title_with_dashes( $this->name );
			}
		}

		// If not set, default to the setting for public.
		if ( null === $args['show_ui'] ) {
			$args['show_ui'] = $args['public'];
		}

		// If not set, default to the setting for show_ui.
		if ( null === $args['show_in_menu'] || ! $args['show_ui'] ) {
			$args['show_in_menu'] = $args['show_ui'];
		}

		// If not set, default to the setting for public.
		if ( null === $args['show_in_nav_menus'] ) {
			$args['show_in_nav_menus'] = $args['public'];
		}

		// If not set, default to the setting for show_ui.
		if ( null === $args['show_tagcloud'] ) {
			$args['show_tagcloud'] = $args['show_ui'];
		}

		// If not set, default to the setting for show_ui.
		if ( null === $args['show_in_quick_edit'] ) {
			$args['show_in_quick_edit'] = $args['show_ui'];
		}

		$default_caps = array(
			'manage_terms' => 'manage_categories',
			'edit_terms'   => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_posts',
		);

		$args['cap'] = (object) array_merge( $default_caps, $args['capabilities'] );
		unset( $args['capabilities'] );

		$args['object_type'] = array_unique( (array) $object_type );

		// If not set, use the default meta box
		if ( null === $args['meta_box_cb'] ) {
			if ( $args['hierarchical'] ) {
				$args['meta_box_cb'] = 'post_categories_meta_box';
			} else {
				$args['meta_box_cb'] = 'post_tags_meta_box';
			}
		}

		$args['name'] = $this->name;

		foreach ( $args as $property_name => $property_value ) {
			$this->$property_name = $property_value;
		}

		$this->labels = get_taxonomy_labels( $this );
		$this->label  = $this->labels->name;
	}

	/**
	 * Adds the necessary rewrite rules for the taxonomy.
	 *
	 * @since WP-4.7.0
	 *
	 * @global WP $wp Current ClassicPress environment instance.
	 */
	public function add_rewrite_rules() {
		/* @var WP $wp */
		global $wp;

		// Non-publicly queryable taxonomies should not register query vars, except in the admin.
		if ( false !== $this->query_var && $wp ) {
			$wp->add_query_var( $this->query_var );
		}

		if ( false !== $this->rewrite && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
			if ( $this->hierarchical && $this->rewrite['hierarchical'] ) {
				$tag = '(.+?)';
			} else {
				$tag = '([^/]+)';
			}

			add_rewrite_tag( "%$this->name%", $tag, $this->query_var ? "{$this->query_var}=" : "taxonomy=$this->name&term=" );
			add_permastruct( $this->name, "{$this->rewrite['slug']}/%$this->name%", $this->rewrite );
		}
	}

	/**
	 * Removes any rewrite rules, permastructs, and rules for the taxonomy.
	 *
	 * @since WP-4.7.0
	 *
	 * @global WP $wp Current ClassicPress environment instance.
	 */
	public function remove_rewrite_rules() {
		/* @var WP $wp */
		global $wp;

		// Remove query var.
		if ( false !== $this->query_var ) {
			$wp->remove_query_var( $this->query_var );
		}

		// Remove rewrite tags and permastructs.
		if ( false !== $this->rewrite ) {
			remove_rewrite_tag( "%$this->name%" );
			remove_permastruct( $this->name );
		}
	}

	/**
	 * Registers the ajax callback for the meta box.
	 *
	 * @since WP-4.7.0
	 */
	public function add_hooks() {
		add_filter( 'wp_ajax_add-' . $this->name, '_wp_ajax_add_hierarchical_term' );
	}

	/**
	 * Removes the ajax callback for the meta box.
	 *
	 * @since WP-4.7.0
	 */
	public function remove_hooks() {
		remove_filter( 'wp_ajax_add-' . $this->name, '_wp_ajax_add_hierarchical_term' );
	}
}
