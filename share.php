<?php
/**
 * The share section.
 *
 * Text links only. Every network here publishes a real share URL you can link
 * to directly — no SDKs, no iframes, no buttons phoning home, and no counts.
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Networks that offer a genuine share URL.
 *
 * %1$s is the permalink, %2$s the title, %3$s the image. Email and Copy Link
 * are handled separately since they are not networks.
 */
function bare_bones_social_share_networks() {
    return array(
        'linkedin'  => array('LinkedIn',  'https://www.linkedin.com/sharing/share-offsite/?url=%1$s'),
        'x'         => array('X',         'https://x.com/intent/post?url=%1$s&text=%2$s'),
        'facebook'  => array('Facebook',  'https://www.facebook.com/sharer/sharer.php?u=%1$s'),
        'reddit'    => array('Reddit',    'https://www.reddit.com/submit?url=%1$s&title=%2$s'),
        'pinterest' => array('Pinterest', 'https://pinterest.com/pin/create/button/?url=%1$s&description=%2$s&media=%3$s'),
        'threads'   => array('Threads',   'https://www.threads.net/intent/post?text=%2$s %1$s'),
        'bluesky'   => array('Bluesky',   'https://bsky.app/intent/compose?text=%2$s %1$s'),
        'email'     => array('Email',     'mailto:?subject=%2$s&body=%1$s'),
        'copy'      => array('Copy Link', ''),
    );
}

/**
 * Build the share row.
 *
 * Renders as: Share on LinkedIn ↗ · X ↗ · Facebook ↗ | Email ↗ · Copy Link
 * The pipe separates the networks from the universal fallbacks.
 */
function bare_bones_social_render_share($post_id = 0) {
    $post_id = $post_id ? $post_id : get_the_ID();
    if (!$post_id) {
        return '';
    }

    $chosen = bare_bones_social_get('share_networks', array());
    if (empty($chosen) || !is_array($chosen)) {
        return '';
    }

    $all   = bare_bones_social_share_networks();
    $url   = get_permalink($post_id);
    $title = get_the_title($post_id);
    $image = function_exists('bare_bones_social_og_image') ? bare_bones_social_og_image($post_id) : '';

    $arrow = ' <span class="bbsocial-arrow" aria-hidden="true">&#8599;</span>';
    $links = array();
    $extra = array();

    foreach ($all as $slug => $network) {
        if (!in_array($slug, $chosen, true)) {
            continue;
        }

        if ($slug === 'copy') {
            // Href is the plain permalink, so this still works with JS off.
            $extra[] = '<a class="bbsocial-copy" href="' . esc_url($url) . '">Copy Link</a>';
            continue;
        }

        $href = sprintf(
            $network[1],
            rawurlencode($url),
            rawurlencode($title),
            rawurlencode($image)
        );

        $link = '<a class="bbsocial-share-' . esc_attr($slug) . '" href="' . esc_url($href) . '" rel="nofollow noopener"'
            . ($slug === 'email' ? '' : ' target="_blank"') . '>'
            . esc_html($network[0]) . $arrow . '</a>';

        if ($slug === 'email') {
            $extra[] = $link;
        } else {
            $links[] = $link;
        }
    }

    if (!$links && !$extra) {
        return '';
    }

    $sep  = ' <span class="bbsocial-sep" aria-hidden="true">&middot;</span> ';
    $html = '<div class="bbsocial-share">';

    if ($links) {
        $label = bare_bones_social_get('share_label');
        if ($label) {
            $html .= esc_html($label) . ' ';
        }
        $html .= implode($sep, $links);
    }

    if ($extra) {
        if ($links) {
            $html .= '<span class="bbsocial-div" aria-hidden="true">|</span>';
        }
        $html .= implode($sep, $extra);
    }

    return $html . '</div>';
}

/**
 * Append the share row to single posts. Priority 20, ahead of the follow
 * section at 21.
 */
add_filter('the_content', 'bare_bones_social_append_share', 20);
function bare_bones_social_append_share($content) {
    if (!bare_bones_social_get('show_share')) {
        return $content;
    }

    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $share = bare_bones_social_render_share();
    if ($share) {
        bare_bones_social_needs_copy_script(true);
    }

    return $content . $share;
}

// Shortcode: [bare_bones_share]
add_shortcode('bare_bones_share', function() {
    $share = bare_bones_social_render_share();
    if ($share) {
        bare_bones_social_needs_copy_script(true);
    }
    return $share;
});

/**
 * Flag that the copy script is needed on this page.
 */
function bare_bones_social_needs_copy_script($set = false) {
    static $needed = false;

    if ($set) {
        $needed = true;
    }

    return $needed;
}

/**
 * The clipboard snippet.
 *
 * Printed once, in the footer, and only on pages that actually rendered a
 * Copy Link. Delegated from the document so it does not care how many share
 * rows are on the page. With JS off the link is just the permalink.
 */
add_action('wp_footer', 'bare_bones_social_copy_script', 99);
function bare_bones_social_copy_script() {
    if (!bare_bones_social_needs_copy_script()) {
        return;
    }
    ?>
<script>
document.addEventListener('click',function(e){
    var a=e.target.closest('.bbsocial-copy');
    if(!a||!navigator.clipboard)return;
    e.preventDefault();
    var t=a.textContent;
    navigator.clipboard.writeText(a.href).then(function(){
        a.textContent='Copied!';
        setTimeout(function(){a.textContent=t;},2000);
    });
});
</script>
    <?php
}
