jQuery(document).ready(function() {

    var tks_baloon;
    
    tks_baloon = {
        //[1]baloon表示の処理-----------------------------
        show: function(dom,text,pos="left"){
            
            dom.showBalloon({
                contents: text,
                position: pos,
                showDuration:0,
                hideDuration: 1000,
                css: {
                    "width": "12em",
                    "fontSize": ".9rem",
                    "border-radius": "5px",
                    "padding": "10px",
                    "border": "none",
                    "boxShadow": "0 10px 25px 0 rgba(0, 0, 0, .5)",
                    "background-color": "#5bb0c2",
                    "opacity":"0.9"
                    },
            }).addClass('active');
        },

        //[2]baloon非表示の処理
        hide: function(dom){
                dom.hideBalloon().removeClass('active');
        }
    }

    window.TKS_BL = tks_baloon;

    //他の箇所をクリックしたら閉じるように
    // jQuery(document).on('click', function (e) {
    //     //if (!jQuery(e.target).closest('.baloon').length) {
    //         //elem.removeClass('is-active').fadeOut(speed);
    //         hideAction(elem);
    //     //}
    // });

    // elem.balloon({
    //     // 吹き出しを右に出すと画面の邪魔にならない場合が多いです
    //     position: "left",
    //     // 吹き出し端に付く正三角形のサイズ（高さ）
    //     tipSize: 5,
    //     // 吹き出しの CSS 設定です
    //     css: {
    //     //"height": "270px",
    //     "opacity": "1",
    //     "color": "#000",
    //     "font-size": "16px",
    //     "border-radius": "10px",
    //     "border": "solid 2px #A63814",
    //     "padding": "10px",
    //     "background-color": "#5bb0c2",
    //     "opacity": 1,
    //     },
    //     // CSS の対象となる、吹き出しの内部コンテンツを定義します
    //     "html": true,
    //     "contents": '<img src="https://winofsql.jp/image/sab.gif"> 受講できます'
    //     });
});