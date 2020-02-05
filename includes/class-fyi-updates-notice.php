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
        add_action('admin_head',[$this, 'plugin_style']);

        add_filter('plugin_action_links_' . WP_FYI_UDATES_NOTICE_NAME, [$this, 'plugin_action_links']);

        add_action('fyi_cron_set', ['FYI_Cron', 'set']);
        add_action('fyi_cron_delete', ['FYI_Cron', 'delete']);
        add_action(WP_FYI_CRON_NAME, ['FYI_Cron', 'fyi_updates_notice_run']);
        add_filter('cron_schedules', ['FYI_Cron', 'weekly_add_interval']);

        $this->interval = [
            WP_FYI_CRON_STOP => __('Stop', FYI_T_DOMAIN),
            WP_FYI_CRON_DATE => __('Once a day', FYI_T_DOMAIN),
            WP_FYI_CRON_WEEKLY => __('Once a week', FYI_T_DOMAIN),
        ];
    }
    /**
     * Load style.js.
     *
     * @return void
     */
    public function plugin_style()
    {
        wp_enqueue_style(FYI_T_DOMAIN, WP_FYI_UDATES_NOTICE_URL. 'assets/fyi-updates.css');
        wp_enqueue_script(FYI_T_DOMAIN, WP_FYI_UDATES_NOTICE_URL. 'assets/fyi-updates.js');
    }

    //admin_headで管理画面のCSSを追加
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
            __('Update notification settings', FYI_T_DOMAIN),
            __('Update Notice', FYI_T_DOMAIN),
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
            $fyi_option = ['cron' => 0, 'mail' => '','tags' => ''];
            update_option('fyi_notice_setting_option', $fyi_option);
        }
?>
        <div class="wrap">
            <h2><?php _e("WP_FYI_Updates_Notice", FYI_T_DOMAIN); ?></h2>
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
        <h3><?php echo  _e('Current state', FYI_T_DOMAIN); ?></h3>
        <p><?php echo  _e('The following information will be notified by email.', FYI_T_DOMAIN);?></p>

        <h4><?php echo  _e('## Wordpress', FYI_T_DOMAIN); ?></h4>
        <?php if (empty($version['core']['updates'])) : ?>
            <p><?php echo  _e('There is no updatable information.', FYI_T_DOMAIN); ?></p>
            <?php else : foreach ($version['core']['updates'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>

        <h4><?php echo  _e('## Plugin', FYI_T_DOMAIN); ?></h4>
        <?php if (empty($version['plugins']['updates'])) : ?>
            <p><?php echo  _e('There are no updatable plugins.', FYI_T_DOMAIN); ?></p>
            <?php else : foreach ($version['plugins']['updates'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>

        <h4><?php echo  _e('## Themes', FYI_T_DOMAIN); ?></h4>
        <?php if (empty($version['themes']['updates'])) : ?>
            <p><?php echo  _e('There are no updatable themes.', FYI_T_DOMAIN); ?></p>
            <?php else : foreach ($version['themes']['updates'] as $key => $value) : ?>
                <p>- <?php echo $value; ?></p>
        <?php endforeach;
        endif; ?>

        <h4><?php echo  _e('## translation', FYI_T_DOMAIN); ?></h4>
        <?php if (empty($version['translation'])) : ?>
            <p><?php echo  _e('No translations are available for update.', FYI_T_DOMAIN); ?></p>
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
        add_settings_section('notifier_id', __('Update notification settings', FYI_T_DOMAIN), [$this, 'setting_header'], 'notifier-plugin-config');
        add_settings_field('cron', __('interval', FYI_T_DOMAIN), [$this, 'setting_from_cron'], 'notifier-plugin-config', 'notifier_id');
        add_settings_field('mail', __('email address', FYI_T_DOMAIN), [$this, 'setting_from_mail'], 'notifier-plugin-config', 'notifier_id');
        add_settings_field('exclude', __('Exclude keywords', FYI_T_DOMAIN), [$this, 'setting_from_exclude'], 'notifier-plugin-config', 'notifier_id');
    }
    /**
     * Form Page  header
     *
     * @return void
     */
    public function setting_header()
    {
        $runmss = __('No setting', FYI_T_DOMAIN);
        $time = wp_next_scheduled(WP_FYI_CRON_NAME);
        if ($time) {
            $runmss = sprintf(__('After %s', FYI_T_DOMAIN), date('Y-m-d H:i', $time));
        }
        ?>
        <p><?php echo _e('Set up notification of updatable information such as Worepress / Plugin.', FYI_T_DOMAIN); ?>
        <p><strong><?php echo _e('Next run', FYI_T_DOMAIN); ?>：<?php echo $runmss; ?></strong></p>
        <?php
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
                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo _e($value, FYI_T_DOMAIN); ?></option>
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
     * Form exclude construction
     *
     * @return void
     */
    public function setting_from_exclude()
    {
        global $fyi_option;
    ?>
    <input id="fiy_tag" type="text" name="fyi_notice_setting_option[tag][]" minlength="5" size="20" maxlength="20" placeholder="<?php echo _e('5 characters or more', FYI_T_DOMAIN); ?>" >
    <input id="fiy_tagbtn" type="button" value="追加" class="button action" disabled>
    <p class="description"><?php echo _e('Plugin themes you want to exclude from email notifications (exclude keywords containing keywords))', FYI_T_DOMAIN); ?></p>
    <div id="fiy_tags">
    <?php foreach ($fyi_option['tag'] as $tag): if (!empty($tag)):?>
        <span class="tag"><input type="hidden" name="fyi_notice_setting_option[tag][]" value="<?php echo esc_html($tag); ?>"><?php echo esc_html($tag); ?></span>
    <?php endif; endforeach;?>
    </div>
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
