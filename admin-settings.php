<?php
/**
 * Settings screen.
 *
 * One page, three sections. There is not enough here to justify tabs.
 */

if (!defined('ABSPATH')) { exit; }

add_action('admin_init', function() {
    register_setting('bare_bones_social', BARE_BONES_SOCIAL_OPTION, array(
        'type'              => 'array',
        'sanitize_callback' => 'bare_bones_social_sanitize',
        'default'           => array(),
        'show_in_rest'      => false,
    ));
});

/**
 * Sanitize on save. Unknown keys are dropped rather than passed through.
 */
function bare_bones_social_sanitize($input) {
    $input = is_array($input) ? $input : array();
    $icons = bare_bones_social_icons();
    $out   = array(
        'profiles'       => array(),
        'default_image'  => '',
        'show_share'     => empty($input['show_share']) ? 0 : 1,
        'share_label'    => isset($input['share_label']) ? sanitize_text_field($input['share_label']) : '',
        'share_networks' => array(),
        'show_follow'    => empty($input['show_follow']) ? 0 : 1,
        'follow_title'   => isset($input['follow_title']) ? sanitize_text_field($input['follow_title']) : '',
    );

    // Intersect against the supported list so a hand-crafted POST cannot add
    // networks we have no share URL for.
    if (!empty($input['share_networks']) && is_array($input['share_networks'])) {
        $out['share_networks'] = array_values(array_intersect(
            array_map('sanitize_key', $input['share_networks']),
            array_keys(bare_bones_social_share_networks())
        ));
    }

    if (!empty($input['default_image'])) {
        $out['default_image'] = esc_url_raw(trim($input['default_image']), array('http', 'https'));
    }

    if (!empty($input['profiles']) && is_array($input['profiles'])) {
        foreach ($input['profiles'] as $slug => $url) {
            $url = esc_url_raw(trim($url), array('http', 'https'));
            if ($url && isset($icons[$slug])) {
                $out['profiles'][$slug] = $url;
            }
        }
    }

    return $out;
}

/**
 * Render the settings screen.
 */
