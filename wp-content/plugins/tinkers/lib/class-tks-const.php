<?php

class tks_const{
    /**
     * 動作モード(オプション設定)
     */
    const SYSTEM_MODE_VALUE_HOUJIN = 0;
    const SYSTEM_MODE_VALUE_KOJIN = 1;
    const SYSTEM_MODE = self::SYSTEM_MODE_VALUE_HOUJIN;                              //モード1の場合は、個人モード、0の場合は法人モード(ここで変更)
    const SYSTEM_MANAGER_NAME = 'プログラミングスクール・ティンカーズ事務局';         //サイト内で共通で使用される管理者名（ユーザーに表示される）
    const SYSTEM_MANAGER_MAIL = "support@tinkers.online";                            //サイト内で共通で試用される管理者Email（ユーザーに表示される）
    const SYSTEM_MANAGER_TEL = '03-5708-5013';                                       //サイト内で共通で使用される管理者電話番号（ユーザーに表示される）
    const SYSTEM_PASWDCHECK_STRONG = false;                                           //PmProでユーザーチェックアウト時(登録時)におけるパスワードのチェックを強化する
    const SYSTEM_PASWDCHECK_LENGTH = 6;                                               //パスワードで許可する桁数(ProfileBuilderの設定もなおさないとダメ)
    /**
     * 生徒一人当たりの単価
     */
    const PRICE_STUDENT_PER_PERSON = 1200;          
    
    /**
     * カード支払いを設定してあるプランID一覧
     */
    const PLAN_IDS_BY_CARD = array(
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        16,
        17,
        18,
        19,
        20,
        26,
        27,
        28,
        29,
        30,
        31,
        32,
        33,
        34,
        35
    );
    /**
     * 振込払いを設定してあるプランID一覧
     */
    const PLAN_IDS_BY_CHECK = array(
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        21,
        22,
        23,
        24,
        25,
        36,
        37,
        38,
        39,
        40,
        41,
        42,
        43,
        44,
        45
    );

    /**
     * 管理者によるプランの登録（管理者によるユーザー[リーダー]作成時、紐づけるプランID
     */
    const PLAN_ID_FOR_ADD_LEADER_BY_ADMIN = 1;  //振込払い、応援プラン

    /**
     * アカウントページPmProにて、「ご請求情報を表示させないユーザー一覧」
     * 
     */
    const ACCOUNT_PAGE_HIDE_INVOICE_SECTION_USER = array(
        7,      //Tinkers
        49,     //Sampleユーザ
        95,     //ZKidsさん
        161     //ラムスさん
    );
    /**
     * オンライン授業の（マンツーマン）
     * のZoomID
     */
    const TKSOPT_ONLINE_MAN_TO_MAN_ZOOM_ID = "862075528";
    
