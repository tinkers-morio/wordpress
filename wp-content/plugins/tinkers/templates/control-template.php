<?php
/**
 * チェックアウトページのテンプレート化
 * paid-memberships-pro\pages\checkout.phpフォルダのテンプレート
 */
function tks_pmpro_pages_shortcode_checkout($content)
{

	ob_start();
	include(TKS_PLUGIN_DIR . "templates/tks-pmpro-template-checkout.php");
	$temp_content = ob_get_contents();
	ob_end_clean();

	return $temp_content;
}
add_filter("pmpro_pages_shortcode_checkout", "tks_pmpro_pages_shortcode_checkout");

/**
 * プランの選択画面をテンプレート化
 * paid-memberships-pro\pages\levels.phpフォルダのテンプレート
 */
function tks_pmpro_pages_shortcode_levels($content)
{

	ob_start();
	include(TKS_PLUGIN_DIR . "templates/tks-pmpro-template-levels.php");
	$temp_content = ob_get_contents();
	ob_end_clean();

	return $temp_content;
}
add_filter("pmpro_pages_shortcode_levels", "tks_pmpro_pages_shortcode_levels");

/**
 * アカウント画面をテンプレート化
 * paid-memberships-pro\shortcodes\pmpro_account.phpのテンプレート
 */
function tks_pmpro_pages_shortcode_account($content)
{
	$template = "templates/tks-pmpro-template-account.php";
	//レガシーユーザーの場合は、テンプレートを切り替える
	if (in_array(get_current_user_id(),tks_const::ACCOUNT_PAGE_HIDE_INVOICE_SECTION_USER,true)){
		$template = "templates/tks-pmpro-template-account-legacy.php";
	}
	ob_start();
	include(TKS_PLUGIN_DIR . $template);
	$temp_content = ob_get_contents();
	ob_end_clean();

	return $temp_content;
}
add_filter("pmpro_pages_shortcode_account", "tks_pmpro_pages_shortcode_account");