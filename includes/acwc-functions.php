<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once plugin_dir_path(__FILE__) . 'hooks.php';

// Added the menu item to the admin menu
function acwc_add_menu() {
    add_menu_page(
        'Appify Chat',
        'Appify Chat',
        'manage_options',
        'appifychat-settings',
        'acwc_settings_page',
        'dashicons-whatsapp'
    );
}
add_action('admin_menu', 'acwc_add_menu');

function acwc_enqueue_frontend_script() {

    // Fetched data from the database for displaying in the form
    $settings = acwc_get_record();

    // Convert the settings data to JSON
    $acw_settings_json = wp_json_encode($settings);

    // Enqueue your plugin's custom JS
    $script_url = plugin_dir_url(__FILE__) . 'assets/acw-script.js';
    wp_enqueue_script('acw-script', $script_url, array(), '1.0', true);

    // Add the settings data as inline script
    $inline_script = 'var acw_settings = ' . $acw_settings_json . '; var acw_site_url = "' . esc_url(site_url()) . '";';
    wp_add_inline_script('acw-script', $inline_script, 'before');
    // wp_localize_script('acw-script', 'acw_settings', $acw_settings_json);

}
add_action('wp_enqueue_scripts', 'acwc_enqueue_frontend_script');

// Enqueue Bootstrap and custom styles in the admin area
function acwc_plugin_enqueue_admin($hook) {
    // Load only on your plugin's admin pages
    if ($hook != 'toplevel_page_appifychat-settings') {
        return;
    }

    // Define the version
    $version = '1.0.0'; // You can update this to match your plugin's version

    // Enqueue Bootstrap CSS & JavaScript
    wp_enqueue_style('bootstrap-css', plugin_dir_url(__FILE__) . 'assets/bootstrap.min.css', array(), "5.2.3");
    wp_enqueue_script('bootstrap-js', plugin_dir_url(__FILE__) . 'assets/bootstrap.bundle.min.js', array(), "5.3.3", true);

    // Enqueue your plugin's custom CSS & JavaScript
    wp_enqueue_style('acw-css', plugin_dir_url(__FILE__) . 'assets/style.css', array(), $version);
    wp_enqueue_script('acw-script', plugin_dir_url(__FILE__) . 'assets/acw-script.js', array(), $version, true);

    // Add the settings data as inline script
    $inline_script = 'var acw_site_url = "' . esc_url(site_url()) . '";';
    wp_add_inline_script('acw-script', $inline_script, 'before');

    wp_enqueue_media();
    wp_enqueue_script('acw-script-admin', plugin_dir_url(__FILE__) . 'assets/acw-script-admin.js', array(), $version, true);

    // // // Add the settings data as inline script
    // $admin_inline_script = 'setTimeout(loadData, 2000)';
    // wp_add_inline_script('acw-script-admin', $admin_inline_script, 'after');

}
add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
add_action('admin_enqueue_scripts', 'acwc_plugin_enqueue_admin');

