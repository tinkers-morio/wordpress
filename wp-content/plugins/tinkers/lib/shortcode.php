<?php
/**
 * ログインページのデザイン用（publicbuilderのログイン画面用)
 * テキストボックスにアイコンを表示(ユーザーIDとパスワード)
 * ログイン状態を保存しますかの表示を無効
 * ※.wppb-alertは、文字が白になるので、背景が白の場合は注意！！！
 */
function tks_login_page_design(){

	//if ( is_page( '7318' ){
	
	add_action('wp_footer', function () {
	?>
		<style>
			input.input {
			    font-family: FontAwesome;
			    font-style: normal;
			    font-weight: normal;
			    text-decoration: inherit;

			}
			#user_login,
			#user_pass{
				padding:0.8em 0.8em 0.8em 0.8em;
				width:100% !important;
				border-radius:10px;
			}
			input.input:focus {
				outline: none;
				/*フォーカスした時に枠線を太く濃く*/
				border: solid 4px #00ECFF;
			}
			#wppb-submit{
				margin-top:0.8em;
				padding:0.3em 0.3em;
				font-size:1.1em;
				background:#F18F01;
				width:100% !important;
			}
			.wppb-error{
				border-radius:10px;
			}
			.wppb-alert,
			.wppb-alert > *{
				color:white;
			}
			.wppb-alert a{
			  display       : inline-block;
			  border-radius : 20px;          /* 角丸       */
			  text-align    : center;      /* 文字位置   */
			  cursor        : pointer;     /* カーソル   */
			  padding       : 0.7em 0.7em;   /* 余白       */
			  background    : #F18F01;     /* 背景色     */
			  color         : #ffffff;     /* 文字色     */
			  line-height   : 1em;         /* 1行の高さ  */
			  transition    : .3s;         /* なめらか変化 */
			  box-shadow    : 6px 6px 3px #666666;  /* 影の設定 */
			}
			.wppb-alert a:hover {
			  box-shadow    : none;        /* カーソル時の影消去 */
			}
		</style>
		<script type='text/javascript'>
		jQuery("#user_login")[0].setAttribute('placeholder', ' \uf007 ' + jQuery("label[for='user_login']").text());
		jQuery("#user_pass")[0].setAttribute('placeholder', ' \uf13e ' + jQuery("label[for='user_pass']").text());
		jQuery("label[for='user_login']").remove();
		jQuery("label[for='user_pass']").remove();
		jQuery(".login-remember").hide();
		jQuery('form').attr('autocomplete', 'off');
		jQuery("#user_login")[0].setAttribute('autocomplete', 'new-password');
		jQuery("#user_pass")[0].setAttribute('autocomplete', 'new-password');
		</script>
	<?php
		
	});
//	}

}
add_shortcode('tks_login_desing','tks_login_page_design');
/**
 * 動画ムービーを表示するためのVimeo動画用タグ出力(iframe)
 * path=URL
 * time=再生開始タイム
*/
function tks_get_tag_vimeo($arg){

	extract(shortcode_atts(array(
			'path' => '',
			'time' => ''
			), $arg));

	if ($path == ''){

		return "";

	}else{ 
		
		if ($time != ''){
			$time = '#t=' . $time;
		}

		$doga_path = $path . $time . '?byline=0';

		return "<iframe src='" . $doga_path . "'  width='640' height='360' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
	}

}
add_shortcode('vimeo_doga','tks_get_tag_vimeo');

/**
 * 現在ログイン中のユーザーが属する教室名を出力する
 * ショートコード
 * 
*/
function tks_get_school_dispname(){

	if (is_user_logged_in()){
		//管理者の場合は、display_nameを表示		
		$user = wp_get_current_user();
		if (current_user_can('administrator')){
			return tks_get_user_display_name($user->get('id'));
		} 
		//ログインユーザーのリーダーを取得
		$leader_id = tks_get_leader_of_student($user->get('id'));
		//教室名を取得
		$leader = tks_get_leaders_meta($leader_id);
		//教室名が設定されていなければ空文字を返す
		if ( empty( $leader->school_name)){
			return '';
		}

		return $leader->school_name;
	}
}
add_shortcode('tks_login_school_name','tks_get_school_dispname');

