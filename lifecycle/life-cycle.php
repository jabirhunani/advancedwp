<?php
/**
 * Plugin Name: Lifecycle Test
 */

// add_action('plugins_loaded', function () {
//     error_log('🔥 plugins_loaded fired');
// });

// add_action('init', function () {
//     error_log('🔥 init fired');
// });

// add_action('wp_loaded', function () {
//     error_log('🔥 wp_loaded fired');
// });

// add_action('the_title', function ($title) {
//     return '🔥 ' . $title;
// });

// add_filter('the_title', function ($title) {
//     return '🔥 ' . $title;
// });

add_action('wp_footer', function () {
    ?>
    <form method="post">
        <input type="hidden" name="my_action" value="delete_something">
        <button type="submit">Delete Something</button>
    </form>
    <?php
});

add_action('init', function () {
    if (isset($_POST['my_action'])) {
        error_log('❌ Action triggered WITHOUT nonce');
    }
});
add_action('wp_footer', function () {
    echo wp_create_nonce('my_secure_action');
});
