jQuery(document).ready(function() {
    jQuery("input").focus(function(){
        msg = getHelpMsg(jQuery(this).attr('id'));
        TKS_BL.show(jQuery(this),msg); //[1]baloon表示の処理を着火
    }).blur(function(){
        TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
    });

    jQuery("input").on({'mouseenter' : function(){
        msg = getHelpMsg(jQuery(this).attr('id'));
        TKS_BL.show(jQuery(this),msg); //[1]baloon表示の処理を着火
    },'mouseleave' : function(){
        TKS_BL.hide(jQuery(this)); //[2]baloon非表示の処理を着火
    }
    });
    
    function getHelpMsg(el_id){
        if (el_id == "username"){
            return 'ログインするためのIDを入力します。<br>登録後にユーザー名を変更する事はできません。<br><ul style="color:blue;"><li>6文字以上20字以内で入力して下さい。</li><li>半角英数字 ※大文字と小文字は同一視されます</li><li>記号「. _ -」 ※半角スペースは「_」に変換されます</li></ul>';
        }
        if (el_id == "password"){
            return 'ログインするためのパスワードを入力します。<br><ul style="color:blue;"><li>8文字以上20字以内で入力して下さい。</li><li>半角英数記号が使用できます。</li></ul>';
        }
        if (el_id == "password2"){
            return '確認のためもう一度パスワードを入力します。';
        }
        if (el_id == "bemail"){
            return 'メールアドレスを入力します。';
        }
        if (el_id == "bconfirmemail"){
            return '確認のためもう一度メールアドレスを入力します。';
        }
        if (el_id == "company_name"){
            return '会社名/団体名を入力します。<br><span style="color:blue">個人の場合は、「個人」とご入力下さい。</span>';
        }
        if (el_id == "first_name"){
            return 'お名前(苗字)を入力します。';
        }
        if (el_id == "last_name"){
            return 'お名前(苗字)を入力します。';
        }
        if (el_id == "tks_zipcode"){
            return '郵便番号を入力します。<br><span style="color:blue;">ハイフンは必要ございません。</span>';
        }
        if (el_id == "tks_address1"){
            return '住所を入力します。';
        }
        if (el_id == "tks_phone"){
            return '電話番号を入力します。<br><span style="color:blue;">ハイフンは必要ございません。</span>';
        }
    }
    

});