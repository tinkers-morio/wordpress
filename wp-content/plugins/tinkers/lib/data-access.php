<?php

function tks_delete_leader_meta(){
	
	$users = tks_get_leaders();
	
	if( !empty( $users ) ) {
		foreach($users as $user){
			delete_user_meta( $user->user_id, 'tks_sendmail_his' );
		}
	}
}

function tks_init_leader_meta(){
	
	$users = tks_get_leaders();
	
	if( !empty( $users ) ) {
		foreach($users as $user){
			tks_update_usermeta_value( $user->user_id, 'tks_sendmail_his','未送信' );
		}
	}
}
function tks_get_leaders(){
	global $wpdb;
	$query = "SELECT * FROM {$wpdb->users} a LEFT JOIN {$wpdb->usermeta} b ON b.user_id=a.ID where b.meta_key like 'learndash_group_leaders_%'";
	return $wpdb->get_results( $wpdb->prepare($query,null) );
}

function tks_get_leaders_meta($user_id = -1){
	global $wpdb;
	//$query = "SELECT * FROM {$wpdb->users} a LEFT JOIN {$wpdb->usermeta} b ON b.user_id=a.ID where b.meta_key like 'learndash_group_leaders_%' ORDER BY b.meta_value IS NULL ASC, b.meta_value ASC";
	$query = "SELECT " .
    			"u.ID as user_id," .
				"u.user_login," .
				"u.display_name," .
			    "u.user_email," .
			    "m1.meta_value AS firstname," .
			    "m2.meta_value AS lastname," .
				"m3.meta_value as sendmaildate," .
				"m4.meta_value as group_id," .
				"m5.meta_value as school_name," .
				"m6.meta_value as zipcode," .
				"m7.meta_value as monthly_price," .
				"m8.meta_value as student_free_count," .
				"m9.meta_value as student_price," .
				"m10.meta_value as before_seikyu_tsuki" .
			" FROM {$wpdb->users} u" .
			" JOIN {$wpdb->usermeta} m1 ON (m1.user_id = u.ID AND m1.meta_key = 'first_name')" .
			" JOIN {$wpdb->usermeta} m2 ON (m2.user_id = u.ID AND m2.meta_key = 'last_name')" .
			" LEFT JOIN {$wpdb->usermeta} m3 ON (m3.user_id = u.ID AND m3.meta_key = 'tks_sendmail_his')" .
			" JOIN {$wpdb->usermeta} m4 ON (m4.user_id = u.ID AND m4.meta_key like 'learndash_group_leaders_%')" .
			" LEFT JOIN {$wpdb->usermeta} m5 ON (m5.user_id = u.ID AND m5.meta_key = 'tks_school_name')" .
			" LEFT JOIN {$wpdb->usermeta} m6 ON (m6.user_id = u.ID AND m6.meta_key = 'tks_zipcode')" .
			" LEFT JOIN {$wpdb->usermeta} m7 ON (m7.user_id = u.ID AND m7.meta_key = 'tks_monthly_price')" .
			" LEFT JOIN {$wpdb->usermeta} m8 ON (m8.user_id = u.ID AND m8.meta_key = 'tks_student_free_count')" .
			" LEFT JOIN {$wpdb->usermeta} m9 ON (m9.user_id = u.ID AND m9.meta_key = 'tks_student_price')" .
			" LEFT JOIN {$wpdb->usermeta} m10 ON (m10.user_id = u.ID AND m10.meta_key = 'tks_before_seikyu_tsuki')";
	
	if ($user_id > 0){
		$query = $query . " WHERE u.ID = %d";
		return $wpdb->get_row($wpdb->prepare($query, $user_id));
	}else{
		return $wpdb->get_results( $wpdb->prepare($query,null) );
	}

}

/*
 * グループの中で、生徒数のみを取得する（リーダーを省いた数を取得）
 * @param $leader_id リーダーであるユーザーID
*/
function tks_get_only_student_count($leader_id){
	global $wpdb;
	$member_count = 0;
	
	$sql = "SELECT * FROM {$wpdb->users} a LEFT JOIN {$wpdb->usermeta} b ON b.user_id=a.ID WHERE b.meta_key = 'learndash_group_users_%d'";	
	$IDs = $wpdb->get_results($wpdb->prepare($sql,tks_learndash_get_administrators_group_ids( $leader_id)[0]));
	if (empty($IDs)){
		return $member_count;
	}
	if (count($IDs) > 0){
		foreach($IDs as $ID){
			if (!tks_learndash_is_group_leader_user( $ID->ID )){
				$member_count = $member_count + 1;
			}
		}
	}
	return $member_count;

}

/*
 * ユーザーの属するグループIDを取得する
*/
function tks_get_group_id($user_id){
	global $wpdb;
	if (tks_learndash_is_group_leader_user( $user_id )){
		$group_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->users} a LEFT JOIN {$wpdb->usermeta} b ON b.user_id=a.ID where b.meta_key like 'learndash_group_leaders_%%' and ID = %d", $user_id));
	}else{
		$group_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->users} a LEFT JOIN {$wpdb->usermeta} b ON b.user_id=a.ID where b.meta_key like 'learndash_group_users_%%' and ID = %d", $user_id));
	}
	return (empty($group_id))? 0:$group_id;
}