    /**
     * 管理画面設定DBフィールド名
     */
    const TKSOPT_VISIBLE_PAYMENT_PAGE = 'tksopt_visible_payment_page';                  //支払ページをプロフィールに表示させるか否か
    const TKSOPT_USER_VISIBLE_PAYMENT_PAGE = 'tksopt_user_visible_payment_page';        //支払ページをプロフィールに表示させるか否か(ユーザー毎の設定)
    const TKSOPT_VIDO_AT_END_MESSAGE_TITLE = 'tks_video_at_end_message_title';          //動画視聴完了後に表示させるメッセージタイトル
    const TKSOPT_VIDO_AT_END_MESSAGE = 'tks_video_at_end_message';                      //動画視聴完了後に表示させるメッセージタイトル
    const TKSOPT_PLAN_BASIC = 'tksopt_plan_basic';                                      //basicプランに紐づけるプランID
    const TKSOPT_PLAN_REGULAR = 'tksopt_plan_regular';                                  //regularプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE1 = 'tksopt_plan_advance1';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE2 = 'tksopt_plan_advance2';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE3 = 'tksopt_plan_advance3';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE4 = 'tksopt_plan_advance4';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE5 = 'tksopt_plan_advance5';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE6 = 'tksopt_plan_advance6';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE7 = 'tksopt_plan_advance7';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE8 = 'tksopt_plan_advance8';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE9 = 'tksopt_plan_advance9';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE10 = 'tksopt_plan_advance10';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE11 = 'tksopt_plan_advance11';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE12 = 'tksopt_plan_advance12';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE13 = 'tksopt_plan_advance13';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE14 = 'tksopt_plan_advance14';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE15 = 'tksopt_plan_advance15';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE16 = 'tksopt_plan_advance16';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE17 = 'tksopt_plan_advance17';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE18 = 'tksopt_plan_advance18';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE19 = 'tksopt_plan_advance19';                                  //advanceプランに紐づけるプランID
    const TKSOPT_PLAN_ADVANCE20 = 'tksopt_plan_advance20';                                  //advanceプランに紐づけるプランID
    //プランIDを一括取得するための配列
    const TKSOPT_PLAN_ARRAY = array(
        1 => tks_const::TKSOPT_PLAN_BASIC,                                      //basicプランに紐づけるプランID
        2 => tks_const::TKSOPT_PLAN_REGULAR,                                  //regularプランに紐づけるプランID
        3 => tks_const::TKSOPT_PLAN_ADVANCE1,                                  //advanceプランに紐づけるプランID
        4 => tks_const::TKSOPT_PLAN_ADVANCE2,                                  //advanceプランに紐づけるプランID
        5 => tks_const::TKSOPT_PLAN_ADVANCE3,                                  //advanceプランに紐づけるプランID
        6 => tks_const::TKSOPT_PLAN_ADVANCE4,                                  //advanceプランに紐づけるプランID
        7 => tks_const::TKSOPT_PLAN_ADVANCE5,                                  //advanceプランに紐づけるプランID
        8 => tks_const::TKSOPT_PLAN_ADVANCE6,                                  //advanceプランに紐づけるプランID
        9 => tks_const::TKSOPT_PLAN_ADVANCE7,                                  //advanceプランに紐づけるプランID
        10 => tks_const::TKSOPT_PLAN_ADVANCE8,                                  //advanceプランに紐づけるプランID
        11 => tks_const::TKSOPT_PLAN_ADVANCE9,                                  //advanceプランに紐づけるプランID
        12 => tks_const::TKSOPT_PLAN_ADVANCE10,                                  //advanceプランに紐づけるプランID
        13 => tks_const::TKSOPT_PLAN_ADVANCE11,                                  //advanceプランに紐づけるプランID
        14 => tks_const::TKSOPT_PLAN_ADVANCE12,                                  //advanceプランに紐づけるプランID
        15 => tks_const::TKSOPT_PLAN_ADVANCE13,                                  //advanceプランに紐づけるプランID
        16 => tks_const::TKSOPT_PLAN_ADVANCE14,                                  //advanceプランに紐づけるプランID
        17 => tks_const::TKSOPT_PLAN_ADVANCE15,                                  //advanceプランに紐づけるプランID
        18 => tks_const::TKSOPT_PLAN_ADVANCE16,                                  //advanceプランに紐づけるプランID
        19 => tks_const::TKSOPT_PLAN_ADVANCE17,                                  //advanceプランに紐づけるプランID
        20 => tks_const::TKSOPT_PLAN_ADVANCE18,                                  //advanceプランに紐づけるプランID
        21 => tks_const::TKSOPT_PLAN_ADVANCE19,                                  //advanceプランに紐づけるプランID
        22 => tks_const::TKSOPT_PLAN_ADVANCE20                                  //advanceプランに紐づけるプランID
    );

