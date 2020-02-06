<?php
/**
 * Acquisition of various update information
 *
 * @package WP_FYI_Updates_Notice\includes\FYI_Check
 */

if (!defined('ABSPATH')) {
    die('sorry, access this file.');
}

class FYI_Check
{
    /**
     * __construct
     */
    public function __construct()
    {
    }
    /**
     * all update information
     *
     * @param boolean $filter
     * @return array $result all update information
     */
    public function update_check($filter = false)
    {
        $result = [];
        $result['general'] = wp_get_update_data();
        $result['core'] = self::core();
        $result['plugins'] = self::plugins();
        $result['themes'] = self::themes();
        $result['translation'] = self::translation();

        if ($filter) {
            $this->noticeFilter($result);
        }

        return $result;
    }
    /**
     * core update information
     *
     * @return array $updates update information
     */
    public function core()
    {
        $updates = [];
        $result = [];

        $result = get_site_transient('update_core');
        if (!$result) {
            $result = get_site_option( '_site_transient_update_core');
        }

        $result = json_decode(json_encode($result), true);
        $locale = get_locale();

        foreach ($result['updates'] as $key => $e) {
            
            if ($e['response']  == 'upgrade' && $e['locale'] == $locale) { 
                
                $updates['updates'][] = sprintf(__('WordPress %s-%s Can be updated!!', FYI_T_DOMAIN), $e['version'], $e['locale']);
            }
        }
        return $updates;
    }
    /**
     * plugins update information
     *
     * @return array $updates update information
     */
    public function plugins()
    {
        $updates = [];
        $result = [];
        $result = get_site_transient('update_plugins');
        $result = json_decode(json_encode($result), true);
        foreach ($result['response'] as $key => $e) {
            $updates['updates'][] = sprintf(__('%s (%s Can be updated!!)', FYI_T_DOMAIN), $e['slug'], $e['new_version']);
        }
        return $updates;
    }
    /**
     * themes update information
     *
     * @return array $updates update information
     */
    public function themes()
    { 
        $updates = [];
        $result = [];
        $result = get_site_transient('update_themes');
        $result = json_decode(json_encode($result), true);
        foreach ($result['response'] as $key => $e) {
            $updates['updates'][] = sprintf(__('%s (%s Can be updated!!)', FYI_T_DOMAIN), $key, $e['new_version']);
        }
        return $updates;
    }
    /**
     * translation update information
     *
     * @return array $updates update information
     */
    public function translation()
    { 
        $updates = [];
        $result = [];
        $result = wp_get_translation_updates();
        $result = json_decode(json_encode($result), true);

        
        foreach ($result as $key => $e) {

            $updates['updates'][] = sprintf(__('%s lang-%s (%s Can be updated!!)', FYI_T_DOMAIN), $e['type'], $e['language'], $e['version']);
        }
        return $updates;
    }
    /**
     * noticeFilter
     *
     * @param array $result
     * @return void
     */
    public function noticeFilter(&$result)
    { 

        $option = get_option('fyi_notice_setting_option');

        foreach ($result['plugins']['updates'] as $key => $plugin) {
            foreach ($option['tag'] as $tag) {
                if(strpos($plugin, $tag) !== false){
                    unset($result['plugins']['updates'][$key]);
                    break;
                }
            }
        }

        foreach ($result['themes']['updates'] as $key => $plugin) {
            foreach ($option['tag'] as $tag) {
                if(strpos($plugin, $tag) !== false){
                    unset($result['themes']['updates'][$key]);
                    break;
                }
            }
        }
    }
}
