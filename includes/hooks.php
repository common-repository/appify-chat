<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
function acwc_create_table() {
    global $wpdb;

    // Define table name with WordPress prefix
    $table_name = $wpdb->prefix . 'acwc';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        number VARCHAR(15) NOT NULL,
        message VARCHAR(500) NOT NULL,
        position VARCHAR(255) NOT NULL,
        horizontal_space INT NOT NULL,
        vertical_space INT NOT NULL,
        radius INT NOT NULL,
        icon VARCHAR(255) NOT NULL,
        mobile_link VARCHAR(255) NOT NULL,
        box_tb_padding INT NOT NULL,
        box_lr_padding INT NOT NULL,
        box_radius INT NOT NULL,
        box_bgcolor VARCHAR(7) NOT NULL,
        box_font_color VARCHAR(7) NOT NULL,
        box_shadow_color VARCHAR(7) NOT NULL
    ) $charset_collate;";

    // Include upgrade functions for dbDelta
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // Execute the query to create the table
    dbDelta($sql);

    // Check for any errors in table creation
    if ($wpdb->last_error) {
        error_log('Database table creation error: ' . $wpdb->last_error);
        return;
    }
}

function acwc_insert_record() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acwc';

    $data = array(
        'number' => sanitize_text_field('+1 9999 999 999'),
        'message' => sanitize_text_field('Hey, there could you please assist me?'),
        'position' => sanitize_text_field('bottom_right'),
        'horizontal_space' => sanitize_text_field(10),
        'vertical_space' => sanitize_text_field(50),
        'radius' => sanitize_text_field(100),
        'icon' => sanitize_text_field(''),
        'mobile_link' => sanitize_text_field('whatsapp://'),
        'box_tb_padding' => sanitize_text_field(10),
        'box_lr_padding' => sanitize_text_field(16),
        'box_radius' => sanitize_text_field(6),
        'box_bgcolor' => sanitize_text_field('#f2f2f2'),
        'box_font_color' => sanitize_text_field('#000000'),
        'box_shadow_color' => sanitize_text_field('#aaaaaa'),
    );

    $result = $wpdb->insert($table_name, $data);

    // Check for any errors in table insertion 2
    if ($result === false) {
        // Log the error for debugging purposes
        error_log('Database insert error: ' . $wpdb->last_error);
    } else {
        // Log success message
        error_log('Record inserted successfully');
    }
}

function acwc_toast_show($message) {
// Add inline script to show the toast message
    $inline_script = "setTimeout(() => handleToast(true, '" . esc_js($message) . "'), 100);";
    wp_add_inline_script('acw-script-admin', $inline_script);
    // echo "<script>setTimeout(() => handleToast(true, '" . esc_js($message) . "'), 100);</script>";
}
add_action('acwc_toast_show', 'acwc_toast_show');

