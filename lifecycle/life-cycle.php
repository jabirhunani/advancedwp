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


add_action('wp_footer', function () {
    ?>
    <form method="post">
        <?php wp_nonce_field('save_name_action', 'save_name_nonce'); ?>
        <input type="text" name="username" placeholder="Enter name">
        <button type="submit">Save</button>
    </form>
    <?php
});

add_action('init', function () {

    if (
        isset($_POST['username']) &&
        isset($_POST['save_name_nonce']) &&
        wp_verify_nonce($_POST['save_name_nonce'], 'save_name_action')
    ) {
        $name = sanitize_text_field($_POST['username']);
        update_option('my_saved_name', $name);
    }
});
add_action('wp_footer', function () {
    $name = get_option('my_saved_name');
    if ($name) {
        echo '<p>Hello ' . esc_html($name) . '</p>';
    }
});

add_action('wp_footer', function () {

    if (isset($_GET['name'])) {
        $name = sanitize_text_field($_GET['name']);
        echo '<p>Welcome back ' . esc_html($name) . '</p>';
    } else {
        echo '<p>Welcome Guest</p>';
    }

});
