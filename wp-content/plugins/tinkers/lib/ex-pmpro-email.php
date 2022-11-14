<?php
/**
 * PmProのメール件名本文に含まれる!!xxx!!形式の変数を補完する
 * https://gist.github.com/andrewlimaza/e781af196ff8cf4894be9155a1cde6c5
 */
function my_pmpro_email_data( $data, $email ) {
	global $current_user;

	$first_name = get_user_meta( $current_user->ID, 'first_name', true );
	$last_name = get_user_meta( $current_user->ID, 'last_name', true );
	$school_name = get_user_meta( $current_user->ID, 'tks_school_name', true );
    $company_name = get_user_meta( $current_user->ID, 'company_name', true );
	$bank_info = tks_const_str::AFTER_CHECKOUT_COMFIRM_MSG_PAY_BY_CHECK_BANK_INFO;
	$next_payment = tks_pmpro_payment_date_text();

	$data['first_name'] = ( empty( $first_name ) )?'':$first_name; // display nothing for email ariable !!first_name!! if empty.
	$data['last_name'] = ( empty( $last_name ) )?'':$last_name; // display nothing for email ariable !!last_name!! if empty.
	$data['school_name'] = ( empty( $school_name ) )?'':$school_name; // display nothing for email ariable !!first_name!! if empty.
	$data['company_name'] = ( empty( $company_name ) )?'':$company_name;
    if ($data['company_name'] == '個人'){
        $data['company_name'] = '';
    }
	$data['bank_info'] = $bank_info;
	$data['next_payment'] = $next_payment;

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_email_data', 10, 2 );