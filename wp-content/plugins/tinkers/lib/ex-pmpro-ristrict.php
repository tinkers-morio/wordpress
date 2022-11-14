<?php
/**
 * ユーザーがログアウトしているか、メンバーでない場合、メンバーのみのコンテンツを[メンバーシップレベル]ページにリダイレクトします。
 * https://www.paidmembershipspro.com/redirect-non-members-away-from-member-content/
 */
add_action( 'template_redirect', 
function() {

    //ログインしてないなら抜ける（ProfileBuilderの機能制限に任せる）
    if (!is_user_logged_in()){
        return;
    }
    //リクエストURLが、ルートのURLだった場合は、リーダーと生徒によってスタートページを分岐する
    //$current_url = (is_ssl() ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $current_url = rtrim((is_ssl() ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"],"/");      //URLの末尾のスラッシュを除去
    $homeurl = rtrim(home_url(),"/");                                                                                       //URLの末尾のスラッシュを除去
    //URLを比較                                                                                       
    if (strtolower($homeurl) == strtolower($current_url)){
        //管理者の場合はグループリーダー一覧
        if (current_user_can('administrator')){
            $redirect_url = tks_get_home_url(tks_const::PAGE_LEADER_LIST_FOR_ADMIN);
            wp_safe_redirect( $redirect_url );
            exit;
        }
        //生徒の場合は、コースページへ遷移
        if (current_user_can('subscriber')){
            $redirect_url = tks_get_home_url(tks_const::PAGE_STUDENT_MY_PAGE);
            wp_safe_redirect( $redirect_url );
            exit;
        }
        //リーダーの場合は、スタートページへ遷移
        if (current_user_can('group_leader')){
            if (tks_is_sample_group_leader() ){
                $redirect_url = tks_get_home_url(tks_const::PAGE_START_SAMPLE_LEADER);
                wp_safe_redirect( $redirect_url );
                exit;
            }else{
                $redirect_url = tks_get_home_url(tks_const::PAGE_START_LEADER);
                wp_safe_redirect( $redirect_url );
                exit;
            }
        }
    }

    //管理者の場合はアクセス無制限
    if (current_user_can('administrator')){
        return;
    }

	$user_id = get_current_user_id();
	
    //制限をバイパスできるユーザーか否か
    if (in_array($user_id, tks_const::RISTRICT_BYPASS_USER, true)){
        return;
    }
	//生徒の場合は、リーダーを見る
	if (user_can($user_id,'subscriber')){
		$leader_id = (int)tks_get_leader_of_student($user_id)["user_id"];
		if (in_array($leader_id, tks_const::RISTRICT_BYPASS_USER, true)) return;
	}

	global $post;
	//投稿や固定ページにPaidMembershipの制限設定をしている場合はこちらを使って判定
    //$plan_access_info =  pmpro_has_membership_access($post->ID,$user_id,true);
    
    //生徒登録ページの場合
    if (is_page(tks_const::PAGE_REGIST_STUDENT)){
        //生徒をこれ以上登録できるか否かをチェック（登録できなければエラー）
        if (!tks_can_add_student($user_id)){
            //wp_redirect( pmpro_url( 'levels' ) );
            $redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
            wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_MAX_STUDENT, $redirect_url ) );
            exit;

        }
    }
    
    //LearnDashレッスン・トピック、クイズページの場合
    if (tks_is_lesson_topic_page() || tks_is_quiz_page()){
        //リーダーではない場合は、所属リーダーがsampleか否かを調べる
        if (!tks_learndash_is_group_leader_user($user_id)){
            //生徒の場合は、親であるリーダーの権限を確認
            $user_id = (int)tks_get_leader_of_student($user_id)["user_id"];
        }
        //サンプルリーダーか否かの確認
        $isSample = tks_is_sample_group_leader($user_id);
        //サンプルリーダーの場合
        if ($isSample){
            //アクセス可能なレッスン・トピックであるか？（配列に存在するPostIDのみアクセス可能とする）
            $result = in_array($post->ID, tks_const::SAMPLE_USER_CAN_ACCESS_POST);
            if ($result === false){
                $redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
                wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_FOR_SAMPLE, $redirect_url ) );
                exit;
            }
        }
        else
        //申込プランによって閲覧できるレッスン、トピック、クイズかを調べる
        {
			//メンバーの最後のオーダー情報を取得する
			$pmpro_invoice = new MemberOrder();
			//$pmpro_invoice->getLastMemberOrder(NULL, array('success', 'pending', 'cancelled', ''));
			$pmpro_invoice->getLastMemberOrder($user_id,null); //第二引数にはNullを設定しとかないとactiveのステータスしか取得できない
			if(!empty($pmpro_invoice) && !empty($pmpro_invoice->id)){
				//pendingの場合は、入金済みか否かのメッセージを表示
				if($pmpro_invoice->status == "pending" && $pmpro_invoice->gateway == "check"){
					$redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
					wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_PENDING, $redirect_url ) );
					exit;
				}
				//成功以外のステータスは、なんらかの異常ステータス（PaidMembershipの設定画面、会員ページを見よ）
				if ($pmpro_invoice->status != "success"){
					$redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
					wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_PENDING, $redirect_url ) );
					exit;
				}
			}
			
            //申込中のプランを取得
            $plans = tks_pmpro_get_member($user_id);

			//プランを持っていて、且つ、ステータスが、成功の場合のオーダーが最終オーダーの場合のみアクセス可能
            if (!empty($plans) && ($pmpro_invoice->status == "success") ){
                
				$course_id = learndash_get_course_id();
				//リーダー、生徒が受講可能なコースを判定(プランに基づくコースとユーザー毎に設定された追加コースを判定)
				//if (! in_array( $course_id, tks_get_course_by_plan($plans->ID))){
				if (! in_array( $course_id, tks_get_course_by_plan($plans->ID)) && ! in_array($course_id,tks_get_extra_course_by_user($user_id))){
					$redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
					wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_FOR_NO_ATTEND, $redirect_url ) );
					exit;
				}

            }else{
                $redirect_url = tks_get_home_url(tks_const::PAGE_RESTRICT_DEF);
                wp_safe_redirect( add_query_arg( 'restrict', tks_const::ERR_RESTRICT_FOR_NOPLAN, $redirect_url ) );
                exit;
            }
        }
    }
});
