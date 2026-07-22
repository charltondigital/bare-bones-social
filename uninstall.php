<?php
/**
 * One row in, one row out. Nothing else was ever created.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) { exit; }

delete_option('bare_bones_social_settings');
