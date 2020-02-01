<?php
/**
 * Information that can be manually updated on the main unit and plug-ins will be provided.
 *
 * @package WP_FYI_Updates_Notice
 *
 * Plugin Name: WP_FYI_Updates_Notice
 * Plugin URI: https://daisy.link
 * Description: ご利用中のWordPress本体・プラグイン等で手動更新が可能な情報をお知らせします。
 * Author: DAISY
 * Version: 0.9.0
 * Author URI: https://daisy.link
 */

if (!defined('ABSPATH')) {
    die('sorry, access this file.');
}

define('WP_FYI_PG_NAME', 'WP_FYI_Updates_Notice');

define('WP_FYI_CRON_NAME', 'fyi-updates-notice_event');

define('WP_FYI_CRON_STOP', 0);

define('WP_FYI_CRON_DATE', 1);

define('WP_FYI_CRON_WEEKLY', 2);

define('WP_FYI_UDATES_NOTICE_FILE', __FILE__);

define('WP_FYI_UDATES_NOTICE_DIR', plugin_dir_path(__FILE__));

define('WP_FYI_MAIL_TPL', WP_FYI_UDATES_NOTICE_DIR . 'templates/template_mail.php');

require_once(dirname(__FILE__) . '/includes/class-fyi-updates-notice.php');
require_once(dirname(__FILE__) . '/includes/class-fyi-check.php');
require_once(dirname(__FILE__) . '/includes/class-fyi-cron.php');
require_once(dirname(__FILE__) . '/includes/class-fyi-mail.php');

new FYI_UpdatesNotice;

register_activation_hook( __FILE__, ['FYI_UpdatesNotice', 'activation']);
register_deactivation_hook( __FILE__, ['FYI_UpdatesNotice', 'deactivation']);