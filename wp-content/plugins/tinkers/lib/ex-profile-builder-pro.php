<?php

/*
 * 新規ユーザー登録時（登録画面の下、認証情報をユーザーに通知するのチェックを隠す。とりあえずメール送る設定にする）
 */
add_filter('wppb_send_credentials_checkbox_logic',
function($requestdata, $form)
{
	return '<li class="wppb-form-field wppb-send-credentials-checkbox"><input id="send_credentials_via_email" type="hidden" name="send_credentials_via_email" value="sending"/></li>';
}, 10, 2);

/* プロフィール編集画面を表示する際、編集対象ユーザーリストに表示するユーザーを
 * グループに属するユーザーのみにする
 * adminの場合は全部表示
 * plugin:profile builder
 *
*/
add_filter('wppb_edit_other_users_dropdown_query_args',
function($query)
{

	if (!current_user_can('administrator')) {

		$query['meta_key'] = 'learndash_group_users_' . tks_learndash_get_administrators_group_ids(get_current_user_id())[0];
	}
	return $query;
}, 10, 1);

/* プロフィール編集画面を表示する際、編集対象ユーザーの権限を指定
 * グループに属するユーザーのみにする
 * 
*/
add_filter('wppb_edit_profile_user_dropdown_role',
function($arg)
{

	if (is_page(tks_const::PAGE_EDIT_STUDENT_FOR_LEADER)) {
		$arg = 'Subscriber';
	}
	return $arg;
}, 10, 1);

/*
 * プロフィール編集画面を表示したときのドロップダウンの挙動
 * 自分のみにする(false)
*/
add_filter('wppb_display_edit_other_users_dropdown',
function($arg)
{
	if (is_page(tks_const::PAGE_EDIT_LEADER) || is_page(tks_const::PAGE_EDIT_LEADER_FOR_ADMIN) || is_page(tks_const::PAGE_EDIT_STUDENT_FOR_LEADER) || is_page(tks_const::PAGE_REGIST_SCHOOL) || is_page(tks_const::PAGE_REGIST_SCHOOL2)) {
		return false;
	}
	return $arg;
},10, 1 );

/*
 * プロフィール編集更新時にて、姓名の変更に表示名を合わせる
 * 講師用ダッシュボードでは、表示名で昇順になるため、合わせる必要がある
 * plugin:profile builder
*/
add_filter('wppb_build_userdata', 
function($content, $global)
{

	if ($global['action'] == 'register') {
		if (is_page(tks_const::PAGE_REGIST_LEADER_FOR_ADMIN)) {
			$content['display_name'] =  $global['first_name'] . $global['last_name'];
		} else {
			$content['display_name'] = $global['username'] . '_' . $global['first_name'] . $global['last_name'];
		}
	} else {
		if (is_page(tks_const::PAGE_EDIT_LEADER)) {
			$content['display_name'] =  $global['first_name'] . $global['last_name'];
		} else {
			$edit_user = empty($global['edit_user']) ? get_current_user_id() : $global['edit_user'];
			$content['display_name'] = get_userdata($edit_user)->user_login . '_' . $global['first_name'] . $global['last_name'];
		}
	}

	return $content;
}, 11, 2);

/*
 * グループリーダーが生徒を登録するとき、グループリーダーを登録するとき、にLearnDashの情報をuser_metaへ同時登録する
 * 
 * plugin:profile builder
*/
add_action('wppb_register_success', 
function($request, $form_name, $user_id)
{
	//グループリーダーを登録する場合
	if (is_page(tks_const::PAGE_REGIST_LEADER_FOR_ADMIN)) {

		//リーダー登録(ユーザーIDが空の場合は、ProfileBuilderによってユーザー登録時メール確認がONなっている)
		//その場合は、wppb_activate_userアクションで登録を行う
		if (!empty($user_id)){
			//pforileBuilderによるリーダー登録時は、管理者が登録しているため、プランを手動で割り当てる
			tks_pmpro_changeMembershipLevel(tks_const::PLAN_ID_FOR_ADD_LEADER_BY_ADMIN,$user_id,"admin_changed");
			tks_regist_leader($user_id,$request['tks_school_name']);
		}
	}

	//生徒登録の場合
	if (is_page(tks_const::PAGE_REGIST_STUDENT)) {

		//生徒登録
		tks_regist_student($user_id);
		//$student_email = $http_request['email'];

		//$query = $wpdb->get_row($wpdb->prepare("SELECT activation_key FROM ".$wpdb->prefix."signups WHERE user_email = %s", $student_email));
 
		//wppb_manual_activate_signup($query->activation_key);
	
	}

	//remove_action('wppb_save_form_field','tks_save_learndash_info_for_regist_student');
}, 10, 3);


/*
 * グループリーダーが生徒を更新するとき、グループリーダーを更新するとき、にLearnDashの情報をuser_metaへ同時登録する
 * 
 * plugin:profile builder
*/
add_action('wppb_edit_profile_success',
function($request, $form_name, $user_id)
{

	if (isset($request['tks_school_name']) && (tks_learndash_is_group_leader_user($user_id))) {
		$group_id = tks_learndash_get_administrators_group_ids($user_id)[0];
		$group_post = array(
			'ID'           => $group_id,
			'post_title'     => $request['tks_school_name']
		);
		$post_id = wp_update_post($group_post);	//postidがグループIDとなる
	}

	//体験教室を完了済みにするオプションがYesの場合は、受講完了済みにする
	if (isset($request[tks_const::TKSOPT_TKS_TAIKEN_COMPLETE])) {
		//体験教室が完了しているかをチェック
		$step_total = learndash_get_course_steps_count( tks_const::COURCSE_ID_TAIKEN );
		$step_completed = learndash_course_get_completed_steps( $user_id, tks_const::COURCSE_ID_TAIKEN );
		$progress = floor($step_completed / $step_total * 100);
		if ($progress == 100) return;	//既に完了済みの場合は、何もしない
		 
		$taiken_complete = $request[tks_const::TKSOPT_TKS_TAIKEN_COMPLETE];
		if (! empty($taiken_complete) && $taiken_complete == 'yes'){
			tks_complete_course($user_id,tks_const::COURCSE_ID_TAIKEN);
		}
	}
	
}, 10, 3);

/*
 * プロフィール編集更新時にて、Emailの重複チェックを回避する
 * 
 * plugin:profile builder
*/
add_filter('wppb_check_form_field_default-e-mail',
function($message, $field, $request_data, $form_location)
{
	if (is_page(tks_const::PAGE_REGIST_STUDENT)) {
		return null;
	}

	if (tks_learndash_is_group_leader_user($request_data['edit_user'])) {
		return $message;	//tks_debug(1,$request_data);
	}

	return $message;
}, 11, 4);

/*
 * prefixフィールドのエラーチェック
*/
add_filter('wppb_check_form_field_input',
function($message, $field, $request_data, $form_location)
{
	if (current_user_can('administrator')) return;
	
	//tks_school_nameのエラーチェック(教室名)
	if ($field['field'] == 'Input' && ($field['meta-name'] == 'tks_school_name' || $field['meta-name'] == 'tks_prefix')) {
		
		//edit_userが空という事は、新規か自分自身なので、カレントユーザー
		if (!empty($request_data['edit_user'])) {
			$edit_user = $request_data['edit_user'];
			if ( ! tks_learndash_is_group_leader_user($edit_user)) {
				return;
			}
		} else {
			$edit_user = get_current_user_id();
		}

		//サブスクリプションが生徒を登録できるプランか否かの判定（生徒登録できないプランの場合は、未入力チェックもしない）
		$plans = tks_pmpro_get_member($edit_user);
		if ( tks_is_plan_manage_students($plans->ID) == false ){
			return __('生徒を登録できるプランではありません', 'tinkers');	//ここはこないはず
		}
	} else {
		//未入力チェック
		if ((isset($request_data[$field['meta-name']]) && (trim($request_data[$field['meta-name']]) == '')) && ($field['required'] == 'Yes')) {
			return wppb_required_field_error($field["field-title"]);
		}
	}

	//tks_prefixのエラーチェック(生徒IDプレフィックス)
	if ($field['field'] == 'Input' && $field['meta-name'] == 'tks_prefix') {

		if (isset($request_data[$field['meta-name']]) && trim($request_data[$field['meta-name']]) != '') {
			$input = $request_data[$field['meta-name']] = preg_replace("/( |　)/", "", $request_data[$field['meta-name']] );
			//$input = $request_data[$field['meta-name']];
			if (is_numeric($input)) {
				return __('プレフィックスは数字のみでの登録はできません。半角英字を含めてください。', 'tinkers');
			}			
			if (!is_hankaku($input)) {
				return __('半角英字で入力してください', 'tinkers');
			}

			if (mb_strlen($input) > 5) {
				return __('5桁以内で入力してください', 'tinkers');
			}
			//新規登録でない場合は前回保存値を取得、同じならば重複チェックしない
			//if ($request_data["action"] == "edit_profile") {
			$prefix = get_user_meta($edit_user, 'tks_prefix', true);
			if (!empty($prefix) && $input == $prefix){
				return;
			}

			if (!empty($prefix) && $input != $prefix) {
				return __('プレフィックスは変更できません。', 'tinkers');	
			}else if (tks_is_exists_prefix($input)){
				return __('このプレフィックスは既に使用されています。<br>他のプレフィックスをお試し下さい。', 'tinkers');
			}
		}

	}

	return $message;
}, 20, 4);

/*
 * 日付フィールドのエラーチェック
 *
 */
add_filter('wppb_check_form_field_datepicker',
function($message, $field, $request_data, $form_location)
{

	//誕生日フィールド
	if ($field['field'] == 'Datepicker' && $field['meta-name'] == 'tks_date_of_birth') {
		if (isset($request_data[$field['meta-name']]) && trim($request_data[$field['meta-name']]) != '') {
			if (!checkDatetimeFormat($request_data[$field['meta-name']])) {
				return __('正しい日付を入力して下さい。(yyyy/mm/dd)', 'tinkers');
			}
		} else {
			return null;
		}
	}
	return $message;
}, 20, 4);

