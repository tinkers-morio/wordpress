<?php

/**
 * 動画視聴後の動作（LearnDash3以上）
 * 動画の視聴が終わったら次へ進むボタンのラベルを切り替える
 *
 * for learnDash3
 */
function tks_at_end_movie(){
	
	//LeanDashのテーマが３以外は抜ける
	if (! tks_is_learndash_theme3()){
		return;
	}
	//LearnDashのレッスントピックページでなければ抜ける
	if (! tks_is_lesson_topic_page()){
		return;
	}
	$post_id = get_the_id();
	
	//レッスンの場合
	if (is_singular('sfwd-lessons')){
		$post_meta = get_post_meta($post_id, '_sfwd-lessons', true);	//レッスンメタ情報を取得
		//動画のURLを取得しJavaScriptに動画があるレッスン、トピックであるかのフラグを設定
		if (array_key_exists('sfwd-lessons_lesson_video_url',$post_meta) && !empty($post_meta['sfwd-lessons_lesson_video_url'])){
			$has_movie = 'let has_movie = true;';
		}

	}elseif (is_singular('sfwd-topic')) {
		$post_meta = get_post_meta($post_id, '_sfwd-topic', true);		//トピックメタ情報を取得
		//動画のURLを取得しJavaScriptに動画があるレッスン、トピックであるかのフラグを設定
		if (array_key_exists('sfwd-topic_lesson_video_url',$post_meta) && !empty($post_meta['sfwd-topic_lesson_video_url'])){
			$has_movie = 'let has_movie = true;';
		}
	}else{
		return;		//以外は、Vimeo動画が正しく接続されているか監視する必要がないのでここで終わり
	}

	$movie_at_end_status = get_post_meta( $post_id, '_tks_lms_movie_at_end_status', true );
	$movie_at_end_use_custom = get_post_meta( $post_id, '_tks_lms_movie_at_end_message_use_custom', true );
	$movie_at_end_after_link_open = get_post_meta( $post_id, '_tks_lms_movie_at_end_link_open', true );
	$movie_at_end_after_link = get_post_meta( $post_id, '_tks_lms_movie_at_end_link', true );
	$movie_at_end_after_message_title = get_post_meta( $post_id, '_tks_lms_movie_at_end_message_title', true );
	$movie_at_end_after_message = get_post_meta( $post_id, '_tks_lms_movie_at_end_message', true );

	if (!empty($movie_at_end_status) || $movie_at_end_status != 0){
		

		if (empty($movie_at_end_use_custom) || 
			$movie_at_end_use_custom != 'yes' || 
			empty($movie_at_end_after_message_title) ||
			empty($movie_at_end_after_message)){

			$movie_at_end_after_message_title = get_option(tks_const::TKSOPT_VIDO_AT_END_MESSAGE_TITLE,'');
			$movie_at_end_after_message = get_option(tks_const::TKSOPT_VIDO_AT_END_MESSAGE,'');
		}

		if (!empty($movie_at_end_after_message_title) && !empty($movie_at_end_after_message)){
			$movie_at_end_after_message = str_replace('\\n', 'n\r', $movie_at_end_after_message);
		}

		if (!empty($movie_at_end_after_link_open) &&
			$movie_at_end_after_link_open == 'yes'){

			if (empty($movie_at_end_after_link)){
				$ridirect = '';
			}else{
				$ridirect = $movie_at_end_after_link;
			}
		}
		
		//リンク先がない場合は、普通のメッセージ
		if (empty($ridirect)){
			$script = 'window.open("' . $movie_at_end_after_link .  '");';
			$script = 
			'swal({
				title: "' . $movie_at_end_after_message_title .'",
				text: ("'. $movie_at_end_after_message .'"),
				icon: "success",
				button: "OK",
			});';

		}else{
			$script = 
			'swal({
				title: "' . $movie_at_end_after_message_title .'",
				text: ("'. $movie_at_end_after_message .'"),
				icon: "info",
				allowOutsideClick: false,
				buttons: {
					ok: "OK",
					cancel: "キャンセル"
				}
			})
			.then(function(val) {
				if (val == "ok") {
					window.open("' . $ridirect .  '");
				} 
			});';
		}
	}


	echo "<script type='text/javascript'>";
	echo "jQuery(function(){";
	echo (empty($has_movie))?"":$has_movie;
echo <<< EOM
	let player = new Vimeo.Player(jQuery('.ast-oembed-container iframe'));
	
	//監視ターゲットの取得(完了ボタンがある場合のみ監視開始)
	let btns = jQuery('.learndash_mark_complete_button');
	let display = btns.is(':visible');
	
	if ((!player) && (has_movie)) {
		alert("ご不便をかけて申し訳ございません。なんらかの障害により動画サーバーに接続できません。時間をおいて再度接続して下さい。");
		return;
	}

	if (player){

		player.on('play', function() {
		console.log('Played the video');
		});

		player.getVideoTitle().then(function(title) {
		console.log('title:', title);
		});

		// 最後まで再生した時
		player.on('ended', function(data) {
			if (display){
				btns.css('animation','blinkAnime 0.7s infinite alternate');
			}
EOM;
	if (!empty($movie_at_end_status) || $movie_at_end_status != 0){
		echo $script;
	}
echo <<< EOM
		
			console.log('再生終了', data);
			//swal("Good job!", "You clicked the button!", "success");
		});
		
	}
	});
	</script>
EOM;

}
add_action('wp_footer', 'tks_at_end_movie');

/**
 * 動画視聴後の動作（レガシー版LearnDash2）
 * 動画の視聴が終わったら次へ進むボタンのラベルを切り替える
 *
 * for learnDash レガシー
 */
