<?php
/**
 * ユーザーがどのレベル(プラン)を持っているかをチェックし、プラン選択画面で表示させないようにする
 * Dynamically display certain levels on the Membership Levels page based on the current user's active level
 * This example allows you to show/hide specific levels on the Membership Levels page.
 */
function dynamic_pmpro_levels_array( $levels ) {	
	// Get all the levels
	$levels = pmpro_getAllLevels( false, true );

	//ZKids用のプランは外しておく
	if (pmpro_hasMembershipLevel( '15' )){
		$levels = tks_hidden_plan_level("c",$levels);
		$levels = tks_hidden_plan_level("f",$levels);
	}else{
		unset( $levels['15'] );
	}
	
	//$test = $_GET['level'];
	$gateway = $_GET['gw'];	//プラン一覧を表示するリンクに付加されているクエリパラメータ

	//パラメータ無の場合
	if (empty($gateway)){
		if (! pmpro_hasMembershipLevel( ) ){
			$gateway = "c";
		}elseif(pmpro_hasMembershipLevel( tks_const::PLAN_IDS_BY_CARD)){
			$gateway = "c";
		}elseif(pmpro_hasMembershipLevel( tks_const::PLAN_IDS_BY_CHECK)){
			$gateway = "f";
		}
	}

	if ($gateway == "c"){
		//カード払い
		$levels = tks_hidden_plan_level("f",$levels);
		if (! pmpro_hasMembershipLevel( ) || pmpro_hasMembershipLevel( '1' )){		
			unset( $levels['3'] );
			unset( $levels['4'] );
			unset( $levels['5'] );
			unset( $levels['6'] );
			unset( $levels['7'] );
			unset( $levels['16'] );
			unset( $levels['17'] );
			unset( $levels['18'] );
			unset( $levels['19'] );
			unset( $levels['20'] );
			unset( $levels['26'] );
			unset( $levels['27'] );
			unset( $levels['28'] );
			unset( $levels['29'] );
			unset( $levels['30'] );
			unset( $levels['31'] );
			unset( $levels['32'] );
			unset( $levels['33'] );
			unset( $levels['34'] );
			unset( $levels['35'] );
		}else{
			unset( $levels['1'] );
		}
	}elseif($gateway == "f"){
		//振込払い
		$levels = tks_hidden_plan_level("c",$levels);
		if (! pmpro_hasMembershipLevel( ) || pmpro_hasMembershipLevel( '8' ) ){
			unset( $levels['10'] );
			unset( $levels['11'] );
			unset( $levels['12'] );
			unset( $levels['13'] );
			unset( $levels['14'] );
			unset( $levels['21'] );
			unset( $levels['22'] );
			unset( $levels['23'] );
			unset( $levels['24'] );
			unset( $levels['25'] );
			unset( $levels['36'] );
			unset( $levels['37'] );
			unset( $levels['38'] );
			unset( $levels['39'] );
			unset( $levels['40'] );
			unset( $levels['41'] );
			unset( $levels['42'] );
			unset( $levels['43'] );
			unset( $levels['44'] );
			unset( $levels['45'] );
		}else{
			unset( $levels['8'] );
		}
	}

	
	return $levels;
}
add_filter( 'pmpro_levels_array', 'dynamic_pmpro_levels_array', 10, 2 );

/**
 * プラン一覧画面のサブファンクション
 * pmpro_levels_arrayフィルターからのみ使用
 * 非表示にするプランを設定し、配列からアンセットする
 * @param plan_by_gateway:非表示にする支払い方法に関連づくプラン種別（'f'：振込プランを非表示 'c':カードプランを非表示)
 */
function tks_hidden_plan_level($plan_by_gateway,$levels){
	
	if ($plan_by_gateway == 'c'){
		unset( $levels['1'] );
		unset( $levels['2'] );
		unset( $levels['3'] );
		unset( $levels['4'] );
		unset( $levels['5'] );
		unset( $levels['6'] );
		unset( $levels['7'] );
		unset( $levels['16'] );
		unset( $levels['17'] );
		unset( $levels['18'] );
		unset( $levels['19'] );
		unset( $levels['20'] );
		unset( $levels['26'] );
		unset( $levels['27'] );
		unset( $levels['28'] );
		unset( $levels['29'] );
		unset( $levels['30'] );
		unset( $levels['31'] );
		unset( $levels['32'] );
		unset( $levels['33'] );
		unset( $levels['34'] );
		unset( $levels['35'] );
		return $levels;
	}

	if ($plan_by_gateway == 'f'){
		unset( $levels['8'] );
		unset( $levels['9'] );
		unset( $levels['10'] );
		unset( $levels['11'] );
		unset( $levels['12'] );
		unset( $levels['13'] );
		unset( $levels['14'] );
		unset( $levels['21'] );
		unset( $levels['22'] );
		unset( $levels['23'] );
		unset( $levels['24'] );
		unset( $levels['25'] );
		unset( $levels['36'] );
		unset( $levels['37'] );
		unset( $levels['38'] );
		unset( $levels['39'] );
		unset( $levels['40'] );
		unset( $levels['41'] );
		unset( $levels['42'] );
		unset( $levels['43'] );
		unset( $levels['44'] );
		unset( $levels['45'] );

		return $levels;
	}
	
	return $levels;
}

/**
 * プラン変更画面の画面左下に表示されるホームに戻るリンクを修正
 * 履歴バックにする
 */
add_action( 'wp_footer', function() {
	
	global $pmpro_pages;
    //チェックアウトページの場合
	if ( is_page( $pmpro_pages['levels'] ) ) {
	?>
	<script >
        (function($){
		
		$("#pmpro_levels-return-home").attr("href", "javascript:history.back();");

    })(jQuery);
	</script>
	<?php
	}
});