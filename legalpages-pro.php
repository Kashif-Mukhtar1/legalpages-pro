<?php
/**
 * Plugin Name: LegalPages Pro
 * Plugin URI: https://kashifmukhtar.com/legalpages-pro-help/
 * Description: Ensure legal compliance for your website with the LegalPages Pro Plugin—effortlessly secured within seconds.
 * Version: 1.0
 * Author: Kashif Mukhtar
 * Author URI: https://kashifmukhtar.com
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.0
 * Stable tag: 1.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

// Enqueue cookie banner script and pass Cookies Policy link
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'legalpages-cookies-banner',
        plugin_dir_url(__FILE__) . 'assets/js/cookies-banner.js',
        [],
        false,
        true
    );

    // ✅ Replaced deprecated get_page_by_title with safe WP_Query alternative
    $cookies_query = new WP_Query([
        'post_type' => 'page',
        'post_status' => 'publish',
        'title' => 'Cookies Policy',
        'posts_per_page' => 1
    ]);
    $cookies_link = $cookies_query->have_posts() ? get_permalink($cookies_query->posts[0]->ID) : '';

    wp_localize_script('legalpages-cookies-banner', 'legalpages_vars', [
        'cookies_link' => $cookies_link
    ]);
});

// Add admin menu with shield icon
add_action('admin_menu', function() {
    add_menu_page(
        'LegalPages Pro',
        'LegalPages Pro',
        'manage_options',
        'legalpages-pro',
        'legalpages_pro_admin',
        'dashicons-shield',
        80
    );
});

// Admin Footer Credit
add_filter('admin_footer_text', function($text) {
    return 'Developed with ❤️ by <a href="https://kashifmukhtar.com/" target="_blank">Kashif Mukhtar</a>';
});

// Admin Panel UI
function legalpages_pro_admin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['legalpages_save_settings']) && check_admin_referer('legalpages_settings_action', 'legalpages_settings_nonce')) {
        update_option('legalpages_website', sanitize_text_field($_POST['legalpages_website']));
        update_option('legalpages_email', sanitize_email($_POST['legalpages_email']));
        update_option('legalpages_phone', sanitize_text_field($_POST['legalpages_phone']));
    }

    echo '<div class="wrap legalpages-container">';
    echo '<h1>🛡️ LegalPages Pro— Get legally protected website</h1>';

    echo '<form method="post" action="">';
    echo '<h2>🛠️ Step 1: Fill the fields & Press Save Button</h2>';
    echo '<div class="legalpages-settings-grid" style="align-items: end;">';

    wp_nonce_field('legalpages_settings_action', 'legalpages_settings_nonce');

    echo '<p><label><strong>Domain:</strong> <input type="text" name="legalpages_website" value="' . esc_attr(get_option('legalpages_website', get_bloginfo('url'))) . '"></label></p>';
    echo '<p><label><strong>Phone:</strong> <input type="text" name="legalpages_phone" value="' . esc_attr(get_option('legalpages_phone', '+1234567890')) . '"></label></p>';
    echo '<p><label><strong>Email:</strong> <input type="email" name="legalpages_email" value="' . esc_attr(get_option('legalpages_email', get_option('admin_email'))) . '"></label></p>';

    echo '<p style="margin-top: 110;"><input type="submit" name="legalpages_save_settings" class="button-primary" value="📀 Save Info To Add in My Legal Pages"></p>';
    echo '</div>';
    echo '</form><hr>';

    echo '<form method="post" action="">';
    echo '<h2>📄 Step 2: Select Required Pages and Press Generate Pages Button</h2>';
    echo '<input type="hidden" name="legalpages_generate_submit" value="1">';

    $pages = [
        'privacy_policy'   => '🔐 Privacy Policy',
        'terms_conditions' => '📜 Terms & Conditions',
        'disclaimer'       => '⚠️ Disclaimer',
        'cookies_policy'   => '🍪 Cookies Policy',
        'gdpr'             => '🇪🇺 GDPR Compliance',
        'refund_policy'    => '💸 Refund Policy',
        'dmca'             => '🛡️ DMCA Compliance',
    ];

    echo '<div class="legalpages-grid-cards">';
    foreach ($pages as $slug => $label) {
        echo '<label class="legal-card-option">';
        echo '<input type="checkbox" name="legal_pages[]" value="' . esc_attr($slug) . '">';
        echo '<span>' . esc_html($label) . '</span>';
        echo '</label>';
    }
    echo '</div>';

    echo '<div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-top: 30px;">';
    echo '<button type="submit" class="button button-primary" style="font-size: 16px; padding: 12px 30px; background-color: #047f28; border-color: #047f28; border-radius: 10px; transition: transform 0.3s ease, box-shadow 0.3s ease;">';
    echo '📄 Generate My Selected Legal Pages';
    echo '</button>';
    echo '<a href="https://kashifmukhtar.com/legalpages-pro-help" target="_blank" rel="noopener noreferrer">';
    echo '<img src="' . esc_url(plugin_dir_url(__FILE__) . 'assets/banner-admin.png') . '" alt="LegalPages Pro Help Banner" style="max-height: 60px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />';
    echo '</a>';
    echo '</div>';

    echo '</form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['legal_pages']) && is_array($_POST['legal_pages'])) {
        $selected = array_map('sanitize_text_field', $_POST['legal_pages']);
        $placeholders = [
            '{{site_name}}' => get_bloginfo('name'),
            '{{website}}' => get_option('legalpages_website'),
            '{{email}}' => get_option('legalpages_email'),
            '{{phone}}' => get_option('legalpages_phone'),
            '{{country}}' => get_option('legalpages_country'),
            '{{today}}' => date('F j, Y')
        ];

        echo '<div class="notice notice-info"><ul>';
        $page_titles = [
            'privacy_policy'   => 'Privacy Policy',
            'terms_conditions' => 'Terms & Conditions',
            'disclaimer'       => 'Disclaimer',
            'cookies_policy'   => 'Cookies Policy',
            'gdpr'             => 'GDPR Compliance',
            'refund_policy'    => 'Refund Policy',
            'dmca'             => 'DMCA Compliance'
        ];

        foreach ($selected as $page_key) {
            $template_file = plugin_dir_path(__FILE__) . 'templates/' . $page_key . '.html';
            $title = $page_titles[$page_key] ?? ucwords(str_replace(['_', '&'], [' ', 'and'], $page_key));

            if (file_exists($template_file)) {
                $content = file_get_contents($template_file);
                $content = strtr($content, $placeholders);

                $query = new WP_Query([
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'title' => $title,
                    'posts_per_page' => 1
                ]);

                $existing = !empty($query->posts) ? $query->posts[0] : null;

                if ($existing) {
                    wp_update_post(['ID' => $existing->ID, 'post_content' => $content]);
                    echo '<li>🔄 Updated: <strong>' . esc_html($title) . '</strong> (Page ID: ' . esc_html($existing->ID) . ')</li>';
                } else {
                    $inserted_id = wp_insert_post([
                        'post_title' => $title,
                        'post_content' => $content,
                        'post_status' => 'publish',
                        'post_type' => 'page'
                    ]);
                    echo '<li>✅ Created: <strong>' . esc_html($title) . '</strong> (Page ID: ' . esc_html($inserted_id) . ')</li>';
                }
            } else {
                echo '<li>❌ Missing template for <strong>' . esc_html($title) . '</strong></li>';
            }
        }
        echo '</ul></div>';
    }

    echo '</div>';
}

// Admin CSS
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_legalpages-pro') return;
    wp_enqueue_style('legalpages-admin-style', plugin_dir_url(__FILE__) . 'admin/style.css', [], '1.0');
});

// Shortcode to show legal pages
add_filter('the_content', function($content) {
    global $post;

    if (is_admin() || !is_object($post)) return $content;

    return do_shortcode($content);
}, 99);


