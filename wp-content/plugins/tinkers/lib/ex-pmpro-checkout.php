<?php
/**
 * 申し込みページ(CheckOutページから請求情報を削除する表示しない)
 */
function simple_checkout_remove_billing_address_fields( $include ) {
	return true;
}
add_filter( 'pmpro_include_billing_address_fields', 'simple_checkout_remove_billing_address_fields' );

/**
 * Paid Memberships Pro の新規登録画面に名前と苗字の入力フィールドを追加する
 *
 */
add_action( 'init', 
function(){
    
    //don't break if Register Helper is not loaded
	if(!function_exists( 'pmprorh_add_registration_field' )) {
		return false;
	}
	
//↓動的にプランを作れるなら作って人数を指定してチェックアウト・・できないかなぁ？？
    // if(pmpro_hasMembershipLevel('2', get_current_user_id())){
    //     $fields = array();
    //     $fields[] = new PMProRH_Field(
    //         'student_count',
    //         'number', // type of field
    //         array(
    //             'label'		=> '生徒数',	// custom field label
    //             'size'		=> 20,// input size
    //             //'profile'	=> true,// show in user profile
    //             'required'	=> true,// make this field required
    //             //'html_attributes' => array( 'placeholder' => '個人の場合は個人' )
    //             //'save_function' => 'test_check_feild',    コールバック関数を入れるならこんな感じで
    //         )
    //     );
    //     pmprorh_add_checkout_box("option", "生徒数の追加","<small>追加する生徒数を入力して下さい。</small>"); //order parameter defaults to one more than the last checkout box
    //     foreach($fields as $field){
    //         pmprorh_add_registration_field(
    //             //$field->location,// location on checkout page
    //             'option',
    //             $field// PMProRH_Field object
    //         );
    //     }
    //     return;
    // }
//↑ここまで

    //既になんらかのプランを持っているなら(メンバーなら)入力の必要はないので抜ける
    if (pmpro_hasMembershipLevel()) return;

	$fields = array();

    //  // HTML
    //  $fields[] = new PMProRH_Field(
    //     "<hr>",             // input name, will also be used as meta key
    //     "html",                    // type of field
    //     array(
    //         'location' => 'after_password',
    //         "html" => "<h3>ご契約者情報</h3>"   // accepts HTML code
    //     )
    // );

    // RADIO　個人Or法人
    $fields[] = new PMProRH_Field(
        "tks_zokusei",             // input name, will also be used as meta key
        "radio",                    // type of field
        array(
           'label'		=> '　',	// custom field label
           "options" => array(      // display the different options, no need for a "blank" option
                "kojin" => "法人/団体",
                "hojin" => "個人/個人事業主",
            )    
        )
    );

    $fields[] = new PMProRH_Field(
		'company_name',
		'text', // type of field
		array(
			'label'		=> '会社名/団体名',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> true,// make this field required
            //'html_attributes' => array( 'placeholder' => '個人の場合は個人' )         //プレースホルダーを入れるならこんな感じ
			//'save_function' => 'test_check_feild',                                    //コールバック関数を入れるならこんな感じで
		)
	);

	$fields[] = new PMProRH_Field(
		'first_name',
		'text', // type of field
		array(
			'label'		=> 'お名前(姓)',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> true,// make this field required
			//'save_function' => 'test_check_feild',    コールバック関数を入れるならこんな感じで
		)
	);

    $fields[] = new PMProRH_Field(
		'last_name',
		'text', // type of field
		array(
			'label'		=> 'お名前(名)',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> true,// make this field required
			//'hint' => 'テストです', 
		)
	);

    $fields[] = new PMProRH_Field(
		'tks_zipcode',
		'text', // type of field
		array(
			'label'		=> '郵便番号',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> true,// make this field required
			'hint' => '', 
		)
	);

    $fields[] = new PMProRH_Field(
		'tks_address1',
		'text', // type of field
		array(
			'label'		=> '住所1',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> true,// make this field required
			'hint' => '', 
		)
	);
    $fields[] = new PMProRH_Field(
		'tks_address2',
		'text', // type of field
		array(
			'label'		=> '住所2',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> false,// make this field required
			'hint' => '', 
		)
	);
    $fields[] = new PMProRH_Field(
		'tks_phone',
		'text', // type of field
		array(
			'label'		=> '電話番号',	// custom field label
			'size'		=> 30,// input size
			//'profile'	=> true,// show in user profile
            'just_profile' => true,
			'required'	=> true,// make this field required
			'hint' => '', 
            //'save_function' => 'tks_pmpro_format_my_phone'
		)
	);
    
    pmprorh_add_checkout_box("contract", "ご請求情報","<small>ご契約者様の情報を入力します</small>"); //order parameter defaults to one more than the last checkout box

	//add the fields to default forms
	foreach($fields as $field){
		pmprorh_add_registration_field(
			//field->location,// location on checkout page
            'contract',
			$field// PMProRH_Field object
		);
	}

    //利用規約の表示
    //$tos_link = get_permalink( 38674 ); // Set Terms & Conditions post ID here.   ページで見せる場合
	//$tos_link = wp_get_attachment_url( 38693 ); // Set your media file ID here      PDFで直接見せる場合
    // $tos_link = plugins_url( '../etc/ご利用規約.pdf', __FILE__ );
    // $field = new PMProRH_Field(
    //     '<small>以下のリンクよりご利用規約をPDF形式でご覧いただけます</small>',             // input name, will also be used as meta key
    //     "html",                    // type of field
    //     array(
    //         //"html" => '<small><a href="'. $tos_link .'" target="_blank">ご利用規約</a>をご確認の上チェックボックスにチェックをお付けください</small>',   // accepts HTML code
    //         "html" => '<span><a href="'. $tos_link . '" class="tosbtn fas fa-file-pdf" target="_blank" rel="noopener noreferrer">PDFで開く</a><small>ご利用規約をご確認の上チェックボックスにチェックをお付けください</small></span>',
    //         'location' => 'before_submit_button',
    //     )
    // );

    // pmprorh_add_registration_field(
    //     $field->location,// location on checkout page
    //     $field// PMProRH_Field object
    // );
});