function bare_bones_social_render_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $settings = bare_bones_social_get_settings();
    $profiles = $settings['profiles'];
    $icons    = bare_bones_social_icons();
    $filled   = count(array_filter($profiles));

    $size_number = preg_replace('/[^0-9.]/', '', BARE_BONES_SOCIAL_SIZE);
    $size_unit   = preg_replace('/[0-9.\s]/', '', BARE_BONES_SOCIAL_SIZE);
    ?>
    <div class="wrap bare-bones-social-wrap" style="max-width: 1200px;">
        <div class="wp-header-end"></div>
        <h1 class="bb-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 169 204" width="48" height="48" aria-hidden="true"><path fill="currentColor" d="<?php echo esc_attr(BARE_BONES_SOCIAL_SKULL); ?>"/></svg>
            Bare Bones Social
        </h1>

        <div class="bb-welcome-card">
            <div class="bb-welcome-mark">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 169 204" width="120" height="145" aria-hidden="true"><path fill="currentColor" d="<?php echo esc_attr(BARE_BONES_SOCIAL_SKULL); ?>"/></svg>
            </div>
            <div class="bb-welcome-body">
                <div class="bb-size-callout">
                    <span class="bb-size-number"><?php echo esc_html($size_number); ?></span>
                    <span class="bb-size-unit"><?php echo esc_html($size_unit); ?></span>
                </div>
                <h2 class="bb-welcome-heading">Sharing Without the Payload</h2>
                <p class="bb-welcome-copy">
                    Mainstream social plugins pull in third-party SDKs, poll APIs for share counts nobody reads, and leave transient rows behind for months. This one outputs Open Graph tags, inline SVG icons, and nothing else &mdash; no external requests, no counters, no cron, and a single row in your options table.
                </p>
            </div>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('bare_bones_social'); ?>

            <div class="bb-grid-2">
                <div>
                    <div class="card bb-card">
                        <h3 class="bb-card-title">Profile URLs</h3>
                        <p class="bb-hint">Leave a field blank to hide that icon. Icons render in the order listed.</p>
                        <?php foreach ($icons as $slug => $icon) : ?>
                            <div class="bb-profile-row">
                                <label for="bbsocial-<?php echo esc_attr($slug); ?>">
                                    <?php echo bare_bones_social_icon($slug, 18); ?>
                                    <?php echo esc_html($icon[0]); ?>
                                </label>
                                <input type="url" class="bb-input" id="bbsocial-<?php echo esc_attr($slug); ?>"
                                    name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[profiles][<?php echo esc_attr($slug); ?>]"
                                    value="<?php echo esc_attr(isset($profiles[$slug]) ? $profiles[$slug] : ''); ?>"
                                    placeholder="https://">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <div class="card bb-card">
                        <h3 class="bb-card-title">Share Section</h3>
                        <p class="bb-hint">Text links at the end of every single post. Only networks with a real share URL are listed &mdash; nothing here loads a third-party script.</p>
                        <div class="bb-field-group">
                            <label>
                                <input type="checkbox" value="1"
                                    name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[show_share]"
                                    <?php checked($settings['show_share'], 1); ?>>
                                Show after single posts
                            </label>
                        </div>
                        <div class="bb-field-group">
                            <label for="bbsocial-share-label">Lead-in text</label>
                            <input type="text" class="bb-input" id="bbsocial-share-label"
                                name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[share_label]"
                                value="<?php echo esc_attr($settings['share_label']); ?>">
                        </div>
                        <div class="bb-field-group">
                            <label>Networks</label>
                            <?php foreach (bare_bones_social_share_networks() as $slug => $network) : ?>
                                <label class="bb-checkbox">
                                    <input type="checkbox" value="<?php echo esc_attr($slug); ?>"
                                        name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[share_networks][]"
                                        <?php checked(in_array($slug, $settings['share_networks'], true)); ?>>
                                    <?php echo esc_html($network[0]); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card bb-card">
                        <h3 class="bb-card-title">Follow Section</h3>
                        <p class="bb-hint">Adds your icons to the bottom of every single post.</p>
                        <div class="bb-field-group">
                            <label>
                                <input type="checkbox" value="1"
                                    name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[show_follow]"
                                    <?php checked($settings['show_follow'], 1); ?>>
                                Show after single posts
                            </label>
                        </div>
                        <div class="bb-field-group">
                            <label for="bbsocial-follow-title">Heading</label>
                            <input type="text" class="bb-input" id="bbsocial-follow-title"
                                name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[follow_title]"
                                value="<?php echo esc_attr($settings['follow_title']); ?>">
                        </div>
                    </div>

                    <div class="card bb-card">
                        <h3 class="bb-card-title">Default Share Image</h3>
                        <p class="bb-hint">Used for og:image when a post has no featured image and no image in its content. Paste a URL from your media library. 1200&times;630 works best.</p>
                        <div class="bb-field-group">
                            <input type="url" class="bb-input"
                                name="<?php echo esc_attr(BARE_BONES_SOCIAL_OPTION); ?>[default_image]"
                                value="<?php echo esc_attr($settings['default_image']); ?>"
                                placeholder="https://">
                        </div>
                    </div>

                    <div class="card bb-card">
                        <h3 class="bb-card-title">Diagnostics</h3>
                        <div class="bb-stat">
                            <span class="bb-stat-label">Open Graph</span>
                            <span class="bb-stat-value"><?php echo (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('SEOPRESS_VERSION') || defined('AIOSEO_VERSION')) ? 'Standing down' : 'Active'; ?></span>
                        </div>
                        <div class="bb-stat">
                            <span class="bb-stat-label">Profiles set</span>
                            <span class="bb-stat-value"><?php echo esc_html($filled . ' of ' . count($icons)); ?></span>
                        </div>
                        <div class="bb-stat">
                            <span class="bb-stat-label">Database rows</span>
                            <span class="bb-stat-value">1</span>
                        </div>
                        <div class="bb-stat">
                            <span class="bb-stat-label">Icons shortcode</span>
                            <span class="bb-stat-value"><code>[bare_bones_social]</code></span>
                        </div>
                        <div class="bb-stat">
                            <span class="bb-stat-label">Share shortcode</span>
                            <span class="bb-stat-value"><code>[bare_bones_share]</code></span>
                        </div>
                        <div class="bb-stat bb-stat-last">
                            <span class="bb-stat-label">Version</span>
                            <span class="bb-stat-value"><?php echo esc_html(BARE_BONES_SOCIAL_VERSION); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
