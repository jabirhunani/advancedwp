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

add_action('init', function () {

    if (isset($_GET['do_admin_thing'])) {

        if (! current_user_can('manage_options')) {
            wp_die('❌ You are not allowed to do this');
        }

        echo '✅ Admin-only action executed';
        exit;
    }

});

add_action('rest_api_init', function () {

    register_rest_route('life-cycle/v1', '/hello', [
        'methods'  => 'GET',
        'callback' => function () {
            return [
                'message' => 'Hello from REST API 👋'
            ];
        },
  

        'permission_callback' => function () {
            return current_user_can('manage_options');
        }

    ]);

});

add_action('init', function () {

    register_post_type('movie', [
        'labels' => [
            'name' => 'Movies',
            'singular_name' => 'Movie'
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-video-alt'
    ]);

});


add_action('init', function () {
    if (isset($_GET['movie_id']) && isset($_GET['rating'])) {

        $movie_id = intval($_GET['movie_id']);
        $rating = sanitize_text_field($_GET['rating']);
        $director = sanitize_text_field($_GET['director']);
        $price = intval($_GET['price']);

        update_post_meta($movie_id, 'rating', $rating);
        update_post_meta($movie_id, 'director', $director);
        update_post_meta($movie_id, 'price', $price);
    }
});
add_action('wp_footer', function () {

    if (is_singular('movie')) {
        $rating = get_post_meta(get_the_ID(), 'rating', true);
        $director = get_post_meta(get_the_ID(), 'director', true);
        $price = get_post_meta(get_the_ID(), 'price', true);

        echo '<p>Rating: ' . esc_html($rating) . '</p>';
        echo '<p>Director: ' . esc_html($director) . '</p>';
        echo '<p>Price: ' . esc_html($price) . '</p>';
    }

});


add_action('admin_menu', function () {
    add_menu_page(
        'Movie Settings',
        'Movie Settings',
        'manage_options',
        'movie-settings',
        'movie_settings_page_html',
    );
});

function movie_settings_page_html() { ?>
    <div class="wrap">
        <h1>Movie Settings</h1>
        <form method="post">
            <?php
            wp_nonce_field('save_movie_settings');
            $price = get_option('movie_default_price', '');?>
            <label for="default_ticket_price">Default Ticket Price: </label><br>
            <input type="text" name="default_ticket_price" id="default_ticket_price" value="<?php echo esc_attr($price); ?>">
            <br><br>

            <input type="submit" name="save_settings" value="Save">
        </form>
    </div>
<?php    
}

add_action('admin_init', function() {
    if(isset($_POST['save_settings']) && isset($_POST['default_ticket_price']) &&  wp_verify_nonce($_POST['_wpnonce'], 'save_movie_settings')){
        if(!current_user_can('manage_options')){
            return;
        }
        $price = sanitize_text_field($_POST['default_ticket_price']);
        update_option('movie_default_price', $price);
    }
});