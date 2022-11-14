<?php

/**
 * 翻訳がうまく行かない部分をgetTextフィルターで制御する
 */
function my_gettext_string_changes($translated_text, $text, $domain)
{
	// if($domain == "pmproap" && $text == "Purchase this Content (%s)"){
	// 	$translated_text = "Purchase Now for %s";
    // }elseif($domain == "pmproap" && $text == "Choose a Membership Level"){
	//   $translated_text = "Choose a Plan";
    // }

    //LearnDashNoteプラグインの文字修正    
    if ($domain == "sfwd-lms" ){
        if (isset($_GET['user'])){
            $user_id = $_GET['user'];
            $user = get_user_by('id',$user_id);    
            if ($text == $user->user_nicename . '\'s notes'){
                $translated_text =  $user->first_name . ' ' . $user->last_name . 'さん';
            }
        }
    }
    //BadgeOS
    if ($domain == "badgeos"){
        switch ($text){
            case "Last Earned %s":
                $translated_text = "最近手に入れた%s";
                break;
            case "Grid":
                $translated_text = "グリッド";
                break;
            case "List":
                $translated_text = "リスト";
                break;
            default:
                break;
        }
    }
	
	if ($domain == "paid-memberships-pro" ){
		if ($text == "Return to Home"){
			$translated_text = "戻る";
		}
	}

	return $translated_text;
}
add_filter('gettext', 'my_gettext_string_changes', 10, 3);