function tks_change_caption_next_topic()
{
	//LeanDashのテーマが３の場合は抜ける
	if (tks_is_learndash_theme3()){
		return;
	} 

	//LearnDashのレッスンかトピックページ且つ、タグがチャレンジのページは、チャレンジ問題のページ
    if (is_singular('sfwd-lessons') || is_singular('sfwd-topic')) {
		
		echo "	<script type='text/javascript'>";
        echo "	var isCharenge = false;\n";

        if (current_user_can('subscriber')) {
            if (has_tag('チャレンジ')) {
                $msg = "isCharenge = true;\n var dispMsg = 'チャレンジ問題ができたらこのボタンをクリックして次へ進もう！';";
            } elseif (has_tag('まとめ')) {
                $msg = "var dispMsg = 'このボタンをクリックしていちばんの下のクイズ問題にチャレンジだ！';";
            } else {
                $msg = "var dispMsg = 'このボタンをクリックして次へ進もう！';";
            }
        } else {
            if (has_tag('チャレンジ')) {
                $msg = "isCharenge = true;\n var dispMsg = '視聴完了（チャレンジ問題を試してからこのボタンをクリックして次へ進みます）';";
            } elseif (has_tag('まとめ')) {
                $msg = "var dispMsg = '視聴完了（このボタンをクリックしてトピック一覧下のクイズ問題に進みます）';";
            } else {
                $msg = "var dispMsg = '視聴完了（次へ進みます）';";
            }
        }
        echo $msg;

echo <<< EOM
	//監視ターゲットの取得(完了ボタンがある場合のみ監視開始)
	//const target = document.getElementById("learndash_mark_complete_button");
	var target = jQuery('.learndash_mark_complete_button');
	if (target){
	
		//初期化
		target.style['animation'] = 'none';

		//オブザーバーの作成
		const observer = new MutationObserver(records => {
		//ボタンテキストをガイドにする
			if (target.disabled == false){
				target.value = dispMsg;
				//チャレンジ問題でない場合は、完了ボタンを点滅させる
				if (isCharenge == false){
					target.style['animation'] = 'blinkAnime 0.5s infinite alternate';
				}
				observer.disconnect();
			}
		});

		//監視オプションの作成(属性かつdisabledの状態だけ監視する)
		const options = {
			attributes: true,
			attributeFilter: ["disabled"]
		};

		//監視の開始
		observer.observe(target, options);
	}

	</script>
EOM;
    }

}
add_action('wp_footer', 'tks_change_caption_next_topic');

/*
 * LearnDash3のログイン機能は使わないのに、ログイン周りのタグがログイン画面に出力されてしまうため
 * タグがあることによって、LearnDashのログイン処理が優先され、ログインエラーを画面に出力する事ができない
 * タグを削除する対応（LearnDash->設定->アクティブテンプレート->LearnDash3.0にすると発生(レガシーの場合は発生しない)
*/
add_filter( 'login_form_top', 
function($content){
	
	if (! method_exists('LearnDash_Theme_Register','get_active_theme_key'))
		return $content;

	if (tks_is_learndash_theme3())
		return __return_empty_string();
	
	return $content;

},9999,1 );

/**
 * マイページの場合、class=learndash_profile_detailsを書き換える
 * LearnDashによってユーザー詳細情報を表示している箇所を書き換える
 * プロフィールページのリンクも作る
 * ※メールアドレスや、いらない情報があるので！
 *
 * ※本メソッドをコメントアウトする事でオリジナルを表示する
 *
 * for learnDash
 */
function tks_customize_my_page()
{

    if (is_page(tks_const::PAGE_STUDENT_MY_PAGE)) {
		?>
		<script type='text/javascript'>
			<?php //コースリストとヘッダーを非表示 ?>
			jQuery('.ld-item-list-items').hide();
			jQuery('.ld-section-heading').hide();
			//jQuery('.learndash-wrapper').next('br').css('color','red');
		<?php

		if (tks_learndash_is_group_leader_user()){
		    echo "jQuery('.ld-profile-edit-link').eq(0).hide();";
		}else{
	        echo "jQuery('.ld-profile-edit-link').eq(0).text('マイプロフィール');";
		}
		
		
		echo "</script>";
		

    }
}
add_action('wp_footer', 'tks_customize_my_page');

add_filter( 'edit_profile_url',function($url, $user_id, $scheme){
	
	if (is_page(tks_const::PAGE_STUDENT_MY_PAGE)) 
		return tks_get_home_url(tks_const::PAGE_EDIT_STUDENT);

	return $url;

}, 10,3);

/*
 * リーダーのuser_idから受講中のコース一覧を取得する
*/
function tks_get_enroll_course_list($user_id)
{
	$group_id = tks_get_group_id($user_id);
    $courses = tks_learndash_group_enrolled_courses($group_id);
    $courses = array_map('intval', $courses);
    $courses = ld_course_list(array('post__in' => $courses, 'array' => true, 'orderby' => 'post_title', 'order' => 'ASC'));

    $enroll_corces = array();

	//id_course_listの戻り値が、配列か反復処理可能なオブジェクトか否かを判定
	if (is_iterable($courses)){
		foreach ($courses as $c) {
			if (sfwd_lms_has_access($c->ID, $user_id)) {
				array_push($enroll_corces, $c);
			}
		}

		return $enroll_corces;
	}else{
		return;
	}
}

/*
 * グループに属するユーザーのアクティビティを取得する
*/
function tks_get_user_activity_corse_list($group_id, $user_id, $corse_id)
{

    if (empty($group_id) || empty($user_id) || empty($corse_id)) {
        return null;
    }

    $activity = tks_learndash_reports_get_activity(array(
		'post_types' => 'sfwd-courses',
		'activity_types' => 'course', 
		'group_ids' => $group_id, 
		'user_ids' => $user_id, 
		'course_ids' => $corse_id, 
		'per_page' => 0));

    $activity = $activity['results'];

    return $activity[0];

//	$activity_list = array();

//	foreach($activity as $a){
//		array_push($activity_list,$a);
//	}

//	return $activity_list;
}

/**
 * 終了証に表示させるロゴを取得する
 */
add_shortcode('tks_get_certificate_logo', 
function()
{
	$defult = tks_get_home_url(tks_const::URL_CIRTIFICATE_LOGO,false);
	//$defult = tks_get_home_url('/wp-content/uploads/2018/07/LMS_LOGO-e1530931550871.png',false);
	
	//管理者はデフォルトロゴ
	if (current_user_can('administrator')) {
		$url = $defult;
		//グループリーダー
	} elseif (tks_learndash_is_group_leader_user()) {
		$user_id = wp_get_current_user()->ID;
		//生徒
	} elseif (current_user_can('subscriber')) {
		$user_id = tks_get_leader_of_student(wp_get_current_user()->ID)['ID'];
	} else {
		$url = $defult; 
	}

	if (!empty($user_id)){
		$image_id = get_user_meta($user_id, 'tks_custom_logo', true);
		if (empty($image_id)) {
			$url = $defult;
		}else{
			$url = wp_get_attachment_url($image_id);
		}
	}
	
	return '<img src="' . $url . '" alt="logo" width="200" height="80" />';
	
});

/**
 * LearnDashフォーカスモードがOnの場合、ヘッダーに表示するユーザー名を整形する
 */
