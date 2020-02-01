<?php
/**
 * Email transmission processing
 *
 * @package WP_FYI_Updates_Notice\includes\FYI_Mail
 */

if (!defined('ABSPATH')) {
    die('sorry, access this file.');
}

class FYI_Mail
{
    /** @var array Update information  */
    public $version;

    /** @var string email subject  */
    public $subject;

    /** @var string the content of the email */
    public $message;

    /**
     * __construct
     *
     * @param array $version
     */
    public function __construct($version)
    {
        $this->version = $version;
        $this->init();
    }
    /**
     * Initial processing
     *
     * @return void
     */
    public function init()
    {
        $this->subject();
        $this->message();
    }
    /**
     * Email transmission processing
     *
     * @param string $mail
     * @return void
     */
    public function notice($mail)
    {
        wp_mail( $mail, $this->subject, $this->message);
    }
    /**
     * Subject set
     *
     * @return void
     */
    public function subject()
    {
        $site = get_option('blogname');
        $this->subject = sprintf(__('[ %s ] WordPress更新可能な情報（本体・プラグインなど）のお知らせ !!', WP_FYI_PG_NAME), $site);
    }
    /**
     * Body set
     *
     * @return void
     */
    public function message()
    {
        $plot = $this->version;
        @ob_start();
        require_once(WP_FYI_MAIL_TPL);
        $this->message = @ob_get_contents();
        @ob_end_clean();
    }
}
