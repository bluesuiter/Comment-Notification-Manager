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
        if (!empty($user_query->results)) {
            foreach ($user_query->results as $user) {
        ?>
                <tr>
                    <td><?= $user->data->display_name ?></td>
                    <td><?= isset($user->roles[0]) ? $user->roles[0] : '' ?></td>
                    <td>
                        <?php $meta = get_userdata(get_user_meta($user->ID, 'comment_notification_forward', true)) ?>
                        <?= (!empty($meta) ? $meta->user_login : 'Self'); ?>
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="add-forward" data-user="<?= $user->ID ?>">
                            <span class="dashicons dashicons-edit"></span>
                        </a>
                    </td>
                </tr>
        <?php
            }
        } else {
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
            <input type="hidden" name="_userLogin" value="" />
            <input type="hidden" name="action" value="cnm_updater" />
            <p><input type="submit" name="save_record" value="Save" /></p>
        </form>
    </div>
</div>
<div id="loding_plceholdr" class="cnmformcontainer" style="display:none;">
    <img src="<?php echo plugins_url('../img/clock.svg', __FILE__) ?>" alt="Loading" />
</div>
<link href="<?php echo plugins_url('../css/style.css', __FILE__) ?>" rel="stylesheet" />
<script src="<?php echo plugins_url('../js/script.js', __FILE__) ?>"></script>