/*
 * ログインエラーメッセージのカスタマイズ
 * パスワードリセットのリンクを抑止する
 * plugin:profile builder
*/
add_filter('wppb_login_wp_error_message',
function($error_string, $student)
{

	//	$LostPassURL = site_url('/wp-login.php?action=lostpassword');

	if ($student->get_error_code() == 'incorrect_password') {
		$error_string = '<strong>' . __('ERROR', 'profile-builder') . '</strong>: ' . __('入力されたパスワードが違います', 'tinkers') . ' ';
	}
	if ($student->get_error_code() == 'invalid_username') {
		$err_user_id = $_POST['log'];
		$error_string = '<strong>' . __('ERROR', 'profile-builder') . '</strong>: ' . sprintf (__('入力されたユーザー名:[%s]が見つかりません。', 'tinkers'), $err_user_id ).' ';
	}

	// $wppb_generalSettings = get_option('wppb_general_settings');
	
	// // if login with email is enabled change the word username with email
	// if ($wppb_generalSettings['loginWith'] == 'email')
	// 	$error_string = str_replace(__('username', 'profile-builder'), __('email', 'profile-builder'), $error_string);

	// // if login with username and email is enabled change the word username with username or email
	// if ($wppb_generalSettings['loginWith'] == 'usernameemail')
	// 	$error_string = str_replace(__('username', 'profile-builder'), __('username or email', 'profile-builder'), $error_string);

	return $error_string;
}, 10, 2);

/*
 * プロフィール編集画面専用ショートコード：生徒/リーダー削除のリンクボタンを表示する
 * 
 * plugin:profile builder
*/
function tks_shortcode_del_user($arg)
{

	extract(shortcode_atts(array(
		'user_id' => '',
		'redirect' => ''
	), $arg));

	if ($user_id == '') {
		$user_id = isset($_GET['edit_user']) ? $_GET['edit_user'] : null;
	}

	if (!empty($user_id)) {

		if (user_can($user_id, 'subscriber')) {
			$button_cap = "生徒を削除する";
			if (current_user_can('administrator')) {
				$redirect =  tks_get_home_url(tks_const::PAGE_LEADER_LIST_FOR_ADMIN);
			}
		} else {
			$button_cap = "リーダーを削除する";
		}
		//SeetAlertライブラリを読み込み
		tks_include_sweet_alert();

		$url = add_query_arg(array('action' => 'tks_request_delete_user', 'user_id' => $user_id, 'redirect' => $redirect));
		
		echo  "<div><button id='btn_del_user'>" . $button_cap . "</button></div>";
		echo  "<div id='show_dialog' title='確認ダイアログ' style='display:none;'><p></p></div>";


		add_action('wp_footer', function () use ($url, $user_id) {

			?>

			<script type='text/javascript'>
				<?php
				if (current_user_can('administrator')) {
					$candel = 'true';
				}else{
					$candel = (tks_is_sample_group_leader()? 'false':'true');
				}
				echo "var can_del = " . $candel . ";";	//サンプルユーザーの場合は削除不可
				echo "var user_name = '" . tks_get_user_display_name($user_id) . "';";
				if (current_user_can('administrator')) {
					echo "var del_url = '" . $url . "';";
				}else{
					if (tks_is_sample_group_leader()){
						echo "var del_url = '';";
					}else{
						echo "var del_url = '" . $url . "';";
					}
				}
							?> 
				jQuery("#btn_del_user").click(function() {
					if (can_del === true){
						swal({
								title: '削除確認',
								text: 'ユーザーデータ:' + user_name + 'さんを削除します。\n削除すると学習記録も全て削除されます。\nこの操作は元に戻せません。よろしいですか？',
								icon: "error",
								allowOutsideClick: false,
								buttons: {
									ok: "OK",
									cancel: "キャンセル"
								}
							})
							.then(function(val) {
								if (val == "ok") {
									// Okボタンが押された時の処理
									window.location.href = del_url;
								}
							});
					}else{
						swal('削除確認','試用ユーザー様は、削除できません。','error');
					}
							
				});

			</script>
		<?php
		});
	}
}
add_shortcode('tks_del_user', 'tks_shortcode_del_user');


//削除のリクエストがあった場合のみ生徒削除関数をアクションに追加する
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'tks_request_delete_user') {
	add_action('init', 'tks_delete_user');
}
function tks_delete_user()
{
	//リクエストからユーザーIDを取得する
	$user_id = intval($_REQUEST['user_id']);
	//リクエストからリダイレクト先を取得する
	$redirect =  $_REQUEST['redirect'];
	if (empty($user_id)) {
		return;
	}
 
	//wp_delete_userを使用するために、一時的にインクルード
	require_once(ABSPATH . 'wp-admin/includes/user.php');

	//リーダー配下の生徒は、deleteフィルターによって削除
	//ユーザーがリーダーか否かをチェックする
	if (tks_learndash_is_group_leader_user($user_id)) {

		$group_id = tks_get_group_id($user_id);
		//$group_id = tks_learndash_get_administrators_group_ids( $user_id)[0];
		$members = tks_get_member_of_group($user_id);
		foreach ((array) $members as $student) {
			tks_learndash_delete_user_data($student->user_id);
			wp_delete_user($student->user_id);
		}

		//リーダーがまだ存在する場合は、グループにリーダーとしてのみ登録されていた場合である
		if (!empty(get_userdata($user_id)->ID)) {
			tks_learndash_delete_user_data($user_id);
			wp_delete_user($user_id);
		}

		//グループも削除
		wp_delete_post($group_id);
	} else {
		tks_learndash_delete_user_data($user_id);
		wp_delete_user($user_id);
	}

	remove_action('init', 'tks_delete_user');

	//処理終了後にリダイレクト
	if (!empty($redirect)) {
		$redirect = tks_get_home_url($redirect);
		wp_redirect( $redirect, 303);
		exit();
	}
}