/**
 * 現在ログイン中のユーザー表示名を出力する
 * ショートコード
 * 
*/
function tks_get_title_loginuser_dispname(){

	if (is_user_logged_in()){
		$user = wp_get_current_user();
		if (tks_learndash_is_group_leader_user($user->get('id'))){
			//return $user->get('display_name') . "さんでログイン中";
			//return get_user_meta($user->get('id'),'tks_school_name',true) . " " . tks_get_user_display_name($user->get('id')) . "さんでログイン中";
			if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
				return tks_get_user_display_name($user->get('id')) . "さんでログイン中";
			}else{
				return "【リーダー】" . tks_get_user_display_name($user->get('id')) . "さんでログイン中";
			}
		}else{
			return tks_get_user_display_name($user->get('id')) . "さんでログイン中";
		}
	}

}
add_shortcode('tks_loginuser_dispname','tks_get_title_loginuser_dispname');

function tks_get_loginuser_name(){

	if (is_user_logged_in()){
		$user = wp_get_current_user();

		return tks_get_user_display_name($user->get('id'));
	}

}
add_shortcode('tks_loginuser_name','tks_get_loginuser_name');

/*
 * 本部問い合わせフォーム用のショートコード
 * フォームに予めユーザー名とメールアドレスを表示させる
*/
function tks_sc_contact_to_honbu_set_userinfo(){
	
	add_action( 'wp_footer', function() {

		if (!tks_learndash_is_group_leader_user()){
			return;
		}

		$user = wp_get_current_user();
		$user_id = $user->ID;

		$user_email = $user->user_email;
		$user_name = tks_get_user_display_name($user_id);
		$school_name = get_user_meta($user_id,'tks_school_name',true);

		?>
		<script type='text/javascript'>
		jQuery(document).ready(function() {
	
			var user_name = '<?php echo ( $school_name . "（" . $user_name . " 様）"); ?>';
			var user_email = '<?php echo $user_email; ?>';

			jQuery('input[name="your-name"]').val(user_name);
			jQuery('input[name="your-email"]').val(user_email);
		});
		</script>
		<?php
	});
}
add_shortcode('tks_contact_to_honbu_set_userinfo','tks_sc_contact_to_honbu_set_userinfo');

/*
 * ポスト一覧をテーブル表示する
*/
function tks_sc_show_posts_intable($arg){

	extract(shortcode_atts(array(
		'category' => '',
		), $arg));

	$posts = tks_get_post_by_category($category);
	
	if( !empty( $posts ) ) {

		echo "<table id='group_list' class='footable'>";
		echo "<thead>";
	  	echo "<tr>";
			echo '<th></th>';
			echo '<th nowrap data-sort-initial="true">'. __( "わざタイトル", 'tinkers' ) .'</th>';
		echo '</tr></thead>';

		foreach($posts as $post){
		
			echo '<tr>';
				echo '<td nowrap>'. $post->ID .'</td>';
				echo '<td nowrap><a href="'. $post->guid . '" target="_blank">' . $post->post_title .'</a></td>';
			echo '</tr>';
		}

		echo '</table>';
	}
}
add_shortcode('tks_show_posts_intable','tks_sc_show_posts_intable');

/**
 * 渡されたPostIDの投稿を表示する
 */
add_shortcode('tks_show_post_contents',
function($arg){

	extract(shortcode_atts(array(
		'postid' => '',
		), $arg));

	$post = get_post($postid);

	echo apply_filters('the_content', $post->post_content); 
});

/**
 * Scratch用の埋め込み共通デザインに整形したコンテンツを返す
 */
add_shortcode('tks_scratch_embed',
function($arg){

	extract(shortcode_atts(array(
			'urlid' => ''
			), $arg));

	if ($urlid == ''){

		return "";

	}else{ 
		
		$contens_ary = [
			'<div class="scratch-box">',
			'<p>',
			'<iframe src="https://scratch.mit.edu/projects/%s/embed" allowtransparency="true" width="485" height="420" frameborder="0" scrolling="no" allowfullscreen></iframe>',
			'</p>',
			'<p><h4 align="center"><a href="https://scratch.mit.edu/projects/%s/editor/" rel="noopener" target="_blank">スクラッチを開く</a></h4></p>',
			'</div>',
			'<div class="memo-box">※一部スクラッチサイト側の問題で埋め込み作品が正常に表示、または動作しない場合があります。その場合は、「スクラッチを開く」ボタンをクリックして直接スクラッチサイトでご覧ください。</div>'
		];

		return str_replace( '%s', $urlid, implode('', $contens_ary));

		//$ret = "<iframe class='scratch-box' src='" . $url . "/embed' allowtransparency='true' width='485' height='402' frameborder='0' scrolling='no' allowfullscreen></iframe>";
		
	}

});

