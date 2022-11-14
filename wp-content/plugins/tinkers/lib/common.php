<?php

/*
*/
function tks_include_tinkers_js(){
	wp_enqueue_script( 'tinkers-js', plugins_url( '../js/tinkers.js', __FILE__ ), array('jquery'), null, true );
}
/*
 * jquery-cofirmライブラリのインクルード
*/
function tks_include_jqconfirm(){
	wp_enqueue_script('jquery-confirm-min','https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js', array('jquery'));
	wp_enqueue_style( 'jquery-confirm-min-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css' );
}

/*
 * jquery-cofirm(sweetalert)ライブラリのインクルード
*/
function tks_include_sweet_alert(){
	//wp_enqueue_script('jquery-confirm-min','https://unpkg.com/sweetalert/dist/sweetalert.min.js', array('jquery'));
	wp_enqueue_script('sweetalert-min','https://unpkg.com/sweetalert/dist/sweetalert.min.js', array('jquery'));
}

function tks_include_lesson_topics_style(){
	wp_enqueue_style( 'tinkers-css-lesson_topic', plugins_url( '../css/style-lesson-topis.css', __FILE__ ) );
}

/*
 * jquery-dialogライブラリのインクルード
*/
function tks_include_jqdialog(){
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style('jquery-ui-dialog-min-css', includes_url() . 'css/jquery-ui-dialog.min.css');
	wp_enqueue_style('font-a', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css');
}

/**
 * Vimeo playerコントローラjSのインクルード
 */
function tks_include_vimeo(){
	
	wp_enqueue_script('vimeo-ui-control','https://player.vimeo.com/api/player.js');
}

function tks_include_processing(){
	
	// //wp_enqueue_script('processing-js','https://raw.githubusercontent.com/processing-js/processing-js/v1.4.8/processing.min.js');
	// wp_enqueue_script('processing-js',plugins_url('../js/processing.min.js', __FILE__ ));
	// wp_enqueue_script('tinkers-processing-js',plugins_url('../js/tinkers_processing.js', __FILE__ ));
	// wp_register_script('google-code-prettify-js', 'https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js?skin=default', null, null, false);
	// //wp_enqueue_script('google-code-prettify-js');

	//登録だけいしておいて使用するところでenqueueする(うまく動作しないなら上記を復活、下コメント)
	wp_register_script('processing-js',plugins_url('../js/processing.min.js', __FILE__ ), null, null, false);
	wp_register_script('tinkers-processing-js',plugins_url('../js/tinkers_processing.js', __FILE__ ), null, null, false);
	wp_register_script('google-code-prettify-js', 'https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js?skin=default', null, null, false);

}

/**
 * baloon.jsのインクルード
 * 
 */
function tks_include_baloon($type){
	if ($type == "sample"){
		wp_enqueue_script('baloon',plugins_url('../js/for_sample_user/baloon.js', __FILE__ ));
	}
	if ($type == "help"){
		wp_enqueue_script('baloon',plugins_url('../js/help_baloon/baloon.js', __FILE__ ));
	}
	if ($type == "regist"){
		wp_enqueue_script('baloon',plugins_url('../js/regist_baloon/baloon.js', __FILE__ ));
		wp_enqueue_script('baloon-regist',plugins_url('../js/regist_baloon/baloon_regist.js', __FILE__ ));
	}
	wp_enqueue_script('jquery.balloon.min',plugins_url('../js/jquery.balloon.min.js', __FILE__ ));
}

/*
 * 日付から学年を出す
*/
function tks_get_age($birth){

	$birth = trim($birth);

	if (empty($birth)){
		return;
	}
	
	$now = date("Ymd"); 

	//$birth = "20090522";
	$birth = str_replace("-", "", $birth);
	$birth = str_replace("/", "", $birth);

	return floor(($now-$birth)/10000);

}

/*
 * jQueryのカレンダー日本語化
 * 呼び出し元でechoしないでOK
*/
function tks_js_datepicker($element){
	?>
	jQuery(function($){
	$(<?php echo "'" . $element . "'"?>).datepicker();
	 
	  // 日本語化
	  jQuery.datepicker.regional['ja'] = {
		showAnim: 'show',
		showButtonPanel: true,
		firstDay: 1, 
		changeYear: true,
		changeMonth: false,
		yearRange: '-50:+0',
		closeText: '閉じる',
	    prevText: '<前',
	    nextText: '次>',
	    currentText: '今日',
	    monthNames: ['1月','2月','3月','4月','5月','6月',
	    '7月','8月','9月','10月','11月','12月'],
	    monthNamesShort: ['1月','2月','3月','4月','5月','6月',
	    '7月','8月','9月','10月','11月','12月'],
	    dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
	    dayNamesShort: ['日','月','火','水','木','金','土'],
	    dayNamesMin: ['日','月','火','水','木','金','土'],
	    weekHeader: '週',
	    dateFormat: 'yy/mm/dd',
	    isRTL: false,
	    showMonthAfterYear: true,
	    yearSuffix: '年'};
	  jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ja']);
	});
	<?php

}

/*
 * 正しい日付書式かをチェックする
*/
function checkDatetimeFormat($datetime){
    return $datetime === date("Y/m/d", strtotime($datetime));
}

/*
 * 半角英数チェック
*/
function is_hankaku($text) {
	
    if (preg_match("/^[a-zA-Z0-9]+$/",$text)) {
        return true;
    }
    
	return false;
    
}

/**
 * 本LMSでは、姓名が逆なので、プラグイン等でusers.display_nameを使用
 * している箇所を本メソッドで補う
 * 使用箇所：ld-propanel-reporting-filter-group-row.php（行：34,36）
 * 			ld-propanel-reporting-filter-course-row.php(行：27)
 * for learnDash
*/
function tks_get_user_display_name($user_id){

	return get_user_meta($user_id,'first_name',true) . ' ' . get_user_meta($user_id,'last_name',true);
}

/*
 * home_urlを実行してURLを返す
*/
function tks_get_home_url($path, $end_lash=false){
	if (empty($path)){
		return home_url();
	}
	if ($end_lash){
		return trailingslashit(home_url($path));
	}

	return home_url($path);
}

/**
 * array_diff関数で、比較元が空の場合Nullを返すので
 * 空の場合は、比較元を返すようにする
 */
function array_diff_ex($array1,$array2){
	if (empty($array2)){
		return $array1;
	}
	return array_diff($array1,$array2);
}

/**
 * 現在日付との差異を返す(日のみの比較)
 * @param $dat 比較する日付
 * 未来ならプラス
 * 過去ならマイナス
 * 同じなら0
 */
function diff_from_now_date($dat){
	//設定タイムゾーンを保存
	$originalZone = date_default_timezone_get();
	date_default_timezone_set('Asia/Tokyo');

	$now = new DateTime(date('Y/m/d'));	//現在日付を取得
	$dat = new DateTime($dat);
	//$test = $dat->format('Y-m-d H:i:s'); //test
	//日数の差を取得
	$diff=$now->diff($dat);
	$d = $diff->days;
	if ($diff->invert == 1){
		$d = $d * -1;
	}
	
	//設定タイムゾーンを戻す
	date_default_timezone_set($originalZone);

	return $d;

}

/**
 * 時刻表示にAM/am/PM/pmが含まれている場合
 * 午前 or 午後に置き換えする(AM12:00　→ 午前12:00)
 * 引数はこんな感じで事前にフォーマットしておく  →  date_i18n('ag:i'...
 * @param string $time 置き換えする時刻(24時間表記の場合は何もせずそのまま値を返す)
 */
function replace_time_notation_jp($time){
	
	$tmp = mb_strtoupper($time);

	if(strpos($tmp,'AM') !== false){
		return str_replace('AM','午前',$tmp);
	}elseif (strpos($tmp,'A') !== false){
		return str_replace('A','午前',$tmp);
	}elseif (strpos($tmp,'PM') !== false){
		return str_replace('PM','午後',$tmp);
	}elseif (strpos($tmp,'P') !== false){
		return str_replace('P','午後',$tmp);
	}else{
		return $time;
	}

}

/**
 * スラッグからページのタイトルを取得する
 * @param $slug スラッグ名
 * @return 取得されたページのタイトル
 */
function get_title_from_slug($slug){
	$get_page_id = get_page_by_path($slug);
	$post_info = get_post( $get_page_id );
	return $post_info->post_title; //タイトル
}

//Taken from: http://www.bitrepository.com/how-to-extract-domain-name-from-an-e-mail-address-string.html
/**
 * メールアドレスからドメイン部分のみを取得する
 */
function tks_getDomainFromEmail( $email ) {
    // Get the data after the @ sign
    $domain = substr( strrchr( $email, "@" ), 1);

    return $domain;
}