/*
 * 生徒一覧を表示する
*/
add_shortcode('tks_list_student', 'tks_shortcode_list_student');
function tks_shortcode_list_student()
{

	$isAdmin = false;
	//カレントユーザーを取得
	$user_id = get_current_user_id();
	if (user_can($user_id, 'administrator')) {
		//管理者か否か
		$isAdmin = true;
		if (isset($_GET['action']) && $_GET['action'] == 'tks_request_list_students' && isset($_GET['group_reader'])) {
			$user_id = $_GET['group_reader'];
		} else {
			echo "<h4>ユーザーIDの取得に失敗しました。一覧を表示する事ができません。</h4>";
			return;
		}
	}
	//生徒数が0の場合は、登録を促す
	if (tks_get_only_student_count($user_id) == 0){
		$STUDENT_REGIST_URL = tks_get_home_url(tks_const::PAGE_REGIST_STUDENT);
		echo '<div style="margin-bottom:5px;">生徒が登録されていません。<br>登録をするには、以下のボタンをクリックして下さい。</div>';
        echo '<a href="' . $STUDENT_REGIST_URL . '" class="ast-button" style="padding:10px 20px;display: inline-block;">生徒の登録</a><br>';
        echo '<div style="margin-top:10px;font-size: small;color: #5F9EA0">※お子さまの登録は、上部のメニュー「生徒管理→生徒登録」からも行う事ができます。</div></p><hr>';
		return;
	}
	$members = tks_get_member_of_group($user_id);
	$student_count = tks_get_only_student_count($user_id);

	if (!empty($members)) {
		if ($isAdmin) {
			echo "<div style='text-align: right;'><button id='tks_close_window' onClick='window.close();'>閉じる</button></div><p>";
		}
		echo "<H4><div id='tks_student_count' style='display: inline-block; _display: inline; color:blue;text-align: right;float: right; '>【現在の生徒数：" . $student_count . "人】</div></H4>";
		echo "<H4><div style='text-align: right;'>" . date_i18n('Y-m-d H:i:s') . __('現在', 'tinkers') . "</div></H4>";
		echo "<div style='float:left;'>" . __('全行選択：', 'tinkers') . "<input type='checkbox' name='check_all_row' id='check_all_row' style='transform: scale(1.5);'>";
		echo "<button id='tks_logout_btn' style='margin-left: 0.5em;margin-bottom:0.5em;border-radius:10px'>" . __('チェックした生徒をログアウト', 'tinkers') . "</button></div>";
		echo "<p></p>";
		echo "<table id='group_list' style='width: 100%;' class='footable' >";
		echo "<thead>";
		echo "<tr>";
		echo '<th></th>';
		echo '<th data-sort-ignore="true"></th>';
		echo '<th nowrap data-sort-ignore="true" style="width:16%"></th>';
		echo '<th nowrap data-sort-initial="true">' . __("ユーザーID", 'tinkers') . '</th>';
		echo '<th nowrap>' . __("氏名", 'tinkers') . '</th>';
		echo '<th nowrap data-type="numeric" style="text-align: center;width:5%;">' . __("年齢", 'tinkers') . '</th>';
		echo '<th nowrap data-type="date" style="width:16%">' . __("登録日", 'tinkers') . '</th>';
		echo '<th nowrap data-type="date" style="width:16%">ログイン履歴</th>';
		echo '<th nowrap data-type="object" style="width:10%">ステータス</th>';
		echo '<th style="width:16%;word-wrap:break-all;">最終閲覧ページ</th>';
		//管理者は生徒データを編集しない(直接フロントエンドから更新すると生徒のリーダーが管理者になってしまうため)
		if (!$isAdmin) {
			echo '<th nowrap data-sort-ignore="true" style="text-align: center;width:5%">' . __('詳細', 'tinkers') . '</th>';
		}
		echo '</tr></thead>';

		//生徒一覧用のアバター表示
		add_filter('get_avatar', 'tks_custom_student_avatar', 999999, 5);

		foreach ($members as $student) {

			if ( ! tks_learndash_is_group_leader_user($student->user_id)) {

				$leader_profile_link = tks_get_home_url(tks_const::PAGE_EDIT_STUDENT_FOR_LEADER,true) . '?edit_user=' . $student->user_id;

				$login_his = get_user_meta($student->user_id, 'learndash-last-login', true);
				if (empty($login_his)) {
					$login_his = "未使用";
					$activity_course = '';
					$activety_title = '';
					$activety_url = '';	
				} else {
					//LearnDashによって保存されているタイムスタンプ
					$originalZone = date_default_timezone_get();	//デフォルトタイムゾーンを保存
					date_default_timezone_set('Asia/Tokyo');
					$login_his = date("Y/m/d H:i:s", $login_his);
					//$user_registered_date = date("Y/m/d H:i:s", date("Y/m/d H:i:s", strtotime($student->user_registered . " +9 hours", time())));
			        date_default_timezone_set($originalZone);		//デフォルトタイムゾーンに戻す
					$activity_info = tks_get_last_activity_pageinfo($student->user_id);
					if (! empty($activity_info)){
						if (empty($activity_info['course_title'])){				//コース
							$activety_title = $activity_info['title'];			//最終閲覧ページタイトル							
						}else{
							$activety_title = $activity_info['course_title'] . '：<br>' . $activity_info['title'];			//最終閲覧ページタイトル
						}
						$activety_url = $activity_info['link'];					//ページURL
					}
				}
				//ログインセッションが0の場合ログアウト中
				if ($student->session_tokens == 0) {
					$status = "0";
				} else {
					$status = "<img alt='ログイン中' src='" . plugins_url("../etc/user_32px.png", __FILE__) . "' width='32' height='32' />";
				}
				$edit = "<img alt='' src='" . plugins_url("../etc/edit_user_32px.png", __FILE__) . "' width='32' height='32' />";
				echo '<tr>';
				echo '<td nowrap>' . $student->user_id . '</td>';
				echo '<td style="text-align: center;vertical-align:middle"><input type="checkbox" style="transform: scale(1.5);"></td>';
				echo '<td nowrap style="text-align: center;vertical-align:middle">' . get_avatar($student->user_id, 48) . '</td>';
				echo '<td nowrap style="text-align: left;vertical-align:middle">' . $student->user_login . '</td>';
				echo '<td nowrap style="vertical-align:middle">' . $student->firstname . " " . $student->lastname . '</td>';
				echo '<td style="text-align:center;vertical-align:middle">' . tks_get_age($student->date_of_birth) . '</td>';
				echo '<td nowrap style="vertical-align:middle">' . date("Y/m/d H:i:s", strtotime($student->user_registered . " +9 hours", time())) . '</td>';
//				echo '<td nowrap style="vertical-align:middle">' . $user_registered_date . '</td>';
				echo '<td nowrap style="text-align: center;vertical-align:middle;color: blue;">' . $login_his . '</td>';
				echo '<td nowrap style="text-align: center;vertical-align:middle;color: transparent ;">' . $status . '</td>'; //文字のみを透過
				echo '<td style="text-align: left;vertical-align:middle"><a href="' . $activety_url . '" target="_blank">' . $activety_title . '</a></td>';
				if (!$isAdmin) {
					echo '<td nowrap style="text-align: center;vertical-align:middle"><a href="' . $leader_profile_link . '">' . $edit . '</a></td>';
					//					echo '<td nowrap style="text-align: center;"><a href="' .$leader_profile_link .'">'. $edit . '</a></td>';
				}
				echo '</tr>';
			}
		}
		//フィルター削除
		remove_filter('get_avatar', 'tks_custom_student_avatar');

		echo '</table>';

		//フッターへ出力
		add_action('wp_footer', function() {

			//sweet alertのインクルード
			tks_include_sweet_alert();
			//tinkers.js読み込み
			tks_include_tinkers_js();

			?>

		<script>
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>

		<script type='text/javascript'>
			jQuery(document).ready(function() {

				footable_tr_default("#group_list");

				jQuery("#group_list tr").click(function() {
					console.log(jQuery("#corseList").val());
					if (jQuery(this)[0].rowIndex == 0) {
						return;
					}

					footable_tr_default("#group_list");
					footable_tr_click(jQuery(this));

				});

				jQuery('#check_all_row').on('change', function() {
					footable_set_row_all_check("#group_list", jQuery('#check_all_row').is(':checked'));
				});

				jQuery('#tks_logout_btn').on('click', function() {
					request_logout();
				});

				function request_logout() {
					jQuery("#tks_logout_btn").prop("disabled", true);
					var uids = new Array();
					jQuery('.footable tr').each(function(index, row) {

						//ログイン状態を示すカラム7番目が0の場合はログインしていない
						status_login = (jQuery(row).children().eq(7).text() != '0');
						if (jQuery(row).find('input:checkbox').is(':checked') && status_login) {
							uids.push(jQuery(row).find("td:first").text());

						}
					});

					if (uids.length == 0) {
						swal("ログアウト対象の生徒がいません");
						jQuery("#tks_logout_btn").prop("disabled", false);
						return;
					}

					//確認
					swal({
							title: "チェックした生徒のログアウトを実行します",
							icon: "info",
							allowOutsideClick: false,
							buttons: {
								ok: "OK",
								cancel: "キャンセル"
							}
						})
						.then(function(val) {
							if (val == "ok") {
								// Okボタンが押された時の処理
								jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										'action': 'tks_logout_members',
										'uids': uids,
									},
									success: function(response) {
										jQuery("#tks_logout_btn").prop("disabled", false);
										alert(response);
										window.location.reload();
									}
								});
								return false;
							} else {
								jQuery("#tks_logout_btn").prop("disabled", false);
								return false;
							}
						});

				}


			});
		</script>
		<?php
		});
	}
	return;
}