    const TKSOPT_MAX_STUDENT_COUNT = 'tks_user_max_student';                            //user_meta（管理画面ユーザー編集から設定）生徒登録可能人数（プランに縛られない）保存値が0の場合は、プランの人数が優先される、0以上の場合はプランの人数よりもこちらが優先される
    const TKSOPT_EXTRA_APPEND_COURSE = 'tks_extra_append_course';                       //user_meta（管理画面ユーザー編集から設定）プランに縛られない追加コース、プランに設定されたコース以外で、受講可能なコースをユーザーごとに設定できる(カンマ区切りで設定)            
    const TKSOPT_PLAN_BASIC_STUDENTS_COUNT = 'tksopt_plan_basic_student_count';         //生徒登録可能人数(Basicプラン)
    const TKSOPT_PLAN_REGULAR_STUDENTS_COUNT = 'tksopt_plan_regular_student_count';     //生徒登録可能人数(regularプラン)
    const TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT = 'tksopt_plan_advance1_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT = 'tksopt_plan_advance2_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT = 'tksopt_plan_advance3_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT = 'tksopt_plan_advance4_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT = 'tksopt_plan_advance5_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE6_STUDENTS_COUNT = 'tksopt_plan_advance6_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE7_STUDENTS_COUNT = 'tksopt_plan_advance7_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE8_STUDENTS_COUNT = 'tksopt_plan_advance8_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE9_STUDENTS_COUNT = 'tksopt_plan_advance9_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE10_STUDENTS_COUNT = 'tksopt_plan_advance10_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE11_STUDENTS_COUNT = 'tksopt_plan_advance11_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE12_STUDENTS_COUNT = 'tksopt_plan_advance12_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE13_STUDENTS_COUNT = 'tksopt_plan_advance13_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE14_STUDENTS_COUNT = 'tksopt_plan_advance14_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE15_STUDENTS_COUNT = 'tksopt_plan_advance15_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE16_STUDENTS_COUNT = 'tksopt_plan_advance16_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE17_STUDENTS_COUNT = 'tksopt_plan_advance17_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE18_STUDENTS_COUNT = 'tksopt_plan_advance18_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE19_STUDENTS_COUNT = 'tksopt_plan_advance19_student_count';     //生徒登録可能人数(advanceプラン)
    const TKSOPT_PLAN_ADVANCE20_STUDENTS_COUNT = 'tksopt_plan_advance20_student_count';     //生徒登録可能人数(advanceプラン)

    const TKSOPT_PLAN_BASIC_STUDENTS_COUNT_DEF = 5;                                     //生徒登録可能人数(Basicプラン)初期値
    const TKSOPT_PLAN_REGULAR_STUDENTS_COUNT_DEF = 10;                                  //生徒登録可能人数(regularプラン)初期値
    const TKSOPT_PLAN_ADVANCE1_STUDENTS_COUNT_DEF = 11;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE2_STUDENTS_COUNT_DEF = 12;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE3_STUDENTS_COUNT_DEF = 13;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE4_STUDENTS_COUNT_DEF = 14;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE5_STUDENTS_COUNT_DEF = 15;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE6_STUDENTS_COUNT_DEF = 16;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE7_STUDENTS_COUNT_DEF = 17;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE8_STUDENTS_COUNT_DEF = 18;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE9_STUDENTS_COUNT_DEF = 19;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE10_STUDENTS_COUNT_DEF = 20;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE11_STUDENTS_COUNT_DEF = 21;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE12_STUDENTS_COUNT_DEF = 22;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE13_STUDENTS_COUNT_DEF = 23;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE14_STUDENTS_COUNT_DEF = 24;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE15_STUDENTS_COUNT_DEF = 25;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE16_STUDENTS_COUNT_DEF = 26;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE17_STUDENTS_COUNT_DEF = 27;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE18_STUDENTS_COUNT_DEF = 28;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE19_STUDENTS_COUNT_DEF = 29;                                  //生徒登録可能人数(advanceプラン)初期値 
    const TKSOPT_PLAN_ADVANCE20_STUDENTS_COUNT_DEF = 30;                                  //生徒登録可能人数(advanceプラン)初期値 
    
    const TKSOPT_PLAN_BASIC_COURSE_STUDENT = 'tksopt_plan_basic_course_student';        //basicプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_REGULAR_COURSE_STUDENT = 'tksopt_plan_regular_course_student';    //regularプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE1_COURSE_STUDENT = 'tksopt_plan_advance1_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE2_COURSE_STUDENT = 'tksopt_plan_advance2_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE3_COURSE_STUDENT = 'tksopt_plan_advance3_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE4_COURSE_STUDENT = 'tksopt_plan_advance4_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE5_COURSE_STUDENT = 'tksopt_plan_advance5_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE6_COURSE_STUDENT = 'tksopt_plan_advance6_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE7_COURSE_STUDENT = 'tksopt_plan_advance7_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE8_COURSE_STUDENT = 'tksopt_plan_advance8_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE9_COURSE_STUDENT = 'tksopt_plan_advance9_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE10_COURSE_STUDENT = 'tksopt_plan_advance10_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE11_COURSE_STUDENT = 'tksopt_plan_advance11_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE12_COURSE_STUDENT = 'tksopt_plan_advance12_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE13_COURSE_STUDENT = 'tksopt_plan_advance13_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE14_COURSE_STUDENT = 'tksopt_plan_advance14_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE15_COURSE_STUDENT = 'tksopt_plan_advance15_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE16_COURSE_STUDENT = 'tksopt_plan_advance16_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE17_COURSE_STUDENT = 'tksopt_plan_advance17_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE18_COURSE_STUDENT = 'tksopt_plan_advance18_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE19_COURSE_STUDENT = 'tksopt_plan_advance19_course_student';    //advanceプランに紐づけるコースID(生徒)
    const TKSOPT_PLAN_ADVANCE20_COURSE_STUDENT = 'tksopt_plan_advance20_course_student';    //advanceプランに紐づけるコースID(生徒)

