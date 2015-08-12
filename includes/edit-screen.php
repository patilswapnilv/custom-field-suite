<?php

class cfs_edit_screen
{

    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_post' ) );
        add_action( 'delete_post', array( $this, 'delete_post' ) );
    }


    /**
     * Add the CFS settings menu
     */
    function admin_menu() {
        if ( ! apply_filters( 'cfs_disable_admin', false ) ) {
            add_submenu_page( 'edit.php?post_type=cfs', __( 'Settings', 'cfs' ), __( 'Settings', 'cfs' ), 'manage_options', 'cfs-settings', array( $this, 'page_settings' ) );
        }
    }


    /**
     * Save post
     */
    function save_post( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! isset( $_POST['cfs']['save'] ) ) {
            return;
        }
        if ( false !== wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( wp_verify_nonce( $_POST['cfs']['save'], 'cfs_save_fields' ) ) {
            $fields = isset( $_POST['cfs']['fields'] ) ? $_POST['cfs']['fields'] : array();
            $rules = isset( $_POST['cfs']['rules'] ) ? $_POST['cfs']['rules'] : array();
            $extras = isset( $_POST['cfs']['extras'] ) ? $_POST['cfs']['extras'] : array();

            CFS()->field_group->save( array(
                'post_id'   => $post_id,
                'fields'    => $fields,
                'rules'     => $rules,
                'extras'    => $extras,
            ) );
        }
    }


    /**
     * Delete post
     */
    function delete_post( $post_id ) {
        global $wpdb;

        if ( 'cfs' != get_post_type( $post_id ) ) {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cfs_values WHERE post_id = " . (int) $post_id );
        }

        return true;
    }


    /**
     * Settings page
     */
    function page_settings() {
        include( CFS_DIR . '/templates/page-settings.php' );
    }


    /**
     * Register meta boxes
     */
    function add_meta_boxes() {

        add_meta_box( 'cfs_fields', __('Fields', 'cfs'), array( $this, 'meta_box' ), 'cfs', 'normal', 'high', array( 'box' => 'fields' ) );
        add_meta_box( 'cfs_rules', __('Placement Rules', 'cfs'), array( $this, 'meta_box' ), 'cfs', 'normal', 'high', array( 'box' => 'rules' ) );
        add_meta_box( 'cfs_extras', __('Extras', 'cfs'), array( $this, 'meta_box' ), 'cfs', 'normal', 'high', array( 'box' => 'extras' ) );
    }


    function meta_box( $post, $metabox ) {
        $box = $metabox['args']['box'];
        include( CFS_DIR . "/templates/meta_box_$box.php" );
    }
}

new cfs_edit_screen();
