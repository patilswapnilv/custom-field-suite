<?php
/*
Plugin Name: Custom Field Suite
Plugin URI: https://github.com/mgibbs189/custom-field-suite
Description: Add custom fields to your WordPress edit screens
Version: 2.4.5
Author: Matt Gibbs
Author URI: http://customfieldsuite.com/
License: GPLv2
*/

class Custom_Field_Suite
{

    public $api;
    public $form;
    public $fields;
    public $field_group;
    private static $instance;


    /**
     * Define constants
     */
    function __construct() {
        define( 'CFS_VERSION', '2.4.5' );
        define( 'CFS_DIR', dirname( __FILE__ ) );
        define( 'CFS_URL', plugins_url( 'custom-field-suite' ) );

        add_action( 'init', array( $this, 'init' ) );
    }


    /**
     * Singleton
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new Custom_Field_Suite;
        }
        return self::$instance;
    }


    /**
     * Initialize
     */
    function init() {

        foreach ( array( 'api', 'upgrade', 'field', 'field-group', 'edit-screen', 'ajax', 'revision' ) as $f ) {
            include( CFS_DIR . "/includes/$f.php" );
        }

        $this->register_post_type();
        $this->fields = $this->get_field_types();
    }


    /**
     * Register the field group post type
     */
    function register_post_type() {
        register_post_type( 'cfs', array(
            'public'            => false,
            'show_ui'           => true,
            'capability_type'   => 'page',
            'hierarchical'      => false,
            'supports'          => array( 'title' ),
            'query_var'         => false,
            'labels'            => array(
                'name'                  => __( 'Field Groups', 'cfs' ),
                'singular_name'         => __( 'Field Group', 'cfs' ),
                'all_items'             => __( 'All Field Groups', 'cfs' ),
                'add_new_item'          => __( 'Add New Field Group', 'cfs' ),
                'edit_item'             => __( 'Edit Field Group', 'cfs' ),
                'new_item'              => __( 'New Field Group', 'cfs' ),
                'view_item'             => __( 'View Field Group', 'cfs' ),
                'search_items'          => __( 'Search Field Groups', 'cfs' ),
                'not_found'             => __( 'No Field Groups found', 'cfs' ),
                'not_found_in_trash'    => __( 'No Field Groups found in Trash', 'cfs' ),
            ),
        ));
    }


    /**
     * Define (and extend) field types
     */
    function get_field_types() {
        $field_types = apply_filters( 'cfs_field_types', array(
            'text'          => CFS_DIR . '/includes/fields/text.php',
            'textarea'      => CFS_DIR . '/includes/fields/textarea.php',
            'wysiwyg'       => CFS_DIR . '/includes/fields/wysiwyg.php',
            'hyperlink'     => CFS_DIR . '/includes/fields/hyperlink.php',
            'date'          => CFS_DIR . '/includes/fields/date/date.php',
            'color'         => CFS_DIR . '/includes/fields/color/color.php',
            'true_false'    => CFS_DIR . '/includes/fields/true_false.php',
            'select'        => CFS_DIR . '/includes/fields/select.php',
            'relationship'  => CFS_DIR . '/includes/fields/relationship.php',
            'user'          => CFS_DIR . '/includes/fields/user.php',
            'file'          => CFS_DIR . '/includes/fields/file.php',
            'loop'          => CFS_DIR . '/includes/fields/loop.php',
            'tab'           => CFS_DIR . '/includes/fields/tab.php',
        ) );

        foreach ( $field_types as $name => $file ) {
            $field_types[ $name ] = include_once( $file );
        }

        return $field_types;
    }


    /**
     * Generate input field HTML
     */
    function create_field( $field ) {
        $defaults = array(
            'type' => 'text',
            'input_name' => '',
            'input_class' => '',
            'options' => array(),
            'value' => '',
        );

        $field = (object) array_merge( $defaults, (array) $field );
        $this->fields[ $field->type ]->html( $field );
    }


    /**
     * Abstractions
     */
    function get( $field_name = false, $post_id = false, $options = array() ) {
        if ( false !== $field_name ) {
            return $this->api->get_field( $field_name, $post_id, $options );
        }
        return $this->api->get_fields( $post_id, $options );
    }


    function get_field_info( $field_name = false, $post_id = false ) {
        return $this->api->get_field_info( $field_name, $post_id );
    }


    function get_reverse_related( $post_id, $options = array() ) {
        return $this->api->get_reverse_related( $post_id, $options );
    }


    function save( $field_data = array(), $post_data = array(), $options = array() ) {
        return $this->api->save_fields( $field_data, $post_data, $options );
    }


    function form( $params = array() ) {
        ob_start();
        $this->form->render( $params );
        return ob_get_clean();
    }
}


function CFS() {
    return Custom_Field_Suite::instance();
}


$cfs = CFS();
