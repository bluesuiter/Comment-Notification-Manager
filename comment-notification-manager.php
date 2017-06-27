<?php

/*
Plugin Name: Comment Notification Manager
Plugin URI: 
Description: 
Version: 0.6.06
Author: BlueSuiter
Author URI: 
Donate link: 
License: GPL2
*/

    if(!defined('ABSPATH'))
    {
        die;
    }


    if(file_exists(dirname(__FILE__) . '/admin/admin-interface.php'))
    {
        require_once(dirname(__FILE__) . '/admin/admin-interface.php');
        
        $_cnmAdminInterface = new _cnmAdminInterface();

        add_action('wp_ajax_userList', array($_cnmAdminInterface, 'handleUserManageList'));
        add_action('wp_ajax_cnm_updater', array($_cnmAdminInterface, 'handleNotificationReciever'));
        
        add_action('admin_menu', array($_cnmAdminInterface, '_cnmAddAdminPages'));
    }


    if(file_exists(dirname(__FILE__) . '/mail/comment-email.php'))
    {
        require_once(dirname(__FILE__) . '/mail/comment-email.php');
        new Custom_Comment_Email();
    }
    

    function _cnmCommentModerationRecipients($emails, $comment_id) 
    {
        $comment = get_comment($comment_id);
        $post = get_post($comment->comment_post_ID);
        
        $forwardTo = get_user_meta($post->post_author, 'comment_notification_forward', true);
        
        /*/ Return the email which meant to recieve mail for a post.*/
        if (!empty(trim($forwardTo)))
        {
            $user = get_user_by('id', $forwardTo);
        }
        else
        {
            $user = get_user_by('id', $post->post_author);            
        }        

        return array($user->user_email);
    }
    add_filter('comment_moderation_recipients', '_cnmCommentModerationRecipients', 11, 2);
    add_filter('comment_notification_recipients', '_cnmCommentModerationRecipients', 11, 2);



    if(!function_exists('handlePostData'))
    {
        function handlePostData($key)
        {
            if(isset($_POST[$key]))
            {
                return $_POST[$key];
            }
        }
    }