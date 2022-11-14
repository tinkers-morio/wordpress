<?php

/**
 * ユーザーの badgeos achievementsを取得する
 *
 * @param  array $args クエリパラメータ配列
 * @return array      achievementオブジェクト|ない場合空配列
 */
function tks_badgeos_get_user_achievements( $args = array() ) {
    
    if (function_exists('badgeos_get_user_achievements')){
        return badgeos_get_user_achievements($args);
    }else{
        return array();
    }
}