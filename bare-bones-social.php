<?php
/**
 * Plugin Name: Bare Bones Social
 * Description: Open Graph tags, a follow section after posts, and a social icons widget. No share counts, no external assets, one options row.
 * Author: Charlton Digital
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) { exit; }

// Constants
define('BARE_BONES_SOCIAL_OPTION',  'bare_bones_social_settings');
define('BARE_BONES_SOCIAL_PATH',    plugin_dir_path(__FILE__));
define('BARE_BONES_SOCIAL_URL',     plugin_dir_url(__FILE__));
define('BARE_BONES_SOCIAL_VERSION', '0.1.0');
// Measured per release with the Plugin Size Meter tool. Update alongside VERSION.
define('BARE_BONES_SOCIAL_SIZE',    '24 KB');

// Load Core
require_once BARE_BONES_SOCIAL_PATH . 'includes/helpers.php';
require_once BARE_BONES_SOCIAL_PATH . 'includes/open-graph.php';
require_once BARE_BONES_SOCIAL_PATH . 'includes/share.php';
require_once BARE_BONES_SOCIAL_PATH . 'includes/follow.php';

// Load Admin
if (is_admin()) {
    require_once BARE_BONES_SOCIAL_PATH . 'admin/admin-settings.php';
}

// Menu Registration
add_action('admin_menu', function() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 169 204"><path fill="white" d="' . BARE_BONES_SOCIAL_SKULL . '"/></svg>';
    add_menu_page('Bare Bones Social', 'Bare Bones Social', 'manage_options', 'bare-bones-social', 'bare_bones_social_render_settings', 'data:image/svg+xml;base64,' . base64_encode($svg), 24.7);
});

add_action('admin_enqueue_scripts', function($hook) {
    if (strpos($hook, 'bare-bones-social') !== false) {
        wp_enqueue_style('bbsocial-css', BARE_BONES_SOCIAL_URL . 'assets/admin-style.css', array(), BARE_BONES_SOCIAL_VERSION);
    }
});
