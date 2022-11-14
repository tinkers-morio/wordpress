<?php
/*
Plugin Name: Tinkers-plugin for lms
Plugin URI: https://www.tinkers.jp
Description: TinkersLMS プラグインカスタマイズ(LearnDash,BadgOS,ProfileBuilder,if-menu,PaidMemberSubscription) for LMS
Author: Tinkers programing school
Version: 1.1
Author URI: https://www.tinkers.jp
*/
 

define('TKS_PLUGIN_DIR', plugin_dir_path(__FILE__));

include_once(TKS_PLUGIN_DIR . '/lib/shortcode.php');
include_once(TKS_PLUGIN_DIR . '/lib/common.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-if-menu.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-learndash.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-profile-builder-pro.php');
include_once(TKS_PLUGIN_DIR . '/lib/page-custom.php');
include_once(TKS_PLUGIN_DIR . '/lib/shortcode.php');
include_once(TKS_PLUGIN_DIR . '/lib/tks-debug.php');
include_once(TKS_PLUGIN_DIR . '/lib/admin.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-badgeos.php');
include_once(TKS_PLUGIN_DIR . '/lib/data-access.php');
include_once(TKS_PLUGIN_DIR . '/lib/kti-config.php');
include_once(TKS_PLUGIN_DIR . '/lib/class-tks-const.php');
include_once(TKS_PLUGIN_DIR . '/lib/class-tks-const-str.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-account-profile.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-checkout.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-confirm.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-discount.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-levels.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-ristrict.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-pmpro-email.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-mts-simple-booking.php');
include_once(TKS_PLUGIN_DIR . '/lib/ex-astra.php');
include_once(TKS_PLUGIN_DIR . '/wrap/tks-learndash.php');
include_once(TKS_PLUGIN_DIR . '/wrap/tks-pmpro.php');
include_once(TKS_PLUGIN_DIR . '/wrap/tks-badgeos.php');
include_once(TKS_PLUGIN_DIR . '/wrap/tks-mts-simple-booking.php');
include_once(TKS_PLUGIN_DIR . '/lib/tks-help.php');
include_once(TKS_PLUGIN_DIR . '/templates/control-template.php');
include_once(TKS_PLUGIN_DIR . '/lib/tks-gettext.php');

//include_once(TKS_PLUGIN_DIR . '/res/tks-block-emails.php');

add_action( 'admin_menu', 'register_tinkers_custom_menu_page' );
function register_tinkers_custom_menu_page(){
    add_menu_page( 'Tinkers用設定', 'Tinkers用設定',
    'manage_options', 'custompage', 'tks_setting_page', '', 6 ); 
}
 
