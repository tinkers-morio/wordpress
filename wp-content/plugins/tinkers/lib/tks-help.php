<?php

/**
 * コース一覧専用
 * ヘルプ表示用のメッセージやアイコンを返す
 * 引数を省略時は、デフォルトメッセージとアイコンを使用
 * @param $progress　進捗割合(省略不可)
 * @param $msg 表示するメッセージ
 * @param $icon 表示するアイコン
 * 
 */
function tks_get_course_help_msg($progress,$msg="",$icon=""){
    
    if ($progress == 0){
		$msg = empty($msg)?'まずは、ここから始めましょう！':$msg;
		$icon = empty($icon)?'far far fa-smile-wink fa-3x':$icon;
	}elseif ($progress > 0 && $progress < 25) {
		$msg = empty($msg)?'とても順調です！がんばりましょう！':$msg;
		$icon = empty($icon)?'far far fa-smile-wink fa-3x':$icon;
    }elseif ($progress > 25 && $progress < 50) {
		$msg = empty($msg)?'はりきっていきましょう！':$msg;
		$icon = empty($icon)?'far far fa-smile-wink fa-3x':$icon;
    }elseif ($progress > 50 && $progress < 75) {
		$msg = empty($msg)?'半分をこえました！<br>がんばりましょう！':$msg;
		$icon = empty($icon)?'far fa-grin-squint fa-3x':$icon;
    }elseif($progress > 75 && $progress < 100) {
		$msg = empty($msg)?'あと少しですよ！がんばって！':$msg;
		$icon = empty($icon)?'far fa-smile-beam fa-3x':$icon;
    }

    return ["msg" => $msg,"icon" => $icon];
}

/**
 * ヘルプ用のバルーンを表示する
 */