add_action('pmpro_checkout_before_form',function(){
    $LOGO_URL = get_site_icon_url(); 
    echo '<p><img src="' . $LOGO_URL .  '" alt="" width="100" height="100" class="alignleft size-full wp-image-35281" /></p><br>';
});

/**
 * 利用規約の説明や、PDFで開くリンクの表示
 */
add_action('pmpro_checkout_after_tos_fields',function(){
    //$tos_link = get_permalink( 38674 ); // Set Terms & Conditions post ID here.   ページで見せる場合
	//$tos_link = wp_get_attachment_url( 38693 ); // Set your media file ID here      PDFで直接見せる場合
    $tos_link = plugins_url( '../etc/ご利用規約.pdf', __FILE__ );
    echo '<small><strong>以下のリンクよりご利用規約をPDF形式でご覧いただけます</strong></small><p>';
    echo '<span><a href="'. $tos_link . '" class="tosbtn fas fa-file-pdf" target="_blank" rel="noopener noreferrer">PDFで開く</a></span><span><small style="margin-left:0.3em;">ご利用規約をご確認の上チェックボックスにチェックをお付けください</small></span>';
});

//お申込み画面入力チェック
function tks_pmpro_registration_checks($okay)
{
	// global $pmpro_msg, $pmpro_msgt, $current_user;
	// $firstname = $_REQUEST['firstname'];
	// $lastname = $_REQUEST['lastname'];
	// $companyname = $_REQUEST['companyname'];
	// $repname = $_REQUEST['repname'];
    
	// if($firstname && $lastname && $companyname && $repname || $current_user->ID)
	// {
	// 	//all good
	// 	return true;
	// }
	// else
	// {
	// 	$pmpro_msg = "The first name, last name, company name, and rep number/name fields are required.";
	// 	$pmpro_msgt = "pmpro_error";
	// 	return false;
	// }
    if ($okay){

        /*************************/
        //【チェック】生徒人数の追加プランの申し込みで、ユーザーがまだ応援プランや、新規の場合はエラー
        global $pmpro_level;
        //ユーザーのレベルを取得
        $user_level = pmpro_getMembershipLevelForUser();

        //申し込みするプランのレベルが２以上で、自分の申し込みプランが2より下の場合はエラー
        $error = false;
        if( ! $user_level && ($pmpro_level->id > 2 && $pmpro_level->id < 8)){
            if( $user_level->id < 2 ){
                $error = true;
            }
        }
        if( ! $user_level && ($pmpro_level->id > 8 && $pmpro_level->id < 15)){
            if( $user_level < 9){
                $error = true;
            }
        }
        
        if ($error){
            pmpro_setMessage("このプランにお申込み頂くには、はじめにビジネスプランへのご加入が必要です。", "pmpro_error");
            //このエラーはここで終了(これ以上チェックしない)            
            return false;
        }

        /*************************/
        //【チェック】登録済み生徒数がダウングレードしようとしているプランの人数より多い場合はエラー
        //登録済みの生徒数を取得
		$student_count = tks_get_only_student_count(get_current_user_id());
        if ($student_count > tks_get_can_regist_student_count($pmpro_level->id)){
            
            pmpro_setMessage("申し込みのプランの登録可能生徒数は、現在の登録済みの生徒数より少ないため、先に登録生徒を削除して下さい。", "pmpro_error");
            //このエラーはここで終了(これ以上チェックしない)            
            return false;
        }

        /*ここからエラーメッセージを積み上げするエラーチェック*/
        $err_msg = '';
        /*************************/
        //ユーザー名にメールアドレスは使えない
        global $username;
        if(!empty($username) && strpos($username, "@") !== false) {
            $okay = false;
            $err_msg = tks_stack_br_string($err_msg,'ユーザーIDにメールアドレスは使用できません。');
//            pmpro_setMessage( 'ユーザーIDにメールアドレスは使用できません。', 'pmpro_error' );
        }
        /*************************/
        //ユーザー名に全角文字は使えない
        if ( isset( $_REQUEST['username'] ) ) {
            $username = $_REQUEST['username'];
            $len = mb_strlen($username, "UTF-8");
            $wdt = mb_strwidth($username, "UTF-8");
            if ($len != $wdt){
                $okay = false;
                $err_msg = tks_stack_br_string($err_msg,'ユーザーIDに全角文字は使用できません。');
            }elseif($wdt < 6){
                $okay = false;
                $err_msg = tks_stack_br_string($err_msg,'ユーザーIDは、6文字以上にして下さい。');
            }else{
                if (preg_match('|%([a-fA-F0-9][a-fA-F0-9])|', $username) ||
                    preg_match('/&.+?;/',$username) ||
                    preg_match( '|[^a-z0-9 _.\-@]|i', $username )){
                        $okay = false;
                        $err_msg = tks_stack_br_string($err_msg,'ユーザーIDに使用できない文字が含まれています。記号は「.」「-」「_」のみ使用できます。');
                    
                }
            }
            if ($okay){
                $username = sanitize_user( $username , true);
            }
        }

        /**************************/
        //パスワードの有効性チェック
        if ( isset( $_REQUEST['username'] ) && isset( $_REQUEST['password'] )) {
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];

            // Check for length (8 characters)
            if ( strlen( $password ) < tks_const::SYSTEM_PASWDCHECK_LENGTH ) {
                $okay = false;
                $err_msg = tks_stack_br_string($err_msg,'パスワードは8文字以上で設定して下さい。');
            }

            // Check for username match
            if ( $password == $username ) {
                $okay = false;
                $err_msg = tks_stack_br_string($err_msg,'ユーザーIDと異なるパスワードを設定して下さい。');
            }

            // Check for containing username
            if ( strpos( $password, $username ) !== false ) {
                $okay = false;
                $err_msg = tks_stack_br_string($err_msg,'ユーザーIDを含めたパスワードは設定できません。');
            }
            //パスワードのチェック強化の場合は、以下チェック
            if (tks_const::SYSTEM_PASWDCHECK_STRONG){
                // Check for lowercase
                if ( ! preg_match( '/[a-z]/', $password ) ) {
                    $okay = false;
                    $err_msg = tks_stack_br_string($err_msg,'パスワードには、少なくとも1つの小文字を含めて下さい。');
                }

                // Check for uppercase
                if ( ! preg_match( '/[A-Z]/', $password ) ) {
                    $okay = false;
                    $err_msg = tks_stack_br_string($err_msg,'パスワードには、少なくとも1つの大文字を含めて下さい。');
                }

                // Check for numbers
                if ( ! preg_match( '/[0-9]/', $password ) ) {
                    $okay = false;
                    $err_msg = tks_stack_br_string($err_msg,'パスワードには、少なくとも1つの数字を含めて下さい。');
                }

                // Check for special characters
                if ( ! preg_match( '/[\W]/', $password ) ) {
                    $okay = false;
                    $err_msg = tks_stack_br_string($err_msg,'パスワードには、少なくとも1つの特殊文字を含めてください。');
                }
            }
        }

        /*************************/
        //メールアドレス有効性チェック
        $email = $_REQUEST['bemail'];
        if ( tks_checkForInvalidDomain( $email ) ) {
            //global $pmpro_msg, $pmpro_msgt;
            $okay = false;
            $err_msg = tks_stack_br_string($err_msg,'有効なメールアドレスを入力して下さい。');
            //pmpro_setMessage( '有効なメールアドレスを入力して下さい。', 'pmpro_error' );
            //$pmpro_msg = "有効なメールアドレスを入力して下さい。";
            //$pmpro_msgt = "pmpro_error";
            
        }

        /*************************/
        //〒番号が不正
        $zipcode = $_REQUEST['tks_zipcode'];
        if (strlen($zipcode) > 0 && strlen(tks_format_number($zipcode) == 0)){
            $okay = false;
            $err_msg = tks_stack_br_string($err_msg,'有効な郵便番号を入力して下さい。');
            //pmpro_setMessage( '有効な郵便番号を入力して下さい。', 'pmpro_error' );
        }

        /*************************/
        //電話番号が不正
        $phone = $_REQUEST['tks_phone'];
        if (strlen($phone) > 0 && strlen(tks_format_number($phone) == 0)){
            $okay = false;
            //global $pmpro_msg,$pmpro_msgt;
            //$pmpro_msg = "おおい.<br>aaaaaa";
		    //$pmpro_msgt = "pmpro_error";
            $err_msg = tks_stack_br_string($err_msg,'有効な電話番号を入力して下さい。');
            //pmpro_setMessage( '有効な電話番号を入力して下さい。<br>aaaaaaaaaaaa', 'pmpro_error' );
            //pmpro_setMessage( '有効な電話番号を入力して下さい。', 'pmpro_error' );
        }

        //スタックしたエラーメッセージをセットする
        if (! $okay){
            pmpro_setMessage( $err_msg, 'pmpro_error' );
        }
    }
    return $okay;

}
add_filter("pmpro_registration_checks", "tks_pmpro_registration_checks",10,1);
//add_filter("pmpro_registration_checks", "tks_pmpro_registration_checks");