/*
 * 生徒進捗状況一覧
*/
add_shortcode('tks_list_student_progress', 'tks_shortcode_list_student_progress');
function tks_shortcode_list_student_progress()
{
	$isAdmin = false;
	//カレントユーザーを取得
	$user_id = get_current_user_id();
	if (user_can($user_id, 'administrator')) {
		$isAdmin = true;
		if (((isset($_GET['action']) && $_GET['action'] == 'tks_request_list_students_progress') || ($_GET['action'] == 'tks_corse_select')) && isset($_GET['group_reader'])) {
			$user_id = $_GET['group_reader'];
		} else {
			echo "<h4>ユーザーIDの取得に失敗しました。一覧を表示する事ができません。</h4>";
			return;
		}
	}

	//生徒数が0の場合は、登録を促す
	if (tks_get_only_student_count($user_id) == 0){
		$STUDENT_REGIST_URL = tks_get_home_url(tks_const::PAGE_REGIST_STUDENT);
		echo '<div style="margin-bottom:5px;">生徒が登録されていません。<br>登録をするには、以下のボタンをクリックして下さい。</div>';
        echo '<a href="' . $STUDENT_REGIST_URL . '" class="ast-button" style="padding:10px 20px;display: inline-block;">生徒の登録</a><br>';
        echo '<div style="margin-top:10px;font-size: small;color: #5F9EA0">※お子さまの登録は、上部のメニュー「生徒管理→生徒登録」からも行う事ができます。</div></p><hr>';
		return;
	}

	//ユーザー一覧を取得
	$members = tks_get_member_of_group($user_id);
	if (empty($members)) {
		echo "<h4>受講中の生徒がいません。</h4>";
		return;
	}
	//生徒数
	$student_count = tks_get_only_student_count($user_id);

	//受講中コースを取得
	$corses = tks_get_enroll_course_list($user_id);
	if (empty($corses)) {
		echo "<h4>受講中のコースがありません。</h4>";
		return;
	}

	//リクエスト（コースID）取得
	$corse_id = 0;
	if (isset($_GET['action']) && $_GET['action'] == 'tks_corse_select' && isset($_GET['cid'])) {
		$corse_id = absint($_GET['cid']);
	}

	//一覧表示の開始
	if ($isAdmin) {
		echo "<div style='text-align: right;'><button id='tks_close_window' onClick='window.close();'>閉じる</button></div><p>";
	}
	echo "<H4><div id='tks_student_count' style='display: inline-block; _display: inline; color:blue;text-align: right;float: right; '>【現在の生徒数：" . $student_count . "人】</div></H4>";
	echo "<H4><div style='text-align: right;'>" . date_i18n('Y-m-d H:i:s') . __('現在', 'tinkers') . "</div></H4>";
	echo "<DIV style='float:left'><label>コースを選択：";
	echo "<select id='corseList'>";
	if ($corse_id == 0) {
		echo "<option value='' selected='selected'>選択してください</option>";
	}
	foreach ($corses as $c) {
		echo "<OPTION value=" . $c->ID . " >" . $c->post_title . "</OPTION>\n";
	}
	echo "</select>";
	//echo "<button id='tks_course_btn'>" . __( '表示', 'tinkers' ) . "</button>";
	echo "</label></div>";
	echo "<p></p>";
	echo "<table id='group_list' class='footable' >";
	echo "<thead>";
	echo "<tr>";
	echo '<th></th>';
	echo '<th nowrap data-sort-ignore="true" style="width:8%"></th>';
	echo '<th nowrap data-sort-initial="true" style="width:5%">' . __("ID", 'tinkers') . '</th>';
	echo '<th nowrap style="width:10%">' . __("氏名", 'tinkers') . '</th>';
	echo '<th nowrap style="width:10%">' . __("開始日", 'tinkers') . '</th>';
	echo '<th nowrap style="width:20%">' . __("最終受講ステップ", 'tinkers') . '</th>';
	echo '<th nowrap data-type="numeric">' . __("進捗状況", 'tinkers') . '</th>';
	if (tks_is_highlevel_mission_course($corse_id)){
		echo '<th nowrap data-sort-ignore="true" style="text-align: center;width:5%">' . __("ヒント使用数", 'tinkers') . '</th>';
	}
	echo '</tr></thead>';

	//生徒一覧用のアバター表示
	add_filter('get_avatar', 'tks_custom_student_avatar', 999999, 5);
	//リーダーのユーザーIDからグループIDを取得
	$group_id = tks_get_group_id($user_id);

	foreach ($members as $student) {

		if ( ! tks_learndash_is_group_leader_user($student->user_id)) {

			if ($corse_id != 0) {
				//activityの取得
				$activity = tks_get_user_activity_corse_list($group_id, $student->user_id, $corse_id);
				//activityメタ情報
				$activity_meta = $activity->activity_meta;
				//コース毎の最後に受講していたステップ
				$step_last_id = absint($activity_meta['steps_last_id']);

				//トピックタイトルを取得
				$step_title = '';
				//$step_link = '';
				if ($step_last_id != 0) {
					$step_title = '<a href="' . get_permalink($step_last_id) . '" target="_blank">' . get_post($step_last_id)->post_title . '</a>';
					//トピックの場合はレッスンを取得
					if ( 'sfwd-topic' === get_post_type( $step_last_id ) ) {
						$lname = get_post(get_post_meta($step_last_id,'lesson_id',true))->post_title;
						$step_title = "【" . $lname . "】<br>" . $step_title;
					}else{
						$step_title = "【" . $step_title . "】";
					}

					//$step_title = get_post($step_last_id)->post_title;
					//$step_link = get_permalink($step_last_id);
				}
				//受講数
				//$step_total = absint($activity_meta['steps_total']);
				$step_total = learndash_get_course_steps_count( $corse_id );
				//受講完了数
				$activity_started = '';
				//$step_completed = absint($activity_meta['steps_completed']);
				$step_completed = learndash_course_get_completed_steps( $student->user_id, $corse_id );
				if ($step_completed == 0 || $step_total == 0) {
					$progress = null;
				} else {
					//コース受講開始日を取得
					if ($activity->activity_started != 0) {
						$activity_started = date_i18n('Y/m/d', $activity->activity_started);
					}
					$progress = floor($step_completed / $step_total * 100);
					if ($progress == 100){
						$certificate_link = tks_learndash_get_course_certificate_link($corse_id,$student->user_id);
						if (empty($certificate_link)){
							$step_title = '全ステップ完了';
						}else{
							$step_title = '<a href="' . $certificate_link . '" target="_blank"><span class="ld-alert-icon ld-icon ld-icon-certificate"></span> 修了証書<br>[取得日：' . date_i18n('Y/m/d',$activity->activity_completed) . ']</a>';
						}
					}
				}
			}

			$edit = "<img alt='' src='" . plugins_url("../etc/edit_user_32px.png", __FILE__) . "' width='32' height='32' />";
			echo '<tr>';
			echo '<td nowrap>' . $student->user_id . '</td>';
			echo '<td nowrap style="text-align: center;">' . get_avatar($student->user_id, 48) . '</td>';
			echo '<td nowrap class="user_login" style="text-align: left;">' . $student->user_login . '</td>';
			echo '<td nowrap>' . $student->firstname . " " . $student->lastname . '</td>';
			echo '<td nowrap>' . $activity_started . '</td>';
			echo '<td style="text-align: left;">' . $step_title . '</td>';
			if (empty($progress)) {
				if ($corse_id != 0) {
					echo '<td nowrap >' . __("開始されていません", 'tinkers') . '</td>';
				} else {
					echo '<td nowrap ></td>';
				}
			} else {
				//echo '<td nowrap ><br><span class="tks_progress tks_progress_w_' . $progress . '" style="margin-top:0pt;margin-left:0pt;"></span><span style="margin-top:0pt;margin-left:0pt">' . $progress . '%</span></td>';
				echo '<td nowrap class="tks_progress tks_progress_w_' . $progress . '"><br><strong>' . $progress . '%</strong></td>';
			}
			//ヒント使用数
			if (tks_is_highlevel_mission_course($corse_id)){
				if (empty($progress)) {
					echo '<td nowrap ></td>';
				}else{
					$info_ico = "<img alt='ヒント使用数へ' src='" . plugins_url("../etc/link_32px.png", __FILE__) . "' width='24' height='24' />";
					echo '<td nowrap style="text-align: center;"><a href=' . tks_get_home_url('student/?corse_id=' . $corse_id . '&user_login=' . $student->user_login) . ' target="_blank">' . $info_ico . '</a></td>';
				}
			}
			echo '</tr>';
		}
	}
	//フィルター削除
	remove_filter('get_avatar', 'tks_custom_student_avatar');

	echo '</table>';

	echo '<div class="popup_content"></div>';

	//フッターへ出力
	add_action('wp_footer', function () use ($corse_id) {

		//sweet alertのインクルード
		tks_include_sweet_alert();
		//tinkers.js読み込み
		tks_include_tinkers_js();
		wp_enqueue_style( 'tinkers-css', plugins_url( '../css/style-tks-sutudent-progress.css', __FILE__ ) );
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');


		?>
	<script type='text/javascript'>
		jQuery(document).ready(function() {
			var aryhighlevel = [<?php echo implode(',',tks_const::COURCSE_HIGHT_LEVEL) ?>];
			var redirect_url = '<?php echo add_query_arg(array('action' => 'tks_corse_select')); ?>';
			var corseID = '<?php echo $corse_id; ?>';
 
			if (corseID != 0) {
				jQuery("#corseList").val(corseID);
			}

			footable_tr_default("#group_list");

			jQuery("#group_list tr").click(function() {

			 	footable_tr_default("#group_list");
			 	footable_tr_click(jQuery(this));

			});


			jQuery('#corseList').on('change', function() {
				corse_select();
			});
			
			function corse_select() {
				corseID = jQuery("#corseList").val();
				window.location.href = redirect_url + '&cid=' + corseID;
			}
		});
	</script>
<?php
	});

	return;
}

add_shortcode('tks_list_student_detail', 'tks_list_student_detail');
function tks_list_student_detail()
{
	global $wpdb;

	$course_id = sanitize_text_field($_GET['corse_id']);

	$student_login =  sanitize_text_field($_GET['user_login']);

	$student = get_user_by('login', $student_login);

	$user_id = $student->ID;

	$student_display_name = explode('_', $student->display_name) ? explode('_', $student->display_name)[1] : $student->display_name;

	$list_lession = $wpdb->get_results("SELECT post_id FROM `wp_postmeta` WHERE meta_key = 'course_id' AND meta_value = $course_id ORDER BY post_id ASC" );

	ob_start();
	?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
	#main h1.entry-title {
		text-align: center;
		font-weight: 700;
		font-size: 35px;
		margin: 0 0 40px;
	}

	#btn_close {
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-end;
		margin-bottom: 15px;
		margin-top: -90px;
	}

	#btn_close a {
		background-color: #e08f69;
		display: inline-block;
		vertical-align: middle;
		font-size: 18px;
		padding: 12px 40px;
		-webkit-border-radius: 5px;
		border-radius: 5px;
		color: #fff;
	}

	#btn_close a:hover {
		background-color: #f9b375;
		transform: scale(0.9);
	}

	#btn_close a i.fa {
		display: inline-block;
		vertical-align: middle;
	}

	#btn_close a span {
		display: inline-block;
		vertical-align: middle;
	}
</style>
<script>
	document.addEventListener("DOMContentLoaded", function(event) {
		jQuery('#btn_close a').click(function(e) {
			e.preventDefault();
			window.close();
			//history.go(-1);
		});
	});
