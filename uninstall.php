<?php
/**
 * Plugin removal process
 *
 * @package WP_FYI_Updates_Notice
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option('fyi_notice_setting_option');