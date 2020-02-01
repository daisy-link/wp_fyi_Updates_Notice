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
     * @return array $result all update information
     */
    public function update_check()
    {
        $result = [];
        $result['general'] = wp_get_update_data();
        $result['core'] = self::core();
        $result['plugins'] = self::plugins();
        $result['themes'] = self::themes();
        $result['translation'] = wp_get_translation_updates();
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
                
                $updates['updates'][] = sprintf(__('WordPress %s-%s be update!!', WP_FYI_PG_NAME), $e['version'], $e['locale']);
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
            $updates['updates'][] = sprintf(__('%s (%s be update!!)', WP_FYI_PG_NAME), $e['slug'], $e['new_version']);
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
            $updates['updates'][] = sprintf(__('%s (%s be update!!)', WP_FYI_PG_NAME), $key, $e['new_version']);
        }
        return $updates;
    }
}