/**
 * 引数で渡された文字がEmptyではない場合は<BR>タグで連結する
 */
function tks_stack_br_string($str,$stack){
    if (empty($str)){
        return $stack;
    }
    
    return $str . '<br>' . $stack;
}
/**
 * 新規ユーザー登録時、ログインIDに含まれるスペース文字をアンダースコアに変更
 * 意味あるのか・・？
 */
function tks_format_userlogin_at_save( $userdata ) {

	$userdata['user_login'] = str_replace(' ', '_', $userdata['user_login'] );
    
    return $userdata;
}
add_filter( 'pmpro_checkout_new_user_array', 'tks_format_userlogin_at_save', 10, 1 );

/**
 * 電話番号や郵便番号をフォーマットする
 * 数値のみに変換
 */
function tks_format_number( $phone ) {
    
    return preg_replace("/[^0-9]/",'',$phone);
    
}
add_filter( 'pmpro_format_phone', 'tks_format_number' );

/**
 * 支払が失敗した時のエラーメッセージを日本語へ変換する（Stripe）
 */
//↓↓↓↓支払が失敗した直後のフィルター、フィルター名で検索で、支払しているソースを見る事ができます。
//  add_filter( 'pmpro_checkout_confirmed', function($pmpro_confirmed, $morder){
//      return $pmpro_confirmed;
//  },10,2 );
add_filter( 'pmpro_checkout_message', function($pmpro_msg, $pmpro_msgt){
    //expiry　//Expired
    if (stripos($pmpro_msg,"expired") !== false){
        $pmpro_msg = "入力されたカードは、有効期限が失効しています。他のカードをお試しください。";
        return $pmpro_msg;
    }elseif (strpos($pmpro_msg,"Your card was declined") !== false){
        $pmpro_msg = "入力されたカードは、ご利用する事ができません。他のカードをお試しください。";
        return $pmpro_msg;
    }elseif (strpos($pmpro_msg,"Your card's security code is incorrect") !== false){
        $pmpro_msg = "セキュリティーコード(CVC)が正しくありません。カード裏面のセキュリティーコードをお確かめください。";
        return $pmpro_msg;
    }elseif (strpos($pmpro_msg,"An error occurred while processing your card. Try again in a little bit") !== false){
        $pmpro_msg = "決済処理中にエラーが発生しました。お手数がですが後ほどお試しください。";
        return $pmpro_msg;
    }elseif (stripos($pmpro_msg,"api") !== false){
        $pmpro_msg = "決済処理中にサーバーエラーが発生しました。お手数がですが後ほどお試し頂くか、以下のエラーメッセージをメールかお電話でお問い合わせください。<br>" . $pmpro_msg . "<p>お問合せ先:" . tks_const::SYSTEM_MANAGER_NAME . "<br>電話番号：" . tks_const::SYSTEM_MANAGER_TEL . '<br>メール：' . tks_const::SYSTEM_MANAGER_MAIL . '</p>';
        return $pmpro_msg;
    }elseif (strpos( $pmpro_msg, 'field is required' )){
        $str = str_replace("The", "", $pmpro_msg);
        $str = str_replace("field is required.", "", $str);
        $pmpro_msg = $str . "は省略できません";
    }elseif ( strpos( $pmpro_msg, 'fields are required' ) ) {
        $pmpro_msg = 'すべての必須フィールドを入力してください。';
        
        return $pmpro_msg;
    }else{
        
        //$pmpro_msg = "決済処理中にエラーが発生しました。お手数がですが後ほどお試し頂くか、以下のエラーメッセージをメールかお電話でお問い合わせください。<br>" . $pmpro_msg . "<p>お問合せ先：" . tks_const::SYSTEM_MANAGER_NAME . "<br>電話番号：" . tks_const::SYSTEM_MANAGER_TEL . '<br>メール：' . tks_const::SYSTEM_MANAGER_MAIL . '</p>';
        return $pmpro_msg;
    }
    return $pmpro_msg;

},10,2 );