</script>
<div class="student_details_course">
	<div id="btn_close">
		<a href="#" class="elementor-button-link elementor-button elementor-size-lg elementor-animation-shrink" role="button">
			<i class="fa fa-close" aria-hidden="true"></i>
			<span class="elementor-button-text">とじる</span>
		</a>
	</div>
	<div class="student_name">
		<span class="student_name--id">ID: <?php echo $student_login ?></span>
		<span class="student_name--displayname">なまえ: <ins><?php echo $student_display_name; ?></ins></span></div>
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
				// var_dump(learndash_show_user_course_complete( $user_id ));
				// var_dump($user_id);
				if (count($list_lession) > 0) {
					foreach ($list_lession as $lession) {
						$lession_id = $lession->post_id;
						$lession_title = get_the_title($lession_id);
						$total_hint = get_post_meta($lession_id, 'total_hint');
						$total_hint = ($total_hint && count($total_hint) > 0) ? $total_hint[0] : 0;
						if($total_hint == 0) continue;
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

/*
 * プロフィール画像(Avatar)をprofileBuilderで保存したuser_metaからの画像に切り替える
 * 生徒一覧で使用するので、$id_or_emailは、IDのみ有効とする（生徒はすべて同じEmailなので）
 * plugin:profile builder
*/

function tks_custom_student_avatar($avatar, $id_or_email, $size, $default, $alt)
{

	if (is_numeric($id_or_email)) {
		$my_user_id = $id_or_email;
	} elseif (!is_integer($id_or_email)) {		//ここはEmailの場合なので、通らない
		$student_info = get_user_by('email', $id_or_email);
		$my_user_id = (is_object($student_info) ? $student_info->ID : '');
	} else {
		$my_user_id = $id_or_email;
	}

	if (get_user_meta($my_user_id, 'custom_field_1', true)) {
		$avatar_id = get_user_meta($my_user_id, 'custom_field_1', true);
		$avatar = '<img alt="' . $alt . '" src="' . wp_get_attachment_url($avatar_id) . '" width="' . $size . '" height="' . $size . '" />';
	}

	return $avatar;
}

/*
 * サイトのヘッダーロゴを教室オリジナルの画像に切り替える
 * plugin:profile builder
*/
add_filter('astra_replace_header_logo',
function($image)
{

	//ログインしている場合のみ
	if (!is_user_logged_in()) {
		return $image;
	}

	//管理者はデフォルトロゴ
	if (current_user_can('administrator')) {
		return $image;
		//グループリーダー
	} elseif (tks_learndash_is_group_leader_user()) {
		$user_id = wp_get_current_user()->ID;
		//生徒
	} elseif (current_user_can('subscriber')) {
		$user_id = tks_get_leader_of_student(wp_get_current_user()->ID)['ID'];
	} else {
		return $image;
	}

	$image_id = get_user_meta($user_id, 'tks_custom_logo', true);
	if (empty($image_id)) {
		return $image;
	}
	$image[0] = wp_get_attachment_url($image_id);
	$image[1] = 200;
	$image[2] = 76;


	return $image;
}, 10, 1);

/*
 * ユーザーがログイン時、ユーザー名を使用する事を許可する
 * profilebuilder Ver2.8.8対応
*/
add_filter('wppb_allow_login_with_username_when_is_set_to_email',
function($allow)
{
	return true;
}, 10, 1);

/**
 * 生徒用プロフィール変更画面
 * 姓名を読み取り専用にする
 * 入力ガイドを表示する
 * for profilebuilder
 */
add_action('wp_footer',
function()
{
	//固定ページ(生徒用アカウントページ)の場合のみ実行
	if (is_page(tks_const::PAGE_EDIT_STUDENT)) {
		?>
		<script type='text/javascript'>
			jQuery(document).ready(function() {
				jQuery(".wppb-description-delimiter").each(function(i, elem) {
					jQuery(elem).remove();
				});
				jQuery('#first_name').attr('readonly', true);
				jQuery('#last_name').attr('readonly', true);
				jQuery('#username').attr('style', 'border-width:0px;border-style:None;background-color:transparent;width:200px;');
				jQuery('#username').after('<span class="wppb-description-delimiter" style="color: #00FF00">ログインするときに使用するユーザーIDです</span>');
				jQuery('#first_name').attr('style', 'border-width:0px;border-style:None;background-color:transparent;width:200px;');
				jQuery('#last_name').attr('style', 'border-width:0px;border-style:None;background-color:transparent;width:200px;');
				<?php
				//サンプルユーザー(リーダー)の場合は、パスワードを変更できません
				if (tks_is_sample_user()){
					?>	
					jQuery('#passw1').attr('readonly', true);
					jQuery('#passw2').attr('readonly', true);
					jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000">※試用ユーザー様の場合パスワードを変更する事はできません。</span><span class="wppb-description-delimiter" style="color: #ff0000">変更(へんこう)したら忘れないようにしましょう。パスワードを忘れるとログインできません</span>');
					//jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000">確認(かくにん)のためもう一度パスワードを入力します</span>');
					<?php
				}else{
					?>
					jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000">※パスワードを変更(へんこう)するときだけ入力します。</span><span class="wppb-description-delimiter" style="color: #ff0000">変更(へんこう)したら忘れないようにしましょう。パスワードを忘れるとログインできません</span>');
					jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000">確認(かくにん)のためもう一度パスワードを入力します</span>');
					<?php
				}
			?>
			});
		</script>
		<?php
	//固定ページ(講師用アカウントページ)の場合のみ実行
	} elseif (is_page(tks_const::PAGE_EDIT_STUDENT_FOR_LEADER)) {
		//生徒数の表示
		if (tks_learndash_is_group_leader_user()) {
			?>
			<script type='text/javascript'>
			jQuery(document).ready(function() {
				jQuery('#tks_student_count').text('【現在の生徒数：' + <?php echo tks_get_only_student_count(get_current_user_id()); ?> + '人】');
			});
			</script>
			<?php
		}
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {	
			jQuery(".wppb-description-delimiter").each(function(i, elem) {
				jQuery(elem).remove();
			});
			jQuery('#username').after('<span class="wppb-description-delimiter">ユーザー名は変更できません</span>');
			<?php
			if (tks_is_sample_group_leader()){
				?>
				jQuery('#passw1').attr('readonly', true);
				jQuery('#passw2').attr('readonly', true);
				jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000">※試用ユーザー様の場合パスワードを変更する事はできません。</span>');
				jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000">確認のためもう一度パスワードを入力します</span>');
				<?php
			}else{
				?>
				jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000">※パスワードを変更する場合のみご入力下さい。</span>');
				jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000">確認のためもう一度パスワードを入力します</span>');
				<?php
			}
		?>
		<?php tks_js_datepicker("#tks_date_of_birth"); ?>
		});
		</script>
	<?php
	//固定ページ（リーダー編集、リーダー編集(admin用)のみの実行
	} elseif (is_page(tks_const::PAGE_EDIT_LEADER) || is_page(tks_const::PAGE_EDIT_LEADER_FOR_ADMIN)) {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {	
			jQuery(".wppb-description-delimiter").each(function(i, elem) {
				jQuery(elem).remove();
			});
			jQuery('#tks_prefix').after('<span class="wppb-description-delimiter" style="font-size:small;">プレフィックスを変更する事はできません。</span>');
			jQuery('#tks_school_name').after('<span class="wppb-description-delimiter" style="font-size:small;">本システムを利用する教室名を入力します。</span>');
			jQuery('#username').after('<span class="wppb-description-delimiter" style="font-size:small;">ユーザー名を変更する事はできません。</span>');
			jQuery('#upload_tks_custom_logo_button').after('<span class="wppb-description-delimiter" style="font-size:small;">ロゴ画像は、生徒へ発行する修了証や本システムのロゴに使用します。<br>設定しない場合は<a href="<?php echo tks_const::URL_CIRTIFICATE_LOGO ?>" target="blank">デフォルトロゴ</a>が使用されます。後からプロフィール画面で登録・変更可能です。<br>※推奨画像サイズ:横200px 縦80px</span>');
			jQuery('#tks_option_show_answer').after('<span class="wppb-description-delimiter" style="font-size:small">※ビジネスプランの設定です。<br>ハイレベル問題の解答を登録生徒様に表示するかを設定します。<br>生徒プロフィール画面から個別に変更可能です。</span>');
			<?php
			if (tks_is_sample_group_leader()){
				?>
				jQuery('#passw1').attr('readonly', true);
				jQuery('#passw2').attr('readonly', true);
				jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000;font-size:small;">※試用ユーザー様の場合パスワードを変更する事はできません。</span>');
				jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000;font-size:small;">確認のためもう一度パスワードを入力します</span>');
				<?php
			}else{
				?>
				jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000;font-size:small;">※パスワードを変更する場合のみご入力下さい。</span>');
				jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000;font-size:small;">確認のためもう一度パスワードを入力します</span>');
				<?php
			}	
			?>
			jQuery('#tks_prefix').attr('readonly', true);
		});	
		</script>

		<?php
	//固定ページ（生徒登録）のみ実行
	} elseif (is_page(tks_const::PAGE_REGIST_STUDENT)) {
		?>
		<script type='text/javascript'>
		jQuery(document).ready(function() {	
			jQuery(".wppb-description-delimiter").each(function(i, elem) {
				jQuery(elem).remove();
			});
			jQuery('#username').val('<?php echo tks_get_new_login_id_for_student(get_current_user_id());?>');
			jQuery('#first_name').val(<?php echo (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN)?get_userdata(get_current_user_id())->first_name:''; ?>);
			jQuery('#username').attr('readonly', true);
			jQuery('#username').after('<span class="wppb-description-delimiter" style="font-size:small;">※ユーザー名は自動生成され、変更できません。ログインIDとして使用します。</span>');
			//jQuery('#passw1').after('<span class="wppb-description-delimiter" style="color: #ff0000">※初期パスワード</span>');
			jQuery('#passw2').after('<span class="wppb-description-delimiter" style="color: #ff0000;font-size:small">確認のためもう一度パスワードを入力します</span>');
			jQuery('label[for=email]').hide();
			jQuery('#email').hide();
			jQuery('#email').val('<?php echo tks_const::EMAIL_FOR_STUDENT; ?>');
			jQuery('#tks_taiken_complete').after('<span class="wppb-description-delimiter" style="font-size:small">体験教室で行った内容のコースを受講完了とします。体験教室を受講済の生徒様は「はい」を選択して下さい。<span style="color:red;">※のちほど変更はできません。</span></span>');
			jQuery('#tks_option_show_answer').after('<span class="wppb-description-delimiter" style="font-size:small">※ビジネスプランの設定です。<br>ハイレベル問題の解答を登録生徒様に表示するかを設定します。<br>生徒プロフィール画面から個別に変更可能です。</span>');
			<?php tks_js_datepicker("#tks_date_of_birth"); ?>
		});	
		</script>
		<?php
	//固定ページ（リーダー登録、新規顧客登録）のみ実行
	} elseif (is_page(tks_const::PAGE_REGIST_LEADER_FOR_ADMIN) || is_page(tks_const::PAGE_NEW_REGIST_PB) || is_page(tks_const::PAGE_REGIST_SCHOOL) || is_page(tks_const::PAGE_REGIST_SCHOOL2)) {
		?>
		<script type='text/javascript'>
		jQuery(document).ready(function() {		
			jQuery(".wppb-description-delimiter").each(function(i, elem) {
				jQuery(elem).remove();
			});
			jQuery('#tks_prefix').after('<span class="wppb-description-delimiter" style="font-size:small">生徒IDは、こちらで設定したプレフィックスを使用して自動発番されます。<span style="color:red">（※後ほど変更はできません。半角英字を指定して下さい。）</span><p style="color:blue">例)プレフィックスを「tks」とした場合、生徒IDは次のように先頭にプレフィックスをつけて自動発番されます。 → tks001,tks002...</p></span>');
			jQuery('#tks_school_name').after('<span class="wppb-description-delimiter" style="font-size:small">本システムを利用する教室名を入力します。</span>');
			jQuery('#upload_tks_custom_logo_button').after('<span class="wppb-description-delimiter" style="font-size:small">ロゴ画像は、生徒へ発行する修了証や本システムのロゴに使用します。<br>設定しない場合は<a href="<?php echo tks_const::URL_CIRTIFICATE_LOGO ?>" target="blank">デフォルトロゴ</a>が使用されます。後からプロフィール画面で登録・変更可能です。<br>※推奨画像サイズ:横200px 縦80px</span>');
		<?php
		//リーダー登録、新規顧客登録のみ
		if (is_page(tks_const::PAGE_REGIST_LEADER_FOR_ADMIN) || is_page(tks_const::PAGE_NEW_REGIST_PB)){
			?>
			//jQuery('#username').after('<span class="wppb-description-delimiter"><span style="color:blue">※登録後にユーザー名を変更する事はできません。</span></span>');
			jQuery('#passw2').after('<span class="wppb-description-delimiter">確認のためもう一度パスワードを入力します</span>');
			<?php
		}
		?>
		});
		</script>
		<?php
	}
});