function acwc_update_record() {
    if (isset($_POST['acw_settings_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['acw_settings_nonce'])), 'acw_settings_action')) {

        global $wpdb;
        $table_name = $wpdb->prefix . 'acwc';

        $id = isset($_POST['id']) ? intval(wp_unslash($_POST['id'])) : 0;
        $number = isset($_POST['acw-number']) ? sanitize_text_field(wp_unslash($_POST['acw-number'])) : '';
        $message = isset($_POST['acw-message']) ? sanitize_text_field(wp_unslash($_POST['acw-message'])) : '';
        $box_lr_padding = isset($_POST['acw-msg-box-lr-padding']) ? sanitize_text_field(wp_unslash($_POST['acw-msg-box-lr-padding'])) : '';
        $box_shadow_color = isset($_POST['acw-msg-box-shadow-color']) ? sanitize_hex_color(wp_unslash($_POST['acw-msg-box-shadow-color'])) : '';
        $box_font_color = isset($_POST['acw-msg-box-font-color']) ? sanitize_hex_color(wp_unslash($_POST['acw-msg-box-font-color'])) : '';
        $vertical_space = isset($_POST['acw-vs']) ? sanitize_text_field(wp_unslash($_POST['acw-vs'])) : '';
        $horizontal_space = isset($_POST['acw-hs']) ? sanitize_text_field(wp_unslash($_POST['acw-hs'])) : '';
        $radius = isset($_POST['acw-corner-radius']) ? sanitize_text_field(wp_unslash($_POST['acw-corner-radius'])) : '';
        $position = isset($_POST['acw-position']) ? sanitize_text_field(wp_unslash($_POST['acw-position'])) : '';
        $icon = isset($_POST['acw-icon']) ? sanitize_text_field(wp_unslash($_POST['acw-icon'])) : '';
        $mobile_link = isset($_POST['acw-mobile-link']) ? esc_url_raw(wp_unslash($_POST['acw-mobile-link'])) : '';
        $box_tb_padding = isset($_POST['acw-msg-box-tb-padding']) ? sanitize_text_field(wp_unslash($_POST['acw-msg-box-tb-padding'])) : '';
        $box_radius = isset($_POST['acw-msg-box-radius']) ? sanitize_text_field(wp_unslash($_POST['acw-msg-box-radius'])) : '';
        $box_bgcolor = isset($_POST['acw-msg-box-bgcolor']) ? sanitize_hex_color(wp_unslash($_POST['acw-msg-box-bgcolor'])) : '';

        $data = [
            "number" => $number,
            "message" => $message,
            "position" => $position,
            "horizontal_space" => $horizontal_space,
            "vertical_space" => $vertical_space,
            "radius" => $radius,
            "icon" => $icon,
            "mobile_link" => $mobile_link,
            "box_tb_padding" => $box_tb_padding,
            "box_lr_padding" => $box_lr_padding,
            "box_radius" => $box_radius,
            "box_bgcolor" => $box_bgcolor,
            "box_font_color" => $box_font_color,
            "box_shadow_color" => $box_shadow_color,
        ];

        $where = array('id' => $id);
        $result = $wpdb->update($table_name, $data, $where);

        // Check if the update was successful
        if ($result === false) {
            error_log('Error: ' . $wpdb->last_error);
            do_action('acwc_toast_show', esc_js($wpdb->last_error));
            return;
        }

        // Clear the cache if needed
        wp_cache_delete('acwc_record');
        do_action('acwc_toast_show', esc_js("Settings saved successfully."));
    } else {
        // Nonce verification failed, handle the error or exit gracefully
        echo esc_html('Nonce verification failed. Form submission rejected.');
    }
}

function acwc_get_record() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acwc';

    // Attempt to retrieve the data from cache
    $cached_result = wp_cache_get('acwc_record', 'acwc_plugin');

    if (false === $cached_result) {
        // Data not found in cache, retrieve from database
        // $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %s LIMIT %d", $table_name, 1));
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}acwc LIMIT 1", OBJECT);

        // Cache the result for future use
        wp_cache_set('acwc_record', $result, 'acwc_plugin');
    } else {
        // Data found in cache, use cached result
        $result = $cached_result;
    }

    if ($result) {
        // Returning the first row of results as an object
        return $result;
    } else {
        // Return an object with default empty values if no result is found
        return (object) [
            'number' => '',
            'message' => '',
            'position' => '',
            'horizontal_space' => '',
            'vertical_space' => '',
            'corner_radius' => '',
            'icon' => '',
            'link' => '',
            'tp_btm_padding' => '',
            'lft_rgt_padding' => '',
            'border_radius' => '',
            'bg_color' => '',
            'font_color' => '',
            'shadow_color' => '',
        ];
    }
}

function acwc_store_unique_id($status = true) {
    // Check if the unique ID already exists
    $acwc_unique_id_option = 'acwc_id';
    $acwc_id = get_option($acwc_unique_id_option);
    $response_message = "Record already exists";

    // Make an API call to your server to store the unique ID
    // $site_name = get_bloginfo('name');
    // $site_url = home_url('/');
    // $admin_email = get_option('admin_email');
    // $plugin_status = true;

    // $shop_details = array(
    //     'id' => $unique_id,
    //     'status' => $plugin_status,
    //     'site_name' => $site_name,
    //     'site_url' => $site_url,
    //     'admin_email' => $admin_email,
    // );

    // $response = wp_remote_post('https://whatsappify-wp-api.kodand-info5185.workers.dev/', array(
    //     'method' => 'POST',
    //     'body' => json_encode($shop_details),
    //     'headers' => array('Content-Type' => 'application/json'),
    // ));

    // $response_message = json_encode($response["body"]);

    // if (is_wp_error($response)) {
    //     // Handle the error
    //     error_log('Error storing unique ID: ' . $response->get_error_message());
    //     $response_message = $response->get_error_message();
    //     return ["id" => $unique_id, "message" => $response_message];
    // }

    // If the unique ID does not exist, generate and store a new one
    if (!$acwc_id) {
        // Generate a new UUID
        $acwc_id = uniqid('acwc_', true) . '_' . wp_generate_uuid4();
        $shop_details['id'] = $acwc_id;

        // Store the unique ID in the WordPress options table
        add_option($acwc_unique_id_option, $acwc_id);
    }

    // Make an API call to your server to store the unique ID
    $site_name = get_bloginfo('name');
    $site_url = home_url('/');
    $admin_email = get_option('admin_email');

    $shop_details = array(
        'id' => $acwc_id,
        'status' => $status,
        'site_name' => $site_name,
        'site_url' => $site_url,
        'admin_email' => $admin_email,
        'updated_at' => gmdate("D M j, Y G:i:s T"),
    );

    $response = wp_remote_post('https://whatsappify-wp.appifycommerce.com/', array(
        'method' => 'POST',
        'body' => wp_json_encode($shop_details),
        'headers' => array('Content-Type' => 'application/json'),
    ));

    $response_message = wp_json_encode($response["body"]);

    if (is_wp_error($response)) {
        // Handle the error
        error_log('Error storing unique ID: ' . $response->get_error_message());
        $response_message = $response->get_error_message();
        return ["id" => $acwc_id, "message" => $response_message];
    }

    return ["id" => $acwc_id, "message" => $response_message];
}