/*
 * リーダ－を取得する
 */
function tks_get_leader_of_student($user_id){
	
	$group_id = tks_get_group_id($user_id);
	global $wpdb;

	$sql = "SELECT * FROM {$wpdb->users} a LEFT JOIN {$wpdb->usermeta} b ON b.user_id=a.ID WHERE b.meta_key = %s";

	return $wpdb->get_row($wpdb->prepare($sql,"learndash_group_leaders_" . $group_id), ARRAY_A);
}

/*
 * 生徒登録時における生徒用IDの取得(発番)
 */
function tks_get_new_login_id_for_student($user_id){
	
	$leader = tks_get_leader_of_student($user_id);
	$prefix = tks_get_leader_prefix($user_id);

	$query_args['role'] = 'subscriber';
	$query_args['meta_key'] = 'learndash_group_users_' . $leader['meta_value'];
	$query_args['order'] = 'DESC';

	$users = get_users($query_args);

	if (count($users) > 0){
		//数値のみを取得
		$user_id_no = preg_replace('/[^0-9]/', '', $users[0]->user_login);	
		//数値か否かの判定
		if (ctype_digit($user_id_no)){
			$user_id_no = intval($user_id_no);
		}else{
			$user_id_no = 0;
		}
	}else{
		$user_id_no = 0;
	}

	//プリフェックスに+1し、UserID(user_login)を発行
	return $prefix . str_pad(($user_id_no + 1), 3, 0, STR_PAD_LEFT);

}

/*
 * グループリーダーが登録したプレフィックスを取得
 *
*/
function tks_get_leader_prefix($user_id){

	$leader = tks_get_leader_of_student($user_id);
	$leader_uid = $leader['user_id'];

	return get_user_meta($leader_uid,"tks_prefix",true);

}

/*
 * ユーザーIDから同じグループに属する全メンバーを取得する
 * ※リーダーも含む
*/
function tks_get_member_of_group($leader_id){
	global $wpdb;

	$group_id = tks_learndash_get_administrators_group_ids( $leader_id)[0];
	
		$query = "SELECT " .
				"u.ID as user_id," .
				"u.user_login," .
				"u.display_name," .
			    "u.user_email," .
				"u.user_registered," .
			    "m1.meta_value AS firstname," .
			    "m2.meta_value AS lastname," .
				"m3.meta_value AS date_of_birth," .
				"m4.meta_value as group_id," .
				"CASE WHEN m5.meta_value is null THEN '0' ELSE '1' END as session_tokens" .
			" FROM {$wpdb->users} u" .
			" JOIN {$wpdb->usermeta} m1 ON (m1.user_id = u.ID AND m1.meta_key = 'first_name')" .
			" JOIN {$wpdb->usermeta} m2 ON (m2.user_id = u.ID AND m2.meta_key = 'last_name')" .
			" LEFT JOIN {$wpdb->usermeta} m3 ON (m3.user_id = u.ID AND m3.meta_key = 'tks_date_of_birth')" .
			" JOIN {$wpdb->usermeta} m4 ON (m4.user_id = u.ID AND m4.meta_key = 'learndash_group_users_%d')" .
			" LEFT JOIN {$wpdb->usermeta} m5 ON (m5.user_id = u.ID AND m5.meta_key = 'session_tokens')";

	return $wpdb->get_results( $wpdb->prepare($query,$group_id) );
}

/*
 * wp_usermetaの更新
 * meta_keyが空の場合は、Keyを作る
*/
function tks_update_usermeta_value($userid, $metakey, $metavalue){

	$ret = update_user_meta( $userid, $metakey, $metavalue );
	
	if (!$ret){
		$ret = add_user_meta( $userid, $metakey, $metavalue, true );
	}
	
	return $ret;
}

/*
 * 指定されたカテゴリの投稿データを取得する（wp_posts）
 *
*/
function tks_get_post_by_category($category){
	global $wpdb;

		$query = "SELECT * " .
				" FROM {$wpdb->posts} p" .
				" INNER JOIN {$wpdb->term_relationships} m1 ON p.ID = m1.object_id" .
				" WHERE m1.term_taxonomy_id = %d " .
				" AND p.post_status = 'publish' " .
				" AND p.post_type = 'post'" .
				" ORDER BY p.post_title ASC";

	return $wpdb->get_results( $wpdb->prepare($query,$category) );
}

/**
 * 生徒プリフィックスが存在するかを返す
 */
function tks_is_exists_prefix($serch_prefix){
	global $wpdb;
	
	$sql = "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'tks_prefix' AND meta_value = '%s'";	
	$result = $wpdb->get_row($wpdb->prepare($sql, $serch_prefix));
	
	if (empty($result)){
		return false;
	}

	return true;	
	
	
}