add_filter( 'ld_focus_mode_welcome_name', 
function($user_nicename, $user_data){
	return '<BR>' . tks_get_user_display_name($user_data->ID) . 'さん';
},9999,2 );

/**
 * 生徒プロフィール画面(マイページ)において、ポイント数情報を表示する箇所を編集する(バッジ獲得数を表示させる)
 */
add_filter( 'learndash_profile_stats',
function($profile_array, $user_id)
{

	foreach ($profile_array as $key => &$profile) {
		if ($profile['class'] == 'ld-profile-stat-points'){
			$achivement = tks_badgeos_get_user_achievements(array('user_id'=>$user_id));
			$profile['title'] = __('バッジ','tinkers');
			$profile['value'] = (empty($achivement) ? 0:count($achivement));
			//ポイント情報を表示させない場合は以下を復活させる
			//$ret = array_splice($profile_array, $key,1);
			break;
		}
	}

	return $profile_array;

},10,2);

/**
 * LearnDashレッスン・トピック設定画面に拡張タブを表示する
 */
add_filter( 'learndash_content_tabs',
function($tabs,$context, $course_id, $user_id){
	global $post;

	for ($i = 1; $i <= tks_const::EXTRA_TAB_MAX_COUNT; $i++){
	
		$tab_title = get_post_meta( $post->ID, '_tks_lms_extra_tab_title' . $i, true );
		$tab_icon = get_post_meta( $post->ID, '_tks_lms_extra_icon' . $i, true );
		$tab_content = get_post_meta( $post->ID, '_tks_lms_extra_tab' . $i, true );
		//html・ショートコードを実行し整形して出力させる
		$tab_content = wp_specialchars_decode( $tab_content, ENT_QUOTES );
		if ( ! empty( $tab_content ) ) {
			$tab_content = do_shortcode( $tab_content );
			$tab_content = wpautop( $tab_content );
		}
		$tab_only_leader = get_post_meta( $post->ID, '_tks_lms_extra_tab_only_leader' . $i, true );

		$visible_ok = false;

		if ($tab_only_leader == 'on'){
			$visible_ok = (tks_learndash_is_group_leader_user() || current_user_can('administrator'));
			if (tks_is_highlevel_mission_lesson_topic_page() || tks_is_shikkari_lesson_topic_page()){
				//生徒だった場合は、リーダーのユーザー設定(拡張タブを表示する設定がONになっているかチェックして表示する)
				if (!tks_learndash_is_group_leader_user($user_id)){
					//生徒の場合は、親であるリーダーの設定を確認
					$leader = tks_get_leader_of_student($user_id);
					//if (get_the_author_meta(tks_const::TKSOPT_SHOW_EXTRA_TAB,$leader["ID"]) == tks_const::TKSOPT_SHOW_EXTRA_TAB_VAL1){
					//リーダーの設定が表示しないだった場合は、生徒がどんな設定でも表示しない。つまりリーダーが表示OKの場合のみ生徒の設定（YesOrNo）が有効となる
					//ハイレベルの場合
					if (tks_is_highlevel_mission_lesson_topic_page()){
						if (get_user_meta((int)$leader["user_id"],tks_const::TKSOPT_SHOW_EXTRA_TAB_HILEVEL,true) == tks_const::TKSOPT_SHOW_EXTRA_TAB_VAL_YES){
							if (get_user_meta($user_id,tks_const::TKSOPT_SHOW_EXTRA_TAB_HILEVEL,true) == tks_const::TKSOPT_SHOW_EXTRA_TAB_VAL_YES){
								$visible_ok = true;
							}
							
						}
					}
					//しっかりの場合
					if (tks_is_shikkari_lesson_topic_page()){
						if (get_user_meta((int)$leader["user_id"],tks_const::TKSOPT_SHOW_EXTRA_TAB_SHIKKARI,true) == tks_const::TKSOPT_SHOW_EXTRA_TAB_VAL_YES){
							if (get_user_meta($user_id,tks_const::TKSOPT_SHOW_EXTRA_TAB_SHIKKARI,true) == tks_const::TKSOPT_SHOW_EXTRA_TAB_VAL_YES){
								$visible_ok = true;
							}
							
						}
					}

				}
			}
		}else{
			$visible_ok = true;
		}
		
		if ($visible_ok){
			$extra_tab = create_extra_tab_setting_araay( $tab_title, $tab_icon, $tab_content, $i);

			if (! empty($extra_tab)){
				//LearnDashのタブ配列へ追加
				array_push($tabs,$extra_tab);
			}
		}
	}
	
	return $tabs;

},10,4);

/**
 * LearnDashの拡張タブ用配列を生成して返す
 */
function create_extra_tab_setting_araay($tab_title, $tab_icon, $tab_content, $tab_index){
	
	//表示すべきタブのコンテンツがあるか否か
	if (empty($tab_content)) {
		return __return_null();
	}

	//タイトルが省略されていた場合は、デフォルトタイトル使用
	if (empty($tab_title)) {
		$tab_title = '追加テキスト' . (($tab_index == 0)?'':$tab_index);
	}

	//アイコンが省略されていた場合は、デフォルトアイコン使用
	if (empty($tab_icon)){
		$tab_icon = 'ld-icon-materials';
	}

	$extra_tab = array(
        'id'        =>  'tks_extra_content' . $tab_index,
        'icon'      =>  $tab_icon,
        'label'     =>  $tab_title,
        'content'   => $tab_content,
        'condition' => ( isset($tab_content) && !empty($tab_content) )
	);

	return $extra_tab;
}

/*
add_filter( 'learndash_metabox_save_fields_learndash-lesson-display-content-settings',
function($filter_saved_fields, $settings_metabox_key, $settings_screen_id ){
	//$test = array('lesson_materials2'=>'testaaaaaaaa');
	//$filter_saved_fields = array_merge($filter_saved_fields,$test);
	//$filter_saved_fields[lesson_materials_enabled] = "on";
	return $filter_saved_fields;

}, 30, 3 );

add_filter( 'learndash_metabox_save_fields_learndash-topic-display-content-settings',
function($filter_saved_fields, $settings_metabox_key, $settings_screen_id ){
	//$test = array('lesson_materials2'=>'testaaaaaaaa');
	//$filter_saved_fields = array_merge($filter_saved_fields,$test);
	//$filter_saved_fields[lesson_materials_enabled] = "on";
	return $filter_saved_fields;

}, 30, 3 );
*/

