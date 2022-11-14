<?php
/**
 * ★★試用期間の無料サービスを1度使ったユーザーは、再度、試用期間を使えなくする
 * 
 * This code stores data when a user checks out for a level. 
 * If that user tries to checkout for the same level, the Subscription Delay is removed. 
 * The user is instead charged for their first subscription payment at checkout.
 * ユーザーがレベルをチェックアウトするときにデータを保存します。 
 * そのユーザーが同じレベルをチェックアウトしようとすると、サブスクリプション遅延が削除されます。 
 * 代わりに、ユーザーはチェックアウト時に最初のサブスクリプションの支払いに対して課金されます。
 *
 */
//↓↓ここから
// ユーザーがトライアルレベルに達したときに記録します
function one_time_trial_save_trial_level_used( $level_id, $user_id ) {
	//対象のレベルIDをトライアルレベルのIDに設定します（固定するならこちら）
	//$trial_level_id = 1; // Membership Level ID
    //if ( $level_id == $trial_level_id ) {	
    $subscription_delay = get_option( 'pmpro_subscription_delay_' . $level_id, '' );    
    if (!empty($subscription_delay)){
    	// ユーザーメタを追加して、ユーザーが1回限りのトライアルを受けたことを記録します。
		update_user_meta( $user_id, 'pmpro_trial_level_used', $level_id );
	}	
}
add_action( 'pmpro_after_change_membership_level', 'one_time_trial_save_trial_level_used', 10, 2 );

// ユーザーがチェックアウト時に1回限りのトライアルを受け取ったかどうかを確認します。
function one_time_trial_delay_pmpro_registration_checks() {

    global $current_user;

	//対象のレベルIDをトライアルレベルのIDに設定します
	//$trial_level_id = 1; // Membership Level ID   //レベル固定にするならこちら
    
    if ( ! empty( $_REQUEST['level'] ) ) {
		$checkout_level_id = intval( $_REQUEST['level'] );
	}

    //if ( ! empty( $current_user->ID ) && ! empty( $checkout_level_id ) && $checkout_level_id == $trial_level_id ) {   //レベル固定にするならこちら
    if (! empty( $checkout_level_id )){
        $subscription_delay = get_option( 'pmpro_subscription_delay_' . $checkout_level_id, '' );    
    }
    if ( ! empty( $current_user->ID ) && ! empty( $checkout_level_id ) && ! empty($subscription_delay) ) {    
        
		//現在のユーザーのメタ情報をを確認します。
		$already = get_user_meta( $current_user->ID, 'pmpro_trial_level_used', true );
        //現在加入中のプランの残日数を確認します。
        $day_left = tks_get_pmpro_day_left();

		// サブスクリプションの遅延をチェックアウトから削除します。サブスクリプションをすぐに開始します（支払を開始）
        // 既に試用期間を使っている、かつ、残日数がない場合は、サブスクリプションを遅延する必要はないので、以下のフィルターを削除します。
		if ( $already && empty($day_left)) {
			remove_filter( 'pmpro_profile_start_date', 'pmprosd_pmpro_profile_start_date', 10, 2);
			remove_action( 'pmpro_after_checkout', 'pmprosd_pmpro_after_checkout' );
			remove_filter( 'pmpro_next_payment', 'pmprosd_pmpro_next_payment', 10, 3);
			remove_filter( 'pmpro_level_cost_text', 'pmprosd_level_cost_text', 10, 2);
			remove_action( 'pmpro_save_discount_code_level', 'pmprosd_pmpro_save_discount_code_level', 10, 2);
		}
	}	
}
add_filter( 'init', 'one_time_trial_delay_pmpro_registration_checks' );

