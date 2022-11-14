<?php
/**
 * 申し込み情報ページのリンクを変更する
 */
add_filter( 'pmpro_account_profile_action_links', function($pmpro_profile_action_links ){
    $pmpro_profile_action_links['edit-profile'] = sprintf("<a id='pmpro_actionlink-profile' href='" . esc_url(home_url( '/' ) . tks_const::PAGE_EDIT_LEADER) . "'>プロフィールを編集</a>");
    $pmpro_profile_action_links['change-password']=$pmpro_profile_action_links['logout'];
    $pmpro_profile_action_links['logout']="";
    return $pmpro_profile_action_links;
});

//「有効期限」を「更新日」に変更します
//このフックは、以下のwp_renewal_dates_setup関数で設定されます
function change_expiration_date_to_renewal_date($translated_text, $original_text, $domain) {
    if($domain === 'paid-memberships-pro' && $original_text === 'Expiration')
        $translated_text = '次回支払日';
    
    return $translated_text;
}

// ユーザーに更新日があるかどうかを判断し、代わりにそれを表示するようにフックを設定する
function wp_renewal_dates_setup() {
    global $current_user, $pmpro_pages;
    
    // PMProがアクティブでない場合
    if(!function_exists('pmpro_getMembershipLevelForUser'))
        return;
    
    // ユーザーに有効期限がある場合は、PMProに「間もなく」有効期限が切れることを伝えて、更新リンクが表示されるようにします
    $membership_level = pmpro_getMembershipLevelForUser($current_user->ID);            
    if(!empty($membership_level) && !pmpro_isLevelRecurring($membership_level))
        add_filter('pmpro_is_level_expiring_soon', '__return_true');    
    
    if( is_page( $pmpro_pages[ 'account' ] ) ) {
        //ユーザーに有効期限がない場合は、フィルターを追加して「有効期限」を「更新日」に変更します   
        if(!empty($membership_level) && (empty($membership_level->enddate) || $membership_level->enddate == '0000-00-00 00:00:00'))
            add_filter('gettext', 'change_expiration_date_to_renewal_date', 10, 3);        
        
        // ユーザーの最後の注文がPayPalExpressであったかどうかを確認します。そうでない場合は、Stripeであったと想定します。
        //これらのフィルターは、ゲートウェイにアクセスすることで、次の支払い計算をより正確にします
        $order = new MemberOrder();
        $order->getLastMemberOrder( $current_user->ID );
        if( !empty($order) && $order->gateway == 'paypalexpress') {
            add_filter('pmpro_next_payment', array('PMProGateway_paypalexpress', 'pmpro_next_payment'), 10, 3);    
        }else{
            add_filter('pmpro_next_payment', array('PMProGateway_stripe', 'pmpro_next_payment'), 10, 3);    
        }
    }
    add_filter('pmpro_account_membership_expiration_text', function($expiration_text){
        //次回更新日を取得
        $next_payment =  tks_pmpro_payment_date_text();
        if (empty($next_payment)){
            //次回更新日が取得できなければ有効期限を表示
            return $expiration_text;
        }
        return $next_payment;
    },10,1);    
}
add_action('wp', 'wp_renewal_dates_setup', 11);

// [プロファイルの編集]ページに管理者向けのユーザーの試用メタ設定を表示します。
function one_time_trial_show_trial_level_used( $user ) { 
	if ( current_user_can( 'edit_users' ) ) { ?>
		<h3>One-Time Trial</h3>
		<table class="form-table"> 
			<tbody>
				<tr>
					<th scope="row"></th>
					<td>
						<?php 
							$already = get_user_meta( $user->ID, 'pmpro_trial_level_used', true );
							//if ( ! empty( $already ) && $already == '1' ) {
                            if ( ! empty( $already ) ) {    
								echo '試用期間(トライアル期間)消化済み';
							} else {
								echo 'まだ試用期間は、未使用です';
							}
						?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}
add_action( 'show_user_profile', 'one_time_trial_show_trial_level_used' );
add_action( 'edit_user_profile', 'one_time_trial_show_trial_level_used' );

/*
 * メンバーシップアカウント情報ページ
 * コメントアウトされている箇所は、無料レベルだったらキャンセルリンクを表示しないようにしている
 * TinkersOnlineでは、無条件にキャンセルリンクを非表示にする
 * Hide cancel link from membership account page if
 * membership is free. Useful if using code to
 * give membership access til next expiration date on cancellation.
 */
function my_pmpro_free_remove_cancel_link( $pmpro_member_action_links ) {
	//$member_level = pmpro_getMembershipLevelForUser( $current_user->ID );

    //if( pmpro_isLevelFree($member_level) ){
	if (tks_const::SYSTEM_MODE == tks_const::SYSTEM_MODE_VALUE_HOUJIN){
		unset( $pmpro_member_action_links['cancel'] );
	}	
    //}
	return $pmpro_member_action_links;
}
add_filter( 'pmpro_member_action_links', 'my_pmpro_free_remove_cancel_link', 10);