function tks_baloon_for_help(){
		wp_enqueue_style('font-a', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css');	//fontAwsomeインクルード
		tks_include_baloon("help");																//Balloonインクルード
				
		add_action('wp_footer', function() {

			$show_balloon = false;

			//コース一覧ページだった場合は、体験コースにココから始めるよう促すバルーンを表示
			if (is_page(tks_const::PAGE_COURSES) || is_page(tks_const::PAGE_STUDENT_MY_PAGE)){
				$user_id = get_current_user_id();
				$position = "top";

				//体験が修了しているか？
				$is_completed = tks_learndash_course_completed($user_id,tks_const::COURCSE_ID_TAIKEN);
				//体験が修了していなければ体験用バルーン
				if (!$is_completed){
					$post_id = tks_const::COURCSE_ID_TAIKEN;
					//進捗状況を取得
					$progress = tks_get_progress_course($user_id,tks_const::COURCSE_ID_TAIKEN);
					//Balloon表示コンテンツを取得
					$balloon_contents = tks_get_course_help_msg($progress);
					$show_balloon = true;
				}
				if (!$show_balloon){
					//しっかりが修了しているか？
					$is_completed = tks_learndash_course_completed($user_id,tks_const::COURCSE_ID_SHIKKARI);
					if (!$is_completed){
						//体験が修了していればしっかり用のバルーン
						$post_id = tks_const::COURCSE_ID_SHIKKARI;
						//進捗状況を取得
						$progress = tks_get_progress_course($user_id,tks_const::COURCSE_ID_SHIKKARI);
						//Balloon表示コンテンツを取得
						if($progress == 0){
							$taiken_complete = get_user_meta($user_id,tks_const::TKSOPT_TKS_TAIKEN_COMPLETE,true);
							//生徒登録時に体験を完了している場合とでメッセージを切り替える
							if (! empty($taiken_complete) && $taiken_complete == 'yes'){
								$balloon_contents = tks_get_course_help_msg($progress,'次はこちらを学習しよう！');
							}else{
								$balloon_contents = tks_get_course_help_msg($progress);
							}
						}elseif ($progress < 75){
							$balloon_contents = tks_get_course_help_msg($progress);
						}else{
							$balloon_contents = tks_get_course_help_msg($progress,'あと少しですよ！がんばって！<br>終ったらハイレベルにチャレンジ！','far fa-smile-beam fa-3x');
						}
						$show_balloon = true;
					}
				}

				//しっかりが修了していればハイレベル１！
				if (!$show_balloon){
					$is_completed = tks_learndash_course_completed($user_id,tks_const::COURCSE_ID_HIGHT_LV_1);
					if (!$is_completed){
						$post_id = tks_const::COURCSE_ID_HIGHT_LV_1;
						//進捗状況を取得
						$progress = tks_get_progress_course($user_id,tks_const::COURCSE_ID_HIGHT_LV_1);
						if ($progress == 0){
							$balloon_contents = tks_get_course_help_msg($progress,'さあ！いよいよハイレベルにチャレンジ！','far far fa-smile-wink fa-3x');
						}else{
							$balloon_contents = tks_get_course_help_msg($progress);
						}
						$show_balloon = true;
					}
				}

				//しっかりが修了していればハイレベル2！
				if (!$show_balloon){
					$is_completed = tks_learndash_course_completed($user_id,tks_const::COURCSE_ID_HIGHT_LV_2);
					if (!$is_completed){
						$post_id = tks_const::COURCSE_ID_HIGHT_LV_2;
						//進捗状況を取得
						$progress = tks_get_progress_course($user_id,tks_const::COURCSE_ID_HIGHT_LV_2);
						if ($progress == 0){
							$balloon_contents = tks_get_course_help_msg($progress,'次はハイレベル2にチャレンジ！','far far fa-smile-wink fa-3x');
						}else{
							$balloon_contents = tks_get_course_help_msg($progress);
						}
						$show_balloon = true;
					}
				}

				//しっかりが修了していればハイレベル3！
				if (!$show_balloon){
					$is_completed = tks_learndash_course_completed($user_id,tks_const::COURCSE_ID_HIGHT_LV_3);
					if (!$is_completed){
						$post_id = tks_const::COURCSE_ID_HIGHT_LV_3;
						//進捗状況を取得
						$progress = tks_get_progress_course($user_id,tks_const::COURCSE_ID_HIGHT_LV_3);
						if ($progress == 0){
							$balloon_contents = tks_get_course_help_msg($progress,'よくがんばりました！最後のハイレベルにチャレンジ！','far far fa-smile-wink fa-3x');
						}else{
							$balloon_contents = tks_get_course_help_msg($progress);
						}
						$show_balloon = true;
					}
				}
				if ($show_balloon){
					$target = "jQuery('#post-" . $post_id . "')";
				}
			}else{
				//コースの先頭画面の場合、どこから始めるかを表示する
				if (is_singular('sfwd-courses')){
					$post_id = learndash_get_course_id();
					//進捗状況を取得
					$progress = tks_get_progress_course(get_current_user_id(),$post_id);
					//Balloon表示コンテンツを取得
					if ($progress == 0){
						$position = "top";
						$target = "jQuery('.ld-status-icon').first()";
						$balloon_contents = ["msg" => "ここから始めましょう！","icon" => "far far fa-smile-wink fa-3x"];
						$show_balloon = true;
					}
				}
				if (is_singular('sfwd-topic')){
					$position = "left";
					$target = "jQuery('.ld-table-list-header')";
					$balloon_contents = ["msg" => "動画を見てからクイズにチャレンジ！","icon" => "far far fa-smile-wink fa-3x"];
					$show_balloon = true;
				}
				//レッスン一覧ページの場合
				if (is_singular('sfwd-lessons')){
					//レッスン内トピックの進捗状況が100％の場合でコースがしっかりの場合、かつ、章末クイズまで完了していない場合のみ表示
					if (learndash_lesson_progress()['percentage'] == 100 && learndash_get_course_id() == tks_const::COURCSE_ID_SHIKKARI && !learndash_is_lesson_complete(get_current_user_id(),learndash_get_lesson_id(),$post_id)){
						$position = "left";
						$target = "jQuery('.ld-lesson-topic-list').find('.ld-table-list-item-quiz').filter(':last')";
						$balloon_contents = ["msg" => $post_id . "すべてのトピックが終わったら章末問題にチャレンジ！","icon" => "far far fa-smile-wink fa-3x"];
						$show_balloon = true;
					}
				}
			}

			//バルーン表示
			if ($show_balloon){
				?>
				<script type='text/javascript'>
				jQuery(document).ready(function() {
			
					var msg = '<?php echo _e($balloon_contents["msg"])?>';
					var icon = '<?php echo _e($balloon_contents["icon"])?>';
			
					let contents = '<div style="display: table;"><div><i class="' + icon + '"></i></div><div style="display: table-cell;vertical-align:middle;padding-left:5px">' + msg + '</div></div>';
					//let elem = jQuery('<?php //echo '#post-'.$post_id ?>');
					let elem = <?php echo $target ?>;

					elem.on({'mouseenter' : function(){
							TKS_BL.show(jQuery(this),contents,"<?php echo $position ?>"); //[1]baloon表示の処理を着火
						},'mouseleave' : function(){
							TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
						}
					});
					TKS_BL.show(elem,contents,"<?php echo $position ?>");
					jQuery(window).resize(function(){
					  // 画面幅が変更されたときに実行させたい処理内容
						TKS_BL.hide(<?php echo $target ?>); //[2]baloon非表示の処理を着火
						TKS_BL.show(<?php echo $target ?>,contents,"<?php echo $position ?>"); //[1]baloon表示の処理を着火
					});

				});
				</script>
				<?php
			}
		});
	
}

function tks_confetti_for_coruse(){
	
	add_action('wp_footer', function() {

		$show_confetti = false;
		//コースだけ
		if (is_singular('sfwd-courses')){
			global $post;
			
			//コース終了日を取得
			$course_completed_meta = get_user_meta( get_current_user_id(), 'course_completed_' . $post->ID, true );
			( empty( $course_completed_meta ) ) ? $course_completed_date = '' : $course_completed_date = date_i18n( 'Y/m/d', $course_completed_meta );	
			
			//コース修了日と現在日を比較（終了日だけ花吹雪を表示する）
			if(strtotime(date("Y/m/d")) === strtotime($course_completed_date)){
				$show_confetti = true;
				$title = $post->post_title;
			
				//次のコースのリンクを取得する
				if ($post->ID == tks_const::COURCSE_ID_SHIKKARI){		//修了したコースがしっかりの場合
					$cofirm_button_text = "<i class='fa fa-thumbs-up'></i> 次のコースへ進みますか？";
					$next_course_url = get_permalink(tks_const::COURCSE_ID_HIGHT_LV_1);
					//修了証リンク
					$footer = "<a href='". learndash_get_course_certificate_link( $post->ID,  get_current_user_id() )."' target='_blank' rel='noopener noreferrer'>修了証を見るにはこちらをクリック</a>";
				}
				if ($post->ID == tks_const::COURCSE_ID_HIGHT_LV_1){		//修了したコースがハイレベ１の場合
					$cofirm_button_text = "<i class='fa fa-thumbs-up'></i> 次のコースへ進みますか？";
					$next_course_url = get_permalink(tks_const::COURCSE_ID_HIGHT_LV_2);
					$footer = "<a href='".$next_course_url."'>次のコースへ進むにはこちらをクリック！</a>";
				}
				if ($post->ID == tks_const::COURCSE_ID_HIGHT_LV_2){		//修了したコースがハイレベ2の場合
					$cofirm_button_text = "<i class='fa fa-thumbs-up'></i> 次のコースへ進みますか？";
					$next_course_url = get_permalink(tks_const::COURCSE_ID_HIGHT_LV_3);
					$footer = "<a href='".$next_course_url."'>次のコースへ進むにはこちらをクリック！</a>";
				}
				if ($post->ID == tks_const::COURCSE_ID_HIGHT_LV_3){		//修了したコースがハイレベ2の場合
					$cofirm_button_text = "<i class='fa fa-thumbs-up'></i> OK!";
					$next_course_url = get_permalink(tks_const::COURCSE_ID_HIGHT_LV_3);
					//修了証リンク
					$footer = "<a href='". learndash_get_course_certificate_link( $post->ID,  get_current_user_id() )."' target='_blank' rel='noopener noreferrer'>修了証を見るにはこちらをクリック</a>";
				}
			}
		}
		
		if ($show_confetti){
			
			?>
				<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.3.2/dist/confetti.browser.min.js"></script>
				<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
				<script type='text/javascript'>
				//show message
				swal.fire({
				title: "Good job!",
				text: "<?php _e($title)?>",
				html:"がんばりましたね!<br>あなたは次のコースを修了しました。<p><h2 style='color:#00CB98'>コース名：<br><?php _e($title)?></h2>",
                confirmButtonText:'<i class="fa fa-thumbs-up"></i> 次のコースへ進みますか？',
				//showConfirmButton: false,
				footer: "<?php _e($footer)?>",
                icon: "success",
				backdrop: "rgba(255,252,219,0.4)",
				//toast: true,
				//timer: 20000,
				//button: "OK!",
				}).then((result) => {
					/* Read more about isConfirmed, isDenied below */
					if (result.isConfirmed) {
						window.location.href = "<?php _e($next_course_url)?>";
					}
				});

				//show kamihubuki
				var end = Date.now() + (15 * 1000);				
				var colors = ['#009944', '#fff9b1'];

				(function frame() {
				confetti({
					particleCount: 2,
					angle: 60,
					spread: 55,
					origin: { x: 0 },
					colors: colors
				});
				confetti({
					particleCount: 2,
					angle: 120,
					spread: 55,
					origin: { x: 1 },
					colors: colors
				});

				if (Date.now() < end) {
					requestAnimationFrame(frame);
				}
				}());
				
				</script>
			<?php
		}
	});
}

/**
 * バルーン表示用のJSを出力
 */
function tks_show_baloon($post_id,$balloon_contents,$position="top"){
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function() {

		var msg = '<?php echo _e($balloon_contents["msg"])?>';
		var icon = '<?php echo _e($balloon_contents["icon"])?>';

		let contents = '<div style="display: table;"><div><i class="' + icon + '"></i></div><div style="display: table-cell;vertical-align:middle;padding-left:5px">' + msg + '</div></div>';
		let elem = jQuery('<?php echo '#post-'.$post_id ?>');

		elem.on({'mouseenter' : function(){
				TKS_BL.show(jQuery(this),contents,"<?php echo $position ?>"); //[1]baloon表示の処理を着火
			},'mouseleave' : function(){
				TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
			}
		});
		TKS_BL.show(elem,contents,"<?php echo $position ?>");

	});
	</script>
	<?php

}

/*
 * サンプルリーダーの場合は、ToolTipを表示するようにするためのインクルード(jquery.baloon)
*/
function tks_baloon_enaueue_for_sampleuser(){
	//サンプルリーダーの場合は、ToolTipを表示するようにするためのインクルード(jquery.baloon)
	if (current_user_can('administrator') || tks_is_sample_group_leader()){

		if (is_singular('sfwd-courses') || is_singular('sfwd-lessons')){

			global $post;
			$slug = $post->post_name;
			//しっかり習得コース用のtooltip
			if ($slug == "master"){
				tks_include_baloon("sample");
				wp_enqueue_script('baloon_cource',plugins_url('/js/for_sample_user/baloon_cource.js', __FILE__ ));
				wp_enqueue_script('baloon_cource_master',plugins_url('/js/for_sample_user/baloon_cource_master.js', __FILE__ ));
			}
			//ゲーム教材コース用のtooltip
			if  ($slug == "game"){
				tks_include_baloon("sample");
				wp_enqueue_script('baloon_cource',plugins_url('/js/for_sample_user/baloon_cource.js', __FILE__ ));
				wp_enqueue_script('baloon_cource_game',plugins_url('/js/for_sample_user/baloon_cource_game.js', __FILE__ ));
			}
			//ハイレベル教材コース用のtooltip
			if  (tks_is_highlevel_mission_course(get_the_ID())){
				tks_include_baloon("sample");
				wp_enqueue_script('baloon_cource',plugins_url('/js/for_sample_user/baloon_cource.js', __FILE__ ));
				wp_enqueue_script('baloon_cource_highlevel',plugins_url('/js/for_sample_user/baloon_cource_highlevel.js', __FILE__ ));
			}
			
			//ゲーム教材レッスン用のtooltip
			if(strpos($slug,'g-lesson') !== false){
				tks_include_baloon("sample");
				wp_enqueue_script('baloon_lessons_game',plugins_url('/js/for_sample_user/baloon_lessons_game.js', __FILE__ ));
			}
			//ハイレベル教材レッスン用のtooltip
			if (tks_is_highlevel_mission_lesson_topic_page(get_the_ID())){
				tks_include_baloon("sample");
				wp_enqueue_script('baloon_lessons_highlevel',plugins_url('/js/for_sample_user/baloon_lessons_highlevel.js', __FILE__ ));
			}
			
		}
		//サンプルリーダー用のスタートーページにも出そうと思ったけど、ちょっと変だったのでコメント
		// if (is_page(tks_const::PAGE_START_SAMPLE_LEADER)){
		// 	tks_include_baloon();
		// 	wp_enqueue_script('baloon_sample_start',plugins_url('/js/for_sample_user/baloon_sample_start.js', __FILE__ ));
		// }
	}
}