add_shortcode('tks_processing_embed',
function($arg){
	extract(shortcode_atts(array(
		'pde' => '',
		'code' => 'no'
		), $arg));

	if ($pde == ''){

		return "";

	}else{ 

		$wp_upload_dir = wp_upload_dir();
		$pde_file_path = $wp_upload_dir['baseurl'] . '/tinkers/pde/' . $pde . '/' . $pde . '.pde';
		
		if ($code == 'yes'){
			wp_enqueue_script('google-code-prettify-js');

			$source = implode('',file($pde_file_path));
			$source = str_replace('<','&lt;',$source);
			$source = str_replace('>','&gt;',$source);
			$source = str_replace('>','&amp;',$source);
	
			$source = '<div><pre class="prettyprint linenums"><code>' . $source . '</code></pre></div>';
			
			return ((empty($source))?' ':$source);
		
		}else{
			wp_enqueue_script('processing-js');
			wp_enqueue_script('tinkers-processing-js');
	
			$contens_ary = [
				'<div class="processing-box">',
				'<P><b><h3 align="center">Sketch</h3></b></p>',
				'<canvas id="%s" data-processing-sources="'. $pde_file_path . '"></canvas>',
				'<div>',
				'<div class="processing-button-div"><button type="button" onclick="processing_stop(' . "'" . '%s' . "'" . ')">■</button></div>',
				'<div class="processing-button-div"><button type="button" onclick="processing_start(' . "'" . '%s' . "'" . ')">&#x25b6;</button></div>',
				'<div class="processing-button-div"><button type="button" onclick="location.reload()"><img src="' . plugins_url("../etc/reload.png", __FILE__) . '"></button></div>',
				'</div>',
				'</div>'
			];

			//$ret = str_replace( '%s', $pde, implode('', $contens_ary));
			return str_replace( '%s', $pde, implode('', $contens_ary));
		}
		//return $ret . ((empty($source))?' ':$source);
		
	}
});



/*
* Videoタグを見つけて、PictureInPictureにするショートコード
* ※LearnDashでは、iframeタグを使ってVideoを埋め込んでいるので、以下は失敗する
*/
function tks_show_picture_in_picture(){
echo <<< EOM
	<input type="button" value="ボタン" onclick="showPip();">
	
	<script type='text/javascript'>
	function showPip(){
		if (
			'pictureInPictureEnabled' in document &&
			'querySelectorAll' in document
		) {
			async function pip() {
			/* Select all video elements */
			//const videos = document.querySelectorAll('video');
			const videos = document.querySelectorAll('.ast-oembed-container iframe');
			//const videos = jQuery('.ast-oembed-container iframe')
	
			if (videos.length === 0) {
				window.alert('Sorry, no videos on the page.');
			} else if (videos.length > 0) {
				/* I assume main video in the document is the first one */
				const firstVideo = videos[0];
				
				try {
				if (firstVideo !== document.pictureInPictureElement) {
					/* Request Picture-in-Picture */
					await firstVideo.requestPictureInPicture();
				} else {
					/* Exit Picture-in-Picture */
					await document.exitPictureInPicture();
				}
				} catch (error) {
				console.error(error);
				}
			}
			}
		
			/* Call async pip function */
			pip();
		} else if (!('pictureInPictureEnabled' in document)) {
			window.alert('Picture-in-Picture is disabled.');
		} else if (!document.pictureInPictureEnabled) {
			window.alert('Picture-in-Picture not available.');
		}
	}  
	</script>
EOM;
}
add_shortcode('tks_show_pip','tks_show_picture_in_picture');


/**
 * MTSの予約情報を取得する（ログインユーザー）
 * 予約可能な、予約品目の一覧と現在予約状況を表示させるショートコード
 */