function tks_setting_page(){
    
	if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }


	// フィールドとオプション名の変数
	$hidden_field_name = 'mt_submit_hidden';

	$fid_visible_payment_page = 'tkschk_visible_payment_page';
	//映像終了時メッセージタイトル設定用フィールド名
	$fid_video_at_end_message_title = 'tks_video_at_end_message_title';
	//映像終了時メッセージ設定用フィールド名
	$fid_video_at_end_message = 'tks_video_at_end_message';
	//コースID(レベルID)設定用フィールド名
	$fid_plan_basic = 'tksopt_plan_basic';
	$fid_plan_regular = 'tks_plan_regular';
	$fid_plan_advance1 = 'tks_plan_advance1';
	$fid_plan_advance2 = 'tks_plan_advance2';
	$fid_plan_advance3 = 'tks_plan_advance3';
	$fid_plan_advance4 = 'tks_plan_advance4';
	$fid_plan_advance5 = 'tks_plan_advance5';
	$fid_plan_advance6 = 'tks_plan_advance6';
	$fid_plan_advance7 = 'tks_plan_advance7';
	$fid_plan_advance8 = 'tks_plan_advance8';
	$fid_plan_advance9 = 'tks_plan_advance9';
	$fid_plan_advance10 = 'tks_plan_advance10';
	$fid_plan_advance11 = 'tks_plan_advance11';
	$fid_plan_advance12 = 'tks_plan_advance12';
	$fid_plan_advance13 = 'tks_plan_advance13';
	$fid_plan_advance14 = 'tks_plan_advance14';
	$fid_plan_advance15 = 'tks_plan_advance15';
	$fid_plan_advance16 = 'tks_plan_advance16';
	$fid_plan_advance17 = 'tks_plan_advance17';
	$fid_plan_advance18 = 'tks_plan_advance18';
	$fid_plan_advance19 = 'tks_plan_advance19';
	$fid_plan_advance20 = 'tks_plan_advance20';

	//登録可能数設定用フィールド名
	$fid_plan_basic_students_count = 'tksopt_plan_basic_students_count';
	$fid_plan_regular_students_count = 'tks_plan_regular_students_count';
	$fid_plan_advance1_students_count = 'tks_plan_advance1_students_count';
	$fid_plan_advance2_students_count = 'tks_plan_advance2_students_count';
	$fid_plan_advance3_students_count = 'tks_plan_advance3_students_count';
	$fid_plan_advance4_students_count = 'tks_plan_advance4_students_count';
	$fid_plan_advance5_students_count = 'tks_plan_advance5_students_count';
	$fid_plan_advance6_students_count = 'tks_plan_advance6_students_count';
	$fid_plan_advance7_students_count = 'tks_plan_advance7_students_count';
	$fid_plan_advance8_students_count = 'tks_plan_advance8_students_count';
	$fid_plan_advance9_students_count = 'tks_plan_advance9_students_count';
	$fid_plan_advance10_students_count = 'tks_plan_advance10_students_count';
	$fid_plan_advance11_students_count = 'tks_plan_advance11_students_count';
	$fid_plan_advance12_students_count = 'tks_plan_advance12_students_count';
	$fid_plan_advance13_students_count = 'tks_plan_advance13_students_count';
	$fid_plan_advance14_students_count = 'tks_plan_advance14_students_count';
	$fid_plan_advance15_students_count = 'tks_plan_advance15_students_count';
	$fid_plan_advance16_students_count = 'tks_plan_advance16_students_count';
	$fid_plan_advance17_students_count = 'tks_plan_advance17_students_count';
	$fid_plan_advance18_students_count = 'tks_plan_advance18_students_count';
	$fid_plan_advance19_students_count = 'tks_plan_advance19_students_count';
	$fid_plan_advance20_students_count = 'tks_plan_advance20_students_count';

	//リーダーが受講可能コース指定用画面フィールド名
	$fid_plan_basic_course = 'tksopt_plan_basic_course';
	$fid_plan_regular_course = 'tks_plan_regular_course';
	$fid_plan_advance1_course = 'tks_plan_advance1_course';
	$fid_plan_advance2_course = 'tks_plan_advance2_course';
	$fid_plan_advance3_course = 'tks_plan_advance3_course';
	$fid_plan_advance4_course = 'tks_plan_advance4_course';
	$fid_plan_advance5_course = 'tks_plan_advance5_course';
	$fid_plan_advance6_course = 'tks_plan_advance6_course';
	$fid_plan_advance7_course = 'tks_plan_advance7_course';
	$fid_plan_advance8_course = 'tks_plan_advance8_course';
	$fid_plan_advance9_course = 'tks_plan_advance9_course';
	$fid_plan_advance10_course = 'tks_plan_advance10_course';
	$fid_plan_advance11_course = 'tks_plan_advance11_course';
	$fid_plan_advance12_course = 'tks_plan_advance12_course';
	$fid_plan_advance13_course = 'tks_plan_advance13_course';
	$fid_plan_advance14_course = 'tks_plan_advance14_course';
	$fid_plan_advance15_course = 'tks_plan_advance15_course';
	$fid_plan_advance16_course = 'tks_plan_advance16_course';
	$fid_plan_advance17_course = 'tks_plan_advance17_course';
	$fid_plan_advance18_course = 'tks_plan_advance18_course';
	$fid_plan_advance19_course = 'tks_plan_advance19_course';
	$fid_plan_advance20_course = 'tks_plan_advance20_course';
	//生徒とリーダーが受講可能コース指定用画面フィールド名
	$fid_plan_basic_course_student = 'tksopt_plan_basic_course_student';
	$fid_plan_regular_course_student = 'tks_plan_regular_course_student';
	$fid_plan_advance1_course_student = 'tks_plan_advance1_course_student';
	$fid_plan_advance2_course_student = 'tks_plan_advance2_course_student';
	$fid_plan_advance3_course_student = 'tks_plan_advance3_course_student';
	$fid_plan_advance4_course_student = 'tks_plan_advance4_course_student';
	$fid_plan_advance5_course_student = 'tks_plan_advance5_course_student';
	$fid_plan_advance6_course_student = 'tks_plan_advance6_course_student';
	$fid_plan_advance7_course_student = 'tks_plan_advance7_course_student';
	$fid_plan_advance8_course_student = 'tks_plan_advance8_course_student';
	$fid_plan_advance9_course_student = 'tks_plan_advance9_course_student';
	$fid_plan_advance10_course_student = 'tks_plan_advance10_course_student';
	$fid_plan_advance11_course_student = 'tks_plan_advance11_course_student';
	$fid_plan_advance12_course_student = 'tks_plan_advance12_course_student';
	$fid_plan_advance13_course_student = 'tks_plan_advance13_course_student';
	$fid_plan_advance14_course_student = 'tks_plan_advance14_course_student';
	$fid_plan_advance15_course_student = 'tks_plan_advance15_course_student';
	$fid_plan_advance16_course_student = 'tks_plan_advance16_course_student';
	$fid_plan_advance17_course_student = 'tks_plan_advance17_course_student';
	$fid_plan_advance18_course_student = 'tks_plan_advance18_course_student';
	$fid_plan_advance19_course_student = 'tks_plan_advance19_course_student';
	$fid_plan_advance20_course_student = 'tks_plan_advance20_course_student';

	//プラン(レベル)の説明設定用フィールド名 ※この設定はplansページで使用されます
	$fid_plan_basic_overview = "tksopt_plan_basic_overview";
	$fid_plan_regular_overview = "tksopt_plan_regular_overview";
	$fid_plan_advance1_overview = "tksopt_plan_advance1_overview";
	$fid_plan_advance2_overview = "tksopt_plan_advance2_overview";
	$fid_plan_advance3_overview = "tksopt_plan_advance3_overview";
	$fid_plan_advance4_overview = "tksopt_plan_advance4_overview";
	$fid_plan_advance5_overview = "tksopt_plan_advance5_overview";
	$fid_plan_advance6_overview = "tksopt_plan_advance6_overview";
	$fid_plan_advance7_overview = "tksopt_plan_advance7_overview";
	$fid_plan_advance8_overview = "tksopt_plan_advance8_overview";
	$fid_plan_advance9_overview = "tksopt_plan_advance9_overview";
	$fid_plan_advance10_overview = "tksopt_plan_advance10_overview";
	$fid_plan_advance11_overview = "tksopt_plan_advance11_overview";
	$fid_plan_advance12_overview = "tksopt_plan_advance12_overview";
	$fid_plan_advance13_overview = "tksopt_plan_advance13_overview";
	$fid_plan_advance14_overview = "tksopt_plan_advance14_overview";
	$fid_plan_advance15_overview = "tksopt_plan_advance15_overview";
	$fid_plan_advance16_overview = "tksopt_plan_advance16_overview";
	$fid_plan_advance17_overview = "tksopt_plan_advance17_overview";
	$fid_plan_advance18_overview = "tksopt_plan_advance18_overview";
	$fid_plan_advance19_overview = "tksopt_plan_advance19_overview";
	$fid_plan_advance20_overview = "tksopt_plan_advance20_overview";

	//予約用
	$fid_yoyaku_id = "tksopt_yoyaku_id";
	$fid_yoyaku_onetime_only =  "tksopt_yoyaku_onetime_only";
	$fid_yoyaku_disable =  "tksopt_yoyaku_disable";
	$fid_yoyaku_zoom_id = "tksopt_yoyaku_zoom_id";
	$fld_yoyaku_zoom_link = "tksopt_yoyaku_zoom_link";

	$fid_mail_subject = 'tksopt_mail_subject';
	$fid_mail_body = 'tksopt_mail_body';

	// データベースから既存のオプション値を取得
	$opt_val_visible_payment_page = get_option( tks_const::TKSOPT_VISIBLE_PAYMENT_PAGE, 'no' );
	$opt_val_video_at_end_message_title = get_option( tks_const::TKSOPT_VIDO_AT_END_MESSAGE_TITLE, '' );'';
	$opt_val_video_at_end_message = get_option( tks_const::TKSOPT_VIDO_AT_END_MESSAGE, '' );
	$opt_val_video_at_end_message = str_replace('\\\n','{n}', $opt_val_video_at_end_message);

	//プランのオプションを取得
	$opt_val_plan_basic = get_option( tks_const::TKSOPT_PLAN_BASIC);
	$opt_val_plan_regular = get_option( tks_const::TKSOPT_PLAN_REGULAR);
	$opt_val_plan_advance1 = get_option( tks_const::TKSOPT_PLAN_ADVANCE1);
	$opt_val_plan_advance2 = get_option( tks_const::TKSOPT_PLAN_ADVANCE2);
	$opt_val_plan_advance3 = get_option( tks_const::TKSOPT_PLAN_ADVANCE3);
	$opt_val_plan_advance4 = get_option( tks_const::TKSOPT_PLAN_ADVANCE4);
	$opt_val_plan_advance5 = get_option( tks_const::TKSOPT_PLAN_ADVANCE5);
	$opt_val_plan_advance6 = get_option( tks_const::TKSOPT_PLAN_ADVANCE6);
	$opt_val_plan_advance7 = get_option( tks_const::TKSOPT_PLAN_ADVANCE7);
	$opt_val_plan_advance8 = get_option( tks_const::TKSOPT_PLAN_ADVANCE8);
	$opt_val_plan_advance9 = get_option( tks_const::TKSOPT_PLAN_ADVANCE9);
	$opt_val_plan_advance10 = get_option( tks_const::TKSOPT_PLAN_ADVANCE10);
	$opt_val_plan_advance11 = get_option( tks_const::TKSOPT_PLAN_ADVANCE11);
	$opt_val_plan_advance12 = get_option( tks_const::TKSOPT_PLAN_ADVANCE12);
	$opt_val_plan_advance13 = get_option( tks_const::TKSOPT_PLAN_ADVANCE13);
	$opt_val_plan_advance14 = get_option( tks_const::TKSOPT_PLAN_ADVANCE14);
	$opt_val_plan_advance15 = get_option( tks_const::TKSOPT_PLAN_ADVANCE15);
	$opt_val_plan_advance16 = get_option( tks_const::TKSOPT_PLAN_ADVANCE16);
	$opt_val_plan_advance17 = get_option( tks_const::TKSOPT_PLAN_ADVANCE17);
	$opt_val_plan_advance18 = get_option( tks_const::TKSOPT_PLAN_ADVANCE18);
	$opt_val_plan_advance19 = get_option( tks_const::TKSOPT_PLAN_ADVANCE19);
	$opt_val_plan_advance20 = get_option( tks_const::TKSOPT_PLAN_ADVANCE20);
	//登録可能生徒数を取得
	$opt_val_plan_basic_students_count = get_option( tks_const::TKSOPT_PLAN_BASIC_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_BASIC_STUDENTS_COUNT_DEF);
	$opt_val_plan_regular_students_count = get_option( tks_const::TKSOPT_PLAN_REGULAR_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_REGULAR_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance1_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance2_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance3_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance4_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance5_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance6_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE6_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE6_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance7_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE7_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE7_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance8_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE8_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE8_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance9_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE9_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE9_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance10_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE10_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE10_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance11_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE11_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE11_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance12_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE12_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE12_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance13_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE13_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE13_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance14_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE14_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE14_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance15_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE15_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE15_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance16_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE16_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE16_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance17_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE17_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE17_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance18_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE18_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE18_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance19_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE19_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE19_STUDENTS_COUNT_DEF);
	$opt_val_plan_advance20_students_count = get_option( tks_const::TKSOPT_PLAN_ADVANCE20_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE20_STUDENTS_COUNT_DEF);
	//受講可能コースを取得(リーダー用)
	$opt_val_plan_basic_course = get_option( tks_const::TKSOPT_PLAN_BASIC_COURSE);
	$opt_val_plan_regular_course = get_option( tks_const::TKSOPT_PLAN_REGULAR_COURSE);
	$opt_val_plan_advance1_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE1_COURSE);
	$opt_val_plan_advance2_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE2_COURSE);
	$opt_val_plan_advance3_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE3_COURSE);
	$opt_val_plan_advance4_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE4_COURSE);
	$opt_val_plan_advance5_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE5_COURSE);
	$opt_val_plan_advance6_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE6_COURSE);
	$opt_val_plan_advance7_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE7_COURSE);
	$opt_val_plan_advance8_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE8_COURSE);
	$opt_val_plan_advance9_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE9_COURSE);
	$opt_val_plan_advance10_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE10_COURSE);
	$opt_val_plan_advance11_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE11_COURSE);
	$opt_val_plan_advance12_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE12_COURSE);
	$opt_val_plan_advance13_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE13_COURSE);
	$opt_val_plan_advance14_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE14_COURSE);
	$opt_val_plan_advance15_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE15_COURSE);
	$opt_val_plan_advance16_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE16_COURSE);
	$opt_val_plan_advance17_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE17_COURSE);
	$opt_val_plan_advance18_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE18_COURSE);
	$opt_val_plan_advance19_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE19_COURSE);
	$opt_val_plan_advance20_course = get_option( tks_const::TKSOPT_PLAN_ADVANCE20_COURSE);
	//受講可能コースを取得（生徒とリーダー共通）
	$opt_val_plan_basic_course_student = get_option( tks_const::TKSOPT_PLAN_BASIC_COURSE_STUDENT);
	$opt_val_plan_regular_course_student = get_option( tks_const::TKSOPT_PLAN_REGULAR_COURSE_STUDENT);
	$opt_val_plan_advance1_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE1_COURSE_STUDENT);
	$opt_val_plan_advance2_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE2_COURSE_STUDENT);
	$opt_val_plan_advance3_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE3_COURSE_STUDENT);
	$opt_val_plan_advance4_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE4_COURSE_STUDENT);
	$opt_val_plan_advance5_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE5_COURSE_STUDENT);
	$opt_val_plan_advance6_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE6_COURSE_STUDENT);
	$opt_val_plan_advance7_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE7_COURSE_STUDENT);
	$opt_val_plan_advance8_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE8_COURSE_STUDENT);
	$opt_val_plan_advance9_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE9_COURSE_STUDENT);
	$opt_val_plan_advance10_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE10_COURSE_STUDENT);
	$opt_val_plan_advance11_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE11_COURSE_STUDENT);
	$opt_val_plan_advance12_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE12_COURSE_STUDENT);
	$opt_val_plan_advance13_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE13_COURSE_STUDENT);
	$opt_val_plan_advance14_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE14_COURSE_STUDENT);
	$opt_val_plan_advance15_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE15_COURSE_STUDENT);
	$opt_val_plan_advance16_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE16_COURSE_STUDENT);
	$opt_val_plan_advance17_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE17_COURSE_STUDENT);
	$opt_val_plan_advance18_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE18_COURSE_STUDENT);
	$opt_val_plan_advance19_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE19_COURSE_STUDENT);
	$opt_val_plan_advance20_course_student = get_option( tks_const::TKSOPT_PLAN_ADVANCE20_COURSE_STUDENT);
	//プラン(レベル)の説明文(概要)を取得
	$opt_val_plan_basic_overview = get_option( tks_const::TKSOPT_PLAN_BASIC_OVERVIEW);
	$opt_val_plan_regular_overview = get_option( tks_const::TKSOPT_PLAN_REGULAR_OVERVIEW);
	$opt_val_plan_advance1_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE1_OVERVIEW);
	$opt_val_plan_advance2_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE2_OVERVIEW);
	$opt_val_plan_advance3_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE3_OVERVIEW);
	$opt_val_plan_advance4_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE4_OVERVIEW);
	$opt_val_plan_advance5_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE5_OVERVIEW);
	$opt_val_plan_advance6_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE6_OVERVIEW);
	$opt_val_plan_advance7_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE7_OVERVIEW);
	$opt_val_plan_advance8_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE8_OVERVIEW);
	$opt_val_plan_advance9_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE9_OVERVIEW);
	$opt_val_plan_advance10_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE10_OVERVIEW);
	$opt_val_plan_advance11_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE11_OVERVIEW);
	$opt_val_plan_advance12_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE12_OVERVIEW);
	$opt_val_plan_advance13_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE13_OVERVIEW);
	$opt_val_plan_advance14_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE14_OVERVIEW);
	$opt_val_plan_advance15_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE15_OVERVIEW);
	$opt_val_plan_advance16_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE16_OVERVIEW);
	$opt_val_plan_advance17_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE17_OVERVIEW);
	$opt_val_plan_advance18_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE18_OVERVIEW);
	$opt_val_plan_advance19_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE19_OVERVIEW);
	$opt_val_plan_advance20_overview = get_option( tks_const::TKSOPT_PLAN_ADVANCE20_OVERVIEW);

	//請求メールのオプションを取得
	$opt_val_mail_subject = get_option( tks_const::TKSOPT_MAIL_SUBJECT );
    $opt_val_mail_body = get_option( tks_const::TKSOPT_MAIL_BODY );
	//予約のオプションを取得
	$opt_val_mts_yoyaku_ids = get_option( tks_const::TKSOPT_MTS_YOYAKU_IDS );
	$opt_val_mts_yoyaku_onetime = get_option( tks_const::TKSOPT_MTS_YOYAKU_ONETIME );	//1回のみの予約受付
	$opt_val_mts_yoyaku_disable = get_option( tks_const::TKSOPT_MTS_YOYAKU_DISABLE );	//予約の使用不可
	$opt_val_mts_yoyaku_zoom_ids= get_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMIDS );	//予約に紐づけるZoomID
	$opt_val_mts_yoyaku_zoom_link = get_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMLINK );	//Zoomリンク

    // ユーザーが何か情報を POST したかどうかを確認
	// POST していれば、隠しフィールドに 'Y' が設定されている
	if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
		// POST されたデータを取得
		//***グループリーダーの権限が勝手に外される時の対応 */
		if (isset($_POST['Submit_role'])){
			$update_log = 'グループリーダーの権限';
			check_role_at_login();	//グループリーダーの権限を復活
		}
		/*** 機能を制限する登録を行う(各ポスト) */
		if (isset($_POST['Submit_global'])){
			
			$update_log = 'グローバル設定';

			//申込情報詳細ページの閲覧許可
			$opt_val_visible_payment_page = $_POST[ $fid_visible_payment_page ];
			//動画視聴完了後の表示メッセージ(グローバル)
			$opt_val_video_at_end_message_title = $_POST[ $fid_video_at_end_message_title ];
			$opt_val_video_at_end_message = $_POST[ $fid_video_at_end_message ];
			
			// POST された値をデータベースに保存
			update_option( tks_const::TKSOPT_VISIBLE_PAYMENT_PAGE, $opt_val_visible_payment_page );
			update_option( tks_const::TKSOPT_VIDO_AT_END_MESSAGE_TITLE, $opt_val_video_at_end_message_title );
			update_option( tks_const::TKSOPT_VIDO_AT_END_MESSAGE, str_replace('{n}','\\\n', $opt_val_video_at_end_message) );
		}

		if (isset($_POST['Submit_mail'])){
			
			$update_log = 'メール設定';
			
			//請求メール関連
			$opt_val_mail_subject = $_POST[ $fid_mail_subject ];
			$opt_val_mail_body = $_POST[ $fid_mail_body ];
			
			update_option( tks_const::TKSOPT_MAIL_SUBJECT, $opt_val_mail_subject );
			update_option( tks_const::TKSOPT_MAIL_BODY, $opt_val_mail_body );
		}
				
		/*** 機能を制限する登録を行う(各ポスト) */
		if (isset($_POST['Submit_restrict'])){
			
			$update_log = 'プラン設定';
		
			/******画面更新のため変数更新 ******/

			/*************************************************************************
			リクエストから値を取得
			*************************************************************************/
			//リクエストからプラン(レベル)ＩＤ取得
			$opt_val_plan_basic = $_POST[ $fid_plan_basic ];
			$opt_val_plan_regular = $_POST[ $fid_plan_regular ];
			$opt_val_plan_advance1 = $_POST[ $fid_plan_advance1 ];
			$opt_val_plan_advance2 = $_POST[ $fid_plan_advance2 ];
			$opt_val_plan_advance3 = $_POST[ $fid_plan_advance3 ];
			$opt_val_plan_advance4 = $_POST[ $fid_plan_advance4 ];
			$opt_val_plan_advance5 = $_POST[ $fid_plan_advance5 ];
			$opt_val_plan_advance6 = $_POST[ $fid_plan_advance6 ];
			$opt_val_plan_advance7 = $_POST[ $fid_plan_advance7 ];
			$opt_val_plan_advance8 = $_POST[ $fid_plan_advance8 ];
			$opt_val_plan_advance9 = $_POST[ $fid_plan_advance9 ];
			$opt_val_plan_advance10 = $_POST[ $fid_plan_advance10 ];
			$opt_val_plan_advance11 = $_POST[ $fid_plan_advance11 ];
			$opt_val_plan_advance12 = $_POST[ $fid_plan_advance12 ];
			$opt_val_plan_advance13 = $_POST[ $fid_plan_advance13 ];
			$opt_val_plan_advance14 = $_POST[ $fid_plan_advance14 ];
			$opt_val_plan_advance15 = $_POST[ $fid_plan_advance15 ];
			$opt_val_plan_advance16 = $_POST[ $fid_plan_advance16 ];
			$opt_val_plan_advance17 = $_POST[ $fid_plan_advance17 ];
			$opt_val_plan_advance18 = $_POST[ $fid_plan_advance18 ];
			$opt_val_plan_advance19 = $_POST[ $fid_plan_advance19 ];
			$opt_val_plan_advance20 = $_POST[ $fid_plan_advance20 ];

			//リクエストから講師のみ受講可能コース取得
			$opt_val_plan_basic_course = $_POST[ $fid_plan_basic_course ];
			$opt_val_plan_regular_course = $_POST[ $fid_plan_regular_course ];
			$opt_val_plan_advance1_course = $_POST[ $fid_plan_advance1_course ];
			$opt_val_plan_advance2_course = $_POST[ $fid_plan_advance2_course ];
			$opt_val_plan_advance3_course = $_POST[ $fid_plan_advance3_course ];
			$opt_val_plan_advance4_course = $_POST[ $fid_plan_advance4_course ];
			$opt_val_plan_advance5_course = $_POST[ $fid_plan_advance5_course ];
			$opt_val_plan_advance6_course = $_POST[ $fid_plan_advance6_course ];
			$opt_val_plan_advance7_course = $_POST[ $fid_plan_advance7_course ];
			$opt_val_plan_advance8_course = $_POST[ $fid_plan_advance8_course ];
			$opt_val_plan_advance9_course = $_POST[ $fid_plan_advance9_course ];
			$opt_val_plan_advance10_course = $_POST[ $fid_plan_advance10_course ];
			$opt_val_plan_advance11_course = $_POST[ $fid_plan_advance11_course ];
			$opt_val_plan_advance12_course = $_POST[ $fid_plan_advance12_course ];
			$opt_val_plan_advance13_course = $_POST[ $fid_plan_advance13_course ];
			$opt_val_plan_advance14_course = $_POST[ $fid_plan_advance14_course ];
			$opt_val_plan_advance15_course = $_POST[ $fid_plan_advance15_course ];
			$opt_val_plan_advance16_course = $_POST[ $fid_plan_advance16_course ];
			$opt_val_plan_advance17_course = $_POST[ $fid_plan_advance17_course ];
			$opt_val_plan_advance18_course = $_POST[ $fid_plan_advance18_course ];
			$opt_val_plan_advance19_course = $_POST[ $fid_plan_advance19_course ];
			$opt_val_plan_advance20_course = $_POST[ $fid_plan_advance20_course ];
			
			//リクエストから生徒とリーダー共通受講可能コース取得
			$opt_val_plan_basic_course_student = $_POST[ $fid_plan_basic_course_student ];			
			$opt_val_plan_regular_course_student = $_POST[ $fid_plan_regular_course_student ];
			$opt_val_plan_advance1_course_student = $_POST[ $fid_plan_advance1_course_student ];
			$opt_val_plan_advance2_course_student = $_POST[ $fid_plan_advance2_course_student ];
			$opt_val_plan_advance3_course_student = $_POST[ $fid_plan_advance3_course_student ];
			$opt_val_plan_advance4_course_student = $_POST[ $fid_plan_advance4_course_student ];
			$opt_val_plan_advance5_course_student = $_POST[ $fid_plan_advance5_course_student ];
			$opt_val_plan_advance6_course_student = $_POST[ $fid_plan_advance6_course_student ];
			$opt_val_plan_advance7_course_student = $_POST[ $fid_plan_advance7_course_student ];
			$opt_val_plan_advance8_course_student = $_POST[ $fid_plan_advance8_course_student ];
			$opt_val_plan_advance9_course_student = $_POST[ $fid_plan_advance9_course_student ];
			$opt_val_plan_advance10_course_student = $_POST[ $fid_plan_advance10_course_student ];
			$opt_val_plan_advance11_course_student = $_POST[ $fid_plan_advance11_course_student ];
			$opt_val_plan_advance12_course_student = $_POST[ $fid_plan_advance12_course_student ];
			$opt_val_plan_advance13_course_student = $_POST[ $fid_plan_advance13_course_student ];
			$opt_val_plan_advance14_course_student = $_POST[ $fid_plan_advance14_course_student ];
			$opt_val_plan_advance15_course_student = $_POST[ $fid_plan_advance15_course_student ];
			$opt_val_plan_advance16_course_student = $_POST[ $fid_plan_advance16_course_student ];
			$opt_val_plan_advance17_course_student = $_POST[ $fid_plan_advance17_course_student ];
			$opt_val_plan_advance18_course_student = $_POST[ $fid_plan_advance18_course_student ];
			$opt_val_plan_advance19_course_student = $_POST[ $fid_plan_advance19_course_student ];
			$opt_val_plan_advance20_course_student = $_POST[ $fid_plan_advance20_course_student ];

			//リクエストから登録生徒可能人数取得
			$opt_val_plan_basic_count = $_POST[ $fid_plan_basic_students_count ];
			$opt_val_plan_regular_count = $_POST[ $fid_plan_regular_students_count ];
			$opt_val_plan_advance1_count = $_POST[ $fid_plan_advance1_students_count ];
			$opt_val_plan_advance2_count = $_POST[ $fid_plan_advance2_students_count ];
			$opt_val_plan_advance3_count = $_POST[ $fid_plan_advance3_students_count ];
			$opt_val_plan_advance4_count = $_POST[ $fid_plan_advance4_students_count ];
			$opt_val_plan_advance5_count = $_POST[ $fid_plan_advance5_students_count ];
			$opt_val_plan_advance6_count = $_POST[ $fid_plan_advance6_students_count ];
			$opt_val_plan_advance7_count = $_POST[ $fid_plan_advance7_students_count ];
			$opt_val_plan_advance8_count = $_POST[ $fid_plan_advance8_students_count ];
			$opt_val_plan_advance9_count = $_POST[ $fid_plan_advance9_students_count ];
			$opt_val_plan_advance10_count = $_POST[ $fid_plan_advance10_students_count ];
			$opt_val_plan_advance11_count = $_POST[ $fid_plan_advance11_students_count ];
			$opt_val_plan_advance12_count = $_POST[ $fid_plan_advance12_students_count ];
			$opt_val_plan_advance13_count = $_POST[ $fid_plan_advance13_students_count ];
			$opt_val_plan_advance14_count = $_POST[ $fid_plan_advance14_students_count ];
			$opt_val_plan_advance15_count = $_POST[ $fid_plan_advance15_students_count ];
			$opt_val_plan_advance16_count = $_POST[ $fid_plan_advance16_students_count ];
			$opt_val_plan_advance17_count = $_POST[ $fid_plan_advance17_students_count ];
			$opt_val_plan_advance18_count = $_POST[ $fid_plan_advance18_students_count ];
			$opt_val_plan_advance19_count = $_POST[ $fid_plan_advance19_students_count ];
			$opt_val_plan_advance20_count = $_POST[ $fid_plan_advance20_students_count ];

			//リクエストからプラン(レベル)の概要分を取得
			$opt_val_plan_basic_overview = $_POST[ $fid_plan_basic_overview ];
			$opt_val_plan_regular_overview = $_POST[ $fid_plan_regular_overview ];
			$opt_val_plan_advance1_overview = $_POST[ $fid_plan_advance1_overview ];
			$opt_val_plan_advance2_overview = $_POST[ $fid_plan_advance2_overview ];
			$opt_val_plan_advance3_overview = $_POST[ $fid_plan_advance3_overview ];
			$opt_val_plan_advance4_overview = $_POST[ $fid_plan_advance4_overview ];
			$opt_val_plan_advance5_overview = $_POST[ $fid_plan_advance5_overview ];
			$opt_val_plan_advance6_overview = $_POST[ $fid_plan_advance6_overview ];
			$opt_val_plan_advance7_overview = $_POST[ $fid_plan_advance7_overview ];
			$opt_val_plan_advance8_overview = $_POST[ $fid_plan_advance8_overview ];
			$opt_val_plan_advance9_overview = $_POST[ $fid_plan_advance9_overview ];
			$opt_val_plan_advance10_overview = $_POST[ $fid_plan_advance10_overview ];
			$opt_val_plan_advance11_overview = $_POST[ $fid_plan_advance11_overview ];
			$opt_val_plan_advance12_overview = $_POST[ $fid_plan_advance12_overview ];
			$opt_val_plan_advance13_overview = $_POST[ $fid_plan_advance13_overview ];
			$opt_val_plan_advance14_overview = $_POST[ $fid_plan_advance14_overview ];
			$opt_val_plan_advance15_overview = $_POST[ $fid_plan_advance15_overview ];
			$opt_val_plan_advance16_overview = $_POST[ $fid_plan_advance16_overview ];
			$opt_val_plan_advance17_overview = $_POST[ $fid_plan_advance17_overview ];
			$opt_val_plan_advance18_overview = $_POST[ $fid_plan_advance18_overview ];
			$opt_val_plan_advance19_overview = $_POST[ $fid_plan_advance19_overview ];
			$opt_val_plan_advance20_overview = $_POST[ $fid_plan_advance20_overview ];

			/*************************************************************************
			更新する！
			*************************************************************************/
			//プラン(レベル)IDの更新
			update_option( tks_const::TKSOPT_PLAN_BASIC, $opt_val_plan_basic );
			update_option( tks_const::TKSOPT_PLAN_REGULAR, $opt_val_plan_regular );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE1, $opt_val_plan_advance1 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE2, $opt_val_plan_advance2 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE3, $opt_val_plan_advance3 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE4, $opt_val_plan_advance4 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE5, $opt_val_plan_advance5 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE6, $opt_val_plan_advance6 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE7, $opt_val_plan_advance7 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE8, $opt_val_plan_advance8 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE9, $opt_val_plan_advance9 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE10, $opt_val_plan_advance10 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE11, $opt_val_plan_advance11 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE12, $opt_val_plan_advance12 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE13, $opt_val_plan_advance13 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE14, $opt_val_plan_advance14 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE15, $opt_val_plan_advance15 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE16, $opt_val_plan_advance16 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE17, $opt_val_plan_advance17 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE18, $opt_val_plan_advance18 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE19, $opt_val_plan_advance19 );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE20, $opt_val_plan_advance20 );
			//登録可能生徒数の更新
			update_option( tks_const::TKSOPT_PLAN_BASIC_STUDENTS_COUNT, $opt_val_plan_basic_count );
			update_option( tks_const::TKSOPT_PLAN_REGULAR_STUDENTS_COUNT, $opt_val_plan_regular_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT, $opt_val_plan_advance1_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT, $opt_val_plan_advance2_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT, $opt_val_plan_advance3_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT, $opt_val_plan_advance4_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT, $opt_val_plan_advance5_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE6_STUDENTS_COUNT, $opt_val_plan_advance6_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE7_STUDENTS_COUNT, $opt_val_plan_advance7_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE8_STUDENTS_COUNT, $opt_val_plan_advance8_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE9_STUDENTS_COUNT, $opt_val_plan_advance9_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE10_STUDENTS_COUNT, $opt_val_plan_advance10_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE11_STUDENTS_COUNT, $opt_val_plan_advance11_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE12_STUDENTS_COUNT, $opt_val_plan_advance12_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE13_STUDENTS_COUNT, $opt_val_plan_advance13_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE14_STUDENTS_COUNT, $opt_val_plan_advance14_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE15_STUDENTS_COUNT, $opt_val_plan_advance15_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE16_STUDENTS_COUNT, $opt_val_plan_advance16_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE17_STUDENTS_COUNT, $opt_val_plan_advance17_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE18_STUDENTS_COUNT, $opt_val_plan_advance18_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE19_STUDENTS_COUNT, $opt_val_plan_advance19_count );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE20_STUDENTS_COUNT, $opt_val_plan_advance20_count );

			//リーダー専用受講可能コースの更新
			update_option( tks_const::TKSOPT_PLAN_BASIC_COURSE, $opt_val_plan_basic_course );
			update_option( tks_const::TKSOPT_PLAN_REGULAR_COURSE, $opt_val_plan_regular_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE1_COURSE, $opt_val_plan_advance1_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE2_COURSE, $opt_val_plan_advance2_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE3_COURSE, $opt_val_plan_advance3_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE4_COURSE, $opt_val_plan_advance4_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE5_COURSE, $opt_val_plan_advance5_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE6_COURSE, $opt_val_plan_advance6_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE7_COURSE, $opt_val_plan_advance7_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE8_COURSE, $opt_val_plan_advance8_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE9_COURSE, $opt_val_plan_advance9_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE10_COURSE, $opt_val_plan_advance10_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE11_COURSE, $opt_val_plan_advance11_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE12_COURSE, $opt_val_plan_advance12_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE13_COURSE, $opt_val_plan_advance13_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE14_COURSE, $opt_val_plan_advance14_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE15_COURSE, $opt_val_plan_advance15_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE16_COURSE, $opt_val_plan_advance16_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE17_COURSE, $opt_val_plan_advance17_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE18_COURSE, $opt_val_plan_advance18_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE19_COURSE, $opt_val_plan_advance19_course );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE20_COURSE, $opt_val_plan_advance20_course );

			//リーダー、生徒共通の受講可能コースの更新
			update_option( tks_const::TKSOPT_PLAN_BASIC_COURSE_STUDENT, $opt_val_plan_basic_course_student );
			update_option( tks_const::TKSOPT_PLAN_REGULAR_COURSE_STUDENT, $opt_val_plan_regular_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE1_COURSE_STUDENT, $opt_val_plan_advance1_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE2_COURSE_STUDENT, $opt_val_plan_advance2_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE3_COURSE_STUDENT, $opt_val_plan_advance3_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE4_COURSE_STUDENT, $opt_val_plan_advance4_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE5_COURSE_STUDENT, $opt_val_plan_advance5_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE6_COURSE_STUDENT, $opt_val_plan_advance6_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE7_COURSE_STUDENT, $opt_val_plan_advance7_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE8_COURSE_STUDENT, $opt_val_plan_advance8_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE9_COURSE_STUDENT, $opt_val_plan_advance9_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE10_COURSE_STUDENT, $opt_val_plan_advance10_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE11_COURSE_STUDENT, $opt_val_plan_advance11_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE12_COURSE_STUDENT, $opt_val_plan_advance12_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE13_COURSE_STUDENT, $opt_val_plan_advance13_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE14_COURSE_STUDENT, $opt_val_plan_advance14_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE15_COURSE_STUDENT, $opt_val_plan_advance15_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE16_COURSE_STUDENT, $opt_val_plan_advance16_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE17_COURSE_STUDENT, $opt_val_plan_advance17_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE18_COURSE_STUDENT, $opt_val_plan_advance18_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE19_COURSE_STUDENT, $opt_val_plan_advance19_course_student );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE20_COURSE_STUDENT, $opt_val_plan_advance20_course_student );

			//プラン(レベル)の概要文の更新
			update_option( tks_const::TKSOPT_PLAN_BASIC_OVERVIEW, $opt_val_plan_basic_overview );
			update_option( tks_const::TKSOPT_PLAN_REGULAR_OVERVIEW, $opt_val_plan_regular_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE1_OVERVIEW, $opt_val_plan_advance1_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE2_OVERVIEW, $opt_val_plan_advance2_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE3_OVERVIEW, $opt_val_plan_advance3_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE4_OVERVIEW, $opt_val_plan_advance4_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE5_OVERVIEW, $opt_val_plan_advance5_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE6_OVERVIEW, $opt_val_plan_advance6_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE7_OVERVIEW, $opt_val_plan_advance7_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE8_OVERVIEW, $opt_val_plan_advance8_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE9_OVERVIEW, $opt_val_plan_advance9_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE10_OVERVIEW, $opt_val_plan_advance10_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE11_OVERVIEW, $opt_val_plan_advance11_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE12_OVERVIEW, $opt_val_plan_advance12_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE13_OVERVIEW, $opt_val_plan_advance13_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE14_OVERVIEW, $opt_val_plan_advance14_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE15_OVERVIEW, $opt_val_plan_advance15_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE16_OVERVIEW, $opt_val_plan_advance16_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE17_OVERVIEW, $opt_val_plan_advance17_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE18_OVERVIEW, $opt_val_plan_advance18_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE19_OVERVIEW, $opt_val_plan_advance19_overview );
			update_option( tks_const::TKSOPT_PLAN_ADVANCE20_OVERVIEW, $opt_val_plan_advance20_overview );


			//受講コースの更新
			// $group_id = tks_get_group_id($user_id);
			// //コースの登録(グループ)
			// tks_learndash_set_group_enrolled_courses($group_id, $courses);
			// //個別コース(リーダーのみのコース)
			// tks_learndash_user_set_enrolled_courses($user_id, $courses_leader);	
	
		}

		/*** 予約関係の登録を行う(各ポスト) */
		if (isset($_POST['Submit_yoyaku'])){
			$ary_ids = array();			//予約ID
			$ary_ontimes = array();		//予約一回のみ
			$ary_disable = array();		//予約の使用不可状態
			$ary_zoomids = array();		//予約に紐づけるZoomID
			$i = 1;
			//一旦配列に格納
			while( isset($_POST[$fid_yoyaku_onetime_only . $i]) ){
				$ary_ids[] = $_POST[$fid_yoyaku_id . $i];		
				$ary_ontimes[] =  $_POST[$fid_yoyaku_onetime_only . $i];
				$ary_disable[] =  $_POST[$fid_yoyaku_disable . $i];
				$ary_zoomids[] = $_POST[$fid_yoyaku_zoom_id . $i];
				$i++;
			}
			//カンマ区切りの文字列に変換
			if (!empty($ary_ids)){
				$opt_val_mts_yoyaku_ids = implode(',', $ary_ids);
				$opt_val_mts_yoyaku_onetime = implode(',', $ary_ontimes);
				$opt_val_mts_yoyaku_disable = implode(',', $ary_disable);
				$opt_val_mts_yoyaku_zoom_ids = implode(',',$ary_zoomids);
			}
			//カンマ区切りの状態で保存
			update_option( tks_const::TKSOPT_MTS_YOYAKU_IDS, $opt_val_mts_yoyaku_ids );
			update_option( tks_const::TKSOPT_MTS_YOYAKU_ONETIME, $opt_val_mts_yoyaku_onetime );
			update_option( tks_const::TKSOPT_MTS_YOYAKU_DISABLE, $opt_val_mts_yoyaku_disable );
			update_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMIDS, $opt_val_mts_yoyaku_zoom_ids );
			//Zoomのリンクを保存
			$opt_val_mts_yoyaku_zoom_link = (isset($_POST[$fld_yoyaku_zoom_link]))? $_POST[$fld_yoyaku_zoom_link] : "" ;
			update_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMLINK, $opt_val_mts_yoyaku_zoom_link );
			
		}

		
        // 画面に「設定は保存されました」メッセージを表示

	?>
	<div class="updated"><p><strong><?php _e($update_log . 'が保存されました', 'tinkers' ); ?></strong></p></div>
	<?php
	}
	

	    // ここで設定編集画面を表示

	    echo '<div class="wrap">';

	    // ヘッダー

	    echo "<h2>" . __( 'Tinkersプラグイン設定', 'tinkers' ) . "</h2>";

	    // 設定用フォーム
	    
	?>

	<form name="form1" method="post" action="">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<!--
	<H3><?php _e("【価格設定】", 'tinkers' ); ?></h3>

	<p><?php _e("生徒フリー人数:", 'tinkers' ); ?> 
	<input type="text" name="<?php //echo $fid_sutudent_free_count; ?>" style="text-align: right;" value="<?php //echo $opt_val_student_free_count; ?>" size="1">
	<?php _e("※基本プランで許容される人数", 'tinkers' ); ?> 
	</p>

	<p><?php _e("生徒追加分価格（一人あたり）:", 'tinkers' ); ?> 
	<input type="text" name="<?php //echo $fid_sutudent_price; ?>" style="text-align: right;" value="<?php //echo $opt_val_student_price; ?>" size="3">
	<?php _e("円", 'tinkers' ); ?>
	</p><hr />
	<p>
