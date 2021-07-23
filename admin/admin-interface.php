<?php


class _cnmAdminInterface
{
    public function __construct()
    {
        add_action('wp_ajax_userList', array($this, 'handleUserManageList'));
        add_action('wp_ajax_cnm_updater', array($this, 'handleNotificationReciever'));
    }

    /* Retrieve the userList */
    function handleUserManageList()
    {
        if (wp_verify_nonce(handlePostData('nonce'), "get_user_list") && is_admin()) {
            $user = new WP_User_Query(array(
                'blog' => 0,
                'exclude' => array(handlePostData('user')),
                'fields' => array('ID', 'user_login')
            ));

            $forwardTo = get_user_meta(handlePostData('user'), 'comment_notification_forward', true);

            $res = '<option value="">Self</option>';
            foreach ($user->results as $usr) {
                $res .= '<option ' . ($forwardTo == $usr->ID ? 'selected' : '') . ' value="' . $usr->ID . '">' . ucfirst($usr->user_login) . '</option>';
            }
            wp_send_json($res);
        }
        exit;
    }

    /* Handle Notification Reciever List Updation */
    function handleNotificationReciever()
    {
        if (wp_verify_nonce(handlePostData('get_user_list'), "_cnmUserListGet") && is_admin()) {
            if (update_user_meta(handlePostData('_userLogin'), 'comment_notification_forward', handlePostData('forwardTo'))) {
                wp_send_json('Comment Notification Forward Added');
            }
        }
        exit;
    }

    public function _cnmAddAdminPages()
    {
        $page_title = 'Comment Notification Manager';
        $menu_title = 'CNM';
        $capability = 'manage_options';
        $menu_slug = 'cnm-admin';
        $function = array($this, '_cnmManageUserNotification');
        $icon_url = 'dashicons-megaphone';
        $position = '15';

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
        wp_localize_script('jquery', '_cnmAjaxVars', array('_cnmgulnonc' => wp_create_nonce('get_user_list')));
    }


    public function _cnmManageUserNotification()
    {
        global $wpdb;
        $user_query = new WP_User_Query(array('blog_id' => 0, 'roles__not_in' => 'Subscriber'));
        /* User Loop*/
        require_once(__DIR__ . '/views/admin-interface.view.php');
    }
}
