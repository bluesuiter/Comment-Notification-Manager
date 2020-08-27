<?php
    

class _cnmAdminInterface{


    public function __construct()
    {
        add_action('wp_ajax_userList', array($this, 'handleUserManageList'));
        add_action('wp_ajax_cnm_updater', array($this, 'handleNotificationReciever'));
    }

    /* Retrieve the userList */
    function handleUserManageList()
    {
        if(wp_verify_nonce(handlePostData('nonce'), "get_user_list") && is_admin())
        {
            $user = new WP_User_Query(array(
                        'blog' => 0, 
                        'exclude' => array(handlePostData('user')), 
                        'fields' => array('ID', 'user_login')
                    ));

            $forwardTo = get_user_meta(handlePostData('user'), 'comment_notification_forward', true);

            $res = '<option value="">Self</option>';
            foreach($user->results as $usr)
            {
                $res .= '<option '. ($forwardTo == $usr->ID ? 'selected' : '') .' value="'. $usr->ID .'">'. ucfirst($usr->user_login) .'</option>';
            }
            wp_send_json($res);
        }
        exit;
    }

    /* Handle Notification Reciever List Updation */
    function handleNotificationReciever()
    {
        if(wp_verify_nonce(handlePostData('get_user_list'), "_cnmUserListGet") && is_admin())
        {
            if(update_user_meta(handlePostData('_userLogin'), 'comment_notification_forward', handlePostData('forwardTo'))){
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
        ?>
        <h1>Comment Notificaiton Manger</h1>
        <table class="wp-list-table widefat fixed striped cnmTbl">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>User Role</th>
                    <th>Notificaitons Receiver</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        <?php 
        if($user_query->results)
        {
            foreach ($user_query->results as $user) 
            { 
        ?>
            <tr>
                <td><?php echo $user->data->display_name ?></td>
                <td><?php echo isset($user->roles{0}) ? $user->roles{0} : '' ?></td>
                <td>
                    <?php $meta = get_userdata(get_user_meta($user->ID, 'comment_notification_forward', true)) ?>
                    <?php echo (!empty($meta) ? $meta->user_login : 'Self'); ?>
                </td>
                <td>
                    <a href="javascript:void(0)" class="add-forward" data-user="<?php echo $user->ID ?>">
                        <span class="dashicons dashicons-edit"></span>
                    </a>
                </td>
            </tr>
        <?php
            }
        } 
        else 
        {
            echo '<tr><td colspan="5">No users found.</td></tr>';
        }
        ?>
            </tbody>
        </table>

        <div id="cnmformcontainer" class="cnmformcontainer" style="display:none;">
            <div class="inner_container">
                <span class="dashicons dashicons-no-alt _cnmClose"></span>
                <form class="_cnmForm" name="" action="" id="_cnmForm">
                    <p class="userList"><select name="forwardTo"></select></p>
                    <?php wp_nonce_field('_cnmUserListGet', 'get_user_list') ?>
                    <input type="hidden" name="_userLogin" value=""/>
                    <input type="hidden" name="action" value="cnm_updater"/>
                    <p><input type="submit" name="save_record" value="Save"/></p>
                </form>
            </div>
        </div>
        <div id="loding_plceholdr" class="cnmformcontainer" style="display:none;">
            <img src="<?php echo plugins_url('img/clock.svg', __FILE__) ?>" alt="Loading"/>
        </div>
        <link href="<?php echo plugins_url('css/style.css', __FILE__) ?>" rel="stylesheet"/>
        <script src="<?php echo plugins_url('js/script.js', __FILE__) ?>"></script>
        <?php   
    }
}
