<?php
/**
 * The follow section after single posts, and the social icons widget.
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Append the follow section to single posts.
 *
 * Priority 21, just after the share row.
 */
add_filter('the_content', 'bare_bones_social_append_follow', 21);
function bare_bones_social_append_follow($content) {
    if (!bare_bones_social_get('show_follow')) {
        return $content;
    }

    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $icons = bare_bones_social_render_icons();
    if (!$icons) {
        return $content;
    }

    $title = bare_bones_social_get('follow_title');
    $html  = '<div class="bbsocial-follow">';

    if ($title) {
        $html .= '<p class="bbsocial-follow-title">' . esc_html($title) . '</p>';
    }

    return $content . $html . $icons . '</div>';
}

/**
 * Social icons widget.
 *
 * A classic WP_Widget rather than a block: it drops straight into any theme's
 * footer widget area, and block themes can still place it through the Legacy
 * Widget block. A real block would need a JS build step, which this plugin
 * does not have and does not want.
 */
add_action('widgets_init', function() {
    register_widget('Bare_Bones_Social_Widget');
});

class Bare_Bones_Social_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bare_bones_social',
            'Bare Bones Social Icons',
            array('description' => 'Your social profile icons, linked. Set the URLs under Bare Bones Social.')
        );
    }

    public function widget($args, $instance) {
        $icons = bare_bones_social_render_icons(isset($instance['size']) ? intval($instance['size']) : 24);

        if (!$icons) {
            return;
        }

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }

        echo $icons;
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $size  = isset($instance['size']) ? intval($instance['size']) : 24;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('size')); ?>">Icon size (px):</label>
            <input class="tiny-text" type="number" min="12" max="96" id="<?php echo esc_attr($this->get_field_id('size')); ?>" name="<?php echo esc_attr($this->get_field_name('size')); ?>" value="<?php echo esc_attr($size); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        return array(
            'title' => sanitize_text_field($new_instance['title']),
            'size'  => min(96, max(12, intval($new_instance['size']))),
        );
    }
}