/*
 * メンバーを強制ログアウトさせる
*/
add_action('wp_ajax_tks_logout_members', 'tks_logout_members');
add_action('wp_ajax_nopriv_tks_logout_members', 'tks_logout_members');
function tks_logout_members()
{

	$uids = $_POST['uids'];

	if (empty($uids)) {
		echo "サーバーへリクエスト送信に失敗しました。\n再度実行して下さい。";
		die();
	} else {
		for ($i = 0; $i < count($uids); $i++) {
			$user_id = $uids[$i];
			if (! tks_learndash_is_group_leader_user($user_id)) {
				delete_user_meta($user_id, 'session_tokens');
			}
		}

		echo "ログアウト完了しました。";
		die();
	}
}

/*
 * 新規顧客登録のメール確認が完了後に表示させるメッセージ
*/
add_filter( 'wppb_success_email_confirmation',
function(){
	
	$locationURL =  tks_get_home_url(tks_const::PAGE_LOGIN);
	//return "<p class='success'>登録が完了しました。 <a href='{$locationURL}'> こちら</a>からログインしてご利用下さい。 <meta http-equiv='Refresh' content='3;url={$locationURL}' /></p>";
	return "<p class='success'>登録が完了しました。続いてお支払い手続きを行います。</p>";
} );


/*
 * ProfileBuilderのEmail確認登録がONになっている場合に実行される
 * その際に、コース登録などを行う
*/
add_action( 'wppb_activate_user', 
function($user_id, $password, $meta){

	if ( $meta['user_email'] == tks_const::EMAIL_FOR_STUDENT ){
		//生徒を登録
		tks_regist_student($user_id);
	}else{
		//リーダーを登録
		tks_regist_leader($user_id, $meta['tks_school_name']);
	}
	
},20,3);

/*
* 生徒の場合ユーザー登録メール確認の手順を一時的に無効にする
*/
add_filter( 'wppb_email_confirmation_on_register',
function($emailConfirmation, $request){
	
	//管理者が登録している場合は、メール確認は要らない
	if (is_user_logged_in() && current_user_can('administrator')) {
		return false;
	}

	if ( $request['email'] == tks_const::EMAIL_FOR_STUDENT ){
		return false;
	}else{
		//生徒以外は、設定に従う
		if ( $emailConfirmation === 'yes' ){
			return true;
		}else{
			return false;
		}
	}
},10,2 );

/*
* Confirm user email automatically on specific form

add_action( 'wppb_register_success', 'wppbc_activate_user_on_specific_form', 20, 3 );
function wppbc_activate_user_on_specific_form( $http_request, $form_name, $user_id ){
	global $wpdb;
 
	// if ($form_name != 's-regist')
	
	$student_email = $http_request['email'];
	if ( $student_email != tks_const::EMAIL_FOR_STUDENT )
		return '';
 
	$query = $wpdb->get_row($wpdb->prepare("SELECT activation_key FROM ".$wpdb->prefix."signups WHERE user_email = %s", $student_email));
 
	wppb_manual_activate_signup($query->activation_key);
}
*/

/*
* リーダーの登録
*/
function tks_regist_leader($user_id, $school_name=null){
	
	$leader = new WP_User( $user_id );
	$leader->set_role( "group_leader" );
	$leader->remove_role("subscriber");
	
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		$school_name = $leader->user_login . "_" .  $leader->ID;
	}
	//ありえないが、スクール名がNullの場合は、ログインＩＤをセットする
	if (empty($school_name)){
		$school_name = $leader->user_login;
	}

	//教室名で新しいグループを作成
	$group_post = array(
		'post_author'    => 1,
		'post_title'     => $school_name,
		'post_status'    => 'publish',
		'ping_status'    => 'closed',
		'comment_status' => 'closed',
		'post_type'      => 'groups'
	);
	$post_id = wp_insert_post($group_post);	//postidがグループIDとなる

	update_post_meta($post_id, 'learndash_group_users_' . $post_id, array());
	//リーダーが登録
	//update_post_meta($post_id, 'ld_auto_enroll_group_courses', 'yes');
	//update_post_meta($post_id, 'ld_auto_enroll_group_courses', 'no');
	//userのdisplaynameを更新する
	//個人モードの場合は、displaynameをユーザーIDにする（クイズのランキングに表示されてしまうため）
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		$user_login = get_userdata($user_id)->user_login;
		wp_update_user( array ('ID' => $user_id, 'display_name' => $user_login) );
	}else{
		wp_update_user( array( 'ID' => $user_id, 'display_name' => $leader->first_name . $leader->last_name ) );
	}
	//usermetaもリーダーとして更新
	update_user_meta($user_id, 'learndash_group_leaders_' . $post_id, $post_id);
	update_user_meta($user_id, 'learndash_group_users_' . $post_id, $post_id);
	tks_update_usermeta_value($user_id, 'tks_sendmail_his', '未送信');
	
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		tks_update_usermeta_value($user_id, 'tks_school_name', $school_name);
	}
	//コースを受講(しっかり習得コースは、id=8）
	//コースを受講(ゲームコースは、id=58）
	//コースを受講（まずはここからは、id=10185
	//learndash_set_group_enrolled_courses($post_id, array(8, 58, 10185));
	//learndash_set_group_enrolled_courses($post_id, tks_const::COURSES_PLAN_A_FOR_LEADER);
}

/*
 * 生徒の登録
*/
function tks_regist_student($user_id){

	$group_id = tks_learndash_get_administrators_group_ids(get_current_user_id())[0];
	//個人モードの場合は、displaynameをユーザーIDにする（クイズのランキングに表示されてしまうため）
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		$user_login = get_userdata($user_id)->user_login;
		wp_update_user( array ('ID' => $user_id, 'display_name' => $user_login) );
	}
	update_user_meta($user_id, 'course_points', 0);
	update_user_meta($user_id, 'learndash_group_users_' . $group_id, $group_id);
	update_user_meta($user_id, '_badgeos_can_notify_user', false);
	update_user_meta($user_id, 'credly_user_enable', false);
	//体験教室を完了済みにするオプションがYesの場合は、受講完了済みにする
	$taiken_complete = get_user_meta($user_id,tks_const::TKSOPT_TKS_TAIKEN_COMPLETE,true);
	if (! empty($taiken_complete) && $taiken_complete == 'yes'){
		tks_complete_course($user_id,tks_const::COURCSE_ID_TAIKEN);
	}
	
}

/**
 * ユーザーが属するグループに、申込プランに基づいたコースを割り当てる
 */
function tks_enrol_courses_by_plan($user_id, $plan_id){
	
	if (tks_learndash_is_group_leader_user($user_id)){
	
		//プランAの場合のコース
		if (tks_is_plan_basic($plan_id)){
			
			$courses = tks_get_basic_course();	

			$courses_leader = tks_get_basic_course_leader();
		
		//プランBの場合のコース	
		}elseif (tks_is_plan_regular($plan_id)){
			
			$courses = array_merge( tks_get_basic_course(), tks_get_regular_course() );
			
			$courses_leader = array_merge( tks_get_basic_course_leader(), tks_get_regular_course_leader() );
			
		//プランCの場合のコース	
		}elseif (tks_is_plan_advance1($plan_id)){
		
			$courses = array_merge( tks_get_basic_course(), tks_get_regular_course(), tks_get_advance_course1());
			
			$courses_leader = array_merge( tks_get_basic_course_leader(), tks_get_regular_course_leader(), tks_get_advance_course_leader1() );
		}elseif (tks_is_plan_advance2($plan_id)){
		
			$courses = array_merge( tks_get_basic_course(), tks_get_regular_course(), tks_get_advance_course1(),tks_get_advance_course2());
			
			$courses_leader = array_merge( tks_get_basic_course_leader(), tks_get_regular_course_leader(), tks_get_advance_course_leader1(),tks_get_advance_course_leader2() );
		}elseif (tks_is_plan_advance3($plan_id)){
		
			$courses = array_merge( tks_get_basic_course(), tks_get_regular_course(), tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3());
			
			$courses_leader = array_merge( tks_get_basic_course_leader(), tks_get_regular_course_leader(), tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3() );
		}elseif (tks_is_plan_advance4($plan_id)){
		
			$courses = array_merge( tks_get_basic_course(), tks_get_regular_course(), tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4());
			
			$courses_leader = array_merge( tks_get_basic_course_leader(), tks_get_regular_course_leader(), tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4() );
		}elseif (tks_is_plan_advance5($plan_id)){
		
			$courses = array_merge( tks_get_basic_course(), tks_get_regular_course(), tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5());
			
			$courses_leader = array_merge( tks_get_basic_course_leader(), tks_get_regular_course_leader(), tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5() );
	
		}else{
			return;
		}
	
		$group_id = tks_get_group_id($user_id);
		//コースの登録(グループ)
		tks_learndash_set_group_enrolled_courses($group_id, $courses);
		//個別コース(リーダーのみのコース)
		tks_learndash_user_set_enrolled_courses($user_id, $courses_leader);	
	}
}

