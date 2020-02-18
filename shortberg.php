<?php
/**
 * @package Shortberg
 * @version 1.0.0
 */
/*
Plugin Name: Shortberg
Plugin URI: https://github.com/maxjf1/
Description: Add custom shortcode as WordPress Guttenberg Blocks
Author: Maxwell Souza
Version: 1.0.0
Author URI: https://github.com/maxjf1/
*/

// If imported, manually set constant before the import
defined('SHORTBERG_BASE_URL') or define( 'SHORTBERG_BASE_URL', plugin_dir_url( __FILE__ ) );

global $shorberg_blocks;
$shorberg_blocks = array();

function shortberg_add_block($name, $settings = array()) {
    $settings['name'] = $name;
    if ($settings['richText'])
        $settings['attributes']['richTextContent'] = array(
            'source'   => 'html',
            'selector' => (is_array( $settings['richText'] ) && isset( $settings['richText']['foo'] ))
                ? $settings['richText']['tagName'] : 'p',
        );
    global $shorberg_blocks;
    array_push( $shorberg_blocks, $settings );
}


function shortberg_init() {
    wp_register_script(
        'shortberg-blocks-loader',
        SHORTBERG_BASE_URL . 'js/blocks-loader.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-server-side-render', 'wp-components', 'react'), '1.0.0'
    );

    do_action( 'shortberg_register_blocks' );

    global $shorberg_blocks;

    foreach ($shorberg_blocks as $block) {
        register_block_type( $block['name'], array(
            'render_callback' => $block['shortcode_callback'],
            'attributes'      => $block['attributes'],
            'editor_style'    => $block['editor_style'],
            'style'           => $block['style'],
        ) );
    }


}

add_action( 'init', 'shortberg_init', PHP_INT_MAX );


function shortberg_editor_assets() {
    global $shorberg_blocks;
    $shorberg_blocks = apply_filters( 'shortberg_before_render_blocks', $shorberg_blocks );
    wp_localize_script( 'shortberg-blocks-loader', '__shortberg_args__', array(
        'blocks' => $shorberg_blocks
    ) );
    wp_enqueue_script( 'shortberg-blocks-loader' );
}

add_action( 'enqueue_block_editor_assets', 'shortberg_editor_assets' );