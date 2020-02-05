<?php
/**
 * Basic processing of Cron processing
 *
 * @package WP_FYI_Updates_Notice\includes\FYI_Cron
 */

if (!defined('ABSPATH')) {
    die('sorry, access this file.');
}

class FYI_Cron
{
    /**
     * __construct
     */
    public function __construct()
    {
    }
    /**
     * Cron registration update process
     *
     * @param boolean|array $input Information entered
     * @return void
     */
    public function set($input = false)
    {
        $cron = '';
        $mail = '';

        $fyi_option = get_option('fyi_notice_setting_option');

        if(!$input && $fyi_option['cron']){
            $cron = $fyi_option['cron'];
            $mail = $fyi_option['mail'];
        } else if ($input && isset($input['cron'])) {
            $cron = $input['cron'];
            $mail = $input['mail'];
        }

        switch ($cron) {
            case WP_FYI_CRON_DATE:
                $interval = 'daily';
                break;
            case WP_FYI_CRON_WEEKLY:
                $interval = 'weekly';
            break;
            default:
                $interval = '';
                break;
        }

        if (empty($interval) || empty($mail) ) {
            self::delete();
        } else {
            self::schedule($interval);
        }
    }
    /**
     * Cron settings
     *
     * @param string $interval cron interval
     * @return void
     */
    public function schedule($interval)
    {
        $time = wp_next_scheduled(WP_FYI_CRON_NAME);
        $schedules = wp_get_schedules();

        if ($time) {
            wp_clear_scheduled_hook(WP_FYI_CRON_NAME);
        }
        if (isset($schedules[$interval]['interval'])) {
            wp_schedule_event(time() + $schedules[$interval]['interval'], $interval, WP_FYI_CRON_NAME);
        }
    }
    /**
     * Cron delete
     *
     * @return void
     */
    public function delete()
    {
        wp_clear_scheduled_hook(WP_FYI_CRON_NAME);
    }
    /**
     * interval add
     *
     * @param array $schedules Cron interval type
     * @return void
     */
    public function weekly_add_interval($schedules)
    {
        $schedules['weekly'] = [ 
            'interval' => 604800,
            'display' => __('Once a week', FYI_T_DOMAIN)
        ];
        return $schedules;
    }
    /**
     * Processing at Cron
     *
     * @return void
     */
    public function fyi_updates_notice_run()
    {
        $option = get_option('fyi_notice_setting_option');

        $objVersion =  new FYI_Check($option['tag']);
        $version = $objVersion->update_check();

        if (isset($option['mail']) && $version && !empty($option['mail'])){
            $objMai = new FYI_Mail($version);
            $objMai->notice($option['mail']);
        }
    }
}