/*
* ユーザーが生徒管理（登録、削除、一覧表示を行えるか否か）
* ※有効期限やステータスについては判定しない！その判定は、PMSで行うため
* Subscriptionから判定する
* Paid-member-subscription
*/
function tks_can_user_plan_mange_student($user_id){
	
	//管理者はOK
	if (current_user_can('administrator')){
		return true;
	}

	//支払登録されているメンバーを取得する
	$member = tks_pmpro_get_member( $user_id );
	if( empty($member) ){
		return false;
	}
	
	$ret = false;
	
	$ret = tks_is_plan_manage_students( $member->id);

	return $ret;
}

/**
 * 支払いプランのメンバーか否かを返す
 */
function tks_is_payment_member($user_id){
	
	$member = tks_pmpro_get_member( $user_id );

	if (empty($member)) return false;
	
	return true;

}

/**
 * ユーザーはそのプランに申し込んでいるか
 */
function tks_has_user_plan($user_id, $plan_id){
	$ret = false;
	//支払登録されているメンバーを取得する
	$member = tks_pmpro_get_member( $user_id );
	if ($member->id == $plan_id ){
		$ret = true;
	}

	return $ret;
}

/*
* プランが、生徒管理できるプランか否かを返す
*/
function tks_is_plan_manage_students($plan_id){
	$student_count = tks_get_can_regist_student_count($plan_id);
	
	if (empty($student_count)){
		return false;
	}
	if (intval($student_count) == 0){
		return false;
	}

	return true;
}

/**
 * 登録可能生徒数をチェックしてダメならリダイレクト
 */
// add_filter( 'template_redirect',
// function(){

// 	//生徒登録ページでないなら抜ける
// 	if (is_page(tks_const::PAGE_REGIST_STUDENT)){
		
// 		//ログインユーザーを取得
// 		$user_id = get_current_user_id();
// 		if (user_can($user_id,'administrator')){
// 			return;
// 		}

// 		//生徒登録ができないプランなら何もチェックしない(pmプラグインの機能制限に任せる)
// 		if (tks_learndash_is_group_leader_user($user_id) && tks_can_user_plan_mange_student($user_id)){
			
// 			$member = tks_pmpro_get_member($user_id);
// 			if (! tks_pmpro_isLevelExpiring($member)){
// 				//生徒数を取得する
// 				$member_count = tks_get_only_student_count($user_id);
// 				//プランに基づく生徒の登録制限人数を取得する
// 				$can_manage_student_count = tks_get_can_regist_student_count($member->id);
// 				//生徒の制限数が-1の場合は、対象外
// 				if ( ! empty($can_manage_student_count) ){
// 					if ($member_count >= $can_manage_student_count){
// 						//人数制限をクリアしているプランがない場合のみ
// 						if (empty($restrict)){
// 							$restrict = true;
// 						}
// 					//一つでも生徒数の制限をクリアしているプランがあればOK
// 					}else{
// 						$restrict = false;
// 					}
// 				}				
// 			}
			
// 		}

// 		//制限ONの場合、エラーメッセージページへリダイレクト
// 		if (! empty($restrict) && $restrict == true){
// 			$redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
// 			wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_MAX_STUDENT, $redirect_url ) );
// 			exit;
// 		}
// 	}
// });


/**
 * プランの機能(閲覧)制限にで、制限理由により出力するメッセージコンテンツを切り分け、表示する
 * フィルター：pms_restricted_post_redirect_urlによってURLに渡されるパラメータによって
 * メッセージを切り分ける
 */
add_shortcode('tks_restrict_message', 
function()
{
	$img = '<p><img class="wp-image-6480 size-thumbnail alignleft" src="' . tks_get_home_url("/wp-content/uploads/2018/07/sorry-150x150.png") . '" alt="" width="150" height="150" /></p>';
	$backbtn = '<button type="button" style="border-radius:10px" onclick="history.back()">前のページに戻る</button>';
	
	//リーダーの場合と生徒場合で、制限メッセージを分けて表示させる
	if (tks_learndash_is_group_leader_user()){
		$url_account_info = tks_get_home_url(tks_const::PAGE_ACCOUNT_PLAN_INFO);
		
		$contents_for_upgrade = '<a href="' . $url_account_info .'">お申込み情報</a>のページから行う事ができます。</p>' .
			'<button type="button" style="border-radius:10px;border-style: none;background-color: #00CC33" onclick=' . 'location.href="' . $url_account_info . '"' . '>お申込み情報ページへ</button>';
			
		//URLからパラメータを取得する
		$restrict = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY);

		switch ($restrict) {
			case 'restrict=' . tks_const::ERR_RESTRICT_NO_PLAN:
				$message = 'こちらのコンテンツをご覧頂くためには、プランのアップグレードが必要です。<P>アップグレードは、' . $contents_for_upgrade;
				break;
			case 'restrict=' . tks_const::ERR_RESTRICT_EXPIRE:
				$message = 'お申込みプランの有効期限がきれています。<BR>こちらのコンテンツをご覧頂くためには、プランの更新が必要です。<P>更新は、' . $contents_for_upgrade;
				break;
			case 'restrict=' . tks_const::ERR_RESTRICT_PENDING:
				$message =  'お支払方法がお振込みの場合は、まだご入金を確認できておりません。<BR>ご入金を確認後すぐに使用できます。<br>既にお振込み済みの方はお手数ですが以下までご連絡下さい。<p>ティンカーズ運営事務局：' . tks_const::SYSTEM_MANAGER_MAIL . '</p>';
				break;
			case 'restrict=' . tks_const::ERR_RESTRICT_MAX_STUDENT:
				//生徒Orお子さま登録ページのタイトルをスラッグから取得する
				$title = get_title_from_slug(tks_const::PAGE_REGIST_STUDENT);
				echo '<script>let msg = "' . $title .'";jQuery("title").html(msg);jQuery(".ast-advanced-headers-title").eq(0).text(msg)</script>';

				if (tks_is_sample_group_leader()){
					$student_info_url = tks_get_home_url(tks_const::PAGE_LIST_STUDENT);
					$message = '<p>登録できません。<br>試用ユーザー様は、生徒が既に登録されております。<br>上部メニューバーより「生徒管理→<a href="' . $student_info_url . '">生徒一覧</a>」をご覧下さい。</p><p>' . $backbtn . '</p>';
				}else{
					if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
						//$message = '登録人数がお申込みプランの上限に達しました。<br>新たにお子さまを追加するには、お手数ですが事務局までお問合せください。<P>' . tks_const::SYSTEM_MANAGER_NAME . '<br>' . tks_const::SYSTEM_MANAGER_MAIL . '<br>Tel:' . tks_const::SYSTEM_MANAGER_TEL . '<br>' . $backbtn;
						$message = '登録人数がお申込みプランの上限に達しました。<br>新たにお子さまを追加するには、お手数ですが事務局までお問合せください。<P>' . '<p>' . $backbtn . '</p>';
					}else{
						$message = '登録人数がお申込みプランの上限に達しました。<br>新たに生徒様を追加するには、プランのアップグレードが必要です。<P>アップグレードは、' . $contents_for_upgrade;
					}
				}
				break;
			case 'restrict=' . tks_const::ERR_RESTRICT_FOR_SAMPLE:
				$message = '<p>申し訳ございません。<br>試用ユーザー様は、こちらのコンテンツを御覧頂く事ができません。</p><p>' . $backbtn . '</p>';
				break;
			case 'restrict=' . tks_const::ERR_RESTRICT_FOR_NOPLAN:
				$message = 'こちらのコンテンツをご覧頂くためには、プランへのお申込みが必要です。<P>アップグレードは、' . $contents_for_upgrade;
				break;
			case 'restrict=' . tks_const::ERR_RESTRICT_FOR_NO_ATTEND		:
				$message = '<p>申し訳ございません。<br>こちらへのコースにはアクセスできません。</p><p>' . $backbtn . '</p>';
				break;
			//その他使用不可状態(管理者によって止められている)
			default:
				$message = '<p>申し訳ございません。<br>ただいまこちらのコンテンツを御覧頂く事ができません。</p><p>' . $backbtn . '</p>';
				break;
		}
		
	}else{
		$message = '<p>ごめんなさい。<br>こちらのページには、アクセスできません。</p><p>' . $backbtn . '</p>';
	}
		$contents = '<div style="background:#e4fcff; padding:10px; border:1px solid #00BCD4; border-radius:10px;">' . $img . $message . '<p></div>';
	
	echo $contents;
	
});

/*
 * ProfileBuilder 登録ボタンのテキスト変更
*/
add_filter('wppb_register_button_name', 
function($button_name, $form_name) {
	if (is_page(tks_const::PAGE_NEW_REGIST_PB))
		return '新規お申込み';
	if 	(is_page(tks_const::PAGE_REGIST_STUDENT))
		return '生徒を登録する';
	if (is_page(tks_const::PAGE_REGIST_LEADER_FOR_ADMIN))
		return '登録する';
},30,2);


/**
 * プランBasicリストを取得する
 * 
 * @return array
 */
function tks_get_plan_basic(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_BASIC);
	return explode(',',$plans);

}

/**
 * プランREGULARリストを取得する
 * 
 * @return array
 */
function tks_get_plan_regular(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_REGULAR);
	return explode(',',$plans);

}

/**
 * プランADVANCEリストを取得する
 * 
 * @return array
 */