add_action( 'add_meta_boxes', 
function() {
	//レッスンとトピック設定画面のみに表示
    $screens = array( 'sfwd-lessons','sfwd-topic');
	
    foreach ( $screens as $screen ) {
        add_meta_box(
            'tks-lms-extra-setting-iconlist',
            __( 'Tinkers追加設定(拡張タブアイコン)', 'tinkers' ),
            'tks_extra_setting_for_learndash_icon_list_callback',
			$screen,
			'side'
        );
    }
});

add_action( 'admin_enqueue_scripts','tks_admin_include_css');
function tks_admin_include_css(){
	$current_screen = get_current_screen();
	if (($current_screen->id == 'sfwd-lessons') || ($current_screen->id == 'sfwd-topic')){
		wp_enqueue_style( 'tinkers-admin-css', plugins_url( '../css/tinker-admin.css', __FILE__ ) );		
	}
}

function tks_extra_setting_for_learndash_icon_list_callback($post){
	
	echo '<span style="font-weight: bold;color:blue;font-size: 11pt">使用可能なタブアイコン</span>';
	echo '<p>';
	echo '<span class="ld-icon ld-icon-alert">ld-icon-alert<span><p>';
	echo '<span class="ld-icon ld-icon-unlocked">ld-icon-unlocked<span><p>';
	echo '<span class="ld-icon ld-icon-quiz">ld-icon-quiz<span><p>';
	echo '<span class="ld-icon ld-icon-materials">ld-icon-materials<span><p>';
	echo '<span class="ld-icon ld-icon-download">ld-icon-download<span><p>';
	echo '<span class="ld-icon ld-icon-course-outline">ld-icon-course-outline<span><p>';
	echo '<span class="ld-icon ld-icon-content">ld-icon-content<span><p>';
	echo '<span class="ld-icon ld-icon-complete">ld-icon-complete<span><p>';
	echo '<span class="ld-icon ld-icon-certificate">ld-icon-certificate<span><p>';
	echo '<span class="ld-icon ld-icon-calendar">ld-icon-calendar<span><p>';
	echo '<span class="ld-icon ld-icon-assignment">ld-icon-assignment<span><p>';
	echo '<span class="ld-icon ld-icon-arrow-up">ld-icon-arrow-up<span><p>';
	echo '<span class="ld-icon ld-icon-arrow-right">ld-icon-arrow-right<span><p>';
	echo '<span class="ld-icon ld-icon-arrow-left">ld-icon-arrow-left<span><p>';
	echo '<span class="ld-icon ld-icon-arrow-down">ld-icon-arrow-down<span><p>';
	echo '<span class="ld-icon ld-icon-remove">ld-icon-remove<span><p>';
	echo '<span class="ld-icon ld-icon-comments">ld-icon-comments<span><p>';
	echo '<span class="ld-icon ld-icon-search">ld-icon-search<span><p>';
}


/**
 * LearnDashレッスン、トピック編集画面に、metaboxを追加する
 * LD3用レイアウト対応で、拡張タブを追加するための設定metabox
 */
add_action( 'add_meta_boxes', 
function() {
	//レッスンとトピック設定画面のみに表示
    $screens = array( 'sfwd-lessons','sfwd-topic');

    foreach ( $screens as $screen ) {
        add_meta_box(
            'tks-lms-extra-setting',
            __( 'Tinkers追加設定', 'tinkers' ),
            'tks_extra_setting_for_learndash_lesson_topic_callback',
			$screen,
			'advanced'
        );
    }
});

/**
 * アクションadd_meta_boxesのコールバック関数
 * metabox内のコンテンツを表示する
 */