    const TKSOPT_PLAN_BASIC_COURSE = 'tksopt_plan_basic_course';                        //basicプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_REGULAR_COURSE = 'tksopt_plan_regular_course';                    //regularプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE1_COURSE = 'tksopt_plan_advance1_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE2_COURSE = 'tksopt_plan_advance2_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE3_COURSE = 'tksopt_plan_advance3_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE4_COURSE = 'tksopt_plan_advance4_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE5_COURSE = 'tksopt_plan_advance5_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE6_COURSE = 'tksopt_plan_advance6_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE7_COURSE = 'tksopt_plan_advance7_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE8_COURSE = 'tksopt_plan_advance8_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE9_COURSE = 'tksopt_plan_advance9_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE10_COURSE = 'tksopt_plan_advance10_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE11_COURSE = 'tksopt_plan_advance11_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE12_COURSE = 'tksopt_plan_advance12_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE13_COURSE = 'tksopt_plan_advance13_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE14_COURSE = 'tksopt_plan_advance14_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE15_COURSE = 'tksopt_plan_advance15_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE16_COURSE = 'tksopt_plan_advance16_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE17_COURSE = 'tksopt_plan_advance17_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE18_COURSE = 'tksopt_plan_advance18_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE19_COURSE = 'tksopt_plan_advance19_course';                    //advanceプランに紐づけるコースID（リーダー）
    const TKSOPT_PLAN_ADVANCE20_COURSE = 'tksopt_plan_advance20_course';                    //advanceプランに紐づけるコースID（リーダー）

    const TKSOPT_PLAN_BASIC_OVERVIEW = 'tksopt_plan_basic_overview';                        //basicプランの説明文
    const TKSOPT_PLAN_REGULAR_OVERVIEW = 'tksopt_plan_regular_overview';                    //regularプランの説明文
    const TKSOPT_PLAN_ADVANCE1_OVERVIEW = 'tksopt_plan_advance1_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE2_OVERVIEW = 'tksopt_plan_advance2_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE3_OVERVIEW = 'tksopt_plan_advance3_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE4_OVERVIEW = 'tksopt_plan_advance4_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE5_OVERVIEW = 'tksopt_plan_advance5_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE6_OVERVIEW = 'tksopt_plan_advance6_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE7_OVERVIEW = 'tksopt_plan_advance7_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE8_OVERVIEW = 'tksopt_plan_advance8_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE9_OVERVIEW = 'tksopt_plan_advance9_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE10_OVERVIEW = 'tksopt_plan_advance10_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE11_OVERVIEW = 'tksopt_plan_advance11_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE12_OVERVIEW = 'tksopt_plan_advance12_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE13_OVERVIEW = 'tksopt_plan_advance13_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE14_OVERVIEW = 'tksopt_plan_advance14_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE15_OVERVIEW = 'tksopt_plan_advance15_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE16_OVERVIEW = 'tksopt_plan_advance16_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE17_OVERVIEW = 'tksopt_plan_advance17_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE18_OVERVIEW = 'tksopt_plan_advance18_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE19_OVERVIEW = 'tksopt_plan_advance19_overview';                  //advanceプランの説明文
    const TKSOPT_PLAN_ADVANCE20_OVERVIEW = 'tksopt_plan_advance20_overview';                  //advanceプランの説明文

