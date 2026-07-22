<?php
/**
 * Shared helpers: settings access, the icon library, and the icon renderer.
 */

if (!defined('ABSPATH')) { exit; }

// The family mark, shared with Bare Bones SEO.
define('BARE_BONES_SOCIAL_SKULL', 'M55 204H33V180H55V204ZM94 204H72V180H94V204ZM134 204H112V180H134V204ZM84.5 0C131.168 0 169 38.9512 169 87C169 116.014 155.205 141.709 134 157.516V174H32V155.173C12.5036 139.236 0 114.622 0 87C0 38.9512 37.8319 0 84.5 0ZM84.5 117C73 117 72.5 137.5 73.5 141C74.5001 144.5 94.9999 144.5 95 141C95 137.5 96 117 84.5 117ZM46.5 62C34.0736 62 24 72.0736 24 84.5C24 96.9264 34.0736 107 46.5 107C58.9264 107 69 96.9264 69 84.5C69 72.0736 58.9264 62 46.5 62ZM120.5 62C108.074 62 98 72.0736 98 84.5C98 96.9264 108.074 107 120.5 107C132.926 107 143 96.9264 143 84.5C143 72.0736 132.926 62 120.5 62Z');

/**
 * All settings, merged over defaults.
 *
 * Static cache because this is called by the head tags, the post footer, and
 * the widget on the same request.
 */
function bare_bones_social_get_settings() {
    static $settings = null;

    if ($settings === null) {
        $saved = get_option(BARE_BONES_SOCIAL_OPTION, array());
        $settings = wp_parse_args(is_array($saved) ? $saved : array(), array(
            'profiles'       => array(),  // slug => URL
            'default_image'  => '',
            'show_share'     => 1,
            'share_label'    => 'Share on',
            'share_networks' => array('linkedin', 'x', 'facebook', 'email', 'copy'),
            'show_follow'    => 0,
            'follow_title'   => 'Follow us',
        ));
    }

    return $settings;
}

/**
 * A single setting.
 */
function bare_bones_social_get($key, $fallback = '') {
    $settings = bare_bones_social_get_settings();
    return isset($settings[$key]) ? $settings[$key] : $fallback;
}

/**
 * The icon library.
 *
 * Paths are stored bare and wrapped at render time, all normalised to a 24x24
 * grid. Order here is render order.
 */