function tks_extra_setting_for_learndash_lesson_topic_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'tks_lms_extra_nonce', 'tks_lms_extra_nonce' );
	
	//動画視聴完了後における動作
	//完了後リンクを開く(別ウィンドウ)
	//完了後メッセージを表示する
	//なにもしない
	$movie_at_end_status = get_post_meta( $post->ID, '_tks_lms_movie_at_end_status', true );
	if (empty($movie_at_end_status)){
		$movie_at_end_status = 0;
	}
	$movie_at_end_after_link = get_post_meta( $post->ID, '_tks_lms_movie_at_end_link', true );
	$movie_at_end_after_message_title = get_post_meta( $post->ID, '_tks_lms_movie_at_end_message_title', true );
	//$movie_at_end_after_message = str_replace('\\\n','{n}', get_post_meta( $post->ID, '_tks_lms_movie_at_end_message', true ));
	$movie_at_end_after_message = get_post_meta( $post->ID, '_tks_lms_movie_at_end_message', true );
	$movie_at_end_link_open =  get_post_meta( $post->ID, '_tks_lms_movie_at_end_link_open', true );
	$movie_at_end_after_message_use_custom =  get_post_meta( $post->ID, '_tks_lms_movie_at_end_message_use_custom', true );
	
	echo '<span style="font-weight: bold;color:blue;font-size: 11pt">【動画視聴後の動作】</span>';
	echo '<p>';
	echo '<table width="100%">';
	echo '<tr>';
	echo '<td align="left"><label><input name="tks_lms_movie_at_end_status" type="radio" value="0" ' . ($movie_at_end_status==0?'checked="checked"':'') . '/> なにもしない</label></td>';
	echo '<td></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td align="left"><label><input name="tks_lms_movie_at_end_status" type="radio" value="1" ' . ($movie_at_end_status==1?'checked="checked"':'') . '/> 完了後メッセージを表示する</label>';
	echo '（<label><input id="tks_lms_movie_at_end_message_use_custom" name="tks_lms_movie_at_end_message_use_custom" type="checkbox" value="yes" ' . ($movie_at_end_after_message_use_custom?'checked':'') . '/>カスタムメッセージ使用</label>）</td>';
	echo '<td><label id="lbl_movie_at_end_message_title">タイトル：<input type="text" id= "tks_lms_movie_at_end_message_title" name="tks_lms_movie_at_end_message_title" value="' . esc_attr($movie_at_end_after_message_title) . '" size="90"></label></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td></td>';
	echo '<td><label id="lbl_movie_at_end_message" >メッセージ：<input type="text" id= "tks_lms_movie_at_end_message" name="tks_lms_movie_at_end_message" value="' . esc_attr($movie_at_end_after_message) . '" size="90">※改行は'. "\\" . '\\nと入力</label></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td align="left"><label><input id="tks_lms_movie_at_end_link_open" name="tks_lms_movie_at_end_link_open" type="checkbox" value="yes" ' . ($movie_at_end_link_open?'checked':'') . '/>完了後リンクを開く(別ウィンドウ)</label></td>';
	echo '<td><label id="lbl_movie_at_end_link">リンク先：<input type="text" id= "tks_lms_movie_at_end_link" name="tks_lms_movie_at_end_link" value="' . esc_attr($movie_at_end_after_link) . '" size="90"></label></td>';
	echo '</tr>';
	echo '</table>';
	echo '<hr>';

	echo '<script type="text/javascript">
	jQuery("#tks_lms_movie_at_end_message_use_custom").change(function() {
		checkToggleUseCustom(this.checked);
	});
	checkToggleUseCustom(jQuery("#tks_lms_movie_at_end_message_use_custom").prop("checked"));
	function checkToggleUseCustom(status){
		if(status) {
			jQuery("#lbl_movie_at_end_message").show();
			jQuery("#lbl_movie_at_end_message_title").show();
		}else{
			jQuery("#lbl_movie_at_end_message").hide();
			jQuery("#lbl_movie_at_end_message_title").hide();
		}
	}
	checkToggleLinkOpen(jQuery("#tks_lms_movie_at_end_link_open").prop("checked"));
	jQuery("#tks_lms_movie_at_end_link_open").change(function() {
		checkToggleLinkOpen(this.checked);
	});
	function checkToggleLinkOpen(status){
		if(status) {
			jQuery("#lbl_movie_at_end_link").show();
		}else{
			jQuery("#lbl_movie_at_end_link").hide();
		}
	}
	</script>';

	$settings = array();

	for ($i=1; $i <= tks_const::EXTRA_TAB_MAX_COUNT; $i++) {
		$setting = array(
			'tab_title'  => get_post_meta( $post->ID, '_tks_lms_extra_tab_title' . $i, true ),
			'tab_icon'  => get_post_meta( $post->ID, '_tks_lms_extra_icon' . $i, true ),
			'tab_content' => get_post_meta( $post->ID, '_tks_lms_extra_tab' . $i, true ),
			'tab_only_leader' => get_post_meta( $post->ID, '_tks_lms_extra_tab_only_leader' . $i, true )
		);
		array_push($settings,$setting);
	}

	$count = 1;
	foreach ($settings as $key => $value) {
		
		// アイコンが省略されていたらデフォルトセット
		if (empty($value['tab_icon'] )){
			$value['tab_icon'] = 'ld-icon-materials';
		}
		
		//拡張タブ設定
		echo '<span style="font-weight: bold;color:blue;font-size: 11pt">【拡張タブ - ' . $count . '】</span>';
		echo '<p>';
		echo '<table width="100%">';
		echo '<tr>';
		echo '<th align="left"><label for="tks_lms_extra_tab_title' . $count . '"><strong>拡張タブタイトル' . $count . '</strong></label></th>';
		echo '<td><input type="text" id= "tks_lms_extra_tab_title' . $count . '" name="tks_lms_extra_tab_title' . $count . '" value="' . esc_attr($value['tab_title']) . '" size="90"></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th align="left"><label for="tks_lms_extra_tab_icon' . $count . '"><strong>拡張タブアイコン' . $count . '</strong></label></th>';
		echo '<td><input type="text" id= "tks_lms_extra_tab_icon' . $count . '" name="tks_lms_extra_tab_icon' . $count . '" value="' . esc_attr($value['tab_icon']) . '" size="50"></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th rowspan="2" valign="top" align="left"><label for="tks_lms_extra_tab' . $count . '"><strong>拡張タブコンテンツ' . $count . '</strong></label></th>';
		echo '<td>ここに拡張タブに表示するコンテンツを設定します</td>';
		echo '</tr>';
		echo '<tr><td>';
		echo wp_editor(
			$value['tab_content'],
			"tks_lms_extra_tab". $count,
			array(
				'textarea_name' => "tks_lms_extra_tab" . $count,
				'textarea_rows' => 5,
			)
		);
		echo '</td></tr>';
		
		echo '<tr>';
		echo '<th align="left"><label for="tks_lms_extra_tab_only_leader' . $count . '"><strong>リーダーのみ閲覧可能にする' . $count . '</strong></label></th>';
		echo '<td><input type="checkbox" ' . ($value['tab_only_leader'] == "on" ? 'checked' : '') . ' id= "tks_lms_extra_tab_only_leader' . $count . '" name="tks_lms_extra_tab_only_leader' . $count . '"></td>';
		echo '</tr>';

		echo '</table><p>';
		echo '<hr>';
		$count++;
	}

    //echo '<textarea style="width:100%" id="tks_lms_extra_nonce1" name="tks_lms_extra_nonce1">' . esc_attr( $tab_content ) . '</textarea>';
}

/**
 * LearnDashレッスン・トピックのTinekrs設定を保存する
 *
 * @param int $post_id
 */
