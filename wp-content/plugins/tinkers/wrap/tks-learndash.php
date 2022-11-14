<?php

/**
 * その管理者(リーダー)が管理者であるグループIDを全て取得します
 * 
 * @param  int 		$user_id
 * @return array 	list of group ids
 */
function tks_learndash_get_administrators_group_ids( $user_id, $_MENU = false ) {

    if (function_exists('learndash_get_administrators_group_ids')){ 
        return learndash_get_administrators_group_ids( $user_id, $_MENU );
    }else{
        return;
    }

} 

/**
 * グループの登録済み(受講可能な)コースのリストを取得する
 *  
 * @param  int 		$group_id
 * @return array 	list of courses
 */
function tks_learndash_group_enrolled_courses( $group_id = 0, $bypass_transient = false ) {

    if (function_exists('learndash_group_enrolled_courses')){ 
        return learndash_group_enrolled_courses( $group_id, $bypass_transient );
    }else{
        return;
    }
}

/**
 * Activity Query main function
 *
 * ユーザー/コースアクティビティの新しいlearndash_course_user_activityテーブルをクエリします。
 *
 * @param  array $query_args クエリ引数
 * @param  int $current_user_id クエリを実行するユーザー。指定されない場合、現在のユーザーIDを使用します
 *
 * @return array クエリの結果を返す
 */
function tks_learndash_reports_get_activity( $query_args = array(), $current_user_id = 0 ) {
    
    if (function_exists('learndash_reports_get_activity')){
        return learndash_reports_get_activity( $query_args, $current_user_id );
    }else{
        return;
    }

}

/**
 * ユーザーがグループリーダーか否かを返す
 * Replaces is_group_leader
 * @since 2.3
 * 
 * @param  int|object $user 省略された場合カレントユーザー
 * @return bool リーダーの場合true
 */
function tks_learndash_is_group_leader_user( $user = 0 ) {
    
    if (function_exists('learndash_is_group_leader_user')){
        return learndash_is_group_leader_user( $user );
    }else{
        if (is_numeric( $user )){
            if ($user == 0){
                return current_user_can("group_leader");
            }
            return user_can($user,"group_leader");
        }
    }
}

/**
 * LearnDashユーザーを削除する
 *
 * @param int $user_id user id.
 */
function tks_learndash_delete_user_data( $user_id ) {

    if (function_exists('learndash_delete_user_data')){
        return learndash_delete_user_data($user_id);
    }else{
        return;
    }
}

/**
 * コース修了証のダウンロードリンクを取得する
 *
 * @param  int 		 $course_id
 * @param  int 		 $user_id
 * @return string   リンク
 */
function tks_learndash_get_course_certificate_link( $course_id, $cert_user_id = null ) {

    if (function_exists('learndash_get_course_certificate_link')){
        return learndash_get_course_certificate_link( $course_id, $cert_user_id );
    }else{
        return;
    }
}

/**
 * コースをグループに割り当てる(受講登録)
 *
 * @since 2.2.1
 * 
 * @param  int 		$group_id
 * @param  array 	$group_courses_new 割り当て対象となるコースID配列
 * @return none
 */
function tks_learndash_set_group_enrolled_courses( $group_id = 0, $group_courses_new = array() ) {
    
    if (function_exists('learndash_set_group_enrolled_courses')){
        learndash_set_group_enrolled_courses( $group_id, $group_courses_new );
    }
}

/**
 * コースをユーザーに割り当てる(受講登録)
 *
 * @since 2.2.1
 *
 * @param   int   $user_id user id.
 * @param   array $user_courses_new 割り当て対象となるコースID配列
 * @return none
 */
function tks_learndash_user_set_enrolled_courses( $user_id = 0, $user_courses_new = array() ) {

    if (function_exists('learndash_user_set_enrolled_courses')){
        learndash_user_set_enrolled_courses( $user_id, $user_courses_new );
    }

}

/**
 * LearnDashのテーマが3であるかを返す
 */
function tks_is_learndash_theme3(){

    return (LearnDash_Theme_Register::get_active_theme_key() == LEARNDASH_DEFAULT_THEME);
}

/**
 * ページがLearnDashのレッスンかトピックページかを返す
 */
function tks_is_lesson_topic_page(){
	if (is_singular('sfwd-lessons') || is_singular('sfwd-topic')) {	//Ajaxからの呼び出しはタイプが判定できない
		return true;
	}
	return false;
}

/**
 * LearnDashのクイズページかを返す
 */
function tks_is_quiz_page(){
    if (is_singular('sfwd-quiz')) return true;
    return false;
}
/**
 * ページが、ハイレベルミッション用のページか否かを返す
 */
function tks_is_highlevel_mission_lesson_topic_page($post_id = 0){
	//$post_type = get_post_type();
	//if (is_singular('sfwd-lessons') || is_singular('sfwd-topic')) {	//Ajaxからの呼び出しはタイプが判定できない要らない

		if ($post_id == 0){
			$post_id = get_the_ID();
		}
		$course_id = get_post_meta($post_id, 'course_id');

        //if (has_tag('ハイレベル')) {
        if ($course_id && in_array($course_id[0],tks_const::COURCSE_HIGHT_LEVEL)){
		//if ($course_id && $course_id[0] == 13622) {
			return true;
		}

	//}

	return false;
}

