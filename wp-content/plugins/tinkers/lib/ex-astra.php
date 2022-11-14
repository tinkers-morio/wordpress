<?php

/**
 * 親が、membershipページならばメニューに表示されるボタンを表示させない（カスタムボタンを非表示）
 */
add_filter( 'astra_get_dynamic_header_content_final', function($output){
    $disp = true;
    //ログインしてないならボタン非表示
    if (!is_user_logged_in()){
        $disp = false;
    }else{
        global $post;
        //アクセスしようとしているページに親ページがあるなら
        if (!empty($post->post_parent)){
            //親ページのPostIDからスラッグ名を取得
            $post_parent_name = get_post($post->post_parent)->post_name;
            //親ページがmembershipならボタン非表示
            if ($post_parent_name == tks_const::PAGE_MEMBERSHIP_OYA_PAGE){
                $disp = false;
            }
        }else{
            //アクセスしようとしているページ自体がmembershipならボタン非表示
            if (is_page(tks_const::PAGE_MEMBERSHIP_OYA_PAGE)){
                $disp = false;
            }
        }    
    }
    //フラグがoffかつ、カスタムボタンである場合は(カスタムボタンの属性を持っている要素なら)ボタン非表示
    if ((!$disp) &&
        (strpos($output[0], 'ast-custom-button-link')!==false ||
        strpos($output[0], 'ast-button')!==false)){

        $output[0] = "";
    }

	return $output;

},10,1 );