function tks_save_extra_setting_for_learndash_lesson_topic( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['tks_lms_extra_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['tks_lms_extra_nonce'], 'tks_lms_extra_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

	/* OK, it's safe for us to save the data now. */
	if ( isset( $_POST['tks_lms_movie_at_end_status'] ) ) {
		
		$movie_end_status = $_POST['tks_lms_movie_at_end_status'];
		$message_custom_use = (isset($_POST['tks_lms_movie_at_end_message_use_custom'])?$_POST['tks_lms_movie_at_end_message_use_custom']:'no');
		$link_open = (isset($_POST['tks_lms_movie_at_end_link_open'])?$_POST['tks_lms_movie_at_end_link_open']:'no');

		if ( $movie_end_status == 1){
			update_post_meta( $post_id, '_tks_lms_movie_at_end_status', $movie_end_status );
		}else{
			delete_post_meta( $post_id, '_tks_lms_movie_at_end_status' );
		}
		
		if ( $movie_end_status == 1 && $message_custom_use == 'yes') {
			update_post_meta( $post_id, '_tks_lms_movie_at_end_message_use_custom', $message_custom_use );
		}else{
			delete_post_meta( $post_id, '_tks_lms_movie_at_end_message_use_custom' );
		}
		if ( $movie_end_status == 1 && $message_custom_use == 'yes' ){
			if (isset( $_POST['tks_lms_movie_at_end_message_title'] ) &&  $_POST['tks_lms_movie_at_end_message_title'] != '') {
				update_post_meta( $post_id, '_tks_lms_movie_at_end_message_title',  sanitize_text_field($_POST['tks_lms_movie_at_end_message_title']));
			}
		}
		if ( $movie_end_status == 1 && $message_custom_use == 'yes' ){
			if (isset( $_POST['tks_lms_movie_at_end_message'] ) && $_POST['tks_lms_movie_at_end_message'] != '') {
				$tmp = sanitize_text_field($_POST['tks_lms_movie_at_end_message']);
				update_post_meta( $post_id, '_tks_lms_movie_at_end_message', $tmp);
			}
		}

		if ( $link_open == 'yes' ) {
			update_post_meta( $post_id, '_tks_lms_movie_at_end_link_open', 'yes' );
			if ( isset( $_POST['tks_lms_movie_at_end_link'] ) && $_POST['tks_lms_movie_at_end_link'] != '' ) {
				update_post_meta( $post_id, '_tks_lms_movie_at_end_link', sanitize_text_field($_POST['tks_lms_movie_at_end_link']) );
			}
		} else {
			delete_post_meta( $post_id, '_tks_lms_movie_at_end_link_open' );
		}
	}

	for ($i=1; $i<= tks_const::EXTRA_TAB_MAX_COUNT; $i++){
		// Make sure that it is set.
		if ( isset( $_POST['tks_lms_extra_tab_title' . $i] ) && $_POST['tks_lms_extra_tab_title' . $i] != '') {
			// Update the meta field in the database.
			$tab_title = sanitize_text_field($_POST['tks_lms_extra_tab_title' . $i]);
			update_post_meta( $post_id, '_tks_lms_extra_tab_title' . $i, $tab_title );
		}else{
			delete_post_meta( $post_id, '_tks_lms_extra_tab_title' . $i );
		}

		if ( isset( $_POST['tks_lms_extra_tab_icon' . $i] ) && $_POST['tks_lms_extra_tab_icon' . $i] != '') {
			// Update the meta field in the database.
			$tab_icon = sanitize_text_field($_POST['tks_lms_extra_tab_icon' . $i]);
			update_post_meta( $post_id, '_tks_lms_extra_icon' . $i, $tab_icon );
		}else{
			delete_post_meta( $post_id, '_tks_lms_extra_icon' . $i );
		}

		if ( isset( $_POST['tks_lms_extra_tab' . $i] ) && $_POST['tks_lms_extra_tab' . $i] != '') {
			// Update the meta field in the database.
			update_post_meta( $post_id, '_tks_lms_extra_tab' . $i, $_POST['tks_lms_extra_tab' . $i] );
		}else{
			delete_post_meta( $post_id, '_tks_lms_extra_tab' . $i );
		}

		if ( isset( $_POST['tks_lms_extra_tab_only_leader' . $i] ) && $_POST['tks_lms_extra_tab_only_leader' . $i] == 'on') {
			// Update the meta field in the database.
			update_post_meta( $post_id, '_tks_lms_extra_tab_only_leader' . $i, $_POST['tks_lms_extra_tab_only_leader' . $i] );
		}else{
			delete_post_meta( $post_id, '_tks_lms_extra_tab_only_leader' . $i );
		}
	}
}
add_action( 'save_post', 'tks_save_extra_setting_for_learndash_lesson_topic' );


/*
投稿ページに設定されたmetaデータを連結して表示させる場合
function tks_save_extra_setting_for_learndash_lesson_topic_before_post( $content ) {

    global $post;

    // retrieve the global notice for the current post
    $global_notice = esc_attr( get_post_meta( $post->ID, tks_const::META_KEY_LMS_EXTRA_TAB1, true ) );

    $notice = "<div class='sp_global_notice'>$global_notice</div>";

    return $notice . $content;

}
add_filter( 'the_content', 'tks_save_extra_setting_for_learndash_lesson_topic_before_post' );
*/

/**
 * 生徒の最後のアクセスしたページ(レッスン、トピック)を取得する
 */
function tks_get_last_activity_pageinfo($user_id){
	
	if (empty($user_id)){

		return;

	}

	$last_know_step = get_user_meta( $user_id, 'learndash_last_known_page', true );

	// 受講開始してない(どこにもアクセスしていない)
	if ( empty( $last_know_step ) ) {

		return;
	}
	$step_course_id = 0;

	//取得データがカンマで区切られているか？
	if ( false !== strpos( $last_know_step, ',' ) ) {
		//カンマ区切りでデータを取得
		$last_know_step = explode( ',', $last_know_step );
		$step_id        = $last_know_step[0];	//コース以外のレッスンorトピックID
		$step_course_id = $last_know_step[1];	//コースID
	} else {

		// 数値チェック
		if ( absint( $last_know_step ) ) {
			$step_id = $last_know_step;
		} else {
			//数値でなければ変なデータなのでバイバイ
			return;
		}

	}
	
	//ステップIDとコースIDが同じ場合は、最終アクティビティは、コース
	//異なる場合は、コースのタイトルも取得する
	if ($step_id != $step_course_id && $step_course_id != 0){
		$course_title = get_post( $step_course_id ) ->post_title;
	}

	//最後に開いていたページを取得
	$last_know_post_object = get_post( $step_id );

	if ( null !== $last_know_post_object ) {
		$post_type        = $last_know_post_object->post_type; // getting post_type of last page.
		$label            = get_post_type_object( $post_type ); // getting Labels of the post type.
		$title        	  = $last_know_post_object->post_title;
		
		if ( function_exists( 'learndash_get_step_permalink' ) ) {
			$permalink = learndash_get_step_permalink( $step_id, $step_course_id );
		} else {
			$permalink = get_permalink( $step_id );
		}
	}

	return array(
		'course_title' => $course_title,
		'title' => $title,
		'link' => $permalink
	);
}

/**
 * プランIDから受講可能なコース一覧を取得する(リーダー自動判別)
 * 生徒用+リーダー用　マージして返す
 */
function tks_get_course_by_plan($plan_id){
	$courses = [];
	$isLeader = tks_learndash_is_group_leader_user();
	if (array_search($plan_id, tks_get_plan_basic()) !== false){
		$courses = tks_get_basic_course();
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader());
		}
	}
	if (array_search($plan_id, tks_get_plan_regular()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance1()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance2()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance3()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance4()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance5()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5());
		}
	}

	if (array_search($plan_id, tks_get_plan_advance6()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6());
		}
	}
	
	if (array_search($plan_id, tks_get_plan_advance7()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance8()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance9()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance10()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance11()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance12()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance13()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance14()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance15()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance16()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance17()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance18()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17(),tks_get_advance_course18());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17(),tks_get_advance_course_leader18());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance19()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17(),tks_get_advance_course18(),tks_get_advance_course19());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17(),tks_get_advance_course_leader18(),tks_get_advance_course_leader19());
		}
	}
	if (array_search($plan_id, tks_get_plan_advance20()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17(),tks_get_advance_course18(),tks_get_advance_course19(),tks_get_advance_course20());
		if ($isLeader){
			$courses = array_merge($courses,tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17(),tks_get_advance_course_leader18(),tks_get_advance_course_leader19(),tks_get_advance_course_leader20());
		}
	}

	return $courses;
}
/**
 * プランIDから受講可能なコース一覧を取得する(リーダー用のみ)
 */