-->
	<H3><?php _e("【GroupLeader権限復活】", 'tinkers' ); ?></h3>
		<p>現在のグループリーダーに付与されている権限</p>
		<?php
			//グループリーダーにユーザーを作成する権限があるかどうかをテスト的にチェックし警告
			if (!hasRoleForRoleGroup(get_role( 'group_leader' ),'create_users')){
				echo '<font color="red">※※今すぐ権限復活して下さい※※</font><br>';
			}
			//グループリーダーの全ての付与されている権限を表示する
			$role = get_role( 'group_leader' );
			foreach ($role->capabilities as $key => $value) {
				echo $key . ' = ' . ($value==1?'true':'false') . "<br>";
			}
		?>
		<p>
		<p class="submit">
		<label>※生徒が登録できないなど、権限が外れている場合のみに使用のこと！</label>
		<input type="submit" name="Submit_role" class="button-primary" value="グループリーダーの権限を復活"" />
		</p>
		
		<hr>
	<H3><?php _e("【グローバル設定】", 'tinkers' ); ?></h3>
		<p>
		<label><?php _e( 'グループリーダーのプロフィールに支払情報ページを表示する。', 'tinkers' ); ?></label>
		<select name="<?php echo $fid_visible_payment_page; ?>">
            <option value="yes" <?php echo ($opt_val_visible_payment_page == "yes")?  'selected="selected"' : '' ?>>表示する</option>
            <option value="no" <?php echo ($opt_val_visible_payment_page != "yes")?  'selected="selected"' : '' ?>>表示しない</option>
        </select>
		<br><strong><font color="green">※「表示する」を設定した場合、リーダー毎の設定を無視し全員強制表示</font></strong>
		</p>
		<table style="font-size: 10pt; line-height: 200%;">
		<tr>
		<td align="left" colspan=2><label><?php _e( '動画視聴完了時に表示するメッセージ<br>(各レッスン・トピック設定ページで動画視聴後の動作をメッセージを表示する設定にして下さい)', 'tinkers' ); ?></label></td>
		</tr>
		<tr>
		<td><label><?php _e( 'タイトル', 'tinkers' ); ?></label></td>
		<td><input type="text" name="<?php echo $fid_video_at_end_message_title; ?>" value="<?php echo $opt_val_video_at_end_message_title; ?>" size="40"></td>
		</tr>
		<tr>
		<td><label><?php _e( 'メッセージ', 'tinkers' ); ?></label></td>
		<td><input type="text" name="<?php echo $fid_video_at_end_message; ?>" value="<?php echo $opt_val_video_at_end_message; ?>" size="100">※改行は{n}と入力</td>
		</tr>
		</table>
		<p class="submit">
		<input type="submit" name="Submit_global" class="button-primary" value="グローバル設定を保存"" />
		</p>

		<hr>
	<H3><?php _e("【プランの設定】", 'tinkers' ); ?></h3>
		<table>
		<tr>
		<th width="150"></th>
		<th align="left" width="250"><?php _e("紐づけるプランID", 'tinkers' ); ?> </th>
		<th align="left" width="250"><?php _e("受講可能コースID(共通)", 'tinkers' ); ?> </th>
		<th align="left" width="250"><?php _e("受講可能コースID(リーダー)", 'tinkers' ); ?> </th>
		<th align="left" width="150"><?php _e("登録可能生徒数", 'tinkers' ); ?> </th>
		<th align="left" width="150"><?php _e("プラン概要説明", 'tinkers' ); ?> </th>
		</tr>
		<tr>
		<th valign="top" align="left" ><?php _e("Basicプラン:", 'tinkers' ); ?></th>
		<td valign="top"><input type="text" name="<?php echo $fid_plan_basic; ?>" value="<?php echo $opt_val_plan_basic; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_basic); ?></td>
		<td valign="top"><input type="text" name="<?php echo $fid_plan_basic_course_student; ?>" value="<?php echo $opt_val_plan_basic_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_basic_course_student); ?></td>
		<td valign="top"><input type="text" name="<?php echo $fid_plan_basic_course; ?>" value="<?php echo $opt_val_plan_basic_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_basic_course); ?></td>
		<td valign="top"><input type="text" name="<?php echo $fid_plan_basic_students_count; ?>" value="<?php echo $opt_val_plan_basic_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_basic_overview; ?>" value="<?php echo $opt_val_plan_basic_overview; ?>" size="80"></td>
		</tr>
		<tr><td align="center"><h3>↓</h3></td><td></td><td align="center"><h3>+</h3></td><td align="center"><h3>+</h3></td></tr>
		<tr>
		<th valign="top" align="left"><?php _e("Regularプラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_regular; ?>" value="<?php echo $opt_val_plan_regular; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_regular); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_regular_course_student; ?>" value="<?php echo $opt_val_plan_regular_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_regular_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_regular_course; ?>" value="<?php echo $opt_val_plan_regular_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_regular_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_regular_students_count; ?>" value="<?php echo $opt_val_plan_regular_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_regular_overview; ?>" value="<?php echo $opt_val_plan_regular_overview; ?>" size="80"></td>
		</tr>
		<tr><td align="center"><h3>↓</h3></td><td></td><td align="center"><h3>+</h3></td><td align="center"><h3>+</h3></td></tr>
		<!-- <tr><td colspan="5"><hr></td></tr> -->
		<tr>
		<th valign="top" align="left"><?php _e("Advance+1プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance1; ?>" value="<?php echo $opt_val_plan_advance1; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance1); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance1_course_student; ?>" value="<?php echo $opt_val_plan_advance1_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance1_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance1_course; ?>" value="<?php echo $opt_val_plan_advance1_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance1_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance1_students_count; ?>" value="<?php echo $opt_val_plan_advance1_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance1_overview; ?>" value="<?php echo $opt_val_plan_advance1_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+2プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance2; ?>" value="<?php echo $opt_val_plan_advance2; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance2); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance2_course_student; ?>" value="<?php echo $opt_val_plan_advance2_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance2_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance2_course; ?>" value="<?php echo $opt_val_plan_advance2_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance2_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance2_students_count; ?>" value="<?php echo $opt_val_plan_advance2_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance2_overview; ?>" value="<?php echo $opt_val_plan_advance2_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+3プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance3; ?>" value="<?php echo $opt_val_plan_advance3; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance3); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance3_course_student; ?>" value="<?php echo $opt_val_plan_advance3_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance3_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance3_course; ?>" value="<?php echo $opt_val_plan_advance3_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance3_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance3_students_count; ?>" value="<?php echo $opt_val_plan_advance3_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance3_overview; ?>" value="<?php echo $opt_val_plan_advance3_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+4プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance4; ?>" value="<?php echo $opt_val_plan_advance4; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance4); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance4_course_student; ?>" value="<?php echo $opt_val_plan_advance4_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance4_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance4_course; ?>" value="<?php echo $opt_val_plan_advance4_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance4_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance4_students_count; ?>" value="<?php echo $opt_val_plan_advance4_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance4_overview; ?>" value="<?php echo $opt_val_plan_advance4_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+5プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance5; ?>" value="<?php echo $opt_val_plan_advance5; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance5); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance5_course_student; ?>" value="<?php echo $opt_val_plan_advance5_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance5_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance5_course; ?>" value="<?php echo $opt_val_plan_advance5_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance5_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance5_students_count; ?>" value="<?php echo $opt_val_plan_advance5_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance5_overview; ?>" value="<?php echo $opt_val_plan_advance5_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+6プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance6; ?>" value="<?php echo $opt_val_plan_advance6; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance6); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance6_course_student; ?>" value="<?php echo $opt_val_plan_advance6_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance6_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance6_course; ?>" value="<?php echo $opt_val_plan_advance6_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance6_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance6_students_count; ?>" value="<?php echo $opt_val_plan_advance6_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance6_overview; ?>" value="<?php echo $opt_val_plan_advance6_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+7プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance7; ?>" value="<?php echo $opt_val_plan_advance7; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance7); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance7_course_student; ?>" value="<?php echo $opt_val_plan_advance7_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance7_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance7_course; ?>" value="<?php echo $opt_val_plan_advance7_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance7_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance7_students_count; ?>" value="<?php echo $opt_val_plan_advance7_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance7_overview; ?>" value="<?php echo $opt_val_plan_advance7_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+8プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance8; ?>" value="<?php echo $opt_val_plan_advance8; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance8); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance8_course_student; ?>" value="<?php echo $opt_val_plan_advance8_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance8_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance8_course; ?>" value="<?php echo $opt_val_plan_advance8_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance8_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance8_students_count; ?>" value="<?php echo $opt_val_plan_advance8_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance8_overview; ?>" value="<?php echo $opt_val_plan_advance8_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+9プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance9; ?>" value="<?php echo $opt_val_plan_advance9; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance9); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance9_course_student; ?>" value="<?php echo $opt_val_plan_advance9_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance9_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance9_course; ?>" value="<?php echo $opt_val_plan_advance9_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance9_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance9_students_count; ?>" value="<?php echo $opt_val_plan_advance9_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance9_overview; ?>" value="<?php echo $opt_val_plan_advance9_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+10プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance10; ?>" value="<?php echo $opt_val_plan_advance10; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance10); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance10_course_student; ?>" value="<?php echo $opt_val_plan_advance10_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance10_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance10_course; ?>" value="<?php echo $opt_val_plan_advance10_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance10_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance10_students_count; ?>" value="<?php echo $opt_val_plan_advance10_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance10_overview; ?>" value="<?php echo $opt_val_plan_advance10_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+11プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance11; ?>" value="<?php echo $opt_val_plan_advance11; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance11); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance11_course_student; ?>" value="<?php echo $opt_val_plan_advance11_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance11_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance11_course; ?>" value="<?php echo $opt_val_plan_advance11_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance11_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance11_students_count; ?>" value="<?php echo $opt_val_plan_advance11_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance11_overview; ?>" value="<?php echo $opt_val_plan_advance11_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+12プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance12; ?>" value="<?php echo $opt_val_plan_advance12; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance12); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance12_course_student; ?>" value="<?php echo $opt_val_plan_advance12_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance12_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance12_course; ?>" value="<?php echo $opt_val_plan_advance12_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance12_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance12_students_count; ?>" value="<?php echo $opt_val_plan_advance12_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance12_overview; ?>" value="<?php echo $opt_val_plan_advance12_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+13プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance13; ?>" value="<?php echo $opt_val_plan_advance13; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance13); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance13_course_student; ?>" value="<?php echo $opt_val_plan_advance13_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance13_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance13_course; ?>" value="<?php echo $opt_val_plan_advance13_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance13_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance13_students_count; ?>" value="<?php echo $opt_val_plan_advance13_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance13_overview; ?>" value="<?php echo $opt_val_plan_advance13_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+14プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance14; ?>" value="<?php echo $opt_val_plan_advance14; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance14); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance14_course_student; ?>" value="<?php echo $opt_val_plan_advance14_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance14_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance14_course; ?>" value="<?php echo $opt_val_plan_advance14_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance14_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance14_students_count; ?>" value="<?php echo $opt_val_plan_advance14_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance14_overview; ?>" value="<?php echo $opt_val_plan_advance14_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+15プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance15; ?>" value="<?php echo $opt_val_plan_advance15; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance15); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance15_course_student; ?>" value="<?php echo $opt_val_plan_advance15_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance15_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance15_course; ?>" value="<?php echo $opt_val_plan_advance15_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance15_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance15_students_count; ?>" value="<?php echo $opt_val_plan_advance15_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance15_overview; ?>" value="<?php echo $opt_val_plan_advance15_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+16プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance16; ?>" value="<?php echo $opt_val_plan_advance16; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance16); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance16_course_student; ?>" value="<?php echo $opt_val_plan_advance16_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance16_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance16_course; ?>" value="<?php echo $opt_val_plan_advance16_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance16_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance16_students_count; ?>" value="<?php echo $opt_val_plan_advance16_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance16_overview; ?>" value="<?php echo $opt_val_plan_advance16_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+17プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance17; ?>" value="<?php echo $opt_val_plan_advance17; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance17); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance17_course_student; ?>" value="<?php echo $opt_val_plan_advance17_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance17_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance17_course; ?>" value="<?php echo $opt_val_plan_advance17_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance17_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance17_students_count; ?>" value="<?php echo $opt_val_plan_advance17_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance17_overview; ?>" value="<?php echo $opt_val_plan_advance17_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+18プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance18; ?>" value="<?php echo $opt_val_plan_advance18; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance18); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance18_course_student; ?>" value="<?php echo $opt_val_plan_advance18_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance18_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance18_course; ?>" value="<?php echo $opt_val_plan_advance18_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance18_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance18_students_count; ?>" value="<?php echo $opt_val_plan_advance18_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance18_overview; ?>" value="<?php echo $opt_val_plan_advance18_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+19プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance19; ?>" value="<?php echo $opt_val_plan_advance19; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance19); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance19_course_student; ?>" value="<?php echo $opt_val_plan_advance19_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance19_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance19_course; ?>" value="<?php echo $opt_val_plan_advance19_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance19_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance19_students_count; ?>" value="<?php echo $opt_val_plan_advance19_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance19_overview; ?>" value="<?php echo $opt_val_plan_advance19_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<th valign="top" align="left"><?php _e("Advance+20プラン:", 'tinkers' ); ?></th>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance20; ?>" value="<?php echo $opt_val_plan_advance20; ?>" size="20"><?php echo _tks_pmpro_get_title_html($opt_val_plan_advance20); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance20_course_student; ?>" value="<?php echo $opt_val_plan_advance20_course_student; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance20_course_student); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance20_course; ?>" value="<?php echo $opt_val_plan_advance20_course; ?>" size="25"><?php echo tks_get_title_html($opt_val_plan_advance20_course); ?></td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance20_students_count; ?>" value="<?php echo $opt_val_plan_advance20_students_count; ?>" size="2">人</td>
		<td valign="top" ><input type="text" name="<?php echo $fid_plan_advance20_overview; ?>" value="<?php echo $opt_val_plan_advance20_overview; ?>" size="80"></td>
		</tr>

		<tr>
		<td></td> 
		<td colspan="4"><strong><font size="2" color="green">※複数ある場合は、カンマ区切りで設定する</font></strong></td>
		</tr>
		</table>
		
		</p>
		
		<p class="submit">
		<input type="submit" name="Submit_restrict" class="button-primary" value="プラン設定を保存"/>
		</p>
		
		<hr>
	<H3><?php _e("【予約の設定】", 'tinkers' ); ?></h3>	
		
		<table border="1">
			<tr>
				<th>ID</th>
				<th>予約品目</th>
				<th>1回のみ予約可能</th>
				<th>予約不可にする</th>
				<th>使用するZoomID</th>
			</tr>
			<?php
			//-----予約可能一覧の表示-----
			if (!empty($opt_val_mts_yoyaku_ids)){
				$ary_ids = explode(',', $opt_val_mts_yoyaku_ids);
				$ary_onetime = explode(',', $opt_val_mts_yoyaku_onetime);
				$ary_disable = explode(',', $opt_val_mts_yoyaku_disable);
				$ary_zoomids = explode(',', $opt_val_mts_yoyaku_zoom_ids);
			}	
			//全予約を取得する
			$articles = tks_mts_get_all_articles();
			if (!empty($articles)){
				$i=1;
				foreach ($articles as $key => $article) {
					$y_idx = array_search($key, $ary_ids);	
					$opt_val_mts_yoyaku_onetime = (empty($ary_onetime[$y_idx]))? 'no' : $ary_onetime[$y_idx];
					$opt_val_mts_yoyaku_disable = (empty($ary_disable[$y_idx]))? 'no' : $ary_disable[$y_idx];
					$opt_val_mts_yoyaku_zoom_ids = (empty($ary_zoomids[$y_idx]))? '' : $ary_zoomids[$y_idx];
				?>
					<tr>
						<td><input type="text" name="<?php echo $fid_yoyaku_id . $i ?>" value="<?php echo $key  ?>" readonly/></td>
						<td><?php echo $article["name"]  ?></td>
						<td>
						<select name="<?php echo $fid_yoyaku_onetime_only . $i; ?>">
							<option value="yes" <?php echo ($opt_val_mts_yoyaku_onetime == "yes")?  'selected="selected"' : '' ?>>はい</option>
							<option value="no" <?php echo ($opt_val_mts_yoyaku_onetime != "yes")?  'selected="selected"' : '' ?>>いいえ</option>
						</select>
						</td>
						<td>
						<select name="<?php echo $fid_yoyaku_disable . $i; ?>">
							<option value="yes" <?php echo ($opt_val_mts_yoyaku_disable == "yes")?  'selected="selected"' : '' ?>>はい</option>
							<option value="no" <?php echo ($opt_val_mts_yoyaku_disable != "yes")?  'selected="selected"' : '' ?>>いいえ</option>
						</select>
						<td><input type="text" name="<?php echo $fid_yoyaku_zoom_id . $i ?>" value="<?php echo $opt_val_mts_yoyaku_zoom_ids  ?>" /></td>
						</td>

					</tr>
				<?php	
					$i++;	
				}
			}else{
				echo 'MTSシンプル予約プラグインが停止中です';
			}
			?>
		</table>
		<p>
		<label for="<?php echo $fld_yoyaku_zoom_link ?>">ZoomのURL：</label><input type="text" name="<?php echo $fld_yoyaku_zoom_link ?>" value="<?php echo $opt_val_mts_yoyaku_zoom_link ?>"  style="width:30em;"/>	
		</p>
		<p class="submit">
		<input type="submit" name="Submit_yoyaku" class="button-primary" value="予約の設定を保存"/>
		</p>

		<hr>
	<?php

	//システムモードが法人の場合のみ表示する
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_HOUJIN){

		echo '<H3>' .  _e("【締め請求メール設定】", 'tinkers' ) . '</h3>';
		echo '<p>' . _e("件名:", 'tinkers' );
		echo '<input type="text" name="' . $fid_mail_subject . '" value="' . $opt_val_mail_subject . '" size="90">';
		echo '</p>';

		echo '<p>' .  _e("本文:", 'tinkers' ) . '</p><p>';
		echo '<textarea name="' . $fid_mail_body . '" rows="20" cols="100">' .  $opt_val_mail_body . '</textarea>';
		echo '</p>';
	
		echo '<p class="submit">';
		echo '<input type="submit" name="Submit_mail" class="button-primary" value="メール設定を保存" />';
		echo '</p>';
		echo '<hr />';
	}

	echo '</form>';
	echo '</div>';
	
}