/**
 * チェックアウトページの場合は、プレースホルダーを追加する
 * ・PMProで追加されたLabelタグをプレースホルダーに置き換える
 * ・ヒントを表示する
 */
add_action( 'wp_footer', function() {
	
	global $pmpro_pages;
    //チェックアウトページの場合
	if ( is_page( $pmpro_pages['checkout'] ) ) {
	?>
	<script >
        (function($){
    // //ユーザー名
    // jQuery("#username").attr('placeholder', jQuery("label[for='username']").text());
    // jQuery("label[for='username']").empty();
    // //パスワード
    // jQuery("#password").attr('placeholder', jQuery("label[for='password']").text());
    // jQuery("label[for='password']").empty();
    // //パスワード確認
    // jQuery("#password2").attr('placeholder', jQuery("label[for='password2']").text());
    // jQuery("label[for='password2']").empty();
    // //姓    
    // jQuery("#first_name").attr('placeholder', jQuery("label[for='first_name']").text());
    // jQuery("label[for='first_name']").empty();
    // //名
    // jQuery("#last_name").attr('placeholder', jQuery("label[for='last_name']").text());
    // jQuery("label[for='last_name']").empty();
    // //メールアドレス
    // jQuery("#bemail").attr('placeholder', jQuery("label[for='bemail']").text());
    // jQuery("label[for='bemail']").empty();
    // //メールアドレス確認
    // jQuery("#bconfirmemail").attr('placeholder', jQuery("label[for='bconfirmemail']").text());
    // jQuery("label[for='bconfirmemail']").empty();
    // //郵便番号
    // jQuery("#tks_zipcode").attr('placeholder', jQuery("label[for='tks_zipcode']").text());
    // jQuery("label[for='tks_zipcode']").empty();
    // //住所1
    // jQuery("#tks_address1").attr('placeholder', jQuery("label[for='tks_address1']").text());
    // jQuery("label[for='tks_address1']").empty();
    // //住所2    
    // jQuery("#tks_address2").attr('placeholder', jQuery("label[for='tks_address2']").text());
    // jQuery("label[for='tks_address2']").empty();
    // //電話番号
    // jQuery("#tks_phone").attr('placeholder', jQuery("label[for='tks_phone']").text());
    // jQuery("label[for='tks_phone']").empty();
    // //入力ヒントを表示する
    //jQuery('#username').after('<p><small class="lite">ログインするためのIDです。登録後にユーザー名を変更する事はできません。</small></p>');
    //jQuery('#password2').after('<p><small class="lite">確認のためもう一度パスワードを入力します</small></p>');
    //jQuery('#bconfirmemail').after('<p><small class="lite">確認のためもう一度メールアドレスを入力します</small></p>');
    
    jQuery(function () {
        //choosing payment method
        $('input[name=gateway]').click(function() {
            if($(this).val() == 'stripe') {
                $('#tks_stripe_info').show();
            } else {
                $('#tks_stripe_info').hide();
            }
        });

        let zo_ho = $('input[name=tks_zokusei]:eq(0)');
        let zo_ko = $('input[name=tks_zokusei]:eq(1)');
        if (!zo_ho.prop("checked") && !zo_ko.prop("checked")){
            zo_ho.prop('checked', true);
        }
        
        $('[name=tks_zokusei]').click(function() {
            if (zo_ho.prop('checked')) {
                $('#company_name').val('');
            } else {
                $('#company_name').val('個人');
            }
        });

        jQuery('#tks_zipcode').jpostal({
		    postcode : [
			    '#tks_zipcode'
		    ],
		    address : {
			    '#tks_address1' : '%3%4%5'
		    }
	    });
    });
    
    })(jQuery);
	</script>
	<?php
	}
});