function tks_get_course_by_plan_for_leader($plan_id){
	$courses = [];
	if (array_search($plan_id, tks_get_plan_basic()) !== false){
		$courses = array_merge(tks_get_basic_course_leader());
	}
	if (array_search($plan_id, tks_get_plan_regular()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader());
	}
	if (array_search($plan_id, tks_get_plan_advance1()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1());
	}
	if (array_search($plan_id, tks_get_plan_advance2()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2());
	}
	if (array_search($plan_id, tks_get_plan_advance3()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3());
	}
	if (array_search($plan_id, tks_get_plan_advance4()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4());
	}
	if (array_search($plan_id, tks_get_plan_advance5()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5());
	}
	if (array_search($plan_id, tks_get_plan_advance6()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6());
	}
	if (array_search($plan_id, tks_get_plan_advance7()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7());
	}
	if (array_search($plan_id, tks_get_plan_advance8()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8());
	}
	if (array_search($plan_id, tks_get_plan_advance9()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9());
	}
	if (array_search($plan_id, tks_get_plan_advance10()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10());
	}
	if (array_search($plan_id, tks_get_plan_advance11()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11());
	}
	if (array_search($plan_id, tks_get_plan_advance12()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12());
	}
	if (array_search($plan_id, tks_get_plan_advance13()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13());
	}
	if (array_search($plan_id, tks_get_plan_advance14()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14());
	}
	if (array_search($plan_id, tks_get_plan_advance15()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15());
	}
	if (array_search($plan_id, tks_get_plan_advance16()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16());
	}
	if (array_search($plan_id, tks_get_plan_advance17()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17());
	}
	if (array_search($plan_id, tks_get_plan_advance18()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17(),tks_get_advance_course_leader18());
	}
	if (array_search($plan_id, tks_get_plan_advance19()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17(),tks_get_advance_course_leader18(),tks_get_advance_course_leader19());
	}
	if (array_search($plan_id, tks_get_plan_advance20()) !== false){
		$courses = array_merge(tks_get_basic_course_leader(),tks_get_regular_course_leader(),tks_get_advance_course_leader1(),tks_get_advance_course_leader2(),tks_get_advance_course_leader3(),tks_get_advance_course_leader4(),tks_get_advance_course_leader5(),tks_get_advance_course_leader6(),tks_get_advance_course_leader7(),tks_get_advance_course_leader8(),tks_get_advance_course_leader9(),tks_get_advance_course_leader10(),tks_get_advance_course_leader11(),tks_get_advance_course_leader12(),tks_get_advance_course_leader13(),tks_get_advance_course_leader14(),tks_get_advance_course_leader15(),tks_get_advance_course_leader16(),tks_get_advance_course_leader17(),tks_get_advance_course_leader18(),tks_get_advance_course_leader19(),tks_get_advance_course_leader20());
	}

	return $courses;
}

/**
 * プランIDから受講可能なコース一覧を取得する(生徒用のみ)
 */
function tks_get_course_by_plan_for_student($plan_id){
	$courses = [];
	if (array_search($plan_id, tks_get_plan_basic()) !== false){
		$courses = tks_get_basic_course();
	}
	if (array_search($plan_id, tks_get_plan_regular()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course());
	}
	if (array_search($plan_id, tks_get_plan_advance1()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1());
	}
	if (array_search($plan_id, tks_get_plan_advance2()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2());
	}
	if (array_search($plan_id, tks_get_plan_advance3()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3());
	}
	if (array_search($plan_id, tks_get_plan_advance4()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4());
	}
	if (array_search($plan_id, tks_get_plan_advance5()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5());
	}
	if (array_search($plan_id, tks_get_plan_advance6()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6());
	}
	if (array_search($plan_id, tks_get_plan_advance7()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7());
	}
	if (array_search($plan_id, tks_get_plan_advance8()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8());
	}
	if (array_search($plan_id, tks_get_plan_advance9()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9());
	}
	if (array_search($plan_id, tks_get_plan_advance10()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10());
	}
	if (array_search($plan_id, tks_get_plan_advance11()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11());
	}
	if (array_search($plan_id, tks_get_plan_advance12()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12());
	}
	if (array_search($plan_id, tks_get_plan_advance13()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13());
	}
	if (array_search($plan_id, tks_get_plan_advance14()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14());
	}
	if (array_search($plan_id, tks_get_plan_advance15()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15());
	}
	if (array_search($plan_id, tks_get_plan_advance16()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16());
	}
	if (array_search($plan_id, tks_get_plan_advance17()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17());
	}
	if (array_search($plan_id, tks_get_plan_advance18()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17(),tks_get_advance_course18());
	}
	if (array_search($plan_id, tks_get_plan_advance19()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17(),tks_get_advance_course18(),tks_get_advance_course19());
	}
	if (array_search($plan_id, tks_get_plan_advance20()) !== false){
		$courses = array_merge(tks_get_basic_course(),tks_get_regular_course(),tks_get_advance_course1(),tks_get_advance_course2(),tks_get_advance_course3(),tks_get_advance_course4(),tks_get_advance_course5(),tks_get_advance_course6(),tks_get_advance_course7(),tks_get_advance_course8(),tks_get_advance_course9(),tks_get_advance_course10(),tks_get_advance_course11(),tks_get_advance_course12(),tks_get_advance_course13(),tks_get_advance_course14(),tks_get_advance_course15(),tks_get_advance_course16(),tks_get_advance_course17(),tks_get_advance_course18(),tks_get_advance_course19(),tks_get_advance_course20());
	}


	return $courses;
}

/**
 * コースBasicリストを取得する
 * 
 * @return array
 */
function tks_get_basic_course(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_BASIC_COURSE_STUDENT);
	return explode(',',$plans);

}

/**
 * コースREGULARリストを取得する
 * 
 * @return array
 */
