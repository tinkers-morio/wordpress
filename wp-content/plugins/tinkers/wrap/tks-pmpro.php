<?php

/**
 * 支払いメンバーオブジェクトを返す
 *
 * @param $user_id  - 該当ユーザーID
 *
 * @return PMPRO_Member
 *
 */
function tks_pmpro_get_member( $user_id ) {
    
    if (function_exists('pmpro_getMembershipLevelForUser')){
        return pmpro_getMembershipLevelForUser($user_id);
    }else{
        return;
    }
 }

/**
 * プランが期限切れか否かを返す
 */
function tks_pmpro_isLevelExpiring( $level ) {
    
    if (function_exists('pmpro_isLevelExpiring')){
        return pmpro_isLevelExpiring( $level );
    }else{
        return;
    }
}

/**
 * 特定のユーザーのメンバーシップレベルを特定のレベルに作成、追加、削除、または更新します
 * $ levelは、目的のmembership_levelのIDまたは名前のいずれかです。
 * $ user_idを省略すると、値は$ current_userから取得されます。
 * 
 * @param int $level int $ level新しいレベルとして設定するレベルのID、0を使用してメンバーシップをキャンセルします
 * @param int $ user_idレベルを変更するユーザーのID
 * @param string $ old_level_statusメンバーシップusersテーブルの行に設定するステータス。 （例：inactive、canceled、admin_cancelled、expired）デフォルトは「inactive」です。
 * @param int $ cancel_level設定されている場合、すべてのアクティブなレベルではなく、この1つのレベルのみをキャンセルします（ユーザーごとに複数のメンバーシップをサポートするため）
 */
function tks_pmpro_changeMembershipLevel( $level, $user_id = null, $old_level_status = 'inactive', $cancel_level = null ) {
    if (function_exists('pmpro_changeMembershipLevel')){
        return pmpro_changeMembershipLevel( $level, $user_id, $old_level_status, $cancel_level );
    }else{
        return;
    }
}

/**
 * 全てのプラン(レベル)を返す
 * 
 * @param bool $include_hidden 非表示のプランも含めて取得する
 */
function tks_pmpro_get_all_levels($include_hidden=false){
    if ( !function_exists( 'pmpro_getAllLevels' ) ) { 
        require_once ABSPATH . PLUGINDIR . 'paid-memberships-pro/includes/functions.php'; 
    } 
      
    $result = pmpro_getAllLevels($include_hidden, false,false); 
    return $result;
}

/**
 * Paid Memberships Pro
 * ユーザーの次の支払予定日を取得する
 * ユーザーIDを省略した場合は、現在のログインユーザーの支払予定日
 */
function tks_pmpro_payment_date_text($user_id=null) {
    if(function_exists( 'pmpro_next_payment' )) {
        
        if (empty($user_id)){
            $next_payment = pmpro_next_payment();
        }else{
            $next_payment = pmpro_next_payment($user_id);
        }
            
        if( $next_payment ){
            return date_i18n( get_option( 'date_format' ), $next_payment );
        }
        
        return "";
    }
}

/**
 * Paid MembershipPro
 * ユーザーの有効期限を取得する
 */
function tks_pmpro_expir_date_text($user_id=null) {
    if(function_exists( 'pmpro_getMembershipLevelForUser' )) {
        if (empty($user_id)){
            $user_id = get_current_user();
        }
        $level = pmpro_getMembershipLevelForUser($user_id); 
        if (!empty($level) && !empty($level->enddate)){
            return date_i18n( get_option( 'date_format' ), $level->enddate );
        }
    }
    return "";
}

/**
 * プランの残日数を取得する
 * トライアルの
 */
function tks_get_pmpro_day_left(){
    //次回更新日を取得する際に、フィルターを削除してからでないと試用期間の期日を取得してきてしまうため正確な更新日がとれない
//    remove_filter( 'pmpro_next_payment', 'pmprosd_pmpro_next_payment', 10, 3 );
    $next_payment = pmpro_next_payment();
    $next_payment_day = tks_pmpro_payment_date_text();    //デバッグ用で取得
    //影響を最小限にするため、すぐにフィルターを復活させる
//    add_filter( 'pmpro_next_payment', 'pmprosd_pmpro_next_payment', 10, 3 );

    $days_left = 0;

    if (!empty($next_payment)){
        $todays_date = time();
        $time_left = $next_payment - $todays_date;
        //time left?
        if ( $time_left > 0 ) {
             $days_left = floor( $time_left/( 60*60*24 ) ) ;
            //$start_date = date( 'Y-m-d', strtotime( '+ ' . intval( $days_left ) . ' Days', current_time( 'timestamp' ) ) ) . 'T0:0:0';
        }
    }

    return $days_left;
}