// ユーザーの管理項目を追加
add_action('show_user_profile', 'Add_user_fields');
add_action('edit_user_profile', 'Add_user_fields');
function Add_user_fields($user) {

	if (!tks_learndash_is_group_leader_user($user->ID)) return;

	$selected = get_the_author_meta(tks_const::TKSOPT_USER_VISIBLE_PAYMENT_PAGE, $user->ID);
	if (empty($selected)){
		$selected = 'yes';	//設定値が取得できない場合はデフォルト値 yes
	}

?>
    <h3>Tinkers管理項目</h3>
    <table class="form-table">
        <tr>
            <td>
                <select name="tksopt_user_visible_payment_page" id="tksopt_user_visible_payment_page">
                    <option value="yes" <?php echo ($selected == "yes")?  'selected="selected"' : '' ?>>表示する</option>
                    <option value="no" <?php echo ($selected != "yes")?  'selected="selected"' : '' ?>>表示しない</option>
                </select>
            </td>
			<td><label for="tksopt_user_visible_payment_page">グループリーダーのアカウントメニューに支払情報ページを表示するか否か</label></td>
        </tr>
    </table>
<?php
}
add_action('personal_options_update', 'save_user_fields');
add_action('edit_user_profile_update', 'save_user_fields');
function save_user_fields($user_id) {
    if (! current_user_can('edit_user', $user_id))
        return false;

	if (isset($_POST['tksopt_user_visible_payment_page'])){
		update_user_meta($user_id, tks_const::TKSOPT_USER_VISIBLE_PAYMENT_PAGE, $_POST['tksopt_user_visible_payment_page']);
	}
}