function tks_get_regular_course(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_REGULAR_COURSE_STUDENT);
	return explode(',',$plans);

}

/**
 * コースADVANCEリストを取得する
 * 
 * @return array
 */
function tks_get_advance_course1(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE1_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course2(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE2_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course3(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE3_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course4(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE4_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course5(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE5_COURSE_STUDENT);
	return explode(',',$plans);

}

function tks_get_advance_course6(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE6_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course7(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE7_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course8(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE8_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course9(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE9_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course10(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE10_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course11(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE11_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course12(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE12_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course13(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE13_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course14(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE14_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course15(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE15_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course16(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE16_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course17(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE17_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course18(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE18_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course19(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE19_COURSE_STUDENT);
	return explode(',',$plans);

}
function tks_get_advance_course20(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE20_COURSE_STUDENT);
	return explode(',',$plans);

}

/**
 * コースBasicリストを取得する(リーダー用コース)
 * 
 * @return array
 */
function tks_get_basic_course_leader(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_BASIC_COURSE);
	return explode(',',$plans);

}

/**
 * コースREGULARリストを取得する(リーダー用コース)
 * 
 * @return array
 */
function tks_get_regular_course_leader(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_REGULAR_COURSE);
	return explode(',',$plans);

}

/**
 * コースADVANCEリストを取得する(リーダー用コース)
 * 
 * @return array
 */
function tks_get_advance_course_leader1(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE1_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader2(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE2_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader3(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE3_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader4(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE4_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader5(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE5_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader6(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE6_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader7(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE7_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader8(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE8_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader9(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE9_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader10(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE10_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader11(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE11_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader12(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE12_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader13(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE13_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader14(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE14_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader15(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE15_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader16(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE16_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader17(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE17_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader18(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE18_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader19(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE19_COURSE);
	return explode(',',$plans);

}
function tks_get_advance_course_leader20(){
	
	$plans = get_option(tks_const::TKSOPT_PLAN_ADVANCE20_COURSE);
	return explode(',',$plans);

}

/**
 * エクストラコースを取得する
 * プランに縛られない受講可能なコースを取得（管理画面のユーザー編集画面で設定してある値を取得）
 */
function tks_get_extra_course_by_user($user_id){
	$user_id = intval($user_id);
	$plans = get_user_meta($user_id,tks_const::TKSOPT_EXTRA_APPEND_COURSE,true);
	if (empty($plans)) return array();
	return explode(',',$plans);
}

/**
 * グループリーダーの場合、任意の順序でレッスン、トピックコンテンツにアクセス可能にする
 */
add_filter( 'learndash_previous_step_completed', 
function($enable, $progress_prev_id, $user_id ){
	
	//グループリーダーの場合
	if (tks_learndash_is_group_leader_user($user_id)){
		return true;
	}
	
	return $enable;

}, 30, 3 );

add_filter( 'ld_lesson_access_from__visible_after_specific_date', function($visible_after_specific_date, $lesson_id, $user_id){	//指定日のレッスン公開はこちら
	//echo $lesson_id .'-:-' . $visible_after_specific_date . '<br>';
	return $visible_after_specific_date;
},10,3);	

add_filter( 'ld_lesson_access_from__visible_after', function($lesson_access_from, $lesson_id, $user_id){						//X日後のレッスン公開はこちら
	//echo $lesson_id .'-:-' . $lesson_access_from . '<br>';
	return $lesson_access_from;
},10, 3);

/*
コース一覧のリボンテキストを変更する
 */
add_filter( 'learndash_course_grid_ribbon_text', function($ribbon_text, $course_id, $price_type){
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		$is_completed = tks_learndash_course_completed( get_current_user_id(), $course_id );
		if ($is_completed){
			$ribbon_text = "終わったね！";
		}else{
			if ($course_id == tks_const::COURCSE_ID_TAIKEN){
				$ribbon_text = "ここからはじめよう！";
			}
			if ($course_id == tks_const::COURCSE_ID_SHIKKARI){
				$ribbon_text = "ここから・・の次はこちら！";
			}
			if ($course_id == tks_const::COURCSE_ID_GAME){
				$ribbon_text = "ゲーム作りにチャンレジ！";
			}
			if ($course_id == tks_const::COURCSE_ID_WAZA){
				$ribbon_text = "参考にしよう！";
			}
			if ($course_id == tks_const::COURCSE_ID_HIGHT_LV_1){
				$ribbon_text = "しっかりが終わったら！";
			}
			if ($course_id == tks_const::COURCSE_ID_HIGHT_LV_2){
				$ribbon_text = "しっかりが終わったら！";
			}
			if ($course_id == tks_const::COURCSE_ID_HIGHT_LV_3){
				$ribbon_text = "しっかりが終わったら！";
			}
		}
	}
	return $ribbon_text;
},10,3 );

add_filter( 'learndash_course_grid_course_class', function($course_class, $course_id, $course_options){
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
		$is_completed = tks_learndash_course_completed( get_current_user_id(), $course_id );
		if ($is_completed){
			$course_class .= ' learndash-available learndash-complete';
		}
	}
	return $course_class;
},10, 3);

/*
* コース一覧画面では、コースカテゴリを生徒の場合は表示しないようにする
*/
add_filter('ld_course_list_shortcode_attr_values',function($arg,$arg2){
	
	//生徒の場合は、コースカテゴリ選択のセレクトボックスは表示しない
	$user_id = get_current_user_id();

	if(user_can($user_id,'administrator')){
		$arg['categoryselector'] = 'true';
		return $arg;
	} 

	if (user_can($user_id,'subscriber')){
		$arg['categoryselector'] = '';
	}else{
		$arg['categoryselector'] = 'true';
	}
	
	//リーダーの場合
	if (user_can($user_id,'group_leader')){
		//(ティンカーズリーダー)
		if (in_array($user_id, tks_const::RISTRICT_BYPASS_USER, true)){
			$arg['category__in'] = '102,103,104';
		//一般リーダー	
		}else{
			$arg['category__in'] = '102,103';
		}	
	}
	return $arg;
},2,10);

// add_filter( 'learndash_prerequities_bypass',
// function($bypass_course_limits_admin_users, $user_id, $post_id, $post ){
	
// 	if (tks_learndash_is_group_leader_user($user_id)){
// 		return true;
// 	}
	
// 	return $bypass_course_limits_admin_users;

// },30,4);
//↑こちらでは動かない、順序受講をバイパスする前提として管理者ユーザーである必要があると思われる