function tks_get_plan_advance1(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE1);
	return explode(',',$plans);

}
function tks_get_plan_advance2(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE2);
	return explode(',',$plans);

}
function tks_get_plan_advance3(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE3);
	return explode(',',$plans);

}
function tks_get_plan_advance4(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE4);
	return explode(',',$plans);

}
function tks_get_plan_advance5(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE5);
	return explode(',',$plans);
}
function tks_get_plan_advance6(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE6);
	return explode(',',$plans);
}
function tks_get_plan_advance7(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE7);
	return explode(',',$plans);
}
function tks_get_plan_advance8(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE8);
	return explode(',',$plans);
}
function tks_get_plan_advance9(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE9);
	return explode(',',$plans);
}
function tks_get_plan_advance10(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE10);
	return explode(',',$plans);
}
function tks_get_plan_advance11(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE11);
	return explode(',',$plans);
}
function tks_get_plan_advance12(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE12);
	return explode(',',$plans);
}
function tks_get_plan_advance13(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE13);
	return explode(',',$plans);
}
function tks_get_plan_advance14(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE14);
	return explode(',',$plans);
}
function tks_get_plan_advance15(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE15);
	return explode(',',$plans);
}
function tks_get_plan_advance16(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE16);
	return explode(',',$plans);
}
function tks_get_plan_advance17(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE17);
	return explode(',',$plans);
}
function tks_get_plan_advance18(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE18);
	return explode(',',$plans);
}
function tks_get_plan_advance19(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE19);
	return explode(',',$plans);
}
function tks_get_plan_advance20(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE20);
	return explode(',',$plans);
}










/**
 * 引数で渡されたプランIDがBasicプランであるか否か
 * @param int	プランID
 * @return boolean 
 */
function tks_is_plan_basic($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_basic(),true);
}

/**
 * 引数で渡されたプランIDがRegularプランであるか否か
 * @param int	プランID
 * @return boolean 
 */
function tks_is_plan_regular($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_regular(),true);
}

/**
 * 引数で渡されたプランIDがAdvanceプランであるか否か
 * @param int	プランID
 * @return boolean 
 */
function tks_is_plan_advance1($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance1(),true);
}
function tks_is_plan_advance2($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance2(),true);
}
function tks_is_plan_advance3($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance3(),true);
}
function tks_is_plan_advance4($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance4(),true);
}
function tks_is_plan_advance5($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance5(),true);
}
function tks_is_plan_advance6($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance6(),true);
}
function tks_is_plan_advance7($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance7(),true);
}
function tks_is_plan_advance8($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance8(),true);
}
function tks_is_plan_advance9($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance9(),true);
}
function tks_is_plan_advance10($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance10(),true);
}
function tks_is_plan_advance11($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance11(),true);
}
function tks_is_plan_advance12($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance12(),true);
}
function tks_is_plan_advance13($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance13(),true);
}
function tks_is_plan_advance14($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance14(),true);
}
function tks_is_plan_advance15($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance15(),true);
}
function tks_is_plan_advance16($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance16(),true);
}
function tks_is_plan_advance17($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance17(),true);
}
function tks_is_plan_advance18($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance18(),true);
}
function tks_is_plan_advance19($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance19(),true);
}
function tks_is_plan_advance20($plan_id){
	$plan_id = strval($plan_id);
	return in_array($plan_id,tks_get_plan_advance20(),true);
}





/**
 * プランに基づく生徒の登録制限人数を取得する
 * @param int	プランID
 * @return int 	登録可能生徒数数
*/
function tks_get_can_regist_student_count($plan_id,$user_id=NULL){
	//最初にユーザー個別の登録可能人数を取得（設定値が0以上の場合この設定人数がプランよりも優先される）
	if (empty($user_id)){
		$user_id = get_current_user_id();
	}

	$max_student_count = get_user_meta($user_id,tks_const::TKSOPT_MAX_STUDENT_COUNT,true);	
	if (!empty($max_student_count)){
		return $max_student_count;
	}

	//Basicプランの場合
	if (tks_is_plan_basic($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_BASIC_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_BASIC_STUDENTS_COUNT_DEF);
	}
	//Regularプランの場合
	if (tks_is_plan_regular($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_REGULAR_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_REGULAR_STUDENTS_COUNT_DEF);
	}
	//Advanceプランの場合
	if (tks_is_plan_advance1($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance2($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance3($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance4($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance5($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance6($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE6_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance7($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE7_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance8($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE8_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance9($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE9_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance10($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE10_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance11($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE11_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance12($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE12_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance13($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE13_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance14($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE14_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance15($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE15_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance16($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE16_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance17($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE17_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance18($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE18_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance19($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE19_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}
	if (tks_is_plan_advance20($plan_id)){
		return get_option( tks_const::TKSOPT_PLAN_ADVANCE20_STUDENTS_COUNT,tks_const::TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF);
	}









	return 0;
}

/**
 * 生徒をこれ以上登録できるか
 * 登録人数と、プランの制限人数を比較して登録できるならTrueを返す
 * 
 */
function tks_can_add_student($user_id = null){
	if (empty($user_id)){
		$user_id = get_current_user_id();
	}
	//リーダーの場合
	if (tks_learndash_is_group_leader_user($user_id)){
		//生徒を登録できるプランか？
		if (tks_can_user_plan_mange_student($user_id)){
			//プランを取得する
			$member = tks_pmpro_get_member($user_id);
			//生徒数を取得する
			$member_count = tks_get_only_student_count($user_id);
			//プランに基づく生徒の登録制限人数を取得する
			$can_manage_student_count = tks_get_can_regist_student_count($member->id);
			//生徒の制限数が-1の場合は、対象外
			if ( ! empty($can_manage_student_count) ){
				//サンプルユーザーなら一人のみしか登録できない
				if (tks_is_sample_group_leader()){
					$can_manage_student_count = 1;
				}
				if ($member_count < $can_manage_student_count){
					//人数制限をクリアしているプランがない場合のみ
					return true;
				}
			}				
		}
	}
	return false;	
}

/**
 * 生徒登録完了後のメッセージを直す
 */
add_filter( 'wppb_register_success_message', function($message, $account_name){
	$user = get_user_by( 'login', $account_name );
	if (user_can($user->ID,'subscriber')) {
		$STUDENT_LIST_URL = tks_get_home_url(tks_const::PAGE_LIST_STUDENT);
		
		$message = $message 
		. '<p>登録した生徒様の情報は、メニューより「生徒管理→<a href="' . $STUDENT_LIST_URL . '">生徒一覧</a>」よりご覧いただけます。<br>生徒様がログインする場合は、登録する際に入力して頂いた以下のログイン情報をお使い下さい。</p>'
		. '<p><strong>ログインID：' . $account_name . '<br>パスワード：登録時に入力したパスワード</strong></p>';

		if (tks_can_add_student()){
			$STUDENT_REGIST_URL = tks_get_home_url(tks_const::PAGE_REGIST_STUDENT);
			$message = $message
			. '<p><div style="margin-bottom:5px;">続けて生徒の登録をするには、以下のボタンをクリックして下さい。</div>'
			. '<a href="' . $STUDENT_REGIST_URL . '" class="ast-button" style="padding:10px 20px;display: inline-block;">生徒の登録</a></p>';
		}
		//$message = $message . "\n" ."登録された生徒様の情報は、上部メニューの 「生徒管理 -> 生徒一覧」よりご覧いただけます。" . "\n\n生徒様がログインする場合は、生徒様をご登録した際にご入力頂いたログイン情報をお使い下さい";
	}
	return $message;
},10,2);

/**
 * 生徒登録ができないプランからアップグレードした時に
 * 生徒登録画面にアクセスされたら、生徒情報を登録してもらう
 */
add_action( 'wp', 'student_regist_first_accessed' );
function student_regist_first_accessed(){
	//法人モードの場合のみ
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_HOUJIN){
		//生徒ページで、且つ生徒登録ができるプランであれば教室登録ページへ
		if ((is_page(tks_const::PAGE_REGIST_STUDENT) || 
			is_page(tks_const::PAGE_EDIT_LEADER)) && 
			tks_can_user_plan_mange_student( get_current_user_id())) {
			//必須項目のプリフェックスが登録されていない場合のみ
			if (empty(get_user_meta(get_current_user_id(), 'tks_prefix', true))) {
				if (is_page(tks_const::PAGE_REGIST_STUDENT)){		
					wp_safe_redirect( tks_get_home_url(tks_const::PAGE_REGIST_SCHOOL) );
					exit;
				}elseif(is_page(tks_const::PAGE_EDIT_LEADER)){
					wp_safe_redirect( tks_get_home_url(tks_const::PAGE_REGIST_SCHOOL2) );
					exit;
				}
			}
		}
	}
}
/*
* 既にログインしているの時に表示するメッセージ
* 新しいログイン画面の場合、ログアウトのリンクしかなく、メニューが表示されないので、ホームへリンクを表示する
*/
add_filter( 'wppb_login_message', function($logged_in_message, $user_id, $display_name){

	$home_url = '/';
	$title = "";
	$tag_message = "ホームへ";
	if (tks_learndash_is_group_leader_user()){
		$tag_message = "ホームへ";
		$title = "スタートページへ移動します";
		$home_url = "/" . tks_const::PAGE_START_LEADER;
	}
	if (user_can($user_id,'subscriber')){
		$tag_message = "マイページをひらく";
		$title = "ホーム(マイページをひらきます)";
		$home_url = "/" . tks_const::PAGE_STUDENT_MY_PAGE;
	}

	//return $logged_in_message .= '<a href="' . $home_url . '" title="' . $title . '">' . $tag_message .' »</a>';
	return $logged_in_message .= '<a href="' . $home_url . '" title="' . $title . '" style="color:white;">' . $tag_message .'»</a>';

},3,10 );
