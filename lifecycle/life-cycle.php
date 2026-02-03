<?php
/**
 * Plugin Name: Lifecycle Test
 */

add_action('plugins_loaded', function () {
    error_log('🔥 plugins_loaded fired');
});

add_action('init', function () {
    error_log('🔥 init fired');
});

add_action('wp_loaded', function () {
    error_log('🔥 wp_loaded fired');
});

add_action('the_title', function ($title) {
    return '🔥 ' . $title;
});

// add_filter('the_title', function ($title) {
//     return '🔥 ' . $title;
// });
