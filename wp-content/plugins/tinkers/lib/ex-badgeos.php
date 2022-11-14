<?php

/**
 * badgeOSの一覧ページ（固定ページTinkersアイテム）の場合
 * Optionリスト achievement_filterの初期値を設定する
 * ※デフォルトの初期値は、「全てのアイテム」なので、「取得したアイテム」
 * になるよう修正
 * for badgeOS
 */
function tks_badgeOS_achievement_list_filter() {

	//固定ページ(Tinkersアイテム)の場合のみ実行
	if(is_page( tks_const::PAGE_STUDENT_ACHIEVEMENT )){

echo <<< EOM
		<script type='text/javascript'>
		jQuery(document).ready(function() {
			jQuery('#achievements_list_filter').val('completed');
		});
		</script>
EOM;

	}

}
add_action( 'wp_footer', 'tks_badgeOS_achievement_list_filter' );

/**
 * バッジ獲得の際にポップアップウインドウを表示する際、ViewDetailボタンの代わりに
 * 直接バッジのコンテンツを表示するように修正するフィルターフック
 */
add_filter( 'badgeos_congrats_detail_view_link_achievement', function($html){

	preg_match('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)',$html,$ret);
	$url = $ret[0];

	$post_id = url_to_postid($url);
	$post = get_post($post_id);

	return  '<details><summary>せつめいを見る</summary>' . $post->post_content . '</details>';
}, 10, 1 );