/**
 * ペイメントラベルの書き換え
 * 支払方法プランのラジオボタンに表示されるラベルをカスタマイズ
 * 個人モードの場合と法人モードの場合で修正が必要
 * 及び、お得ラベルも書き換える事
 */
function tks_override_payment_label($payment_label){
    //個人モードの場合
    if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_KOJIN){
        //ラベル書き換え、まずは、置換しやすいように空白除去
        $payment_label = str_replace(" ","",$payment_label);
        //ラジオボタンに表示される料金プランラベルなので、無条件に初回？円の文言を除去
        $payment_label = preg_replace("/初回.*、/","",$payment_label);
        $payment_label = str_replace("円です。","円",$payment_label);
        //ここでは、レベル情報を取得できないので、置換できたかどうかで判定して置換していく
        //月額の場合
        $payment_label = str_replace("月額","【月額】",$payment_label,$count);
        //月額で、半年の場合
        if ($count == 0){   
            $payment_label = str_replace("半年ごとに","【半年ごとに】",$payment_label,$count);
            //ここは実際の基本の料金の割引率を書かないとダメ（手動でメンテナンス）
            if ($count > 0){
                $payment_label = $payment_label . ' <small style="color:silver;">(5%お得！)</small>'; 
                $count = 0;
            }
        }   
        //年額の場合
        if ($count == 0){
            $payment_label = str_replace("年額","【年額】",$payment_label,$count);
            //ここは実際の基本の料金の割引率を書かないとダメ（手動でメンテナンス）
            if ($count > 0){
                $payment_label = $payment_label . ' <small style="color:silver;">(10%お得！)</small>'; 
                $count = 0;
            }
        }
        //日額の場合
        if ($count == 0){
            $payment_label = str_replace("日額","【日額】",$payment_label,$count);
            //ここは実際の基本の料金の割引率を書かないとダメ（手動でメンテナンス）
            // if ($count > 0){
            //     $payment_label = $payment_label . ' <small style="color:silver;">(10%お得！)</small>'; 
            //     $count = 0;
            // }
        }
        return $payment_label;
    }else{
        //法人モードの書き換えはこちらで行う
        
        return $payment_label;
    }
}