add_shortcode('tks_show_mts_booking_info',function(){
	
	//予約している一覧を取得する
	$booking_list = tks_mts_get_users_booking();

	if (empty($booking_list)){
		echo "ご予約頂いているオンライン授業はございません。";
	}else{
		$url = get_permalink(get_page_by_path(MTS_Simple_Booking::PAGE_SUBSCRIPTION));
		$pageUrl = add_query_arg(array('id' => '%id%', 'action' => 'show'), $url);

		// echo "<div class='elementor-element elementor-element-9ececc2 elementor-widget elementor-widget-heading' data-id='9ececc2' data-element_type='widget' data-widget_type='heading.default'>";
		// echo "<div class='elementor-widget-container'><h2 class='elementor-heading-title elementor-size-default'>ご予約済みオンライン授業</h2></div>";
		// echo "</div>";
		//echo "<table id='booking_list' class='footable'>";
		
		echo "<table id='booking_list'>";
		echo "<thead>";
			echo "<tr>";
			echo '<th nowrap data-sort-initial="true">'. __( "ご予約済", 'tinkers' ) .'</th>';
			echo '<th nowrap>予約日</th>';
			echo '<th nowrap>お時間</th>';
			echo '<th nowrap>詳細</th>';
		echo '</tr></thead>';

		foreach($booking_list as $booking_id => $booking){
		
			echo '<tr>';
				echo '<td nowrap>'. $booking['article_name'] .'</td>';
				echo '<td nowrap>' . date_i18n('Y年n月j日', $booking['booking_time']) .'</td>';
				echo '<td nowrap>' . date_i18n('H:i', $booking['booking_time']) .'</td>';
				echo '<td><a href="' . str_replace('%id%', $booking_id, $pageUrl) . '" title="予約詳細">詳細</a></td>';
			echo '</tr>';
		}

		echo '</table>';
	}

	
});


/**
 * 予約可能一覧を取得して、Tableタグで出力
 */
add_shortcode('tks_show_mts_booking_possible_info',function(){
		
		//-----予約している一覧を取得する-----
		$user_id = get_current_user_id();
		$booking_list = tks_mts_get_users_booking($user_id);
		
		//-----予約可能一覧の表示-----
		$articles = tks_mts_get_all_articles();
		
		//配列を逆順にする
		if (!empty($articles)){
			$articles = array_reverse($articles,true);
		}

		$td_style = "style='border: 0px none;'";
		$td_head = "<td style='width:4px;border: 0px none;'>★</td>";
		$td_status_ok = "<td " . $td_style . ">予約できます</td>";
		$td_status_no = "<td " . $td_style . ">予約済み</td>";

		echo "<table style='border: 0px none;'>";
		foreach ($articles as $key => $article) {
			echo "<tr>";
			
			$can_yoyaku = false;	//予約可能か否かフラグ

			//まだ一件も予約されていなければ予約リンクを作成
			if (empty($booking_list)){
				$can_yoyaku = true;
			}else{
				//予約品目一覧の中に既に予約されている品目が発見されない場合は、リンクを発行する
				if (array_search($key, array_column($booking_list, 'article_id')) === false ){
					$can_yoyaku = true;
				}else{
					//1回のみの予約が許可された予約品目でない場合は、リンクを発行する
					if (!tks_mts_is_ontime($key)){
						$can_yoyaku = true;
					}
				}
			}	
			
			if ($can_yoyaku){
				echo $td_head;
				$reserve_url = esc_url(tks_mts_get_permalink_by_mts_article_id($article["article_id"]));
				echo "<td " . $td_style . "><a href='" . $reserve_url. "'>" . $article["name"] . "</a></td>";
				echo $td_status_ok;
			}else{
				echo $td_head;
				echo "<td " . $td_style . ">" . $article["name"] . "</td>";
				echo $td_status_no;
			}
			
			echo "</tr>";
		}
		echo "</table>";
	
});

/**
 * 生徒Orお子さま用のオンライン授業予約一覧を表示する
 * Zoomリンクも表示させるのでそのままZoomと接続
 * 但し、予約日でないとZoomのリンクを有効にしない
 */
