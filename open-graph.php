<?php
/**
 * Open Graph and Twitter Card tags.
 *
 * Bare Bones SEO owns <title>, the meta description and schema. It writes no
 * Open Graph at all, so the two plugins never collide. Where it has a saved
 * title or description for the post we reuse them, so the social card and the
 * search snippet always agree.
 */

if (!defined('ABSPATH')) { exit; }

add_action('wp_head', 'bare_bones_social_output_open_graph', 2);
function bare_bones_social_output_open_graph() {
    if (!is_singular() || is_feed()) {
        return;
    }

    // Stand down if a full SEO suite is already writing these tags.
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('SEOPRESS_VERSION') || defined('AIOSEO_VERSION')) {
        return;
    }

    $post_id = get_queried_object_id();
    if (!$post_id) {
        return;
    }

    $title = bare_bones_social_og_title($post_id);
    $desc  = bare_bones_social_og_description($post_id);
    $image = bare_bones_social_og_image($post_id);

    echo "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '">' . "\n";

    if ($desc) {
        echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    }

    if ($image) {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    } else {
        echo '<meta name="twitter:card" content="summary">' . "\n";
    }

    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";

    if ($desc) {
        echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    }
}

/**
 * Bare Bones SEO's custom title if there is one, otherwise the post title.
 */
function bare_bones_social_og_title($post_id) {
    if (defined('BARE_BONES_SEO_META_TITLE')) {
        $custom = get_post_meta($post_id, BARE_BONES_SEO_META_TITLE, true);
        if (is_string($custom) && trim($custom) !== '') {
            return $custom;
        }
    }

    return get_the_title($post_id);
}

/**
 * Bare Bones SEO's description, then the excerpt, then a trimmed content string.
 */
function bare_bones_social_og_description($post_id) {
    if (defined('BARE_BONES_SEO_META_DESC')) {
        $custom = get_post_meta($post_id, BARE_BONES_SEO_META_DESC, true);
        if (is_string($custom) && trim($custom) !== '') {
            return $custom;
        }
    }

    $post = get_post($post_id);
    if (!$post) {
        return '';
    }

    $text = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
    $text = wp_strip_all_tags(strip_shortcodes($text));

    return wp_trim_words($text, 30, '');
}

/**
 * Image fallback: featured image, then the first image in the content, then
 * the global default from settings.
 */
function bare_bones_social_og_image($post_id) {
    if (has_post_thumbnail($post_id)) {
        $url = get_the_post_thumbnail_url($post_id, 'large');
        if ($url) {
            return $url;
        }
    }

    $post = get_post($post_id);
    if ($post && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->post_content, $match)) {
        return $match[1];
    }

    return bare_bones_social_get('default_image', '');
}
