<?php

function config_lession_hint()
{
    if (tks_is_highlevel_mission_lesson_topic_page()) {
        global $wpdb, $post;
        $current_user_id = get_current_user_id();
        $current_post_id = $post->ID;

        //if (!is_legal_course($current_post_id)) {
        //    return;
        //}

        $list_hints = $wpdb->get_results("SELECT used_hint FROM `tks_highlevel_grades` WHERE `user_id` = $current_user_id AND `post_id` = $current_post_id ");

        $used_hint = get_used_hint($current_user_id, $current_post_id);
        ?>
        <script>
            jQuery("#hint_info").text("使用ヒント数は<?php echo $used_hint; ?>です");
            jQuery(".list_hint_warp > li").each(function() {
                var index = jQuery(this).index();

                var result = check_hint(index);

                if (result) {
                    jQuery(this).find('p').addClass('actived');
                }
            });

            function check_hint(index) {
                var tempArray = <?php echo json_encode($list_hints); ?>;

                var result = tempArray.map(function(a) {
                    return a.used_hint;
                });

                for (var i = 0; i < result.length; i++) {

                    var regexp = index;

                    if (result[i].match(regexp)) {
                        return true;
                    }
                }
                return false;
            }
        </script>
    <?php
        }
    }

    add_action('wp_footer', 'config_lession_hint', 10);

    function get_used_hint($current_user_id, $current_post_id)
    {
        global $wpdb;

        $used_hint = $wpdb->get_results("SELECT * FROM `tks_highlevel_grades` WHERE `user_id` = $current_user_id AND `post_id` = $current_post_id GROUP BY `used_hint`", ARRAY_A);
        if ($used_hint) {
            $used_hint = count($used_hint);
        } else {
            $used_hint = 0;
        }

        return $used_hint;
    }

    function tinker_create_highlevel_db()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = 'tks_highlevel_grades';

        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
        id int(10) NOT NULL AUTO_INCREMENT,
		user_id  INT(20) NOT NULL,
		post_id  int(20) NOT NULL,
		used_hint int(11) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    add_action('admin_init', 'tinker_create_highlevel_db');

    function save_hint_status()
    {

        $url = wp_get_referer();

        $post_id = url_to_postid($url);

        if (!tks_is_highlevel_mission_lesson_topic_page($post_id)){
        //if (!is_legal_course($post_id)) {
            die();
        }

        global $wpdb;

        $hint_id = sanitize_text_field($_POST['hint_id']);

        $user_id = get_current_user_id();

        $table_name = "tks_highlevel_grades";

        $insert_used_hint = $wpdb->insert($table_name, array(
            'id' => null,
            'user_id' => $user_id,
            'post_id' => $post_id,
            'used_hint' => $hint_id
        ));

        //    if ($insert_used_hint) {
        //
        //    }
        die();
    }

    add_action('wp_ajax_save_hint_status', 'save_hint_status');
    add_action('wp_ajax_nopriv_save_hint_status', 'save_hint_status');

    function show_details_student()
    {
        global $wpdb;

        $course_id = sanitize_text_field($_POST['corse_id']);

        $user_login =  sanitize_text_field($_POST['user_login']);

        $user = get_user_by('login', $user_login);

        $user_id = $user->ID;

        $user_display_name = $user->display_name;

        $list_lession = $wpdb->get_results("SELECT post_id FROM `wp_postmeta` WHERE meta_key = 'course_id' AND meta_value = $course_id");

        ob_start();
        ?>
    <div class="student_details_course">
        <div class="student_name"><?php echo $user_display_name; ?></div>
        <table>
            <thead>
                <tr>
                    <th>ミッション名</th>
                    <th>ヒント数</th>
                    <th>使用ヒント数</th>
                    <th>証</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (count($list_lession) > 0) {
                        foreach ($list_lession as $lession) {
                            $lession_id = $lession->post_id;
                            $lession_title = get_the_title($lession_id);
                            $total_hint = get_post_meta($lession_id, 'total_hint');
                            $total_hint = ($total_hint && count($total_hint) > 0) ? $total_hint[0] : 0;
                            $total_used_hint = get_used_hint($user_id, $lession_id);
                            $activity_status = $wpdb->get_results("SELECT activity_status FROM `wp_learndash_user_activity` WHERE `user_id` = $user_id AND post_id = $lession_id");
                            $activity_status = ($activity_status && $activity_status[0]->activity_status == '1') ? '完了' : '';
                            ?>
                        <tr>
                            <td><?php echo $lession_title ?></td>
                            <td><?php echo $total_hint ?></td>
                            <td><?php echo $total_used_hint; ?></td>
                            <td><?php echo $activity_status; ?></td>
                        </tr>
                <?php
                        }
                    }
                    ?>
            </tbody>
        </table>
    </div>
<?php
    echo ob_get_clean();
    die();
}

add_action('wp_ajax_show_details_student', 'show_details_student');
add_action('wp_ajax_nopriv_show_details_student', 'show_details_student');