add_shortcode('tks_show_mts_booking_info_zoom',function(){
	
	if (current_user_can('subscriber')) {
		$user_id = tks_get_leader_of_student(get_current_user_id())['ID'];
	}else{
		$user_id = get_current_user_id();
	}

	//予約している一覧を取得する
	$booking_list = tks_mts_get_users_booking($user_id);

	echo "<h3>オンラインレッスンの予定</h3>";

	if (empty($booking_list)){
		echo "今は参加できるオンライン授業はありません";
	}else{
		//echo "<table id='booking_list' class='footable'>";
		
		echo "<table id='booking_list'>";
		echo "<thead>";
			echo "<tr>";
			echo '<th nowrap data-sort-initial="true">'. __( "レッスンの名前", 'tinkers' ) .'</th>';
			echo '<th nowrap>'. __( "日にち", 'tinkers' ) .'</th>';
			echo '<th nowrap style="text-align:center;">'. __( "始まる時間", 'tinkers' ) .'</th>';
			echo '<th nowrap style="text-align:center;">始めましょう</th>';
		echo '</tr></thead>';

		foreach($booking_list as $booking_id => $booking){
			//予約当日のみZoomリンクを発行
			$pageUrl = "";
			
			if (diff_from_now_date(date_i18n('Y-m-d',$booking['booking_time'])) == 0){	//当日か否かの判定
				//zoomのリンクを取得
				$pageUrl = tks_mts_get_zoom_url($booking['article_id']);

			}
			echo '<tr>';
				echo '<td nowrap>'. $booking['article_name'] .'</td>';
				echo '<td nowrap style="text-align:center;">' . date_i18n('Y年n月j日(D)', $booking['booking_time']) .'</td>';
				echo '<td nowrap style="text-align:center;">' . replace_time_notation_jp(date_i18n('ag:i', $booking['booking_time'])) .'</td>';
				if (empty($pageUrl)){
					echo '<td nowrap style="text-align:center;">当日まで待ってね！</td>';
				}else{
					$URL = "'" . $pageUrl. "','_blank','noreferrer'";
					//echo '<td nowrap style="text-align:center; vertical-align:middle;" >';
					echo '<td nowrap style="text-align:center;">';
					echo '<button type="button" onclick="window.open('. $URL .');">スタート</button>';
					//echo '<input type="button" value="スタート" onclick="window.open('. $URL .');"/>';
					//echo '<a href="' . $pageUrl . '" title="開始ボタン" target="_blank" rel="noopener noreferrer">スタート</a>';
					echo '</td>';
					
				}
			echo '</tr>';
		}

		echo '</table>';
	}

	
});

/**
 * サンプルユーザーのみに見せるコンテンツ
 */
add_shortcode('tks_only_sample_user', function($atts, $content = null){
	
	$isSample = false;
	
	if (current_user_can('administrator')){
		$isSample = true;
	}else{
		
		$user_id = get_current_user_id();

		//リーダーではない場合は、所属リーダーがsampleか否かを調べる
		if (!tks_learndash_is_group_leader_user($user_id)){
			//生徒の場合は、親であるリーダーの権限を確認
			$leader = tks_get_leader_of_student($user_id);
			$isSample = tks_is_sample_group_leader($leader["user_id"]);
		//リーダーの場合は、リーダーが本人がsampleか否かを調べる
		}else{
			$isSample = tks_is_sample_group_leader($user_id);  //sampleリーダーの権限がある場合はサンプルリーダー
		}
	}
	if ($isSample){
		return 
		'<div style="padding: 0.5em 1em;
		margin: 2em 0;font-weight: bold;
		color: #6091d3;/*文字色*/
		background: #cde4ff;
		border: solid 3px #6091d3;/*線*/
		border-radius: 10px;/*角の丸み*/">
		<p style="margin: 0; 
		padding: 0;">' . $content . '</p></div>';
	}
	return "";
});

/**
 * グループリーダーだけに見せるコンテンツ
 * adminもOK
 */
add_shortcode('tks_only_group_leader', function($atts, $content = null){
	
	$user_id = get_current_user_id();

	//リーダーではない場合は、空文字を返す
	if (current_user_can('administrator') || tks_learndash_is_group_leader_user($user_id)){
		return 
		'<div style="padding: 0.5em 1em;
		margin: 2em 0;
		color: #232323;
		background: #fff8e8;
		border-left: solid 10px #ffc06e;">
		<p style="margin: 0; 
		padding: 0;">' . $content . '</p></div>';
	}
	return "";
});

