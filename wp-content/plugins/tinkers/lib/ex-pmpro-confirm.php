<?php
/**
 * 支払完了ページメッセージのカスタマイズ
 *
 * 支払い方法がCheck（振込）だった場合に申し込み後の確認ページに表示されるメッセージを変更する
 * ※通常ゲートウェイの(小切手で支払う)オプションの[手順]メッセージがそのまま使われるのだが、
 * ここでは、振込先情報を表示させたいのでこのように変更する
 *
 */
add_filter("pmpro_confirmation_message", function($message, $pmpro_invoice){
    global $pmpro_invoice;

    //支払い方法がチェックの場合のみサンクスページのメッセージを変える	
    $pmpro_invoice->getMembershipLevel();

    // 現金払い完了時のメッセージ
    if ( $pmpro_invoice->gateway == "check" ){
      $message .= tks_const_str::AFTER_CHECKOUT_COMFIRM_MSG_PAY_BY_CHECK_BANK_INFO . tks_const_str::AFTER_CHECKOUT_COMFIRM_MSG_PAY_BY_CHECK;
    }else{
        if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_HOUJIN){
            $str_seito = "生徒";
            $str_menu = "生徒管理->生徒登録";
        }else{
            $str_seito = "お子さま";
            $str_menu = "お子さま->登録する";
        }
        
        $STUDENT_REGIST_URL = tks_get_home_url(tks_const::PAGE_REGIST_STUDENT);
        //既に子供が登録されている場合は以下のメッセージは表示しない
        if (tks_get_only_student_count(get_current_user_id()) == 0){
            $message .=  '<div style="margin-bottom:5px;">続けて' . $str_seito . 'の登録をするには、以下のボタンをクリックして下さい。</div>';
            $message .=  '<a href="' . $STUDENT_REGIST_URL . '" class="ast-button" style="padding:10px 20px;display: inline-block;">' . $str_seito . 'の登録</a><br>';
            $message .=  '<div style="margin-top:10px;font-size: small;color: #5F9EA0">※' . $str_seito . 'の登録は、上部のメニュー「' . $str_menu . '」より、いつでも行う事ができます。</div></p><hr>';
        }
        
    }
    
    $LOGO_URL = get_site_icon_url(); 
    $message = '<img src="' . $LOGO_URL .  '" alt="" width="143" height="143" class="aligncenter size-full wp-image-35281" /></p>' . $message;
    
    return $message;
},10,2);
