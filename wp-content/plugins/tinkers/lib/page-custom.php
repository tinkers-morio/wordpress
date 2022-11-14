<?php
//フッターへ出力
//すべてのフォームのオートコンプリートをOFFにする
add_action( 'wp_footer', function()  {
	
	?>
	<script type='text/javascript'>
		jQuery('form').attr('autocomplete', 'off');
		jQuery('.wpProQuiz_questionInput').attr('autocomplete', 'off');
	</script>
	<?php
});

