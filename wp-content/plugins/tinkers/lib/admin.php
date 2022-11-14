<?php
/*
* グループリーダー一覧を表示する(Admin)
*/
function tks_show_group_reader_list()
{
	//管理者の場合はグループリーダー一覧
	if (! current_user_can('administrator')){
		return;
	}

	$users = tks_get_leaders_meta();

	if (!empty($users)) {
		if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
			tks_show_group_reader_list_kojin($users);	//SYSTEMが個人モードの場合のリーダー一覧表示
		}else{
			tks_show_group_reader_list_hojin($users);	//SYSTEMが法人モードの場合のリーダー一覧表示
		}
	}
	return;
}
add_shortcode('tks_short_show_group_reader_list', 'tks_show_group_reader_list');

/**
 * リーダー一覧(Admin)
 * システムが、法人モードで動作した場合の一覧
 */
function tks_show_group_reader_list_kojin($users){
	//data-sort-initial="true"　thタグに入れる(デフォルトソート列)
	//data-sort-ignore="true"　 thタグに入れる(ソート無効列)
	//data-type="numeric"       thタグに入れる(ソートタイプ)
	//data-filter='false'		tableタグに入れる(検索Box無効)
	
	echo "<H4><div style='text-align: right;'>" . date_i18n('Y-m-d H:i:s') . "現在</div></H4>";
	//echo "<div style='text-align: right;'><button id='tks_send_email'>選択したユーザーにメール送信</button></div><p>";
	//echo "<div style='float:left;'>全行選択：<input type='checkbox' name='check_all_row' id='check_all_row' style='transform: scale(1.5);'></div>";
	//echo "<table id='group_list' class='footable' data-filter='false'>";
	echo "<table id='group_list' class='footable'>";	//検索Boxを無効
	echo "<thead>";
	echo "<tr>";
	echo '<th></th>';
	//echo '<th data-sort-ignore="true"></th>';
	echo '<th nowrap style="width:5%">' . __("ユーザーID", 'tinkers') . '</th>';
	echo '<th nowrap style="width:5%">' . __("グループ名", 'tinkers') . '</th>';
	echo '<th nowrap style="width:7%">' . __("お名前", 'tinkers') . '</th>';
	echo '<th nowrap style="width:6%">' . __("メールアドレス", 'tinkers') . '</th>';
	echo '<th nowrap style="width:2%">' . __("お子さま<br>登録数", 'tinkers') . '</th>';
	echo '<th nowrap data-sort-ignore="true" style="width:2%">' . __('お子さま情報', 'tinkers') . '</th>';
	echo '<th nowrap data-sort-ignore="true" style="width:2%">' . __('お子さま進捗', 'tinkers') . '</th>';
	echo '<th nowrap style="width:8%">' . __('プラン名', 'tinkers') . '</th>';
	echo '<th nowrap style="width:2%">' . __('子供登録<br>可能人数', 'tinkers') . '</th>';
	echo '<th nowrap style="text-align: center;width:5%;">' . __('プラン開始日', 'tinkers') . '</th>';
	echo '<th nowrap style="text-align: center;width:5%;">' . __('試用期限', 'tinkers') . '</th>';
	echo '<th nowrap style="text-align: center;width:5%;">' . __('次回更新日', 'tinkers') . '</th>';
	echo '<th nowrap style="text-align: center;width:5%;">' . __('有効期限', 'tinkers') . '</th>';
	echo '</tr></thead>';

	foreach ($users as $user) {
		//生徒数を取得
		$student_count = tks_get_only_student_count($user->user_id);
		
		//支払登録されているメンバーを取得する
		$member = tks_pmpro_get_member( $user->user_id );
		$tdrow_span = '';

		$monthly_price = intval($user->monthly_price);
		$student_free_count = intval($user->student_free_count);
		$extra_price = intval($user->student_price);

		$student_extra_count = ($student_count > $student_free_count) ? ($student_count - $student_free_count) : 0;
		$total_extra_price = $student_extra_count * $extra_price;
		$total_all_price = $monthly_price + $total_extra_price;

		$url_list_student = add_query_arg(array('action' => 'tks_request_list_students', 'group_id' => $user->group_id, 'group_reader' => $user->user_id), tks_get_home_url(tks_const::PAGE_STUDENT_LIST_FOR_LEADER,true));
		$url_list_student_progress = add_query_arg(array('action' => 'tks_request_list_students_progress', 'group_id' => $user->group_id, 'group_reader' => $user->user_id),  tks_get_home_url(tks_const::PAGE_STUDENT_LIST_PROGRESS,true));

		$leader_profile_link =  tks_get_home_url( tks_const::PAGE_EDIT_LEADER_FOR_ADMIN, true ) . '?edit_user=' . $user->user_id;

		$info_ico = "<img alt='生徒情報' src='" . plugins_url("../etc/link_32px.png", __FILE__) . "' width='24' height='24' />";

		echo '<tr>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->user_id . '</td>';
		//echo '<td ' . $tdrow_span . 'style="text-align: center;"><input type="checkbox" style="transform: scale(1.5);"></td>';
		echo '<td ' . $tdrow_span . 'nowrap style="text-align: left;"><a href="' . $leader_profile_link . '" >' . $user->user_login . '</a></td>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->school_name . '</td>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->display_name . '</td>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->user_email . '</td>';
		echo '<td ' . $tdrow_span . 'nowrap style="text-align: right;">' . $student_count . ' </td>';
		//生徒数が0人の場合は、詳細へリンクボタンは表示しない
		if ($student_count == 0) {
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"></td>';
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"></td>';
		} else {
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"><a href="' . $url_list_student . '" target="_blank">' . $info_ico . '</a></td>';
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"><a href="' . $url_list_student_progress . '" target="_blank">' . $info_ico . '</a></td>';
		}

		//申し込んでいるプランを全て表示する
		if( !empty( $member ) ){
			echo '<td nowrap style="text-align: left;">' . ( ! empty( $member->name ) ? $member->name : '' ) . '</td>';
			echo '<td nowrap style="text-align: center;">' . tks_get_can_regist_student_count($member->id)  . '</td>';
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $member->startdate ) ? ucfirst( date_i18n( get_option('date_format'), $member->startdate  ) ) : '' ) . '</td>';
			$trialing_until = get_user_meta( $user->user_id, 'pmprosd_trialing_until', true );
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $trialing_until ) ? ucfirst( date_i18n( get_option('date_format'), $trialing_until  ) ) : 'なし' ). '</td>';
			$nextupdate = tks_pmpro_payment_date_text($user->user_id);
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $nextupdate ) ? $nextupdate : 'なし' ). '</td>';
			$expir_date = tks_pmpro_expir_date_text($user->user_id);
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $expir_date ) ? $expir_date : 'なし' ). '</td>';
			echo '</tr>';
		}else{
			//サブスクデータが1件もない場合
			echo '<td nowrap style="text-align: center;">---</td>';
			echo '<td nowrap style="text-align: center;">---</td>';
			echo '<td nowrap style="text-align: center;">---</td>';
			echo '<td nowrap style="text-align: center;">---</td>';
			echo '<td nowrap style="text-align: center;">---</td>';
			echo '<td nowrap style="text-align: center;">---</td>';
			echo '</tr>';
		}
	}

	echo '</table>';
}

