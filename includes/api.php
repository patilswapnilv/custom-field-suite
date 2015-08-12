<?php

class cfs_api
{

    public $cache;


    /**
     * Get a field value
     */
    public function get_field( $field_name, $post_id = false, $options = array() ) {
        global $post;

        $defaults = array( 'format' => 'api' ); // api, input, or raw
        $options = array_merge( $defaults, $options );
        $post_id = empty( $post_id ) ? $post->ID : (int) $post_id;

        // Trigger get_fields() if not in cache
        if ( ! isset( $this->cache[ $post_id ][ $options['format'] ][ $field_name ] ) ) {
            $fields = $this->get_fields( $post_id, $options );
            return isset( $fields[ $field_name ] ) ? $fields[ $field_name ] : null;
        }

        return $this->cache[ $post_id ][ $options['format'] ][ $field_name ];
    }


    /**
     * Get all field values for a specific post
     */
    public function get_fields( $post_id = false, $options = array() ) {

    }


    /**
     * Get properties (label, type, etc) for a field or fields
     */
    public function get_field_info( $field_name = false, $post_id = false ) {

    }


    /**
     * Get referenced post IDs (from relationship fields)
     */
    public function get_reverse_related( $post_id, $options = array() ) {

    }


    /**
     * Get a post's matching field groups (based on placement rules)
     */
    public function get_matching_groups() {

    }


    /**
     * Save field values for a post
     */
    function save_fields( $field_data = array(), $post_data = array(), $options = array() ) {

    }
}

CFS()->api = new cfs_api();