/**
 *  チェックアウトページやレベルページの料金フォーマットを修正する
 * 
 * ★トライアル期間のアドインの日本語化ができていないので以下フィルターで書き換える
 * pmpro-subscription-delays.php #line333付近
 * ★トライアルを既に消費しているユーザーには、トライアル文言も削除する
 * ★その他、料金表示の調整（初回0円ならその文言を表示しない・6月ごとを半年に直すなど）
 */
add_filter( 'pmpro_level_cost_text', function( $cost, $level ){
    global $current_user;
    
    // ログインしていない？なら新規なので表示
	if ( empty( $current_user->ID )) {
    	$can_display = true;
	}else{
        //消化済みなら表示する（なんらかの値が入ってたら使用済み）
        $already = get_user_meta( $current_user->ID, 'pmpro_trial_level_used', true );
        if (!empty($already)){
            $can_display = false;
        }else{
            $can_display = true;
        }
    }
    
    //まだトライアルを使用していないなら表示する
    if ($can_display){
        //ここ↓は、プラグイン[pmpro-subscription-delays]のpotファイルにない部分、なので手動で置き換える
        $cost = str_replace(" after your ","<small>※",$cost); 
        $cost = str_replace(" day trial","日間の試用期間後のご請求となります</small>",$cost); 
        $cost = str_replace("</strong>.","</strong>",$cost);     
        //↑↑↑
    //トライアル消化済みなら表示しない
    }else{
        //この方法だと、cycle_numberが無視されて全て「毎月？円、毎日？円、毎年？円」という表示になってしまうため、正規表現で試用期間の文言を除去する
        // if (is_page( $pmpro_pages[ 'levels' ] )){
        //     $cost = sprintf( __( '<strong>%1$s per %2$s</strong>.', 'paid-memberships-pro' ), pmpro_formatPrice( $level->billing_amount ), pmpro_translate_billing_period( $level->cycle_period ) );
        // }
        $cost = preg_replace("/ after your .*strong>./","",$cost);
    }

    //その他、料金表示の調整
    //★初回0円ならその文言を表示しない
    if ( $level->initial_payment == 0){
        $cost = preg_replace("/初回.*、/","",$cost);
    }
    //「あたり」と「ごとに」対応(1回)
    //$cost = str_replace(" ","",$cost);
    if ($level->cycle_number == 1){
        if ($level->cycle_period == "Month") $cost = str_replace("月あたり"," 月額",$cost);
        if ($level->cycle_period == "Year") $cost = str_replace("年あたり"," 年額",$cost);
        if ($level->cycle_period == "Day") $cost = str_replace("日あたり"," 日額",$cost);
        $cost = str_replace("その後、","",$cost);
    }else{
        if ($level->cycle_period == "Month"){
            if ($level->cycle_number == 6){
                $cost = str_replace("6月ごとに"," 半年ごとに",$cost);
            }else{
                $cost = str_replace("月ごとに"," ヶ月ごとに",$cost);
            }
        }
    }

    return $cost;
}, 99, 2 );