/*
 * ふりかえりクイズ用のメッセージを表示する
*/
function tks_sc_hurikaeri_quiz_message(){


	echo "<span style='color:cornflowerblue;'>さぁ、ふりかえりクイズです！<br>";
	echo "<ruby>全問正解<rt>ぜんもんせいかい</rt></ruby>しないと次へ進めません。がんばってクリアしましょう！</span>";

}
add_shortcode('tks_hurikaeri_quiz_message','tks_sc_hurikaeri_quiz_message');

/*
 * しっかり章末クイズ用のメッセージを表示する
*/
function tks_sc_shikkari_shomatsu_quiz_message(){


	echo "<span style='color:cornflowerblue;'><ruby>章末<rt>しょうまつ</rt></ruby>クイズです<br>";
	echo "全問<ruby>正解<rt>せいかい</rt></ruby>して次のレッスンに進みましょう！</span>";

}
add_shortcode('tks_s_shomatsu_quiz_message','tks_sc_shikkari_shomatsu_quiz_message');


/*
 * オンラインレッスン用のリンクボタンを表示する
*/
function tks_sc_show_online_lesson_button(){
	
	$user_id = get_current_user_id();
	//リーダーではない場合
    if (!tks_learndash_is_group_leader_user($user_id)){
         //生徒の場合は、親であるリーダーの権限を確認
         $leader = tks_get_leader_of_student($user_id);
		 $membe = tks_pmpro_get_member($leader["user_id"]);
	}else{
		$member = tks_pmpro_get_member($user_id);
	}
	
	//オンラインレッスンのプラン（最上位のプラン）の場合
	if (current_user_can("administrator") || tks_is_plan_advance1($member->id)){
		$URL = trailingslashit(get_option( tks_const::TKSOPT_MTS_YOYAKU_ZOOMLINK)) . tks_const::TKSOPT_ONLINE_MAN_TO_MAN_ZOOM_ID;
//		echo '<div style="cursor: pointer;" onclick="window.open(' . "'" . $URL . "'" . ');">' . '<img src="' . plugins_url("../etc/online-start-button.png", __FILE__) . '"></div>';
		echo '<div style="cursor: pointer;text-align: center;display:block;margin:auto;" onclick="window.open(' . "'" . $URL . "'" . ');">' . '<img src="' . plugins_url("../etc/online-start-button.png", __FILE__) . '"></div>';
	}else{
//		echo "<span style='color:cornflowerblue;'>今は<ruby>参加<rt>さんか</rt></ruby>できるオンライン<ruby>授業<rt>じゅぎょう</rt></ruby>はありません</span>";
		echo "<H4 style='text-align: center;display:block;margin:auto;'>今は<ruby>参加<rt>さんか</rt></ruby>できるオンライン<ruby>授業<rt>じゅぎょう</rt></ruby>はありません</H4>";
	}
	
}
add_shortcode('tks_show_online_lesson_button','tks_sc_show_online_lesson_button');

/**(未完成)
 * オンライン個別の予約ページリンクを表示するためのショートコード
 * 購入しているプランが、個別授業のプランでない場合は、プランのアップグレードが必要というメッセージとプラン一覧のページリンクを表示させる
 * 個別需要のプランに加入している場合は、予約ページへのリンクを表示する
 */
add_shortcode('tks_show_mts_booking_kobetsu',function(){
	$article = MTSSB_Article::get_all_articles();
	echo $article;
});

/**
 * クイズ正解の時に出力表示するよくできました画像とアニメーション
 * CSSは、tinkers.phpにてCSSをインクルードしている
 */
function tks_sc_quiz_correct(){

	return "<img class='alignnone size-thumbnail wp-image-41753 bounds' src='" . tks_get_home_url("/wp-content/uploads/tinkers/yokudekimasita.png") . "' alt='' width='150' height='150' />";
	
}
add_shortcode('tks_quiz_correct','tks_sc_quiz_correct');

function tks_sc_test(){

	//$ret = learndash_process_mark_incomplete(49,8,32,false);
	$ret = learndash_process_mark_incomplete(49,8,35870,false);
	//learndash_remove_user_quiz_attempt
	return "消しました！";
}
add_shortcode('tks_test','tks_sc_test');