/**
 * リーダー一覧(Admin)
 * システムが、法人モードで動作した場合の一覧
 */
function tks_show_group_reader_list_hojin($users){
	//data-sort-initial="true"　thタグに入れる(デフォルトソート列)
	//data-sort-ignore="true"　 thタグに入れる(ソート無効列)
	//data-type="numeric"       thタグに入れる(ソートタイプ)
	//data-filter='false'		tableタグに入れる(検索Box無効)
	
	echo "<div style='padding: 20px;;margin:0px auto;width: 100%;overflow-x: scroll;'>";
	echo "<H4><div style='text-align: right;'>" . date_i18n('Y-m-d H:i:s') . "現在</div></H4>";
	echo "<div style='float:left;'>全行選択: <input type='checkbox' name='check_all_row' id='check_all_row' style='transform: scale(1.5);'></div>";
	echo "<div style='text-align: right;'><button id='tks_send_email'>選択したユーザーにメール送信</button></div><p>";
	echo "<table id='group_list' class='footable' data-filter='false'>";
	echo "<thead>";
	echo "<tr>";
	echo '<th></th>';
	echo '<th data-sort-ignore="true" style="width:1%"></th>';
	echo '<th nowrap style="width:8%">' . __("ユーザーID", 'tinkers') . '</th>';
	echo '<th nowrap style="width:15%">' . __("教室名", 'tinkers') . '</th>';
	echo '<th nowrap style="text-align: center;width:2%">' . __("郵便<br>番号", 'tinkers') . '</th>';
	echo '<th nowrap data-sort-ignore="true" style="width:10%">' . __("メールアドレス", 'tinkers') . '</th>';
	echo '<th nowrap style="width:15%">' . __('プラン名', 'tinkers') . '</th>';
	echo '<th nowrap data-type="date" style="text-align: center;width:2%;">' . __('支払<br>方法', 'tinkers') . '</th>';
	echo '<th nowrap data-type="date" style="text-align: center;width:3%;">' . __('ステータス', 'tinkers') . '</th>';
	echo '<th nowrap data-type="date" style="text-align: center;width:5%;">' . __('プラン開始日', 'tinkers') . '</th>';
	echo '<th nowrap data-type="date" style="text-align: center;width:5%;">' . __("試用期限", 'tinkers') . '</th>';
	echo '<th nowrap data-type="date" style="text-align: center;width:5%;color:red;">' . __("次回支払日", 'tinkers') . '</th>';
	echo '<th nowrap data-type="date" style="text-align: center;width:5%;">' . __('有効期限', 'tinkers') . '</th>';
	echo '<th nowrap data-type="numeric" style="text-align: center;width:2%;">' . __("登録<br>生徒数", 'tinkers') . '</th>';
	echo '<th nowrap data-type="numeric" style="text-align: center;width:2%;">' . __("登録<br>可能数", 'tinkers') . '</th>';
	echo '<th nowrap data-sort-ignore="true" style="text-align: center;width:2%">' . __('生徒<br>情報', 'tinkers') . '</th>';
	echo '<th nowrap data-sort-ignore="true" style="text-align: center;width:2%">' . __('生徒<br>進捗', 'tinkers') . '</th>';
	echo '<th nowrap data-sort-ignore="true" style="width:10%">' . __("前回メール送信日", 'tinkers') . '</th>';
	echo '</tr></thead>';

	foreach ($users as $user) {

		//生徒数を取得
		$student_count = tks_get_only_student_count($user->user_id);
		
		//支払登録されているメンバーを取得する
		$member = tks_pmpro_get_member( $user->user_id );
		//ユーザーの最新(最後)の注文状態を取得する
		$order = new MemberOrder();
  		$order->getLastMemberOrder( $user->user_id,NULL );
  		
		$tdrow_span = '';
		
		if (! empty($order)){
			$gateway = (property_exists($order,'gateway'))?$order->gateway:"";
			$status = (property_exists($order,'status'))?$order->status:"";
		}
		$monthly_price = intval($user->monthly_price);
		$student_free_count = intval($user->student_free_count);
		$extra_price = intval($user->student_price);

		$student_extra_count = ($student_count > $student_free_count) ? ($student_count - $student_free_count) : 0;
		$total_extra_price = $student_extra_count * $extra_price;
		$total_all_price = $monthly_price + $total_extra_price;

		$url_list_student = add_query_arg(array('action' => 'tks_request_list_students', 'group_id' => $user->group_id, 'group_reader' => $user->user_id), tks_get_home_url(tks_const::PAGE_STUDENT_LIST_FOR_LEADER,true));
		$url_list_student_progress = add_query_arg(array('action' => 'tks_request_list_students_progress', 'group_id' => $user->group_id, 'group_reader' => $user->user_id),  tks_get_home_url(tks_const::PAGE_STUDENT_LIST_PROGRESS,true));

		$leader_profile_link =  tks_get_home_url( tks_const::PAGE_EDIT_LEADER_FOR_ADMIN, true ) . '?edit_user=' . $user->user_id;

		$info_ico = "<img alt='生徒情報' src='" . plugins_url("../etc/link_32px.png", __FILE__) . "' width='24' height='24' />";

		echo '<tr>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->user_id . '</td>';
		echo '<td ' . $tdrow_span . 'style="text-align: center;"><input type="checkbox" style="transform: scale(1.5);"></td>';
		echo '<td ' . $tdrow_span . 'nowrap style="text-align: left;"><a href="' . $leader_profile_link . '" >' . $user->user_login . '</a></td>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->school_name . '</td>';
		echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;">' . $user->zipcode . '</td>';
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->user_email . '</td>';
		//申し込んでいるプランを全て表示する
		if( !empty( $member ) ){
			echo '<td nowrap style="text-align: left;">' . ( ! empty( $member->name ) ? $member->name : '' ) . '[ID:' . ( ! empty( $member->id ) ? $member->id : '' ) . ']</td>';
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $gateway ) ? $gateway : '' ) . '</td>';
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $status ) ? $status : '' ) . '</td>';
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $member->startdate ) ? ucfirst( date_i18n( get_option('date_format'), $member->startdate  ) ) : '' ) . '</td>';
			$trialing_until = get_user_meta( $user->user_id, 'pmprosd_trialing_until', true );
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $trialing_until ) ? ucfirst( date_i18n( get_option('date_format'), $trialing_until  ) ) : 'なし' ). '</td>';
			$nextupdate = tks_pmpro_payment_date_text($user->user_id);
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $nextupdate ) ? $nextupdate : 'なし' ). '</td>';
			$expir_date = tks_pmpro_expir_date_text($user->user_id);
			echo '<td nowrap style="text-align: center;">' . ( ! empty( $expir_date ) ? $expir_date : 'なし' ). '</td>';
		}else{
			//サブスクデータが1件もない場合
			echo '<td nowrap></td>';
			echo '<td nowrap></td>';
			echo '<td nowrap></td>';
			echo '<td nowrap></td>';
			echo '<td nowrap></td>';
			echo '<td nowrap></td>';
			echo '<td nowrap></td>';
		}
		echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;">' . $student_count . ' </td>';
		echo '<td nowrap style="text-align: center;">' . tks_get_can_regist_student_count($member->id,$user->user_id)  . '</td>';
		//生徒数が0人の場合は、詳細へリンクボタンは表示しない
		if ($student_count == 0) {
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"></td>';
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"></td>';
		} else {
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"><a href="' . $url_list_student . '" target="_blank">' . $info_ico . '</a></td>';
			echo '<td ' . $tdrow_span . 'nowrap style="text-align: center;"><a href="' . $url_list_student_progress . '" target="_blank">' . $info_ico . '</a></td>';
		}

		
		echo '<td ' . $tdrow_span . 'nowrap>' . $user->sendmaildate . '</td>';
		echo '</tr>';
		
	}

	echo '</table>';
	echo '</div>';
	//echo '<button id="tks_send_email" onClick="request_send()">メール送信</button>';

	//フッターへ出力
	add_action('wp_footer', function () {

		//jquery-confirmのインクルード
		tks_include_sweet_alert();
		//tinkers.js読み込み
		tks_include_tinkers_js();
		?>

		<script type='text/javascript'>
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
		<script type='text/javascript'>
			jQuery(document).ready(function() {
				footable_tr_default("#group_list");

				jQuery("#group_list tr").click(function() {
					if (jQuery(this)[0].rowIndex == 0) {
						return;
					}

					footable_tr_default("#group_list");
					footable_tr_click(jQuery(this));

				});

				jQuery('#check_all_row').on('change', function() {
					footable_set_row_all_check('#group_list', jQuery('#check_all_row').is(':checked'));
				});

				jQuery('#tks_send_email').on('click', function() {
					request_send();
				});


				function request_send() {
					jQuery("#tks_send_email").prop("disabled", true);
					var uids = new Array();
					var tsukis = new Array();
					var seikyu_tsuki;
					var before_seikyu_tsuki;
					var err_msg = new Array();
					var school_name;
					jQuery('.footable tr').each(function(index, row) {
						if (index > 0) {

							//教室名
							school_name = jQuery(row).children().eq(3).text();
							//前回請求月
							before_seikyu_tsuki = jQuery(row).children().eq(6).text();

							if (jQuery(row).find('input:checkbox').is(':checked')) {
								seikyu_tsuki = jQuery(row).find('#seikyu_tsuki').val().trim();
								if (seikyu_tsuki == '' || seikyu_tsuki.length == 0) {
									err_msg.push('(請求月未入力)' + school_name);
								} else if (Number(before_seikyu_tsuki) > Number(seikyu_tsuki)) {
									err_msg.push('(過去の請求月)' + school_name);
								} else {
									uids.push(jQuery(row).find("td:first").text());
									tsukis.push(seikyu_tsuki);
								}
							}
						}
					});

					if (err_msg.length > 0) {
						swal("エラー", err_msg.join('\n'), 'warning');
						jQuery("#tks_send_email").prop("disabled", false);
						return;
					}

					if (uids.length == 0) {
						swal("送信先が指定されていません!");
						jQuery("#tks_send_email").prop("disabled", false);
						return;
					}

					//送信確認
					swal({
							title: "メール送信を実行しますか？",
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
										'action': 'tks_send_mail',
										'uids': uids,
										'tsukis': tsukis
									},
									success: function(response) {
										jQuery("#tks_send_email").prop("disabled", false);
										alert(response);
										window.location.reload();
									}
								});
								return false;
							} else {
								jQuery("#tks_send_email").prop("disabled", false);
								return false;
							}
						});
				}
			});
		</script>