/**
 * メンバーシップチェックアウトの利用規約ページのコンテンツの前に、今日の日付を表示します
 */
function show_today_date_before_pmpro_tos_content( $pmpro_tos_content, $pmpro_tos ) {
    //PDFを表示するリンクボタンを利用規約に直接入れるなら下のコメントアウトしたものを復活させる
    //$tos_link = plugins_url( '../etc/ご利用規約.pdf', __FILE__ );
    //$pmpro_tos_content = '<p>' . date( get_option( 'date_format' ) ) . '<span style="float: right;"><a href="'. $tos_link . '" class="tosbtn fas fa-file-pdf" target="_blank" rel="noopener noreferrer">PDFで開く</a></span></p>' . $pmpro_tos_content;
    $pmpro_tos_content = '<p>' . date( get_option( 'date_format' ) ) . $pmpro_tos_content;
	return $pmpro_tos_content;
}
add_filter( 'pmpro_tos_content', 'show_today_date_before_pmpro_tos_content', 10, 2 );

/**
 * 支払ページにクレジットカード会社のロゴを表示
 * Add credit card icons to Paid Memberships Pro Checkout.
 * Download the icons from https://www.paidmembershipspro.com/add-credit-cards-and-paypal-logos-to-checkout-when-using-paypal-gateway-or-add-paypal-express-add-on/
 * Unzip the icons and copy the cc-horizontal.jpg into your PMPro Customizations Plugin.
 * If you haven't setup the PMPro Customizations Plugin yet, follow this guide - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 * www.paidmembershipspro.com
 */
function tks_add_cardcompany_logos_to_checkout(){
	global $pmpro_level;
    
    $gateway = pmpro_getGateway();
    
	//if ( ! pmpro_isLevelFree( $pmpro_level ) && ! $gateway == "check"){
    if ($gateway == 'stripe'){
        echo "<div id='tks_stripe_info'>";
		echo "<h3>ご利用可能なクレジットカード</h3>";
		echo "<img src='". plugins_url( '../etc/cc-horizontal.png', __FILE__ ) ."' />";
        echo "</div>";
	}
}
add_action( 'pmpro_checkout_after_billing_fields', 'tks_add_cardcompany_logos_to_checkout', 10 );

