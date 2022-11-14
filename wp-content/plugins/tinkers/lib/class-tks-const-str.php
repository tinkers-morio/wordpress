<?php

class tks_const_str{

    const STUDENT = "生徒";                     
    const MNU_STUDENT_MANEGEMENT = "生徒管理";   

    /**
     * 申し込み完了後に表示される確認画面のメッセージ（支払いが振込の時のみに使用）
     * filter pmpro_after_checkoutを見よ
     */
    const AFTER_CHECKOUT_COMFIRM_MSG_PAY_BY_CHECK_BANK_INFO = 
    "<p>ご請求額を下記口座へお振込み下さいますようお願い申し上げます。</p>"
    . "<div class='box'>"
    ."<span class='box-title'>お振込先</span>"
    . "<ul style='list-style-type: none;'>"
    . "<li>[振込口座名]:中央産業株式会社（チュウオウサンギョウカブシキガイシャ）</li>"
    . "<li>[銀行名]:三菱UFJ銀行</li>"
    . "<li>[支店名]:小山支店</li>"
    . "<li>[口座種類]:（当座）</li>"
    . "<li>[口座番号]:9000156</li>"
    . "</ul>"
    . "</div>";
    const AFTER_CHECKOUT_COMFIRM_MSG_PAY_BY_CHECK = 
     "<p>お振込み後は、弊社にてご入金の確認後、システムをご利用頂けるようになります。<br />"
    . "お手数をおかけしますが、何卒よろしくお願い申し上げます。</p>";

    const BEFORE_CHECKOUT_MSG_PAY_BY_CHECK = 
    "<p>お申し込み後に、お振込み情報をメールにて送信致します。<br>"
    ."ご入金が確認できましたらすぐに使用できるようになります。</p>";
		
}