    //概要文を一括取得するための配列
    const TKSOPT_PLAN_OVERVIEW_ARRAY = array(
        1 => tks_const::TKSOPT_PLAN_BASIC_OVERVIEW,                                      //basicプランの説明文
        2 => tks_const::TKSOPT_PLAN_REGULAR_OVERVIEW,                                  //regularプランの説明文
        3 => tks_const::TKSOPT_PLAN_ADVANCE1_OVERVIEW,                                  //advanceプランの説明文
        4 => tks_const::TKSOPT_PLAN_ADVANCE2_OVERVIEW,                                  //advanceプランの説明文
        5 => tks_const::TKSOPT_PLAN_ADVANCE3_OVERVIEW,                                  //advanceプランの説明文
        6 => tks_const::TKSOPT_PLAN_ADVANCE4_OVERVIEW,                                  //advanceプランの説明文
        7 => tks_const::TKSOPT_PLAN_ADVANCE5_OVERVIEW,                                 //advanceプランの説明文
        8 => tks_const::TKSOPT_PLAN_ADVANCE6_OVERVIEW,                                  //advanceプランの説明文
        9 => tks_const::TKSOPT_PLAN_ADVANCE7_OVERVIEW,                                  //advanceプランの説明文
        10 => tks_const::TKSOPT_PLAN_ADVANCE8_OVERVIEW,                                  //advanceプランの説明文
        11 => tks_const::TKSOPT_PLAN_ADVANCE9_OVERVIEW,                                  //advanceプランの説明文
        12 => tks_const::TKSOPT_PLAN_ADVANCE10_OVERVIEW,                                  //advanceプランの説明文
        13 => tks_const::TKSOPT_PLAN_ADVANCE11_OVERVIEW,                                  //advanceプランの説明文
        14 => tks_const::TKSOPT_PLAN_ADVANCE12_OVERVIEW,                                  //advanceプランの説明文
        15 => tks_const::TKSOPT_PLAN_ADVANCE13_OVERVIEW,                                  //advanceプランの説明文
        16 => tks_const::TKSOPT_PLAN_ADVANCE14_OVERVIEW,                                  //advanceプランの説明文
        17 => tks_const::TKSOPT_PLAN_ADVANCE15_OVERVIEW,                                  //advanceプランの説明文
        18 => tks_const::TKSOPT_PLAN_ADVANCE16_OVERVIEW,                                  //advanceプランの説明文
        19 => tks_const::TKSOPT_PLAN_ADVANCE17_OVERVIEW,                                  //advanceプランの説明文
        20 => tks_const::TKSOPT_PLAN_ADVANCE18_OVERVIEW,                                  //advanceプランの説明文
        21 => tks_const::TKSOPT_PLAN_ADVANCE19_OVERVIEW,                                  //advanceプランの説明文
        22 => tks_const::TKSOPT_PLAN_ADVANCE20_OVERVIEW                                  //advanceプランの説明文
    );
    
    const TKSOPT_MAIL_SUBJECT = 'tksopt_mail_subject';                                  //請求メールサブジェクト
    const TKSOPT_MAIL_BODY = 'tksopt_mail_body';                                        //請求メール本文

    const TKSOPT_MTS_YOYAKU_IDS = 'tksopt_mts_yoyaku_ids';                              //予約IDのカンマ区切りデータ
    const TKSOPT_MTS_YOYAKU_ONETIME = 'tksopt_mts_yoyaku_onetime';                      //予約が一度しかとれない種類の予約か否かのカンマ区切りのデータ
    const TKSOPT_MTS_YOYAKU_DISABLE = 'tksopt_mts_yoyaku_disable';                      //予約を使用不可にするか否かのカンマ区切りのデータ（Yesが使用不可にする）
    const TKSOPT_MTS_YOYAKU_ZOOMIDS = 'tksopt_mts_yoyaku_zoom_ids';                      //予約に紐づけるZoomID
    const TKSOPT_MTS_YOYAKU_ZOOMLINK = 'tksopt_mts_yoyaku_zoom_link';                    //ZoomリンクURL
    /**コースID */
    const COURCSE_ID_SHIKKARI = 8;                                                      //しっかり習得コース
    const COURCSE_ID_TAIKEN = 10185;                                                    //体験教室
    const COURCSE_ID_GAME = 10962;                                                      //ゲームコース
    const COURCSE_ID_WAZA = 29994;                                                      //技集
    const COURCSE_ID_HIGHT_LV_1 = 13622;                                                //hagitlevel lv1
    const COURCSE_ID_HIGHT_LV_2 = 22140;                                                //hagitlevel lv2
    const COURCSE_ID_HIGHT_LV_3 = 22144;                                                //hagitlevel lv3

    const COURCSE_HIGHT_LEVEL = array(                                                  //ハイレベルコースID配列(コース識別に使用)
        13622,
        22140,
        22144
    );