function acwc_settings_page() {
    global $wp_filesystem;

    // Initialize the WP_Filesystem
    if (!function_exists('WP_Filesystem')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    WP_Filesystem();

    // Check if the form is submitted and nonce is set
    if (isset($_POST['submit']) && isset($_POST['acw_settings_nonce'])) {
        // Verify the nonce
        if (wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['acw_settings_nonce'])), 'acw_settings_action')) {
            // Nonce is valid, proceed with processing form data
            acwc_update_record();
        } else {
            // Nonce is invalid, display an error message or take appropriate action
            echo esc_html('Nonce verification failed. Form submission rejected.');
            return;
        }
    }

    // Path to your HTML file
    $html_file_path = plugin_dir_path(__FILE__) . 'assets/settings.html';

    // Get the settings record
    $settings = acwc_get_record();

    // Ensure settings are not null
    if (!$settings) {
        echo esc_html('Error: Settings record not found.');
        return;
    }

    // Ensure the HTML file exists
    if (!$wp_filesystem->exists($html_file_path)) {
        echo esc_html('Error: HTML file not found.');
        return;
    }

    // Read the HTML file content
    $html_content = $wp_filesystem->get_contents($html_file_path);

    // Use the function to generate and store the unique ID
    $plugin_unique_id = acwc_store_unique_id();

    // Prepare replacements
    $replacements = array(
        '{{id}}' => esc_html($settings->id),
        '{{number}}' => esc_html($settings->number),
        '{{message}}' => esc_html($settings->message),
        '{{position}}' => esc_html($settings->position),
        '{{hSpace}}' => esc_html($settings->horizontal_space),
        '{{vSpace}}' => esc_html($settings->vertical_space),
        '{{cornerRadius}}' => esc_html($settings->radius),
        '{{iconPath}}' => esc_url($settings->icon),
        '{{mobileLink}}' => esc_url($settings->mobile_link),
        '{{boxTbPadding}}' => esc_html($settings->box_tb_padding),
        '{{boxLrPadding}}' => esc_html($settings->box_lr_padding),
        '{{boxRadius}}' => esc_html($settings->box_radius),
        '{{bgColor}}' => esc_html($settings->box_bgcolor),
        '{{fontColor}}' => esc_html($settings->box_font_color),
        '{{shadowColor}}' => esc_html($settings->box_shadow_color),
        // '{{previewScript}}' => esc_url(plugin_dir_url(__FILE__) . 'assets/acw-script.js'),
        '{{pluginId}}' => esc_html($plugin_unique_id["id"]),
        '{{wpSettingsNonce}}' => esc_attr(wp_create_nonce('acw_settings_action')),
        // '{{siteUrl}}' => esc_html(site_url()),
    );

    // Replace placeholders with actual values
    $html_content = str_replace(array_keys($replacements), array_values($replacements), $html_content);
    // echo str_replace(array_map('esc_html', array_keys($replacements)), array_map('esc_html', array_values($replacements)), $html_content);
    // echo str_replace(array_keys($replacements), array_map('esc_html', array_values($replacements)), $html_content);
    // Output the HTML content
    // echo wp_kses_post($html_content);

    echo wp_kses(
        $html_content,
        array(
            'div' => array(
                'id' => array(),
                'class' => array(),
                'style' => array(),
                'role' => array(),
                'aria-live' => array(),
                'aria-atomic' => array(),
            ),
            'form' => array(
                'method' => array(),
                'id' => array(),
                'action' => array(),
                'enctype' => array(),
                'class' => array(),
            ),
            'span' => array(
                'for' => array(),
                'class' => array(),
                'style' => array(),
            ),
            'button' => array(
                'type' => array(),
                'class' => array(),
                'onclick' => array(),
                'name' => array(), 'id' => array(),

            ),
            'input' => array(
                'type' => array(),
                'class' => array(),
                'id' => array(),
                'name' => array(),
                'value' => array(),
                'placeholder' => array(),
                'min' => array(),
                'max' => array(),
                'step' => array(),
                'oninput' => array(),
                'required' => array(),
                'checked' => array(),
                'title' => array(),
            ),
            'label' => array(
                'for' => array(),
                'class' => array(),
                'id' => array(),
            ),
            'select' => array(
                'name' => array(),
                'class' => array(),
                'id' => array(),
                'onchange' => array(),
            ),
            'option' => array(
                'value' => array(),
            ),
            'textarea' => array(
                'name' => array(),
                'id' => array(),
                'class' => array(),
                'placeholder' => array(),
                'required' => array(),
                'value' => array(), 'oninput' => array(),

            ),
            'blockquote' => array(
                'class' => array(),
            ),
            'figcaption' => array(
                'class' => array(),
            ),
            'figure' => array(
                'class' => array(),
            ),
            'cite' => array(
                'title' => array(),
            ),
            'h3' => array(
                'class' => array(),
            ),
            'kbd' => array(
                'class' => array(),
            ),
            'img' => array(
                'id' => array(),
                'src' => array(),
                'alt' => array(),
                'class' => array(),
                'style' => array(),
                'onchange' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
            'em' => array(),
            'strong' => array(),
            'br' => array(),
            'a' => array(
                'href' => array(),
                'title' => array(),
            ),
        )
    );

}