//追加jsやCSSを追加
function second_tinker_assets()
{
	//global $post;
	//$tslug = $post->post_name;
	//$tid = get_the_ID();
	//クイズページの場合に出力するCSS
	if (tks_is_quiz_page()){
		wp_enqueue_style('tinker-style-quiz', plugin_dir_url(__FILE__) . '/css/styl-tks-quiz.css');
	}

	//レッスンやトピックは、ビデオがある前提でコントロールAPIをインクルード
	if (tks_is_lesson_topic_page()){
		tks_include_vimeo();
		tks_include_sweet_alert();
		tks_include_lesson_topics_style();
		tks_include_processing();
	}

	//LearnDashのレッスンかトピックページ且つ、タグがハイレベル,または、進捗一覧のページ
	if (is_page(tks_const::PAGE_STUDENT_LIST_PROGRESS) || tks_is_highlevel_mission_lesson_topic_page()){
    
		//tks_include_jqdialog();	//↓jqueryダイアログの代わりにsweet_alertを使用
		tks_include_sweet_alert();

		wp_enqueue_script('tinker-ajax-js', plugin_dir_url(__FILE__).'/js/tinker_ajax.js', array('jquery'), 1.0, true);
		wp_enqueue_script('bpopup-js', plugin_dir_url(__FILE__) .'/js/bpopup.js', array('jquery'), 1.0, true);
		wp_enqueue_style('tinker-style', plugin_dir_url(__FILE__) . '/css/kti-style.css');

		$register_ajax = array(
			'admin_ajax' => admin_url('admin-ajax.php')
		);

		wp_localize_script('tinker-ajax-js', 'tinker_handler_ajax', $register_ajax);
	}
	//生徒編集画面(リーダー用)の場合
	if (is_page(tks_const::PAGE_EDIT_STUDENT_FOR_LEADER) || is_page(tks_const::PAGE_EDIT_LEADER_FOR_ADMIN)){
		tks_include_sweet_alert();
	}
	
	//PMPRO自動生成ページの場合
	if (is_page(tks_const::PAGE_NEW_REGIST)){
		wp_enqueue_style('font-a', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css');
		wp_enqueue_script('jquery-jpostal',plugins_url('/js/jquery.jpostal.js', __FILE__ ));
		wp_enqueue_style('tinker-style-pmpro-checkout', plugin_dir_url(__FILE__) . '/css/style-pmpro-checkout.css');
		tks_include_baloon("regist");
	}
	//PaidMembersihpPROのプラン選択画面または支払い後の確認ページの場合
	//is_page( $pmpro_pages['checkout'] );これでも判定可能
	if (is_page(tks_const::PAGE_MEMBERSHIP_PLAN) || is_page(tks_const::PAGE_MEMBERSHIP_CONFIRM_DETAILS)){
		wp_enqueue_script('tks_pmpro-translate',plugins_url('/js/tks_pmpro_translate.js', __FILE__ ));
		wp_enqueue_script('tks_pmpro-confirm',plugins_url('/js/tks_pmpro_plan_confirm.js', __FILE__ ));
		if (is_page(tks_const::PAGE_MEMBERSHIP_CONFIRM_DETAILS)){
			wp_enqueue_style('tinker-style-pmpro-confirm-details', plugin_dir_url(__FILE__) . '/css/style-pmpro-confirm-details.css');
		}
	}
	//請求画面
	if (is_page(tks_const::PAGE_MEMBERSHIP_INVOICE) ){
		wp_enqueue_script('tks_paid_membership_pro-translate',plugins_url('/js/tks_pmpro_translate.js', __FILE__ ));
	}
	//プラン選択画面の場合
	if (is_page(tks_const::PAGE_MEMBERSHIP_PLANS)){
		wp_enqueue_script('tks_pmpro-translate_plans',plugins_url('/js/tks_pmpro_translate_plans.js', __FILE__ ));
		wp_enqueue_style('tinker-style-pmpro-plans', plugin_dir_url(__FILE__) . '/css/style-pmpro-plans.css');
	}
	//バッジ一覧ページの場合
	if (is_page(tks_const::PAGE_MY_ACHIEVEMENT)){
		wp_enqueue_style('style-badgeos', plugin_dir_url(__FILE__) . '/css/style-badgeos.css');
	}
	//ヘルプ用のバルーンを表示する(個人モードの場合)
	//if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		tks_baloon_for_help();
	//}
	
	//コースのホーム画面で、修了していれば紙吹雪を表示する(終了日のみ表示)
	tks_confetti_for_coruse();

	//サンプルリーダーの場合は、バルーンを表示するようにするためのインクルード(jquery.baloon)
	tks_baloon_enaueue_for_sampleuser();


}
add_action('wp_enqueue_scripts', 'second_tinker_assets');

/**
 * 投稿タイトルをカンマ区切りの引数から取得する(Tinker管理画面専用)
 * @param $post_ids_comma カンマ区切りのPostID
 */
function tks_get_title_html($post_ids_comma){
	$ret = '';
	$ary_post_id = explode(',',$post_ids_comma);
	if (!empty($ary_post_id)){
		foreach ($ary_post_id as $key => $post_id) {
			if (empty($post_id)) continue;
			$title = get_the_title($post_id);
			if (empty($title)){
				$ret = $ret . '<br><font size="0.7em" color="red">' . $post_id . ':Error</font>';
			}else{
				$ret = $ret . '<br><font size="0.7em" color="blue">' . $post_id . ':' . $title . '</font>';
			}
		}

		return  $ret;

	}else{
		return '';
	}
}

/**
 * 設定画面でプランによる更新前のコースと更新後のコースを比較して
 * 更新前データにしか存在しないコースは、制限を解除
 * 更新対象データのみに存在するコースは、制限を追加
 */
// function update_restriction($befor_plans, $new_plans, $befor_cources, $new_cources){
	
// 	//プランが変更された場合は、前回保存時のプランに関する制限データを削除する
// 	$befor_plan_ary = explode(',',$befor_plans);
// 	$new_plan_ary = explode(',',$new_plans);

// 	$befor_cource_ary = explode(',',$befor_cources);
// 	$new_cource_ary = explode(',',$new_cources);
	
// 	//更新前のデータにしか存在しないプランは削除
// 	$remove_plans = array_diff_ex($befor_plan_ary, $new_plan_ary);
// 	if (! empty($remove_plans)){
// 		delete_coruce_all_restrict( $befor_cource_ary, $remove_plans);
// 	}
// 	//更新前のデータにしか存在しないコースは削除
// 	$remove_cources = array_diff_ex($befor_cource_ary,$new_cource_ary);
// 	if (! empty($remove_cources)){
// 		delete_coruce_all_restrict( $remove_cources, $befor_plan_ary);
// 	}
// 	//更新対象データコースは更新
// 	//add_coruce_all_restrict($new_cource_ary, $new_plan_ary); 
// 	$update_cources = array_diff_ex($new_cource_ary,$befor_cource_ary);
// 	if (! empty($update_cources)){
// 		add_coruce_all_restrict($update_cources, $new_plan_ary);
// 	}
// }

/**
 * 機能制限を解除する(pm subscription)
 * コースに紐づく全レッスン、トピックも全て解除する(チェックを外す)
 */
// function delete_coruce_all_restrict($cource_ids = array(), $plan_ids = array()){
	
// 	if (empty($plan_ids) || count($plan_ids) == 0) return;
	
// 	//コースループ
// 	foreach ($cource_ids as $k_c => $cources_id) {
// 		//コース紐づく全レッスン取得
// 		$lessons = tks_learndash_get_lesson_list($cources_id);
		
// 		if (empty($lessons)) continue;
// 		//ID列のみ配列で抜き出す
// 		$lesson_list = array_column( $lessons, 'ID' );
// 		delete_restrict($lesson_list,$plan_ids);		//全レッスン解除
		
// 		foreach ( $lessons as $k_l => $lesson ) {
// 			//レッスン紐づく全トピック取得
// 			$topics = tks_learndash_get_topic_list($lesson->ID);
			
// 			if (empty($topics))	continue;
// 			//ID列のみ配列で抜き出す
// 			$topic_list = array_column( $topics, 'ID' );
// 			delete_restrict($topic_list,$plan_ids);		//全トピック解除
// 		}
// 	}
// }

/**
 * 機能制限を設定する(pm subscription)
 * コースに紐づく全レッスン、トピックも全て設定する(チェックをする)
 */
// function add_coruce_all_restrict($cource_ids = array(), $plan_ids = array()){
	
// 	if (empty($plan_ids) || count($plan_ids) == 0) return;

// 	//コースループ
// 	foreach ($cource_ids as $k_c => $cources_id) {
// 		//コース紐づく全レッスン取得	
// 		$lessons = tks_learndash_get_lesson_list($cources_id);
		
// 		if (empty($lessons)) continue;
// 		//ID列のみ配列化
// 		$lesson_list = array_column( $lessons, 'ID' );
// 		add_restrict($lesson_list,$plan_ids);			//設定
		
// 		foreach ( $lessons as $k_l => $lesson ) {
// 			//レッスン紐づく全トピック取得
// 			$topics = tks_learndash_get_topic_list($lesson->ID);
			
// 			if (empty($topics))	continue;
// 			//ID列のみ配列化
// 			$topic_list = array_column( $topics, 'ID' );
// 			add_restrict($topic_list,$plan_ids);		//設定
// 		}

// 	}
// }

/**
 * pm memberの機能制限meta_dataを作る
 */
// function add_restrict($post_ids = array(), $plan_ids = array()){
// 	foreach ($post_ids as $k_post => $post_id) {
// 		foreach ($plan_ids as $k_plan => $plan_id) {
// 			if (!empty($plan_id)){
// 				//既に設定(データ)が存在するかチェック
// 				$restrict_ary = get_post_meta($post_id, 'pms-content-restrict-subscription-plan',false);
// 				if (false == in_array( $plan_id, $restrict_ary, true )){
// 					//データが存在しなければ追加
// 					add_post_meta( $post_id, 'pms-content-restrict-subscription-plan', $plan_id );
// 				}
// 			}
// 		}		
// 	}
// }

/**
 * pm memberの機能制限meta_dataを削除する
 */
// function delete_restrict($post_ids = array(), $plan_ids = array()){

// 	foreach ($post_ids as $k_post => $post_id) {
// 		foreach ($plan_ids as $k_plan => $plan_id) {
// 			if (!empty($plan_id)){
// 				delete_post_meta($post_id,'pms-content-restrict-subscription-plan', $plan_id);
// 			}
// 		}		
// 	}
// }

// 固定ページにカテゴリーを設定
function add_categorie_to_pages(){
	register_taxonomy_for_object_type('category', 'page');
}
add_action('init','add_categorie_to_pages');
	
// カテゴリーアーカイブに固定ページを含める
function add_page_to_category_archive( $query ) {
   if ( $query->is_category== true && $query->is_main_query() ) {
   $query->set('post_type', array( 'post', 'page' ));
   }
}
add_action( 'pre_get_posts', 'add_page_to_category_archive' );

// 固定ページにタグを設定
function add_tag_to_page() {
	register_taxonomy_for_object_type('post_tag', 'page');
}
add_action('init', 'add_tag_to_page');
	
// タグアーカイブに固定ページを含める
function add_page_to_tag_archive( $obj ) {
	if ( is_tag() ) {
	$obj->query_vars['post_type'] = array( 'post', 'page' );
	}
}
add_action( 'pre_get_posts', 'add_page_to_tag_archive' );

/**
 * 権限グループの権限をチェック（その権限を持っているか否か）
 */
function hasRoleForRoleGroup($RoleGroup,$cap_name){
	if (isset($RoleGroup->capabilities[$cap_name]) &&
		!empty($RoleGroup->capabilities[$cap_name])){
		
		return true;	
	}
	
	return false;
}

/*
　*　グループリーダーの権限をチェックして、正常に戻す
*/
add_action( 'wp_login', function($user_login, $user){
	if ( 'group_leader' === $user->roles[0] ) {
		check_role_at_login();
	}
}, 10, 2 );

function check_role_at_login() {
	$role = get_role( 'group_leader' );
		
	if (!hasRoleForRoleGroup($role,'create_users')) $role->add_cap( 'create_users' );
	if (!hasRoleForRoleGroup($role,'delete_essays')) $role->add_cap( 'delete_essays' );
	if (!hasRoleForRoleGroup($role,'delete_others_assignments')) $role->add_cap( 'delete_others_assignments' );
	if (!hasRoleForRoleGroup($role,'delete_others_essays')) $role->add_cap( 'delete_others_essays' );
	if (!hasRoleForRoleGroup($role,'delete_published_assignments')) $role->add_cap( 'delete_published_assignments' );
	if (!hasRoleForRoleGroup($role,'delete_published_essays')) $role->add_cap( 'delete_published_essays' );
	if (!hasRoleForRoleGroup($role,'delete_users')) $role->add_cap( 'delete_users' );
	if (!hasRoleForRoleGroup($role,'edit_assignments')) $role->add_cap( 'edit_assignments' );
	if (!hasRoleForRoleGroup($role,'edit_essays')) $role->add_cap( 'edit_essays' );
	if (!hasRoleForRoleGroup($role,'edit_others_assignments')) $role->add_cap( 'edit_others_assignments' );
	if (!hasRoleForRoleGroup($role,'edit_others_essays')) $role->add_cap( 'edit_others_essays' );
	if (!hasRoleForRoleGroup($role,'edit_published_assignments')) $role->add_cap( 'edit_published_assignments' );
	if (!hasRoleForRoleGroup($role,'edit_published_essays')) $role->add_cap( 'edit_published_essays' );
	if (!hasRoleForRoleGroup($role,'edit_users')) $role->add_cap( 'edit_users' );
	if (!hasRoleForRoleGroup($role,'group_leader')) $role->add_cap( 'group_leader' );
	if (!hasRoleForRoleGroup($role,'list_users')) $role->add_cap( 'list_users' );
	if (!hasRoleForRoleGroup($role,'promote_users')) $role->add_cap( 'promote_users' );
	if (!hasRoleForRoleGroup($role,'propanel_widgets')) $role->add_cap( 'propanel_widgets' );
	if (!hasRoleForRoleGroup($role,'publish_essays')) $role->add_cap( 'publish_essays' );
	if (!hasRoleForRoleGroup($role,'read')) $role->add_cap( 'read' );
	if (!hasRoleForRoleGroup($role,'read_assignment')) $role->add_cap( 'read_assignment' );
	if (!hasRoleForRoleGroup($role,'read_essays')) $role->add_cap( 'read_essays' );
	if (!hasRoleForRoleGroup($role,'read_private_essays')) $role->add_cap( 'read_private_essays' );
	if (!hasRoleForRoleGroup($role,'remove_users')) $role->add_cap( 'remove_users' );
	if (!hasRoleForRoleGroup($role,'wpProQuiz_show_statistics')) $role->add_cap( 'wpProQuiz_show_statistics' );
}

/**
 * 設定画面にプランのタイトルを表示する
 */
function _tks_pmpro_get_title_html($level_id_comma){
	
	$ary_level_ids = explode(',',$level_id_comma);
	
	$levels = tks_pmpro_get_all_levels(true);

	$level_names="";
	
	foreach ($ary_level_ids as $key => $level_id) {
		$find = false;
		foreach ( $levels as $level ) {
			if ($level->id == $level_id){
				$find = true;
				$level_names = $level_names . "<br><font size='0.7em' color='blue'>" . $level_id . ":" . $level->name . "</font>";
				break;
			}
		}
		if (! $find){
			$level_names = $level_names . "<br><font size='0.7em' color='red'>" . $level_id . ":該当プランが見つかりません</font>";
		}
	}

	return $level_names;
}

