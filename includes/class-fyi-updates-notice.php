<?php
/**
 * Files related to plug-in basic settings
 *
 * @package WP_FYI_Updates_Notice\includes\FYI_UpdatesNotice
 */

if (!defined('ABSPATH')) {
    die('sorry, access this file.');
}

class FYI_UpdatesNotice
{
    /** @var array interval type */
    public $interval;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->init();
    }
    /**
     * Initial processing
     *
     * @return void
     */
    public function init()
    {

        $plugin_file = plugin_basename( __FILE__ );

        add_action('fyi_cron_set', ['FYI_Cron', 'set']);

        add_action('admin_menu', [$this, 'plugin_menu']);
        add_action('admin_init', [$this, 'plugin_setting']);

        add_filter('plugin_action_links_' . WP_FYI_UDATES_NOTICE_NAME, array( $this, 'plugin_action_links'));

        add_action('fyi_cron_set', ['FYI_Cron', 'set']);
        add_action('fyi_cron_delete', ['FYI_Cron', 'delete']);
        add_action(WP_FYI_CRON_NAME, ['FYI_Cron', 'fyi_updates_notice_run']);
        add_filter('cron_schedules', ['FYI_Cron', 'weekly_add_interval']);

        $this->interval = [
            WP_FYI_CRON_STOP => __('停止', WP_FYI_PG_NAME),
            WP_FYI_CRON_DATE => __('1日1回', WP_FYI_PG_NAME),
            WP_FYI_CRON_WEEKLY => __('週1回', WP_FYI_PG_NAME),
        ];
    }
    /**
     * Add link to plugin list
     *
     * @param array $links
     * @return void
     */
    public function plugin_action_links($links)
    {
        $url = admin_url('admin.php?page=notifier-plugin-config');
        $url = '<a href="' . esc_url( $url ) . '">' . __('Settings') . '</a>';
        array_unshift($links, $url);
        return $links;
    }
    /**
     * Add management menu
     *
     * @return void
     */
    public function plugin_menu()
    {
        add_options_page(
            __('更新通知の設定', WP_FYI_PG_NAME),
            __('更新通知', WP_FYI_PG_NAME),
            'administrator',
            'notifier-plugin-config',
            [$this, 'setting_page']
        );
    }
    /**
     * Construction of management page
     *
     * @return void
     */
    public function setting_page()
    {
        global $fyi_option;

        $fyi_option = get_option('fyi_notice_setting_option');

        if (!$fyi_option) {
            $fyi_option = ['cron' => 0, 'mail' => ''];
            update_option('fyi_notice_setting_option', $fyi_option);
        }
?>
        <div class="wrap">
            <h2><?php _e("WP_FYI_Updates_Notice", WP_FYI_PG_NAME); ?></h2>
            <form method="post" action="<?php echo admin_url("options.php"); ?>">
                <?php
                settings_fields('notifier-setting_group');
                do_settings_sections('notifier-plugin-config');
                submit_button();
                ?>

            </form>
        </div>
        <?php
        $objVersion =  new FYI_Check();
        $version = $objVersion->update_check();
        ?>
        <hr>
        <h3>現在の状態</h3>
        <p><?php echo  _e('以下の情報がメールにて通知されます。', WP_FYI_PG_NAME);?></p>

        <h4>本体</h4>
        <?php if (empty($version['core']['updates'])) : ?>
            <p><?php echo  _e('更新可能な情報はありません。', WP_FYI_PG_NAME); ?></p>
            <?php else : foreach ($version['core']['updates'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>

        <h4>プラグイン</h4>
        <?php if (empty($version['plugins']['updates'])) : ?>
            <p><?php echo  _e('更新可能なプラグインはありません。', WP_FYI_PG_NAME); ?></p>
            <?php else : foreach ($version['plugins']['updates'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>

        <h4>テーマ</h4>
        <?php if (empty($version['themes']['updates'])) : ?>
            <p><?php echo  _e('更新可能なテーマはありません。', WP_FYI_PG_NAME); ?></p>
            <?php else : foreach ($version['themes']['updates'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>

        <h4>翻訳</h4>
        <?php if (empty($version['translation'])) : ?>
            <p><?php echo  _e('更新可能な翻訳はありません。', WP_FYI_PG_NAME); ?></p>
            <?php else : foreach ($version['translation'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>
    <?php
    }
    /**
     * Form Page construction
     *
     * @return void
     */
    public function plugin_setting()
    {
        register_setting('notifier-setting_group', 'fyi_notice_setting_option', [$this, 'validate']);
        add_settings_section('notifier_id', __('通知頻度と通知先の設定', WP_FYI_PG_NAME), [$this, 'setting_header'], 'notifier-plugin-config');
        add_settings_field('cron', __('通知頻度', WP_FYI_PG_NAME), [$this, 'setting_from_cron'], 'notifier-plugin-config', 'notifier_id');
        add_settings_field('mail', __('通知先メールアドレス', WP_FYI_PG_NAME), [$this, 'setting_from_mail'], 'notifier-plugin-config', 'notifier_id');
    }
    /**
     * Form Page  header
     *
     * @return void
     */
    public function setting_header()
    {
        echo _e('本体・プラグイン等の更新が可能になった情報の通知設定を行ってください。', WP_FYI_PG_NAME);
    }
    /**
     * Form cron construction
     *
     * @return void
     */
    public function setting_from_cron()
    {
        global $fyi_option;
    ?>
        <select id="cron" name="fyi_notice_setting_option[cron]">
            <?php foreach ($this->interval as $key => $value) : $selected = ($key == $fyi_option['cron']) ? ' selected' : ''; ?>
                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo _e($value, WP_FYI_PG_NAME); ?></option>
            <?php endforeach; ?>
        </select>
    <?php
    }
    /**
     * Form email construction
     *
     * @return void
     */
    public function setting_from_mail()
    {
        global $fyi_option;
    ?>
        <input type="email" name="fyi_notice_setting_option[mail]" size="30" maxlength="40" value="<?php echo $fyi_option['mail']; ?>">
<?php
    }
    /**
     * Processing at registration
     * 
     * -Cron registration if there is no problem
     * @param array $input
     * @return void
     */
    public function validate($input)
    {
        global $fyi_option;

        if ($input['cron'] != $fyi_option['cron']) {
            FYI_Cron::set($input);
        }
        return $input;
    }
    /**
     * Processing when plugin is enabled
     *
     * @return void
     */
    public function activation()
    {
        do_action('fyi_cron_set');
    }
    /**
     * Processing when the plugin is disabled
     *
     * @return void
     */
    public function deactivation()
    {
        do_action('fyi_cron_delete');
    }
}