/**
 * ページが、しっかり習得コース用のページか否かを返す
 */
function tks_is_shikkari_lesson_topic_page($post_id = 0){

		if ($post_id == 0){
			$post_id = get_the_ID();
		}
		$course_id = get_post_meta($post_id, 'course_id',true);

        if (!empty($course_id)) {
            if ($course_id == tks_const::COURCSE_ID_SHIKKARI){
			    return true;
            }
		}

	return false;
}

/**
 * ハイレベルミッション用のコースページか否かを返す
 */
function tks_is_highlevel_mission_course($post_id = 0){
    if ($post_id == 0){
        $post_id = get_the_ID();
    }
    if (in_array($post_id,tks_const::COURCSE_HIGHT_LEVEL)){
        return true;
    }

    return false;
}

/**
 * レッスンに含まれるトピックのリストを返す
 */
function tks_learndash_get_topic_list($lesson_id = null, $course_id = null){
    if (function_exists('learndash_get_topic_list')){
        return learndash_get_topic_list($lesson_id);
    }
}

/**
 * コースに含まれるレッスンのリストを返す
 */
function tks_learndash_get_lesson_list($course_id = null){
    if (function_exists('learndash_get_lesson_list')){
        return learndash_get_lesson_list($course_id,array( 'num' => 0 ));
    }
}


/**
 * ユーザーがサンプル(試用)グループリーダーか否かを返す
 * 
 * @param  int $user_id 省略された場合カレントユーザー
 * @return bool リーダーの場合true
 */
function tks_is_sample_group_leader( $user_id = 0 ) {
    
    if ($user_id == 0){
        return current_user_can("sample_leader");
    }
    return user_can($user_id,"sample_leader");

}

/**
 * ユーザーが、サンプル(試用)か否かを返す
 * 購読者の場合は、所属するリーダーを調べて試用リーダーの場合もTrueを返す
 * @param  int $user_id 省略された場合カレントユーザー
 * @return bool サンプルユーザーの場合true
 */
function tks_is_sample_user( $user_id = 0 ) {

    if ($user_id == 0){
        $user_id = get_current_user_id();
    }

    //リーダーではない場合は、所属リーダーがsampleか否かを調べる
    if (!tks_learndash_is_group_leader_user($user_id)){
        //生徒の場合は、親であるリーダーの権限を確認
        $leader = tks_get_leader_of_student($user_id);
        $isSample = tks_is_sample_group_leader($leader["user_id"]);
    //リーダーの場合は、リーダーが本人がsampleか否かを調べる
    }else{
        $isSample = tks_is_sample_group_leader($user_id);  //sampleリーダーの権限がある場合はサンプルリーダー   
    }
    return $isSample;
}

/**
 * コースの進捗状況（完了しているか否か）
 * @param int $user_id ユーザーID
 * @param int $course_id コースID
 */
function tks_learndash_course_completed($user_id,$course_id){
    
    if (function_exists('learndash_course_completed')){
        $is_completed = learndash_course_completed( $user_id, $course_id );
    }
    return $is_completed;
}

/**
 * コースの進捗状況を％で返す
 * @param int $user_id ユーザーID
 * @param int $course_id コースID
 */
function tks_get_progress_course($user_id,$course_id){
    
    $step_total = learndash_get_course_steps_count( $course_id );
    $step_completed = learndash_course_get_completed_steps( $user_id, $course_id );
    
    if ($step_completed == 0 || $step_total == 0) {
        $progress = 0;
    }else{
    
        $progress = floor($step_completed / $step_total * 100);
    }

    return $progress;
}
/*
* コースを受講済みにする
*/
function tks_complete_course($user_id=NULL,$course_id){
    //レッスンリストを取得
    $lessons = learndash_get_lesson_list($course_id);
    //完了マークを付けるたびにリダイレクトされちゃかなわんのでnullを送る
    add_filter( 'learndash_completion_redirect', function($link , $post_id){
        return;
    },99,2 );

    foreach($lessons as $lesson){
        //トピックリストを取得
        $topics = learndash_get_topic_list( $lesson->ID );
        //トピック全て完了
        foreach($topics as $topic){
            learndash_process_mark_complete( $user_id, $topic->ID, false, $course_id );  //第三引数は計算のみなので、実際にマークつけん。だがfalseにするとリダレクトなどの処理が入るので上learndash_completion_redirectフィルターでnullを返しておく
        }
        //レッスンを完了
	    learndash_process_mark_complete( $user_id, $lesson->id, false, $course_id );
    }
	
    //learndash_process_mark_complete( $user_id, $course_id );  //コースは要らないっぽい
}

/*個別にレッスンやトピックを受講済みにするならこちら
  //$course_id = 8;	//体験教材コース
	//$lesson_id = 5616;	//体験
	$topics = learndash_get_topic_list( $lesson_id );
	//まずトピック全て完了
	foreach($topics as $topic){
		learndash_process_mark_complete( $user_id, $topic->ID, false, $course_id );
	}
	//最後にレッスンを完了
	learndash_process_mark_complete( $user_id, $lesson_id, false, $course_id );
*/