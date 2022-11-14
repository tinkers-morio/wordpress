<?php

/**
 * 予約フォームの大人、子供、男性、女性の文字を非表示にする
 */
add_filter('booking_form_count_label', 
function($label){
    return "";
});

/**
 * 予約キャンセルフォームのキャンセルボタンキャプション
 */
add_filter('subscription_cancel_button_top', 
function($label){
    return "予約をキャンセル";
});

/**
 * 予約エラー時のメッセージフィルター
 * ワーニング、エラー全て含まれる
 */
add_filter('mts_booking_form_set_message',
function($err_msg,$err_name){
    switch ($err_name) {
        case 'ERROR_SEND_MAIL':
            return 'ご予約メールの送信に失敗しました。お手数ですがお電話で予約のご確認をお願いします。<br>' . tks_const::SYSTEM_MANAGER_NAME . '：電話番号 ' . tks_const::SYSTEM_MANAGER_TEL;
        case 'ERROR_MULTIPLE_BOOKING':
            return 'すでに予約されております。';
    }
    return '';
},10,2);

/**
 * 予約確認画面のヘッダーへ表示するメッセージ
 */
add_filter('booking_form_confirm_before', function($message, $msg_name){
    $message = "<p>※まだ予約は確定していません。ご確認の上「予約する」ボタンをクリックして予約を確定して下さい。</p>"; 
    return $message;
},10,2);

/**
 * 最小予約人数は、1人（0人なんて選ばせない）
 */
add_filter('booking_form_input_number_minimum', function($num, $key){
    $num = 1;
    return $num;
},10,2);

/**
 * 予約メールの件名を編集する（予約品目名をつけないとね）
 */
add_filter('mtssb_mail_booking_subject', function($subject,$fParams){
    $a_title = get_the_title($fParams['article_id']);
    //管理者向けアドレスの場合は、件名にユーザーIDを付ける
    if ($fParams['receiver'] == 'admin'){
        global $mts_simple_booking;
        return '【予約受付】' . '(ユーザーID：' . $mts_simple_booking->oUser->oWPUser->user_login. ")" . $a_title;
    }
    return $subject . $a_title;
},10,2);
/**
 * 予約メールの[ご予約]というタイトルを変更する
 */
add_filter('booking_form_number_title', function($title){
    return "[ご予約内容]";
},10,1);
/**
 * 予約メールの[連絡先]というタイトルを変更する
 */
add_filter('booking_form_client_title',function($title){
    return "[ご予約のお客様]";
},10,1);
/**
 * 予約メールの「名前」というタイトルを変更する
 */
apply_filters('booking_form_name', function($title){
    return "お名前";
},10,1);
/**
 * 予約メールの「現在の予約状況」なんぞ要らない！
 */
add_filter('mtssb_mail_booking_info',function($info,$article){
    return "";
},10,2);
/**
 * 予約キャンセルメールの件名を編集
 */
add_filter('mtssb_mail_cancel_mysubject', function($title){
    global $mts_simple_booking;
    return $title . '(ユーザーID：'.$mts_simple_booking->oUser->oWPUser->user_login. ')';
},10,1);
add_filter('mtssb_mail_exchange',function($article){
    
    $article['subject'] = $article['subject'] . get_the_title($article['aid']);
    return $article;
},10,1);