<?php
	});
}

/*
 * メールを送信する
 * パラメータは、リクエストから取得
*/
function tks_send_mail()
{
	$err_stack = array();
	$uids = $_POST['uids'];
	$tsuki = $_POST['tsukis'];
	$send_date = date_i18n('Y-m-d H:i:s');
	$subject = get_option(tks_const::TKSOPT_MAIL_SUBJECT);

	$message = get_option(tks_const::TKSOPT_MAIL_BODY);

	$search  = array(
		'{last_name}',
		'{first_name}',
		'{school_name}',
		'{taisho_tsuki}',
		'{student_count}',
		'{student_extra_count}',
		'{monthly_price}',
		'{total_extra_price}',
		'{extra_price}',
		'{total_all_price}'
	);

	if (empty($uids)) {
		echo "サーバーへリクエスト送信に失敗しました。\n再度実行して下さい。";
		die();
	} else {
		for ($i = 0; $i < count($uids); $i++) {
			//$userdata = get_userdata( $uids[$i] );
			//リーダー情報を取得
			$leader_meta = tks_get_leaders_meta($uids[$i]);

			//生徒数を取得
			$student_count = tks_get_only_student_count($uids[$i]);
			$student_free_count = intval($leader_meta->student_free_count);

			//月額外生徒数を取得
			$student_extra_count = ($student_count > $student_free_count) ? ($student_count - $student_free_count) : 0;

			$extra_price = intval($leader_meta->student_price);

			$total_extra_price = $student_extra_count * $extra_price;

			//$total_all_price = $monthly_price + $total_extra_price;
			$total_all_price = $total_extra_price;

			$replace = array(
				$leader_meta->lastname,
				$leader_meta->firstname,
				$leader_meta->school_name,
				$tsuki[$i],
				$student_count,
				$student_extra_count,
				number_format($leader_meta->monthly_price),
				number_format($total_extra_price),
				number_format($extra_price),
				number_format($total_all_price)
			);
			$message =  str_replace($search, $replace, $message);

			//メールヘッダー作成;
			$mail_args = array(
				'to' 			=> 	$leader_meta->user_email,
				'subject'		=>	$subject,
				'message'		=>	wpautop($message),
				'attachments'	=>	'',
				'headers' 		=> 	array(
					'content-type: text/html',
					'From: ' . wp_get_current_user()->user_email,
					'Reply-to: ' . wp_get_current_user()->user_email
				)
			);
			//ob_start();
			$ret = wp_mail($mail_args['to'], $mail_args['subject'], $mail_args['message'], $mail_args['headers'], $mail_args['attachments']);
			//$smtp_debug = ob_get_clean();
			if (!$ret) {
				array_push($err_stack, $leader_meta->display_name . "[" . $leader_meta->user_email . "]");
			} else {
				//メール送信日時の更新
				tks_update_usermeta_value($uids[$i], 'tks_sendmail_his', $send_date);
				tks_update_usermeta_value($uids[$i], 'tks_before_seikyu_tsuki', $tsuki[$i]);
			}
		}

		if (count($err_stack) > 0) {
			echo "以下のメール送信に失敗しました。\n" . implode(",", $err_stack);
		} else {
			echo "送信完了しました。";
		}

		die();
	}
}
add_action('wp_ajax_tks_send_mail', 'tks_send_mail');
add_action('wp_ajax_nopriv_tks_send_mail', 'tks_send_mail');