/**
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
 * 小切手で支払うというオプションを銀行振込に変更する
 * Copy the code below into your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_change_disptext_for_check( $translated_text, $text, $domain ) {
	switch ( $translated_text ) {
		case 'Pay by Check' :
			$translated_text = __( '銀行振込', 'paid-memberships-pro' );
			break;
	}
	return $translated_text;
}
add_filter( 'gettext', 'my_pmpro_change_disptext_for_check', 20, 3 );

/**
 * emailのドメインブロック
 */
function tks_checkForInvalidDomain( $email ) {
    $domain = tks_getDomainFromEmail( $email );

    // Update this array to include the domains you want to block
    //$invalid_domains = array( "yopmail.com", "*.aol.com","twzhhq.com","instance-email.com","john-titor.work","1-tm.com","moimoi.re" );
    //↓定数化した↑
    $invalid_domains = tks_const::TKS_BLOCK_MAIL_DOMEIN_FOR_CHECKOUT_PAGE;

    foreach ( $invalid_domains as $invalid_domain ) {
        $components = explode( '.', $invalid_domain );
        $domain_to_check = explode( '.', $domain );
        if (empty($domain_to_check)){
            $domain_to_check = false;
        }else{
            $domain_to_check = (sizeof( $domain_to_check ) > 2 );
        }
        if ( $components[0] == "*" && $domain_to_check ) {
            if ( $components[1] == $domain_to_check[1] && $components[2] == $domain_to_check[2] ) {
                return true;
            }
        } else {
            if ( ! ( strpos( $invalid_domain, $domain ) === false ) ) {
                return true;
            }
        }
    }

    return false;
}
/**
 * ※振込とカード払いで完全にプランを分けたのでもう必要なし
 * チェックアウト時に、価格を調整しる
 * 振込の場合は、前払いにする
 */
// function my_pmpro_checkout_level($level) {
// 	//ゲートウェイによって分ける
// 	if( !empty( $_REQUEST['gateway'] ) ){
// 		//振込の場合は、前払いなので初期費用を調整
// 		if( $_REQUEST['gateway'] == 'check' ){

// 			if( !empty( $_REQUEST['level'] ) ){

// 				if( in_array( $_REQUEST['level'], array( 1, 2, 3, 4, 5, 6, 7 ) ) ){

// 					$level->initial_payment = $level->billing_amount;	//振込の場合は、初回の支払いあり
				
// 				}
				
// 			}

// 		}

// 	}

// 	return $level;
// }
// add_filter("pmpro_checkout_level", "my_pmpro_checkout_level");

/**
 * 新規支払が完了したときにリーダーを登録する
 */
add_action( 'pmpro_after_checkout',  
function($user_id, $member)
{   
    //登録対象のユーザーが既にグループリーダーの場合は、プラン変更のチェックアウト処理であるためリーダーとしてレジストしない
	if (!tks_learndash_is_group_leader_user($user_id)){
        tks_regist_leader($user_id);
    }
	
	$level = $member->membership_level->id;
	//tks_enrol_courses_by_plan($user_id, $member->getMembershipLevelAtCheckout());
	tks_enrol_courses_by_plan($user_id, $level);
		
    //支払い方法がチェックの場合のみサンクスページのメッセージを変える
    global $pmpro_level;
    
    $gateway = pmpro_getGateway();
    
	if ($gateway == 'check'){
        //確認画面に表示されるメッセージを変更する
        remove_filter( 'pmpro_confirmation_message', 'pmpro_pmpro_confirmation_message' );
    }

}, 10 ,2 );

/**
 * 振込払いの時の申込画面に使用するメッセージ
 */
function tks_add_custom_checkout_message_pay_by_check() {
	global $gateway, $pmpro_level;

	if($gateway == "check" && !pmpro_isLevelFree($pmpro_level)) { 
		
	    echo tks_const_str::BEFORE_CHECKOUT_MSG_PAY_BY_CHECK;
		
	}
}
add_action( 'pmpro_checkout_after_payment_information_fields', 'tks_add_custom_checkout_message_pay_by_check' );