    /**
     * ユーザー設定
     */
    const TKSOPT_TKS_TAIKEN_COMPLETE = 'tks_taiken_complete';                           //体験コースを完了済みにするか否か(後ほど変更不可)
    const TKSOPT_SHOW_EXTRA_TAB_HILEVEL = "tks_option_show_answer_hilevel";             //subscriberであっても拡張タブを表示するか否か(ユーザープロフィールで個別に設定する)ハイレベル
    const TKSOPT_SHOW_EXTRA_TAB_SHIKKARI = "tks_option_show_answer_shikkari";           //subscriberであっても拡張タブを表示するか否か(ユーザープロフィールで個別に設定する)しっかり
    const TKSOPT_SHOW_EXTRA_TAB_VAL_NO = false;                                         //表示しない(デフォルト)
    const TKSOPT_SHOW_EXTRA_TAB_VAL_YES = true;                                         //表示する

    /*
    * ページ
    */
    const PAGE_MEMBERSHIP_OYA_PAGE = "membership";
    //新規顧客登録
    const PAGE_NEW_REGIST_PB = 'new-regist-pb'; //profileBuilder(未使用)
    const PAGE_NEW_REGIST = 'new-regist';       //支払い登録画面(PmPro)
    
    //リーダー編集(管理者用)
    const PAGE_EDIT_LEADER_FOR_ADMIN = 'a-edit-leader';
    //リーダー登録(管理者用)
    const PAGE_REGIST_LEADER_FOR_ADMIN = 'a-regist-leader';
    //リーダー編集
    const PAGE_EDIT_LEADER = 't-account';
    //リーダースタートページ
    const PAGE_START_LEADER = 't-start';
    //サンプルリーダースタートページ
    const PAGE_START_SAMPLE_LEADER = 'sample-start';
    //生徒一覧
    const PAGE_LIST_STUDENT ='t-user-list';
    //生徒編集
    const PAGE_EDIT_STUDENT = 's-account';
    //生徒登録
    const PAGE_REGIST_STUDENT = 's-regist';
    //生徒マイページ
    const PAGE_STUDENT_MY_PAGE = 'my-page';
    //生徒編集(リーダー用)
    const PAGE_EDIT_STUDENT_FOR_LEADER = 's-account-edit-leader';
    //教室情報編集
    const PAGE_REGIST_SCHOOL = 'regist-school';     //はじめての生徒登録
    const PAGE_REGIST_SCHOOL2 = 'regist-school2';   //はじめてのプロフィール編集
    //バッジページ
    const PAGE_STUDENT_ACHIEVEMENT = 'my-achievement';
    //ログインページ
    const PAGE_LOGIN = 'login';
    //コース一覧
    const PAGE_COURSES = 'enroll-courses';
    //生徒一覧
    const PAGE_STUDENT_LIST_FOR_LEADER = 't-user-list';
    //生徒進捗一覧
    const PAGE_STUDENT_LIST_PROGRESS = 't-user-list-progress';
    //リーダー一覧
    const PAGE_LEADER_LIST_FOR_ADMIN = 'a-group-list';
    //機能制限メッセージ(プランに申し込んでいない)
    const PAGE_RESTRICT_DEF = 'restrict';
    //お申込み情報ページ
    const PAGE_ACCOUNT_PLAN_INFO = tks_const::PAGE_MEMBERSHIP_OYA_PAGE . '/plans';
    //メンバーシップ（PaidMemberShipPro画面）
    const PAGE_MEMBERSHIP_PLAN = "plan";
    //メンバーシップ（PaidMemberShipProプラン選択画面）
    const PAGE_MEMBERSHIP_PLANS = "plans";
    //メンバーシップ（PaidMemberShipPro画面請求書）
    const PAGE_MEMBERSHIP_INVOICE = "invoice";
    //支払い後の確認ページ
    const PAGE_MEMBERSHIP_CONFIRM_DETAILS = "confirm-details";
    //バッジ一覧のページ
    const PAGE_MY_ACHIEVEMENT = "my-achievement";
    
    /**
     * 機能制限のエラーコード
     */
    const ERR_RESTRICT_NO_PLAN = 100;
    const ERR_RESTRICT_EXPIRE = 200;
    const ERR_RESTRICT_PENDING = 300;
    const ERR_RESTRICT_MAX_STUDENT = 400;
    const ERR_RESTRICT_FOR_SAMPLE = 500;
    const ERR_RESTRICT_FOR_NOPLAN = 600;
    const ERR_RESTRICT_FOR_NO_ATTEND = 700;
    