function bare_bones_social_icons() {
    return array(
        'facebook'  => array('Facebook',  'M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z'),
        'instagram' => array('Instagram', 'M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.9 5.9 0 0 0-2.13 1.38A5.9 5.9 0 0 0 .63 4.14c-.3.76-.5 1.64-.56 2.91C.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.38 2.13a5.9 5.9 0 0 0 2.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.9 5.9 0 0 0 2.13-1.38 5.9 5.9 0 0 0 1.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.9 5.9 0 0 0-1.38-2.13A5.9 5.9 0 0 0 19.86.63c-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0zm0 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07zm0 3.68a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32zm0 10.16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm7.85-10.4a1.44 1.44 0 1 1-2.88 0 1.44 1.44 0 0 1 2.88 0z'),
        'x'         => array('X',         'M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.4l-5.8-7.58-6.64 7.58H.48l8.6-9.83L0 1.15h7.59l5.24 6.93zm-1.29 19.5h2.04L6.49 3.24H4.3z'),
        'youtube'   => array('YouTube',   'M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.08 0 12 0 12s0 3.92.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.92 24 12 24 12s0-3.92-.5-5.81zM9.55 15.57V8.43L15.82 12z'),
        'linkedin'  => array('LinkedIn',  'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05a3.74 3.74 0 0 1 3.37-1.85c3.6 0 4.27 2.37 4.27 5.46zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zm1.78 13.02H3.55V9h3.57zM22.22 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.72V1.72C24 .77 23.2 0 22.22 0z'),
        'tiktok'    => array('TikTok',    'M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94a6.6 6.6 0 0 1-4.9 2.84c-1.85.11-3.71-.39-5.24-1.42a6.85 6.85 0 0 1-2.9-4.85c-.02-.42-.03-.85-.01-1.27a6.84 6.84 0 0 1 2.4-4.5 6.7 6.7 0 0 1 5.6-1.36c.02 1.48-.04 2.96-.04 4.44a3.06 3.06 0 0 0-3.9 1.89c-.15.4-.19.87-.09 1.3a3.02 3.02 0 0 0 3.1 2.55 2.98 2.98 0 0 0 2.46-1.43c.24-.42.51-.85.53-1.35.12-2.29.07-4.57.09-6.86.01-5.15-.01-10.29.02-15.43z'),
        'pinterest' => array('Pinterest', 'M12 0a12 12 0 0 0-4.37 23.17c-.06-.94-.01-2.07.23-3.09.26-1.1 1.75-7.42 1.75-7.42s-.43-.87-.43-2.15c0-2.02 1.17-3.53 2.63-3.53 1.24 0 1.84.93 1.84 2.05 0 1.25-.8 3.12-1.21 4.85-.34 1.45.73 2.64 2.16 2.64 2.6 0 4.35-3.34 4.35-7.29 0-3-2.02-5.25-5.7-5.7-4.16-.06-6.75 3.1-6.75 6.31 0 1.15.34 1.96.87 2.58.24.29.28.4.19.73-.06.24-.21.83-.27 1.06-.09.34-.36.46-.66.33-1.85-.75-2.71-2.78-2.71-5.05 0-3.76 3.17-8.26 9.45-8.26 5.05 0 8.37 3.65 8.37 7.57 0 5.19-2.88 9.06-7.13 9.06-1.43 0-2.77-.77-3.23-1.65l-.88 3.49c-.32 1.14-.94 2.29-1.51 3.19A12 12 0 1 0 12 0z'),
        'threads'   => array('Threads',   'M16.9 11.12a8.4 8.4 0 0 0-.32-.15c-.19-3.48-2.09-5.47-5.28-5.49h-.04c-1.9 0-3.49.81-4.46 2.29l1.75 1.2c.73-1.11 1.87-1.34 2.71-1.34h.03c1.05 0 1.84.3 2.35.9.37.44.62 1.05.74 1.81a13.3 13.3 0 0 0-3-.14c-3.02.17-4.96 1.94-4.83 4.39.07 1.25.69 2.32 1.75 3.01.9.59 2.06.88 3.26.81 1.59-.09 2.84-.69 3.71-1.8.66-.84 1.08-1.93 1.27-3.31.77.47 1.34 1.08 1.66 1.82.54 1.26.57 3.33-1.12 5.02-1.48 1.48-3.25 2.12-5.94 2.14-2.98-.02-5.24-.98-6.71-2.84C2.98 17.7 2.27 15.2 2.24 12c.03-3.2.74-5.7 2.11-7.43C5.82 2.71 8.08 1.75 11.06 1.73c3 .02 5.3.99 6.83 2.86.75.92 1.32 2.08 1.69 3.43l2.05-.55c-.45-1.66-1.16-3.1-2.12-4.28C17.55 1 14.62-.24 11.07-.27h-.01C7.51-.24 4.61 1 2.9 3.2 1.37 5.16.59 7.9.56 11.99v.02c.03 4.09.81 6.83 2.34 8.79 1.71 2.2 4.61 3.44 8.16 3.46h.01c3.16-.02 5.38-.85 7.21-2.68 2.4-2.39 2.32-5.39 1.53-7.23-.57-1.32-1.65-2.39-3.13-3.11zm-5.5 5.28c-1.33.08-2.71-.52-2.78-1.77-.05-.93.66-1.96 2.86-2.09.25-.01.5-.02.74-.02.8 0 1.55.08 2.23.23-.25 3.16-1.74 3.6-3.05 3.65z'),
        'github'    => array('GitHub',    'M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.6-1.4-1.4-1.8-1.4-1.8-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.8 1.3 3.5 1 0-.8.4-1.3.7-1.6-2.7-.3-5.5-1.3-5.5-5.9 0-1.3.5-2.4 1.2-3.2 0-.4-.5-1.6.2-3.2 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.7 1.6.2 2.8.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.5.4.9 1.1.9 2.3v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3z'),
        'discord'   => array('Discord',   'M20.32 4.37a19.8 19.8 0 0 0-4.89-1.52.07.07 0 0 0-.08.04c-.21.38-.44.87-.61 1.25a18.3 18.3 0 0 0-5.49 0 12.6 12.6 0 0 0-.62-1.25.08.08 0 0 0-.08-.04c-1.71.3-3.35.8-4.88 1.52a.07.07 0 0 0-.03.03C.53 9.05-.32 13.58.1 18.06c0 .02.02.04.04.06 2.05 1.5 4.04 2.42 6 3.03a.08.08 0 0 0 .08-.03c.46-.63.87-1.3 1.23-2a.08.08 0 0 0-.04-.11c-.65-.25-1.28-.55-1.88-.9a.08.08 0 0 1-.01-.13l.37-.29a.07.07 0 0 1 .08 0c3.93 1.79 8.18 1.79 12.06 0a.07.07 0 0 1 .08 0l.37.3a.08.08 0 0 1 0 .12c-.6.35-1.23.65-1.89.9a.08.08 0 0 0-.04.11c.36.7.78 1.36 1.23 2a.08.08 0 0 0 .08.03 19.9 19.9 0 0 0 6.02-3.03.08.08 0 0 0 .03-.05c.5-5.18-.84-9.67-3.54-13.66a.06.06 0 0 0-.03-.03zM8.02 15.33c-1.18 0-2.16-1.09-2.16-2.42 0-1.33.96-2.42 2.16-2.42 1.21 0 2.18 1.1 2.16 2.42 0 1.33-.96 2.42-2.16 2.42zm7.97 0c-1.18 0-2.15-1.09-2.15-2.42 0-1.33.95-2.42 2.15-2.42 1.21 0 2.18 1.1 2.16 2.42 0 1.33-.95 2.42-2.16 2.42z'),
        'spotify'   => array('Spotify',   'M12 0a12 12 0 1 0 0 24 12 12 0 0 0 0-24zm5.5 17.31a.75.75 0 0 1-1.03.25c-2.82-1.73-6.37-2.12-10.55-1.16a.75.75 0 1 1-.33-1.46c4.57-1.05 8.5-.6 11.66 1.34.35.22.46.68.25 1.03zm1.47-3.27a.94.94 0 0 1-1.29.31c-3.23-1.98-8.15-2.56-11.97-1.4a.94.94 0 0 1-.54-1.79c4.36-1.32 9.78-.68 13.49 1.6.44.27.58.85.31 1.28zm.13-3.4C15.23 8.34 8.9 8.13 5.2 9.25a1.12 1.12 0 1 1-.65-2.15c4.25-1.29 11.24-1.04 15.67 1.59a1.12 1.12 0 1 1-1.14 1.94z'),
        'rss'       => array('RSS',       'M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18A2.18 2.18 0 0 1 6.18 20 2.18 2.18 0 0 1 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56m0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9z'),
    );
}

