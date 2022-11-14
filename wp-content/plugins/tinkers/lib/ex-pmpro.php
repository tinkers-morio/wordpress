<?php

/** 
 * This stops GlotPress from translating Paid Memberships Pro and loads the language files instead.
 * Add this code to your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 * これにより、GlotPressがPaid Memberships Proを翻訳するのを停止し、代わりに言語ファイルをロードします。 *このコードをPMProカスタマイズプラグインに追加します
 * https://gist.github.com/andrewlimaza/c3e63d969e7c8421bb4da8a53a459b0c
 */
 
 function pmpro_disable_auto_translation( $retval, $item ) {
    // disable automatic translations for Paid Memberships Pro
    if ( 'plugin' === $item->type && 'paid-memberships-pro' === $item->slug ) {
        return false;
    }
    return $retval;
}
add_filter( 'auto_update_translation', 'pmpro_disable_auto_translation', 10, 2 );

/**
 * アクセス許可の確認（PaidMemberShipPro）
 * リーダーのアクセス権を確認し、子供のページアクセスの判定を行う
 */
// add_filter('pmpro_has_membership_access_filter','tks_pmpro_has_membership_access_filter',5,4);
// function tks_pmpro_has_membership_access_filter($hasaccess, $mypost, $myuser, $post_membership_levels){

//     if (!is_user_logged_in() ){
//         return $hasaccess;
//     }

//     global $current_user;
//     $user_id = $current_user->ID;

//     //管理者の場合はアクセス無制限
//     if (user_can($user_id, 'administrator')){
//         return true;
//     }
	
// 	//グループリーダーの場合は、PMPの設定に従う
//     if (tks_learndash_is_group_leader_user($user_id)){
//         return $hasaccess;
//     }

//     //リーダーではない場合
//     if (!tks_learndash_is_group_leader_user($user_id)){
//         //生徒の場合は、親であるリーダーの権限を確認
//         $leader = tks_get_leader_of_student($user_id);
//         //親がいる場合のみ
//         if (!empty($leader)){
//             //親であるリーダーのプランを取得
//             $membership = tks_pmpro_get_member($leader["user_id"]);
//             if (!empty($membership)){
//                 //親のアクセス制限に従う
//                 remove_filter("pmpro_has_membership_access_filter","tks_pmpro_has_membership_access_filter",5);
//                 $ret = pmpro_has_membership_access($mypost->ID,$leader["user_id"],false);
//                 add_filter('pmpro_has_membership_access_filter','tks_pmpro_has_membership_access_filter',5,4);
//                 return $ret;
//             }
//         }
//     }

//     return $hasaccess;
// }

/**
 * 管理者の場合は、PaidMembershipのログ(レポート)記録を無効にする
 * Remove stats tracking for admins.
 * Add this code to your site by following this guide - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
//↓↓ここから
 function pmpro_remove_admin_tracking_skip() {
	if ( current_user_can( 'administrator' ) ) {
		remove_action( 'wp_head', 'pmpro_report_login_wp_views' );
	}
}
add_action( 'init', 'pmpro_remove_admin_tracking_skip' );

function pmpro_pre_login_check( $user_login, $user ) {
	if ( $user->has_cap( 'administrator' ) ) {
		remove_action( 'wp_login', 'pmpro_report_login_wp_login', 10, 2 );
	}
}
add_action( 'wp_login', 'pmpro_pre_login_check', 9, 2 );

function pmpro_remove_visits_check() {
	if ( current_user_can( 'administrator' ) ) {
		remove_action( 'wp', 'pmpro_report_login_wp_visits' );
	}
}
add_action( 'wp', 'pmpro_remove_visits_check', 9 );
//↑↑ここまで

/**
 * 顧客へのメールをカスタマイズする
 */
add_filter("pmpro_email_data", 
function($this_data, $instance){

    $this_data["login_link"] = home_url();
    return $this_data;
},10,2);





//↓動的にプランを作れるなら作って人数を指定してチェックアウト・・できないかなぁ？？
/**
 * チェックアウト画面によって入力された生徒数に応じて金額を変える
 */
// function my_pmpro_checkout_level($level) {
// 	//ゲートウェイによって分けるならこちら
// 	//if( $_REQUEST['gateway'] == 'stripe' ){

// 	if( ! empty( $_REQUEST['student_count'] )){
// 		$add_count = $_REQUEST['student_count'];
// 	}
// 	if( ! empty( $_SESSION['student_count'] )){
// 		$add_count = $_SESSION['student_count'];
// 	}

// 	if( ! empty( $add_count ) ) {
// 		$add_count = intval($add_count);
// 		if ($add_count == 0) return $level;
// 		//$level->initial_payment = $level->initial_payment + 100;	//初期費用を更新するならこちら
// 		//定期払いの金額を更新する(現在の金額　+　(生徒数×生徒単価[税込み]))
// 		$level->billing_amount = $level->billing_amount + ($add_count * (tks_const::PRICE_STUDENT_PER_PERSON * 1.1));
// 		//生徒を追加できるのは、プラン2以降なので、2で取得した人数に生徒数を加算する
// 		$max_student_count = tks_get_can_regist_student_count(2) + $add_count;
// 		//データベースへ保存
// 		update_user_meta(get_current_user_id(),tks_const::TKSOPT_MAX_STUDENT_COUNT,$max_student_count);	
// 	}

// 	return $level;
// }
// add_filter("pmpro_checkout_level", "my_pmpro_checkout_level");