    /*
    * 生徒登録用共通メールアドレス
    */
    const EMAIL_FOR_STUDENT = 'default@tinkers.jp';

    /**
     * 修了証に表示するロゴのデフォルトURL
     */
    const URL_CIRTIFICATE_LOGO = '/wp-content/uploads/2018/07/LMS_LOGO-e1530931550871.png';

    /**
     * LearnDashレッスン・トピック用拡張タブ数
     */
    const EXTRA_TAB_MAX_COUNT = 4;
    
    /**
     * 閲覧チェックtemplate_redirectで行うチェックを回避するユーザー
     */
    const RISTRICT_BYPASS_USER = array(
        7,   //TinkersSchoolのユーザーID
       95    //ZKids様
    );

    /**
     * カスタムフィールド(meta)
     */

     /**
      * sampleユーザーがアクセスできるレッスン・トピックページ
      */
    const SAMPLE_USER_CAN_ACCESS_POST = array(                
       5616,		//体験レッスン
       6438,		//0-01はじめに
       6440,		//0-02「プログラム」って？
       6441,		//0-03「スクラッチ」のがめんのせつめい
       6443,		//0-04スクラッチでできることって？
       6444,		//0-05スクラッチの作品をみてみよう！
       6445,		//0-06作品をつくるときのてじゅん
        110,		//レッスン（アニメーション）
        32,			//トピック（コスチューム）
        29,			//トピック（コスチューム）
        442,		//トピック（コスチューム）
        559,		//トピック（コスチューム）
        688,		//トピック（コスチューム）
        35870,		//ふりかえり1(コスチューム)
        35866,		//ふりかえり2(コスチューム)
        37298,		//章末問題（コスチューム）
        5611,		//レッスン（りんごとり）
        10680,		//レッスン（スターゲッター）
        12180,		//レッスン（ブロックくずし）
        31445,		//レッスン（空飛ぶネコ）
        32212,		//レッスン（ジャンピング・モンキー）
        32218,		//レッスン（ジャンピング・モンキー）
        32220,		//レッスン（ジャンピング・モンキー）
        32222,		//レッスン（ジャンピング・モンキー）
        32224,		//レッスン（ジャンピング・モンキー）
        32226,		//レッスン（ジャンピング・モンキー）
        32748,		//レッスン（チーズ・チェイス）
        32980,		//レッスン（ほうきのうんめい）
        33811,		//レッスン（サムライトレーニング）
        34384,		//レッスン（トロピカルチューン）
        10588,		//トピック（りんごとり）
        10702,		//トピック（スターゲッター）
        12198,		//トピック（ブロックくずし）
        31448,		//トピック（空飛ぶネコ）
        32218,		//トピック（ジャンピング・モンキー）
        32750,		//トピック（チーズ・チェイス）
        33060,		//トピック（ほうきのうんめい）
        33819,		//トピック（サムライトレーニング）
        34507,		//トピック（トロピカルチューン）
        15894,		//ハイレベルコース(はしごののぼりおり)
        22526,		//レッスンジュニアPG検定
        40028,		//ジュニアPG検定（検定とは）
        40108,		//ジュニアPG検定講師向け（検定とは）
        40820		//ゲームを作ろうドリル版
//                15894,
//                15902,
//                15895
    );

    /**
     * チェックアウトページで複数の料金を見せる場合
     * 半年だと割引など・・等々オプションで支払を選ぶ場合
     * 例）
     * '1'=> array(1,2,3)
     * レベルIDが「1」のプランに対して、割引ID（1，2，3）を紐づけるという意味
     */
    const TKS_MULTI_PRICE_FOR_CHECKOUT_PAGE = array(
        '1' => array( 1, 2, 3 ),    //映像授業のみ
        '2' => array( 4, 5, 6 ),    //映像授業+質問タイム
        '4' => array( 7, 8, 9 )     //映像授業+プライベートレッスン
    );

    /**
     * チェックアウトする時に、受け入れない(ブロックするメールアドレスのリスト)
     */
    const TKS_BLOCK_MAIL_DOMEIN_FOR_CHECKOUT_PAGE = array( 
        "yopmail.com", 
        "*.aol.com",
        "twzhhq.com",
        "instance-email.com",
        "john-titor.work",
        "1-tm.com",
        "moimoi.re" 
    );
}