/**
 * One inline SVG.
 *
 * fill is currentColor and no width/height is set on the paths, so the theme's
 * CSS controls colour and size with nothing to override.
 */
function bare_bones_social_icon($slug, $size = 24) {
    $icons = bare_bones_social_icons();

    if (!isset($icons[$slug])) {
        return '';
    }

    return '<svg class="bbsocial-icon bbsocial-icon-' . esc_attr($slug) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . intval($size) . '" height="' . intval($size) . '" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . esc_attr($icons[$slug][1]) . '"/></svg>';
}

/**
 * The icon list. Used by the widget, the shortcode, and the post footer.
 *
 * Only profiles with a saved URL render, in library order.
 */
function bare_bones_social_render_icons($size = 24) {
    $profiles = bare_bones_social_get('profiles', array());

    if (empty($profiles) || !is_array($profiles)) {
        return '';
    }

    $icons = bare_bones_social_icons();
    $out   = '';

    foreach ($icons as $slug => $icon) {
        if (empty($profiles[$slug])) {
            continue;
        }

        $out .= '<li class="bbsocial-item">'
            . '<a class="bbsocial-link" href="' . esc_url($profiles[$slug]) . '" rel="me noopener" target="_blank">'
            . bare_bones_social_icon($slug, $size)
            . '<span class="screen-reader-text">' . esc_html($icon[0]) . '</span>'
            . '</a></li>';
    }

    return $out ? '<ul class="bbsocial-icons">' . $out . '</ul>' : '';
}

// Shortcode: [bare_bones_social] or [bare_bones_social size="32"]
add_shortcode('bare_bones_social', function($atts) {
    $atts = shortcode_atts(array('size' => 24), $atts, 'bare_bones_social');
    return bare_bones_social_render_icons(intval($atts['size']));
});

/**
 * The entire front-end stylesheet, printed inline.
 *
 * Under 500 bytes and costs no HTTP request, which beats enqueuing a file.
 * Everything here is layout only — colour and font come from the theme, and
 * any theme rule can override these since they are plain single-class
 * selectors. Filter 'bare_bones_social_styles' to false to drop it entirely.
 */
add_action('wp_head', 'bare_bones_social_inline_styles', 8);
function bare_bones_social_inline_styles() {
    if (!apply_filters('bare_bones_social_styles', true)) {
        return;
    }

    echo '<style>'
        . '.bbsocial-share{margin:2em 0;padding:.7em 0;border-top:1px solid rgba(127,127,127,.35);border-bottom:1px solid rgba(127,127,127,.35)}'
        . '.bbsocial-share a{text-decoration:none}'
        . '.bbsocial-share a:hover{text-decoration:underline}'
        . '.bbsocial-sep,.bbsocial-div{opacity:.45}'
        . '.bbsocial-div{margin:0 .5em}'
        . '.bbsocial-follow{margin:2em 0}'
        . '.bbsocial-follow-title{font-weight:600;margin:0 0 .6em}'
        . '.bbsocial-icons{display:flex;flex-wrap:wrap;gap:.7em;list-style:none;margin:0;padding:0}'
        . '.bbsocial-icons a{display:inline-flex;color:inherit}'
        . '</style>' . "\n";
}
