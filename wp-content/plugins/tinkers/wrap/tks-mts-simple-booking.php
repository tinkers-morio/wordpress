<?php

/**
 * post_contentに登録されているデータがMTS-Simple-Bookingのカレンダー表示の場合
 * ショートコードの文字列内に挿入されている予約品目IDから、そのショートコードが定義されている
 * 固定ページを取得してページのリンクに変換して返す
 * mts-simple-booking.phpのget_permalink_by_slug関数を参考に
 */
function tks_mts_get_permalink_by_mts_article_id($article_id) {
	global $wpdb;
	$sql = $wpdb->prepare( 
		"
		SELECT ID 
		FROM $wpdb->posts
		WHERE 
		   post_status='publish'
		   AND post_content like '%%_calendar%%' 
		   AND post_content like '%%id=\"%d\"%%'
		", 
		$article_id
	);
	
	$post_id = $wpdb->get_col($sql);

	if (empty($post_id)) {
		return false;
	}

	return get_permalink($post_id[0]);
}

/**
 * ユーザーが予約しているリストを返す
 */
function tks_mts_get_users_booking($user_id=null,$order=array()){
	if (!class_exists('MTSSB_Booking')) {
		//require_once(WP_PLUGIN_DIR . '/mts-simple-booking/mtssb-booking.php');
		return;
	}	
	$mts_booking = new MTSSB_Booking;

	if (empty($user_id)){
		$user_id = get_current_user_id();
	}

	// 予約利用日降順
	if (empty($order)) {
		$order = array(
			'key' => 'booking_time',
			'direction' => 'asc',
		);
	}
	 
	return $mts_booking->get_users_booking($user_id, 0, 10000, $order);

}

/**
 * 全ての予約可能リストを取得する
 */
function tks_mts_get_all_articles(){
	if (!class_exists('MTSSB_Article')){
		return;
	}		
	//-----予約可能一覧の表示-----
	return MTSSB_Article::get_all_articles();
}

/**
 * 予約品目が1回限りの予約のみを受け付ける設定になっているか否かを取得
 * @param int $article_id 予約ID
 */
function tks_mts_is_ontime($article_id){
	//設定を取得する
	$opt_yoyaku_ids = get_option( tks_const::TKSOPT_MTS_YOYAKU_IDS );
	$opt_yoyaku_onetime = get_option( tks_const::TKSOPT_MTS_YOYAKU_ONETIME );	//1回のみの予約受付
	//Emptyの場合は、設定されていない
	if (empty($opt_yoyaku_ids)) return false;
	//設定はカンマ区切りで保存されているので、配列に変換
	$ary_ids = explode(',', $opt_yoyaku_ids);
	$ary_onetime = explode(',', $opt_yoyaku_onetime);
	//予約IDが配列に存在するか
	$y_idx = array_search($article_id, $ary_ids);	

	if (empty($ary_onetime[$y_idx])) return false;
	
	return ($ary_onetime[$y_idx] == 'yes')? true : false;

}

/**
 * 予約に紐づくZoomのリンクを取得する
 */
function tks_mts_get_zoom_url($article_id){
	$zoom_link_head = get_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMLINK);
	$opt_yoyaku_zoom_ids = get_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMIDS );	//ZoomIDsを取得

	//設定を取得する
	$opt_yoyaku_ids = get_option( tks_const::TKSOPT_MTS_YOYAKU_IDS );
	//Emptyの場合は、設定されていない
	if (empty($opt_yoyaku_ids)) return "";
	//設定はカンマ区切りで保存されているので、配列に変換
	$ary_ids = explode(',', $opt_yoyaku_ids);
	//予約IDが配列に存在するか
	$y_idx = array_search($article_id, $ary_ids);
	//ZoomIDが設定されていなければ空文字を返す
	if (empty($opt_yoyaku_zoom_ids[$y_idx])) return "";
	//$test = $opt_yoyaku_zoom_ids[$y_idx];
	return trailingslashit($zoom_link_head) . explode(',',$opt_yoyaku_zoom_ids)[$y_idx];

}