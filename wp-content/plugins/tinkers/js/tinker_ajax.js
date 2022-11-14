var $ = jQuery.noConflict();
var elm_open_hint_p = null;

jQuery(".list_hint_warp > li").each(function(){
  jQuery(this).attr('hint-id',jQuery(this).index())
});   

//初期化タブを全て閉じる(拡張タブがクリックされたら)
jQuery('.ld-tabs-navigation').click(function() {
  if (elm_open_hint_p == null){
    jQuery('.list_hint_warp p')
    .next('.list_hint_warp .list_hint')
    .slideUp();
  }else{
    jQuery('.list_hint_warp p')
    .not(jQuery(elm_open_hint_p))
    .next('.list_hint_warp .list_hint')
    .slideUp();
  }
});

$(".list_hint_warp p").each(function() {
  var isWait = false;
  $(this).click(function() {
    elm_open_hint_p = $(this);

    var hint_id = elm_open_hint_p.parents("li").attr("hint-id");

    if (!elm_open_hint_p.hasClass("actived")) {
      // 確認ダイアログの表示(JQuery)
      var strTitle = "ヒントを表示";
      var strComment = jQuery(this).text() + "を開きますか？";

      // ダイアログのメッセージを設定
      jQuery("#show_dialog").html(strComment);

      // ダイアログを作成
      if (!isWait) {
        //送信確認
						swal({
              title: strComment,
              icon: "info",
              allowOutsideClick: false,
              buttons: {
                ok: "OK",
                cancel: "キャンセル"
              }
            })
            .then(function(val) {
              if (val == "ok") {
                // Okボタンが押された時の処理
                //クリックされた.list_hint_warpの中のp要素に隣接するul要素が開いたり閉じたりする。
                //クリックされた.accordion2の中のp要素以外の.accordion2の中のp要素に隣接する.accordion2の中の.innerを閉じる
                jQuery(".list_hint_warp p")
                  .not(jQuery(elm_open_hint_p))
                  .next(".list_hint_warp .list_hint")
                  .slideUp();
  
                if (!$(this).hasClass("actived")) {
                  $.ajax({
                    cache: false,
                    url: tinker_handler_ajax.admin_ajax,
                    type: "POST",
                    data: {
                      action: "save_hint_status",
                      hint_id: hint_id
                    },
                    beforeSend: function() {
                      isWait = true;
                    },
                    success: function(data, textStatus, jqXHR) {
                      elm_open_hint_p.addClass("actived");
                      elm_open_hint_p.next(".list_hint").slideToggle();
                      jQuery("#hint_info").text("使用ヒント数は" + $(".list_hint_warp li p.actived").length + "です");
                      isWait = false;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                      console.log("The following error occured: " + textStatus, errorThrown);
                    },
                    complete: function(jqXHR, textStatus) {}
                  });
                }
                return false;
              } else {
                return false;
              }
            });




      }
    } else {
      
      jQuery(elm_open_hint_p)
        .next(".list_hint")
        .slideToggle();
      jQuery('.list_hint_warp p')
          .not(jQuery(elm_open_hint_p))
          .next('.list_hint_warp .list_hint')
          .slideUp();
    }
  });
});