// チェックアウト時に価格をフィルタリングして、請求額をすぐに請求します。
function one_time_trial_delay_pmpro_checkout_level( $level ) {

    global $current_user, $discount_code, $wpdb;

	// ログインしていない？
	if ( empty( $current_user->ID ) ) {
		return $level;
	}

	// 割引コードを使用している場合は除きます。(オリジナルのまま、だがなぜ割引コードを使っている場合は除くのか？なのでコメントアウトした)
	// if ( ! empty( $discount_code ) || ! empty( $_REQUEST[ 'discount_code' ] ) ) {
	// 	return $level;
	// }

	// 現在のユーザーのメタ情報を確認します。
	$already = get_user_meta( $current_user->ID, 'pmpro_trial_level_used', true );
    // 現在のプランの残日数を取得します。
    $day_left = tks_get_pmpro_day_left();

	// ユーザーがすでにこのレベルの試用版を持っている場合は、初期支払い=請求額を支払います。If the user already had the trial for this level, make initial payment = billing amount.
	//if ( $level->id == $already ) {       //レベル毎に試用期間を設ける場合はこちら
    //　既に試用版を使っている　かつ、現在加入プランの残日数がない場合は、即決済でOK！
    if (!empty($already) && empty($day_left)){
		$level->initial_payment = $level->billing_amount;
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'one_time_trial_delay_pmpro_checkout_level', 10 );
//↑↑↑ここまで


/**　
 * ★★同じプランで、年間払いなどで割引になる場合、申込フォームにラジオボタンでプランをセレクトできるようにする
 * 
 * https://www.paidmembershipspro.com/add-a-payment-plan-to-a-pmpro-checkout-page/
  * Create payment plans by mapping a level to discount codes representing payment plan options.
  * Useful for offering multiple pricing structures for membership (i.e. Monthly, Annually)
  *
  * Add this code below to your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
  */
//↓↓ここから
global $pmpro_payment_plans;

// Define the payment plans. '3' => array( 11, 12, 13 ) means that membership level with the ID of 3 can be paid using discount codes with the ID 11, 12, and 13
//以下の配列には、対象プランのレベルIDと配列の中には、ラジオボタンで表示させる割引プランIDを設定する
// $pmpro_payment_plans = array(
// 	'1' => array( 1, 2, 3 ),
// );
//定数化した↑↓
$pmpro_payment_plans = tks_const::TKS_MULTI_PRICE_FOR_CHECKOUT_PAGE;

// Show the "Select a Payment Plan" box with options at checkout.
function my_pmpro_payment_plan_checkout_boxes() {
	global $pmpro_payment_plans, $wpdb;
	if ( empty( $_REQUEST['level'] ) || empty( $_REQUEST['discount_code'] ) ) {
		return;
	}

	$level_id         = $_REQUEST['level'];
	$discount_code    = $_REQUEST['discount_code'];
	$discount_code_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->pmpro_discount_codes WHERE code=%s LIMIT 1", $discount_code ) );

	// Make sure passed discount code is valid.
	if ( ! pmpro_checkDiscountCode( $discount_code, $level_id ) ) {
		return;
	}
	// Group doesn't have a payment plan or using a different discount code? return.
	if ( empty( $pmpro_payment_plans ) || ! isset( $pmpro_payment_plans[ $level_id ] ) ) {
		return;
	}

	// Get payment options.
	$payment_options = $pmpro_payment_plans[ $level_id ];
	if ( ! is_array( $payment_options ) ) {
		$payment_options = array( $payment_options );
	}

	// Make sure discount code is a payment option.
	if ( ! in_array( $discount_code_id, $payment_options ) ) {
		return;
	}

	// Create payment plan box.
	//$title = 'Select a payment plan.';
    $title = 'お支払いの選択';
    ?>
    <div id="pmpro_level_options" class="pmpro_checkout">
		<h3><span class="pmpro_checkout-h3-name"><?php esc_attr_e( $title, 'paid-memberships-pro' ); ?></span></h3>
		<div class="pmpro_checkout-fields">
			<div class="pmpro_checkout-field pmpro_checkout-field-radio">
				<?php
				foreach ( $payment_options as $payment_option ) {
					// Make sure discount code is valid.
				    if ( ! pmpro_checkDiscountCode( $wpdb->get_var( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%d LIMIT 1", $payment_option ) ), $level_id ) ) {
						continue;
					}

					// Get discount code infomation.
					$sql_query = "SELECT * FROM $wpdb->pmpro_discount_codes_levels WHERE code_id = '" . $payment_option . "' AND level_id = '" . (int) $level_id . "' LIMIT 1";
					$payment_plan_level = $wpdb->get_row( $sql_query );
					$sql_query = "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id = '" . $payment_option . "'  LIMIT 1";
					$code_name = $wpdb->get_row( $sql_query )->code;
                    //ラベル書き換え(tinkers用にラベルを修正)
                    $payment_label = tks_override_payment_label(pmpro_getLevelCost( $payment_plan_level, false, true ));
					// Apply filters.
					$payment_plan_level = apply_filters( 'pmpro_checkout_level', $payment_plan_level );
					?>
					<div class="pmpro_checkout-field-radio-item">
						<input type="radio" id="pmpro_code_<?php echo $payment_option; ?>" class="my_pmpro_payment_plan_option" name="my_pmpro_payment_plan_option" value="<?php echo $code_name; ?>" <?php checked( $payment_option == $discount_code_id ); ?> >
						<label class="pmpro_label-inline" for="pmpro_code_<?php echo $payment_option; ?>" /><?php echo $payment_label?></label>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<script>
	jQuery(document).ready(function() {			

		// Prevent changing discount code manually
		jQuery('#other_discount_code_p').wrap("<div id='other_discount_code_p_hidden' style='display: none;'></div>");

		// Hide 'discount code updated' message in level cost text on first page load.
		if( jQuery ( "#pmpro_level_cost p" ).length > 1) {
			jQuery('#pmpro_level_cost p').first().hide();
		}

		// Update discount code when different payment plan is chosen
		jQuery('.my_pmpro_payment_plan_option').click(function() {
			// Get chosen payment plan
			code = jQuery('.my_pmpro_payment_plan_option:checked').val();
			// Set corresponding discount code
			jQuery( '#other_discount_code' ).val( code );
			// Update discount code
			jQuery('#other_discount_code_button').click();
		});

		// Save original checkout message
		pmpro_original_message = jQuery('#pmpro_message')[0].outerHTML;
		// After any AJAX call...
		jQuery( document ).ajaxComplete(function() {
			// If the current discount code was updated...
			if( jQuery ( "#pmpro_level_cost p" ).length > 1) {
				// Hide 'discount code updated' message in level cost text
				jQuery('#pmpro_level_cost p').first().hide();
				// Restore the original checkout message
				jQuery('#pmpro_message')[0].outerHTML = pmpro_original_message;
			}
		});
	});
	</script>
	<?php
}
//add_action( 'pmpro_checkout_boxes', 'my_pmpro_payment_plan_checkout_boxes' );             //アカウント情報の後に出力するならこちら
add_action( 'pmpro_checkout_after_level_cost', 'my_pmpro_payment_plan_checkout_boxes' );    //アカウント情報の前に出力するならこちら

function my_pmpro_automatically_give_payment_plan() {
	global $pmpro_payment_plans, $wpdb;

	// Make sure that there is a level and not already a discount code.
	if ( empty( $_REQUEST['level'] ) || ! empty( $_REQUEST['discount_code'] ) ) {
		return;
	}
	$level_id = $_REQUEST['level'];

	// Group doesn't have a payment plan? return.
	if ( empty( $pmpro_payment_plans ) || ! isset( $pmpro_payment_plans[ $level_id ] ) ) {
		return;
	}

	// Get payment plan.
	$payment_plans = $pmpro_payment_plans[ $level_id ];
	if ( ! is_array( $payment_plans ) ) {
		$payment_plans = array( $payment_plans );
	}

	// Give starting payment plan (aka a discount code).
	if ( ! empty( $payment_plans ) ) {
		$code = $wpdb->get_var( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%d LIMIT 1", $payment_plans[0] ) );
		if ( pmpro_checkDiscountCode( $code, $level_id ) ) {
			$_REQUEST['discount_code'] = $code;
		}
	}
}
add_action( 'init', 'my_pmpro_automatically_give_payment_plan' );
//↑↑↑ここまで

/**
 * ★★残りの日数が現在のレベルであるメンバーのメンバーシップ有効期限を延長する
  *有効期限のある他のレベルのチェックアウトを完了したとき。
  *終了日には常に残りの日数を追加してください。
  *プルイン：https：//gist.github.com/3678054
 */
//↓↓ここから
function my_pmpro_checkout_level_extend_memberships( $level ) {
	global $pmpro_msg, $pmpro_msgt, $current_user;

	//does this level expire? are they an existing members with an expiration date?
	if ( ! empty( $level ) && ! empty( $level->expiration_number ) && pmpro_hasMembershipLevel() && ! empty( $current_user->membership_level->enddate ) ) {
		//get the current enddate of their membership
		$expiration_date = $current_user->membership_level->enddate;

		//calculate days left
		$todays_date = time();
		$time_left = $expiration_date - $todays_date;

		//time left?
		if ( $time_left > 0 ) {
			//convert to days and add to the expiration date (assumes expiration was 1 year)
			$days_left = floor( $time_left/( 60*60*24 ) ) ;

			//figure out days based on period
			if ( $level->expiration_period == 'Day' ) {
				$total_days = $days_left + $level->expiration_number;
			} elseif ( $level->expiration_period == 'Week' ) {
				$total_days = $days_left + $level->expiration_number * 7;
			} elseif ( $level->expiration_period == 'Month' ) { 
				$total_days = $days_left + $level->expiration_number * 30;
			} elseif ( $level->expiration_period == 'Year' ) {
			        $total_days = $days_left + $level->expiration_number * 365;
			}

			//update number and period
			$level->expiration_number = $total_days;
			$level->expiration_period = 'Day';
		}
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_extend_memberships' );
//↑↑↑ここまで

/**
 * 他の定額プランへアップグレード、ダウングレードした時に、更新日までの残数を追加して支払スタート日を遅らせる
 */
add_filter( 'pmprosd_modify_start_date', function($start_date, $order, $subscription_delay){
    
    //現在のプランの残日数を取得する
    $days_left = tks_get_pmpro_day_left();
    //残日数があるなら、スタート日に残日数を加算する事で、試用期限を延長する
    if ( $days_left > 0 ) {
        $start_date = date( 'Y-m-d', strtotime( '+ ' . intval( $days_left ) . ' Days', current_time( 'timestamp' ) ) ) . 'T0:0:0';
    }

    return $start_date;

},10,3 );