//生徒用の一覧表示を使用するためコメントアウト
/*
function tks_shortcode_list_student_for_admin(){
	
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='tks_request_list_students'){
		
		$group_id = isset($_REQUEST['group_id']) ? intval($_REQUEST['group_id']):null;
		$school_name = isset($_REQUEST['group_reader']) ? get_user_meta(intval($_REQUEST['group_reader']),'tks_school_name',true):null;

		if (!empty($group_id)){
	
			$query_args['role'] = 'subscriber';
			$query_args['meta_key'] = 'learndash_group_users_' . $group_id;
			$users = get_users($query_args);
			
			echo "<H4><div style='text-align: right;'>教室名：" . $school_name . "</div></H4>";
			echo "<H4><div style='text-align: right;'>生徒数：" . count($users) . "人</div></H4>";
			echo "<H4><div style='text-align: right;'>" . date_i18n('Y-m-d H:i:s') . "現在</div></H4>";

			echo "<table id='group_list' class='footable'>";
			echo "<thead>";
		  	echo "<tr>";
				echo '<th></th>';
				echo '<th data-sort-initial="true">'. __( "ログインID", 'tinkers' ) .'</th>';
				echo '<th>'. __( "生徒名", 'tinkers' ) .'</th>';
				echo '<th>'. __( "登録日", 'tinkers' ) .'</th>';
				//echo '<th data-sort-ignore="true" style="text-align: center;">'. __( '詳細', 'tinkers' ) .'</th>';
			echo '</tr></thead>';

			foreach($users as $user){
				$url =  "/s-account-edit-leader/?edit_user=" . $user->ID;
				echo '<tr>';
					echo '<td>'. $user->user_id .'</td>';
					echo '<td>'. $user->user_login .'</td>';
					echo '<td>'. $user->display_name .'</td>';
					echo '<td>'. date("Y-m-d H:i:s",strtotime($user->user_registered . " +9 hours", time())) .'</td>';
					//Adminで生徒を修正するとグループリーダーがAdminになってしまうため不可
					//echo '<td style="text-align: center;"><a href="' .$url .'" target="_blank">'. __( '詳細', 'tinkers' ) . '</a></td>';
				echo '</tr>';
			}

			echo '</table>';
			

		}
	}
}
add_shortcode('tks_list_student_for_admin','tks_shortcode_list_student_for_admin');
*/
