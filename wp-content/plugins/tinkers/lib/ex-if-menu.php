<?php
/**
 * if Menuの表示非表示条件を追加する
 */

//LearnDashのページか否かを判定
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-learndash',                       				// unique ID for the rule
	'name'      =>  __('IsLearnDash', 'i18n-domain'),                   // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		$url = $_SERVER['REQUEST_URI'];
		return ( is_singular( 'sfwd-courses' ) || is_singular( 'sfwd-lessons' ) || is_singular( 'sfwd-topic' ) || is_singular( 'sfwd-quiz' ) || is_singular( 'sfwd-certificates' ) || is_singular( 'sfwd-assignment' ));
	}
  );

  return $conditions;
});

//LearnDashのLessonページか否かを判定
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-lessons',                       				// unique ID for the rule
	'name'      =>  __('IsLessons', 'i18n-domain'),    // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		$url = $_SERVER['REQUEST_URI'];
		return strstr($url,'/lessons/');
	}
  );

  return $conditions;
});

//faqページかを判定
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-t-faq',                       				// unique ID for the rule
	'name'      =>  __('IsTFAQ', 'i18n-domain'),    // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		$url = $_SERVER['REQUEST_URI'];
		return strstr($url,'/t-faq/');
	}
  );

  return $conditions;
});

//サンプルリーダーではなく、通常のグループリーダーか否か
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-group-leader-not-sample',                       				// unique ID for the rule
	'name'      =>  __('IsGroupLeaderNotSample', 'i18n-domain'),    // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		//$url = $_SERVER['REQUEST_URI'];
		//以下のメソッドは、申込プランのステータスや有効期限は、判定しない(pmsが行うため)
		$userid = get_current_user_id();
		if (user_can($userid,'group_leader')){	
			if (!tks_is_sample_group_leader($userid) ){
				return true;
			}
		}
		return false;
	}
  );

  return $conditions;
});

//有料プランを判定
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'can-plan-manage-student',                       				// unique ID for the rule
	'name'      =>  __('CanPlanManageStudent', 'i18n-domain'),    // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		$url = $_SERVER['REQUEST_URI'];
		//以下のメソッドは、申込プランのステータスや有効期限は、判定しない(pmsが行うため)
		return tks_can_user_plan_mange_student(get_current_user_id() );
	}
  );

  return $conditions;
});

//支払いメンバーに登録されているかを判定
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-payment-member',                       				// unique ID for the rule
	'name'      =>  __('IsPaymentMember', 'i18n-domain'),    // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		$url = $_SERVER['REQUEST_URI'];
		//以下のメソッドは、支払いメンバーであるかを判定
		return tks_is_payment_member(get_current_user_id() );
	}
  );

  return $conditions;
});

//プロフィールに支払い情報を表示するか否か
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-visible-payment-page',                       				// unique ID for the rule
	'name'      =>  __('IsVisiblePaymentPage', 'i18n-domain'),    // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
			$url = $_SERVER['REQUEST_URI'];
			//以下のメソッドは、支払いメンバーであるかを判定
			$option_global = get_option( tks_const::TKSOPT_VISIBLE_PAYMENT_PAGE,'no' );
			$option_user = get_the_author_meta(tks_const::TKSOPT_USER_VISIBLE_PAYMENT_PAGE, get_current_user_id());

			$option_global = (isset($option_global) && $option_global == 'yes')?true:false;
			$option_user = (empty($option_user) || $option_user == 'yes')?true:false;

			//グローバル設定(管理画面：ユーザー)の設定がTrueの場合なら全員強制表示
			if ($option_global) return true;

			if ($option_user){
				return true;
			}else{
				return false;
			}
		}
	);

  return $conditions;
});

//レガシーユーザーかどうか
add_filter('if_menu_conditions', 
function($conditions) {
  $conditions[] = array(
	'id'        =>  'is-notlegacy_user',                       				// unique ID for the rule
	'name'      =>  __('IsNotLegacyUser', 'i18n-domain'),                   // name of the rule
	'condition' =>  function($item) {                  					// callback - must return Boolean
		$userid = get_current_user_id();
		if (in_array($userid,tks_const::ACCOUNT_PAGE_HIDE_INVOICE_SECTION_USER)){
			return false;
		}
		return true;
	}
  );

  return $conditions;
});
